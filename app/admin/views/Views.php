<?php

namespace app\admin\views;

use core\yuan\ViewsAbstract;
use core\yuan\Config;

class Views extends ViewsAbstract
{

	public function eSwitch($url,$value,$y='是',$n='否')
	{
		$html =  '<div class="switch-model">';
		$html .= '<a class = "switch-on '.($value!=1?'switch-hide':'').'" href="'.$url.'?data=0">'.$y.'</a>';
		$html .= '<a class = "switch-off '.($value!=0?'switch-hide':'').'" href="'.$url.'?data=1">'.$n.'</a>';
		$html .= '</div>';

	    echo $html;
	}

	public function eType($type, $color, $config_type)
	{
		$temp = $this->getType($config_type);
		$html = '<span class="badge badge-color'.($color-$type).'">';
        $html .= $temp[$type];
        $html .= '</span>';
        echo $html;
	}
	public function eDate($time)
	{
		echo $time?date('y-m-d H:i',$time):'暂无';
	}

	public function ecDate($val)
	{
		echo date('y-m-d H:i',$val[Config::getMore('database.create_at')]);
	}

	public function euDate($val)
	{
		echo date('y-m-d H:i',$val[Config::getMore('database.update_at')]);
	}
	public function esEditor()
	{
		$url = P('PATH');
		$str = '
<script type="text/javascript" charset="utf-8" src="'.$url.'um/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="'.$url.'um/umeditor.min.js"></script>
<script type="text/javascript" src="'.$url.'um/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">
    //实例化编辑器
    var um = UM.getEditor("myEditor");
</script>';
		echo $str;
	}
	public function eToken()
	{
		$token = 'T'.$this->randString(15);
		$this->setSession(['_token'=>$token]);
		$str = '<input type="hidden" name="_token" value="'.$token.'"/>';
		echo $str;
	}

	protected function checkbox()
	{
		$html = '<label class="choose"><input type="checkbox" value=":value" :choose name=":form_name[]" ><span>:desc</span></label>';
        return $html;
	}
	protected function radio()
	{
		$html = '<label class="choose"><input type="radio" value=":value" :choose name=":form_name" ><span>:desc</span></label>';
        return $html;
	}

	protected function hidden()
	{
		$html = '<input type="hidden" handle=":handle" target=":name" value=":value" name=":form_name" />';
        return $html;
	}
	protected function password()
	{
		$html = '<input type="password" handle=":handle" target=":name" name=":form_name" placeholder=":placeholder"/>';
        return $html;
	}
	protected function text()
	{
		$html = '<input type="text" handle=":handle" target=":name" name=":form_name" value=":value" placeholder=":placeholder"/>';
        return $html;
	}
	protected function number()
	{
		$html = '<input type="number" handle=":handle" target=":name" name=":form_name" value=":value" placeholder=":placeholder"/>';
        return $html;
	}
	protected function date()
	{
		$html = '<input handle=":handle" target=":name" type="text" value=":value" name=":form_name" class="select_time1" placeholder=":placeholder" />';
        return $html;
	}

	protected function foption()
	{
		$html = '<li><div class="close">X</div>
		<img src=":desc" onclick="showCover(this)" data-url=":desc"><input name=":form_name" type="hidden" value=":desc"></li>';
		return $html;
	}
	protected function file()
	{
		$html = '<a class="btn-add-pic" href="javascript: void(0);">
					<span><i class="icon-font">&#xe026;</i>上传图片</span>
                        <input class="file-prew" type="file" size="3" name=":form_name" handle=":handle" target=":name" />
                    </a>
	            <div class="img-line" name=":form_name" type=":oneormore">
	                <div class="image-choose"><ul>:option</ul></div>
	            </div>';
        return $html;
	}
	protected function textarea()
	{
		$html = '<textarea style="height:100px;width:300px;" name=":form_name" handle=":handle" target=":name" placeholder=":placeholder">:value</textarea>';
        return $html;
	}

	protected function option()
	{
		$html = '<option value=":value" :choose>:desc</option>';
		return $html;
	}
	protected function select()
	{
		$html = '<select name=":form_name"><option value="-1">全部</option>:option</select>';
		return $html;
	}
	protected function myextend()
	{
		$html = '<input name=":form_name" haha=":haha" type="text" />';
		return $html;
	}
	protected function editor()
	{
		$html = '<script type="text/plain" id="myEditor" name=":form_name">:value</script>';
		return $html;
	}

	protected function page()
	{
		$html = '<span style="float:right;"> 当前共 :total 条记录，每页 :size 条，当前第 :page/:count 页
                                  <a href=":first">首页</a>
                                  <a href=":previous">上一页</a>
                                  <a href=":next">下一页</a>
                                  <a href=":last">尾页</a>
                              </span>';
		return $html;
	}


}