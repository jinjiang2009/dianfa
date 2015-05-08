<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class event extends base{



	function __construct(){
		parent::__construct();
		$this->load->model ( "contract_model",'m_c' );

	}
/*
	function index($id=null){
		$data = $this->data;
		//$data['judge'] = false;
		$ip = getip();
		if(!empty($id)){
			$ttop = $this ->m_c->getJudge($id,$ip);
			if(empty($ttop)){
				
				#插入
				$arr = array();
				$arr['ip'] = $ip;
				$arr['type'] = 2;
				$arr['recommendid'] = $id;
				$this ->m_c->getInsert('df_toupiao',$arr);
				$this ->m_c->getUpdate($id);
				$string = '投票成功，感谢您的参与！';
				$data['now'] = $this ->m_c->getNow($id);
				if(!empty($data['now'])){
					$data['judge'] = true;
				}
			}else{
				$string = '您已投票，请不要重复投票！';
				
			}
			header( "content-type:text/html;charset=utf-8" );
			echo "<script type=\"text/javascript\">";
			echo "alert(\"{$string}\");";
			echo "</script>";
			
			
		}
		$data['expert'] = $this ->m_c->getSet();
		$data['top'] = $this ->m_c->getBot();
		$data['all'] = $this ->m_c->getAll();
		$this->load->view('default/event',$data);
	}
*/
	function index(){
		$data = $this->data;
		$post = $this->input->post();
		$ip = getip();
		if(!empty($post)){
			$ttop = $this ->m_c->getJudgeByPull($ip);
			if(empty($ttop)){
				#插入
				$arr = array();
				$this ->m_c->getInsertPoll($post,$ip);
				$this ->m_c->getUpdatePoll($post);
				$string = '投票成功，感谢您的参与！';
			}else{
				$string = '您已投票，请不要重复投票！';
	
			}
			header( "content-type:text/html;charset=utf-8" );
			echo "<script type=\"text/javascript\">";
			echo "alert(\"{$string}\");";
			echo "</script>";
	
	
		}
	
		$data['top'] = $this ->m_c->getBotByPull();
		$data['all'] = $this ->m_c->getAll();
		$this->load->view('default/event',$data);
	}
}