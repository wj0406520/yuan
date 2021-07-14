<?php

namespace core\yuan;

abstract class ViewsAbstract
{
    use Common;

	protected $type = '';
	protected $data = [];
	protected $html = [];
	protected $form = [];
	protected $success = '';
	protected $continue = false;
	protected $name = 0;
	protected $page = '';

	abstract protected function hidden();
	abstract protected function password();
	abstract protected function text();

	abstract protected function date();
	abstract protected function file();
	abstract protected function textarea();

	abstract protected function option();
	abstract protected function select();

	abstract protected function checkbox();
	abstract protected function radio();

	abstract protected function page();

	protected function inputLink()
	{
		$type = $this->getOneForm('type');
		if($type=='radio' || $type=='checkbox'){
			$this->chooseLink();
		}
		if($type == 'hidden'
		|| $type =='password'
		|| $type =='text'
		|| $type =='number'
		|| $type =='date'){
			$this->oneLink();
		}
	}
	protected function oneLink()
	{
		$form = $this->getOneForm('form');
		if(method_exists($this,$form)){
			$this->success = call_user_func([$this,$form]);
		}else{
			$this->continue = true;
		}
	}

	protected function chooseLink()
	{
		if($this->getOneForm('form')=='radio'){
			$html = $this->radio();
		}else{
			$html = $this->checkbox();
		}
		$this->success = $this->circle($html, 0);
	}

	protected function selectLink()
	{
		$option = $this->option();
		$html = $this->circle($option);
		$select = $this->select();
		$this->success = str_replace(':option',$html,$select);
	}
	protected function fileLink()
	{
		$option = $this->foption();
		$value = $this->getOneForm('value');
		$data = $this->getOneForm('data');

		if(!is_array($data)){
			$html = $value?str_replace(':desc',$value,$option):'';
		}else{
			$html = $this->circle($option);
		}
		if(!$this->getOneForm('oneormore')){
			$this->setOneForm('form_name',$this->getOneForm('form_name').'[]');
		}
		$file = $this->file();
		$this->success = str_replace(':option',$html,$file);
	}

	protected function returnHtml($html)
	{
		$this->html[$this->name][] = [
			'html'=>$html,
			'name'=>$this->getOneForm('name'),
			'form_name'=>$this->getOneForm('form_name'),
			'value'=>$this->getOneForm('value'),
		];
	}

	protected function circle($html, $is_select = true)
	{
		$re = '';
		$data = $this->getOneForm('data');
		$data || $data = $this->getType($this->getOneForm('sql_type'));
		$value = $this->getOneForm('value');
		$value = is_array($value)?$value:explode(',', $value);
		$search = [':choose',':value',':desc'];
		if(!$data || !is_array($data)){
			return false;
		}
		$str = $is_select?'selected = "selected"':'checked = "checked"';
		foreach ($data as $val => $desc) {
			$choose = in_array($val, $value) ? $str:'';
			$replace = [$choose,$val,$desc];
			$re .= str_replace($search, $replace, $html);
		}
		return $re;

	}
	protected function replace($page = 0)
	{
		$html = $this->success;
		$pattern = "/:([a-z|_]+)/";
		preg_match_all($pattern, $html, $arr);
		// echo $html;
		if(!count($arr[0])){
			return '';
		}
		$search = $arr[0];
		foreach ($arr[1] as &$value) {
			$value = $this->getOneForm($value);
		}
		$html = str_replace($search, $arr[1], $html);
		if(!$page){
			$this->returnHtml($html);
		}else{
			return $html;
		}
	}

	// $value, $name, $form, $type, $sql_type, $placeholder, $target, $handle
	protected function html()
	{

		$data = $this->form;
		$f = $data['form'];

		if($f == 'select'){
			$this->selectLink();
		}

		if($f == 'file'){
			$this->fileLink();
		}

		if($f == 'textarea'){
			$this->success = $this->textarea();
		}

		if($f == 'radio' || $f=='checkbox'){
			$this->chooseLink();
		}
		if($f == 'hidden'|| $f =='password'|| $f =='text'|| $f =='number'|| $f =='date'){
			$this->oneLink();
		}
		if($f == 'editor'){
			$this->oneLink();
		}
		if(strpos($f,'extend')!==false){
			$this->oneLink();
		}
		if($this->continue){
			return false;
		}
		$this->replace();
	}
	protected function getOneForm($key)
	{
		return array_key_exists($key, $this->form)?$this->form[$key]:'';
	}
	protected function setOneForm($key, $value)
	{
		if(array_key_exists($key, $this->form)){
			$this->form[$key] = $value;
		}
	}
	protected function getType($type)
	{
		return Config::getMore('type.'.$type);
	}
	protected function handle($data)
	{
		$a = Config::get('form');
		array_walk($data,function($value,$name) use($a){
	        $str = strtolower(P('URL_CONTROL').'.'.P('URL_MODEL').'.'.$name);
	        $temp = isset($a[$str])?$a[$str]:(isset($a[$name])?$a[$name]:'');
	        !$temp && $this->error('no form '.$name);
			$this->form = $temp;
			$this->form['form_name'] = $name;
			$this->form['value'] = $value['value'];
			$this->form['data'] = $value['data'];
			$this->html();
		});
	}

	public function data($data)
	{
		$this->data = $data;
	}
	public function run()
	{
		if(!$this->data || !is_array($this->data)){
			return false;
		}
		array_walk($this->data,function($value, $name){
			$this->name = $name;
			$this->handle($value);
		});
	}
	public function getForm($name = 0)
	{
		$temp = explode('.', $name);
		if(array_key_exists($name, $this->html)){
			$re = $this->html[$name];
		}else{
			$re = [];
			// Error::setMessage($name.' not exists');
		}
		return $re;
	}
	/*
	(
	    [form] => input
	    [type] => text
	    [name] => 登录账号
	    [placeholder] => 手机号码
	    [handle] => require
	    [verificate] => phone
	    [form_name] => user_login_name
	    [value] => 13888888887
	    [data] =>
	)
	Array
	(
	    [form] => input
	    [type] => text
	    [name] => 登录密码
	    [placeholder] => 登陆密码
	    [verificate] => Array
	        (
	            [0] => fill
	            [1] => string
	            [2] =>
	        )

	    [form_name] => user_login_password
	    [value] =>
	    [data] =>
	)
	Array
	(
	    [form] => select
	    [name] => 选择代理商
	    [handle] => require
	    [verificate] => search
	    [form_name] => choose_manage
	    [value] => 1
	    [data] => Array
	        (
	            [1] => 1～测试账户～13888888888
	        )

	)*/

	public function setForm($form)
	{
		$this->form = $form;
		$this->name = $form['form_name'];
		$this->html();
	}

	public function setPage($page)
	{
		// print_r($page);
		$this->success = $this->page();
		$this->form = $page;
		$html = $this->replace(1);
		$this->page = $html;
		// echo 22;
	}
	public function getPage()
	{
		return $this->page;
	}

    public function error($message)
    {
      Error::setMessage($message);
    }
}