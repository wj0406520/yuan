<?php

// 备份文件

namespace app\admin\views;

use core\yuan\ViewsAbstract;

class Views extends ViewsAbstract
{

	public function checkbox()
	{
		$html = '<label class="choose"><input type="checkbox" value=":value" :choose name=":form_name" ><span>:desc</span></label>';
        return $html;
	}
	public function radio()
	{
		$html = '<label class="choose"><input type="radio" value=":value" :choose name=":form_name" ><span>:desc</span></label>';
        return $html;
	}

	public function hidden()
	{
		$html = '<input type="hidden" handle=":handle" target=":target" value=":value" name=":form_name" />';
        return $html;
	}
	public function password()
	{
		$html = '<input type="password" handle=":handle" target=":target" name=":form_name" placeholder=":placeholder"/>';
        return $html;
	}
	public function text()
	{
		$html = '<input type="text" handle=":handle" target=":target" name=":form_name" value=":value" placeholder=":placeholder"/>';
        return $html;
	}
	public function date()
	{
		$html = '<input handle=":handle" target=":target" type="text" value=":value" name=":form_name" class="select_time1" placeholder=":placeholder" />';
        return $html;
	}

	public function file()
	{
		$html = '<a class="btn_addPic" href="javascript:void(0);">
					<span><i class="icon-font">&#xe026;</i>:name</span>
                        <input class="filePrew" type="file" size="3" name=":form_name" handle=":handle" target=":target" />
                    </a>
                <span class="label badge-color0 file-name"></span>';
        return $html;
	}
	public function textarea()
	{
		$html = '<textarea name=":form_name" handle=":handle" target=":target" placeholder=":placeholder">:value</textarea>';
        return $html;
	}

	public function option()
	{
		$html = '<option value=":value" :choose>:desc</option>';
		return $html;
	}
	public function select()
	{
		$html = '<select name=":form_name"><option>全部</option>:option</select>';
		return $html;
	}
	public function myextend()
	{
		$html = '<input name=":form_name" haha=":haha" />';
		return $html;
	}

	public function page()
	{
		$html = '<span>总共:total 条 每页:size条 :page/:count 页</span>
                	<ul class="pagination">
	                    <li><a href=":first"><<</a></li>
	                    <li><a href=":previous"><</a></li>
	                    <li class="active"><a href="#">:page</a></li>
	                    <li><a href=":next">></a></li>
	                    <li><a href=":last">>></a></li>
	                </ul>';
		return $html;
	}


}