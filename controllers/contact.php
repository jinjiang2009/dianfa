<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class contact extends base{

	

  function __construct(){
    parent::__construct();
    
    
  }
  /**
   * 综合搜索列表页
   */
  public function index(){
  	$data = $this->data;

  	$this->load->view('default/contact.php',$data);
  }

}


