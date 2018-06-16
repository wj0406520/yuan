<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  大文件分片上传文件工具
+----------------------------------------------------------------------
*/
namespace core\tool;

//实例化并获取系统变量传参
//$upload = new Upload($_FILES['file']['tmp_name'],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
//调用方法，返回结果
//$upload->apiReturn();
class UploadSlice
{
    private $filepath = ''; //上传目录
    private $tmpPath;  //PHP文件临时目录
    private $blobNum; //第几个文件块
    private $totalBlobNum; //文件块总数
    private $fileName; //文件名
    private $dir_file_name; //远程路径
    private $file_end_name; //最终路径
    private $ext; //文件后缀名
    private $ftype = ['mp4', 'avi', 'wmv', 'flv','rmvb','jpg','png'];//远程路径

    public function __construct($tmpPath, $blobNum, $totalBlobNum, $fileName)
    {
        $this->tmpPath =  $tmpPath;
        $this->blobNum =  $blobNum;
        $this->totalBlobNum =  $totalBlobNum;
        $this->fileName =  $fileName;

		//上传文件夹
		$dir = '/big/'.date('Ym/d').'/';
		// 上传路径
		$this->filepath = DATA.$dir;
		$this->dir_file_name = $dir;
		$this->ext = substr($this->fileName, strrpos($this->fileName, '.')+1);

		if(!in_array($this->ext, $this->ftype)){
            $data['code'] = 3;
            $data['msg'] = 'file type error';
            $data['file_path'] = '';
            $this->echoJson($data);
		}
        $this->moveFile();
        $this->fileMerge();

    }

    //判断是否是最后一块，如果是则进行文件合成并且删除文件块
    private function fileMerge()
    {
        if($this->blobNum == $this->totalBlobNum){
            $blob = '';
            for($i=1; $i<= $this->totalBlobNum; $i++){
                $blob .= file_get_contents($this->filepath.'/'. $this->fileName.'__'.$i);
            }
			$newname = TIME. mt_rand(100, 999). '.' .$this->ext;
			$this->dir_file_name .= $newname;
			$this->file_end_name = $this->filepath.$newname;
            file_put_contents($this->filepath. $newname,$blob);
            $this->deleteFileBlob();
        }
    }

   //删除文件块
    private function deleteFileBlob()
    {
        for($i=1; $i<= $this->totalBlobNum; $i++){
            @unlink($this->filepath.'/'. $this->fileName.'__'.$i);
        }
    }

    //移动文件
    private function moveFile()
    {
        $this->touchDir();
        $filename = $this->filepath.'/'. $this->fileName.'__'.$this->blobNum;
        move_uploaded_file($this->tmpPath,$filename);
    }

    //API返回数据
    public function apiReturn()
    {
        if($this->blobNum == $this->totalBlobNum){
                if(file_exists($this->file_end_name)){
                    $data['code'] = 2;
                    $data['msg'] = 'success';
                    $data['file_path'] = $this->dir_file_name;
                }
        }else{
                if(file_exists($this->filepath.'/'. $this->fileName.'__'.$this->blobNum)){
                    $data['code'] = 1;
                    $data['msg'] = 'waiting for all';
                    $data['file_path'] = '';
                }
        }
        $this->echoJson($data);
    }

    private function echoJson($data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    //建立上传文件夹
    private function touchDir()
    {
        if(!file_exists($this->filepath)){
            return mkdir($this->filepath, 0777, true);
        }
    }
}