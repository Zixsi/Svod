<?php
class Template_mod extends MX_Controller
{
	private $arrParams = array();

	public function __construct()
	{
		parent::__construct();
	}
	
	private function index()
	{

	}
	
	public function Header($template = 'default', $data = array())
	{
		if(empty($template))
			$template = 'default';
			
		$this->arrParams = array_merge($this->arrParams, $data);
		echo $this->load->view($template.'/header', array('arResult' => $this->arrParams));
	}
	
	public function Footer($template = 'default', $data = array())
	{
		if(empty($template))
			$template = 'default';
	
		$this->arrParams = array_merge($this->arrParams, $data);
		echo $this->load->view($template.'/footer', array('arResult' => $this->arrParams));
	}
}
