<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/29
 * @desc 配置管理
 */
class m_config extends m_base {
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

	}
	
	/**
	 * 主页
	 */
	function index(){
		
		$data = array();
		$data['left'] = $this->getLeftMenu();
		$this->load->view('manage/left',$data);
	}
	
	/**
	 * 案件分类
	 */
	function case_type(){
		$p = array();
		$params=array();
		$params = $this->get_params();
		$p = $params;
		$params ['type'] = empty ( $params ['type'] ) ? '' :$params ['type'];
		$params ['name'] = empty ( $params ['name'] ) ? '' :$params ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->case_type($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_config/case_type?type='.$params ['type'].'&name='.$params ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/case_typelist',$data);
	}
	
	/**
	 * 添加 案件分类
	 */
	function case_typeadd(){
		$p = $this->get_params();
		if($_POST){
			
			$p['descript'] =urldecode($p['descript']);
			$p['name'] =urldecode($p['name']);
			$r = $this->m_d->case_typeadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['pid'] =$this->m_c->category();
			$data['type'] = $this->m_d->getType(true);
			$this->load->view('manage/case_typeadd',$data);
		}
	}
	
	/**
	 * 编辑  案件分类
	 */
	function case_typeedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['info'] = $this->m_d->case_typeinfo($id);
			$data['pid'] =$this->m_c->category();
			$data['category'] = $this->m_c->categoryshow();
			$this->load->view('manage/case_typeadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['descript'] =urldecode($p['descript']);
			$p['name'] =urldecode($p['name']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->case_typeedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 案件分类 显示或隐藏
	 */
	function case_typeshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->case_typeedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}

	/**
	 * 法院列表
	 */
	function court(){
		$p = array();
		$params=array();
		$params = $this->get_params();
		$p = $params;
		$params ['name'] = empty ( $params ['name'] ) ? '' :$params ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_c->court($p);
		$data=array();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['city'] = $this->m_c->getAllCity();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_config/court?&name='.$params ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/courtlist',$data);
	}
	
	/**
	 * 添加 法院
	 */
	function courtadd(){
		if($_POST){
			$p = $this->get_params();
			$p['address'] =urldecode($p['address']);
			$p['description'] =urldecode($p['description']);
			$p['name'] =urldecode($p['name']);
			
			$r = $this->m_c->courtadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['region'] = $this->m_c->region();
			$this->load->view('manage/courtadd',$data);
		}
	}
	
	/**
	 * 编辑 法院
	 */
	function courtedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['info'] = $this->m_c->courtinfo($id);
			$data['region'] = $this->m_c->region();
			if(!empty($data['info'][0]['region'] ))
			$data['city'] = $this->m_c->byuuidName($data['info'][0]['region']);
			$this->load->view('manage/courtadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['address'] =urldecode($p['address']);
			$p['description'] =urldecode($p['description']);
			$p['name'] =urldecode($p['name']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_c->courtedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 法院 显示或隐藏
	 */
	function courtshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_c->courtedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
}