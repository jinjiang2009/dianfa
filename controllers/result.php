<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class result extends base{


	function __construct(){
		parent::__construct();
		$this->load->model ( "contract_model",'m_c' );
	}
	
	function index(){
		$data = $this->data;
		
		$data['result']=$this ->m_c->result();
		$this->load->view('default/result',$data);
	}
	function event(){
		$data = $this->data;
		$data['zhuan']=$this->m_c->getExpert();
		$data['event']=$this->m_c->getBot();
		$this->load->view('default/resevent',$data);
	}
	function wait(){
		$this->output->cache(1440);
		$data = $this->data;
		$this->load->view('default/wait',$data);
	}
}