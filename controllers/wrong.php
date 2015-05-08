<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class wrong extends base{

	

  function __construct(){
    parent::__construct();
    //$this->load->model ( "search_model",'search' );
    
  }
  /**
   * 综合搜索列表页
   */
  public function index(){
  	$data = $this->data;

  	$this->load->view('default/wrong.php',$data);
  }
 
}


