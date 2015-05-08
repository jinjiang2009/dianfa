<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * 企业顾问
 * @author chenxiao
 * @date 2014/09/26
 */
class enterprise extends base {
	private $jdata = array();
	
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "enterprise_model",'m_e' );
	}
	
	function index(){
		//$this->output->cache(1440);
		$data = array();
		$data = $this->data;
		$type = $this->input->get('type');
		$data ['type'] = intval($type);
		$type = empty($type)?'':' and infotype='.intval($type);
		$type .= ' and type=1 ' ;
		$data ['list'] = $this->m_e->productlist($type);
		//$data ['pub']= $this->m_e->public_product(1);
		$this->load->view('default/enterprise',$data);
	}
	
	/**
	 * 产品性情页
	 */
	function product(){
		$id = $this->input->get('id');
		$id = intval($id);
		$data=array();
		$data = $this->data;
		$data ['info'] = $this->m_e->product($id);
		$this->load->view('default/product',$data);
	}
	
		
}