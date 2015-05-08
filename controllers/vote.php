<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class vote extends base{



	function __construct(){
		parent::__construct();
		$this->load->model ( "contract_model",'m_c' );

	}
	
	function index($id=null){
		$data = $this->data;
		$data['judge'] = false;
		$ip = getip();
		if(!empty($id)){
			$ttop = $this ->m_c->judgeIp($id,$ip);
			if(empty($ttop)){
				
				#插入
				$arr = array();
				$arr['ip'] = $ip;
				$arr['type'] = 1;
				$arr['recommendid'] = $id;
				$this ->m_c->insertleft('df_toupiao',$arr);
				$this ->m_c->updatePoll($id);
				$string = '投票成功，感谢您的参与！';
				$data['now'] = $this ->m_c->getNowRecommend($id);
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
		$data['top'] = $this ->m_c->getTop();
		$data['all'] = $this ->m_c->getAllRecommend();
		$this->load->view('default/vote',$data);
	}
		
}