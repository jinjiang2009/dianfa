<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @desc 通用的
 * @date 2014/09/19
 */
class commonality extends base {
	
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "base_model",'m_b' );
	}
	
	/**
	 *地区联动
	 */
	function region(){
		$p = array();
		$p = $this->get_params();
		$p['uuid'] = empty($p['uuid'])?'':$p['uuid'];
		$re = $this->m_b->region($p['uuid']);
		if (empty($re)){
			$this->jdata["success"] = 0;
			$this->json_out($this->jdata);
		}
		$this->jdata["success"] = 1;
		$this->jdata["message"] = $re;
		$this->json_out($this->jdata);
	}
	
}