<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  翻页工具
+----------------------------------------------------------------------
*/
namespace core\tool;

class Page
{

    static public function pageShow($total,$page=false,$pagesize=4)
    {

        $cnt   = ceil($total/$pagesize);  // 得到总页数

        $page  =$page>$cnt?$cnt:$page;

        $uri   = $_SERVER['REQUEST_URI'];

        $parse = parse_url($uri);

        $param = array();
        if(isset($parse['query'])) {
            parse_str($parse['query'],$param);
        }

        // 不管$param数组里,有没有page单元,都unset一下,确保没有page单元,
        // 即保存除page之外的所有单元
        unset($param['page']);

        $url = $parse['path'] . '?';
        if(!empty($param)) {
            $param = http_build_query($param);
            $url = $url . $param . '&';
        }
        $left = $page-1;
        $right = $page+1;
        $first=$url.'page=1';
        $previous=($left<1)?$url.'page=1':$url.'page='.$left;
        $next=($right>$cnt)?$url.'page='.$cnt:$url.'page='.$right;
        $last=$url.'page='.$cnt;
        $page10=($pagesize==10)?'selected="selected"':'';
        $page20=($pagesize==20)?'selected="selected"':'';
        $page40=($pagesize==40)?'selected="selected"':'';

$str=<<<EXT
              <tr>
                <td colspan="20">
                  <span style="float:right;"> 当前共 $total 条记录，每页
                      <form action="?" method="get" id="pagesize" style="display: inline;">
                        <select name="pagesize" onchange="form.submit();">
                          <option value="10" $page10 >10</option>
                          <option value="20" $page20 >20</option>
                          <option value="40" $page40 >40</option>
                        </select>
                      </form>
                      条，当前第 $page 页
                      <a href="$first">首页</a>
                      <a href="$previous">上一页</a>
                      <a href="$next">下一页</a>
                      <a href="$last">尾页</a>
                  </span>
                </td>
              </tr>

EXT;


        return $str;

    }

}