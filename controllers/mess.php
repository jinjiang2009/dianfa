<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * 首页
 * @author chenxiao
 * @date 2014/09/26
 */
class mess extends base {
	//private $jdata = array();
	function __construct() {
		parent::__construct ();
		//$this->jdata["success"] = 0;
		//$this->jdata["message"] = "";
		$this->load->model ( "contract_model",'m_c' );
	}	
	
	function index($id){//echo $id;die;
		//$this->output->cache(1440);
		//$data = array();
		$data = $this->data;
		$data['message'] = $this->m_c->mess($id);
		$data['shangn'] = $id -1;
		$data['xian'] = $id+1;
		$data['shang'] = $this->m_c->mess($data['shangn']);
		$data['xia'] = $this->m_c->mess($data['xian']);
		$this->load->view('default/mess',$data);
	}
	
}	