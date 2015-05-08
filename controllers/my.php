<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * 个人中心
 * @author chenxiao
 * @date 2014/09/26
 */
class my extends base {
	private $jdata = array();
	
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->getUserLoginId();
	}
	
		
}