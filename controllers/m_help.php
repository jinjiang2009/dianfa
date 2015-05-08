<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/29
 * @desc 后台管理
 */
class m_help extends m_base {
	private $jdata = array();
	private $limit =20;
	private $params =array();
	function __construct() {
		parent::__construct ();
		$this->judgeUser();//用户登陆
		
		$this->judgeAuthority();
		$this->params = $this->get_params();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->library('Lib_page');
		$this->load->model ( "m_data_model",'m_d' );
		$this->load->model ( "m_config_model",'m_c' );
		$this->load->model ( "m_web_model",'m_w' );
	}
	/*
	 * 法律援助
	 */
	function lawhelp(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->getLawHelp($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_help/lawhelp?name='.$pa ['name'],
		);
		
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/helplist',$data);
		
	}
	/**
	 * 法援详情
	 */
	function detail(){
		$data = array();
		$p = $this->get_params();
		$data['info'] = $this->m_d->helpInfo($p);
		$data['type'] = $this->m_d->getHelpLawType();
		$this->load->view('manage/helpinfo',$data);
	}
	/**
	 * 法援状态
	 */
	function status(){
		$p=array();
		$p = $this->get_params();
		$p['is_help'] = $p['is_help']==1?2:1;
		$_r2 = $this->m_d->helpedit($p);
		if(!empty($_r2)){
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
}