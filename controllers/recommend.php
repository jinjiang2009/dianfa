<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class recommend extends base{
	private $jdata = array();
	private $limit = 20;
	function __construct(){
		parent::__construct();

		$this->load->model ( "contract_model",'m_c' );
	}
	function index($id){
		$data = $this->data;
		$post = $this->input->post();
		if(!empty($post)){
			$post['is_show'] = 2;
			$this->m_c->getRecommend('df_recommend',$post);
		}
		$data['cons'] = $this->m_c->getPerson($id);
		$data['expert'] = $this->m_c->getExpert();
		$this->load->view('default/recommend'.$id,$data);
	}
}