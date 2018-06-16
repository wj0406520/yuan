<?php

namespace core\yuan;

abstract class ViewsAbstract
{
	protected $type = '';
	protected $data = [];
	protected $html = [];
	protected $form = [];
	protected $success = '';
	protected $continue = false;
	protected $name = 0;
	protected $page = '';

	abstract public function hidden();
	abstract public function password();
	abstract public function text();

	abstract public function date();
	abstract public function file();
	abstract public function textarea();

	abstract public function option();
	abstract public function select();

	abstract public function checkbox();
	abstract public function radio();

	abstract public function page();

	protected function inputLink()
	{
		$type = $this->getOneForm('type');
		if($type=='radio' || $type=='checkbox'){
			$this->chooseLink();
		}
		if($type == 'hidden'
		|| $type =='password'
		|| $type =='text'
		|| $type =='date'){
			$this->oneLink();
		}
	}
	protected function oneLink()
	{
		$type = $this->getOneForm('type');
		if(method_exists($this,$type)){
			$this->success = call_user_func([$this,$type]);
		}else{
			$this->continue = true;
		}
	}

	protected function chooseLink()
	{
		if($this->getOneForm('type')=='radio'){
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

	protected function returnHtml($html)
	{
		$name = $this->getOneForm('name');
		$form_name = $this->getOneForm('form_name');
		$this->html[$this->name][] = ['html'=>$html,'name'=>$name,'form_name'=>$form_name];
	}

	protected function circle($html, $is_select = true)
	{
		$re = '';
		$data = $this->getOneForm('data');
		$data || $data = $this->getType($this->getOneForm('sql_type'));
		$value = $this->getOneForm('value');
		$value = explode(',', $value);
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
		if($data['form'] == 'select'){
			$this->selectLink();
		}

		if($data['form'] == 'file'){
			$this->success = $this->file();
		}
		if($data['form'] == 'textarea'){
			$this->success = $this->textarea();
		}
		if($data['form'] == 'input'){
			$this->inputLink();
		}
		if($data['form'] == 'extend'){
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
	protected function getType($type)
	{
		return Config::get('type.'.$type);
	}
	protected function handle($data)
	{
		$a = Config::get('form');
		array_walk($data,function($value,$name) use($a){
			$this->form = $a[$name];
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
		if(array_key_exists($name, $this->html)){
			$re = $this->html[$name];
		}else{
			Error::setMessage($name.' not exists');
		}
		return $re;
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
}