<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/29
 * @desc 后台管理
 */
class m_web extends m_base {
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
	

	
	/**
	 * 主页
	 */
	function index(){
		
		$data = array();
		$data['left'] = $this->getLeftMenu();
		$this->load->view('manage/left',$data);
	}
	
	/**
	 * 推荐列表
	 */
	function recommend(){
		$p = array();
		$params=array();
		$params = $this->get_params();
		$p = $params;
		$params ['type'] = empty ( $params ['type'] ) ? '' :$params ['type'];
		$params ['name'] = empty ( $params ['name'] ) ? '' :$params ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? ' and type in(1,2,3,13) ' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_w->recommend($p);
	
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$ucodes = '';
		$data['lawyer'] = array();
		if(!empty($r['list'])){
			foreach ($r['list'] as $k =>$v ){
				$ucodes .= empty($ucodes)?'"'.$v['ucode'].'"':',"'.$v['ucode'].'"';
			}
			$data['lawyer'] = $this->m_w->lawyer($ucodes);
		}
		$data['type'] = eval(ART_CATEGORY);
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_web/recommend?type='.$params ['type'].'&name='.$params ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/recommendlist',$data);
	}
	
	/**
	 * 添加推荐
	 */
	function recommendadd(){
		$p = $this->get_params();
		$p['createid'] = $this->getMLId();
		if($_POST){
			$p['url'] = urldecode($p['url']);
			$r = $this->m_w->recommendadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['type'] = eval(ART_CATEGORY);
			$this->load->view('manage/recommendadd',$data);
		}
	}
	
	/**
	 * 编辑推荐
	 */
	function recommendedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_w->recommendinfo($id);
			if(!empty($data['info'][0]['ucode']))
			$data['lawyer'] = $this->m_w->bycodeinfo($data['info'][0]['ucode']);
			$data['type'] = eval(ART_CATEGORY);
			$this->load->view('manage/recommendadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$p['url'] = urldecode($p['url']);
			$_r2 = $this->m_w->recommendedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 推荐显示或隐藏
	 */
	function recommendshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_w->recommendedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	
	/**
	 * 热点问题、动态公告
	 */
	function hot_issue(){
		$p = array();
		$params=array();
		$params = $this->get_params();
		$p = $params;
		$params ['type'] = empty ( $params ['type'] ) ? '' :$params ['type'];
		$params ['name'] = empty ( $params ['name'] ) ? '' :$params ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? ' and type in(4,5,7,8) ' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_w->recommend($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['type'] = eval(ART_CATEGORY);
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_web/hot_issue?type='.$params ['type'].'&name='.$params ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/hot_issuelist',$data);
	}
	
	/**
	 * 添加热点问题、动态公告
	 */
	function hot_issueadd(){
		$p = $this->get_params();
		$p['createid'] = $this->getMLId();
		if($_POST){
			$p['url'] = urldecode($p['url']);
			$r = $this->m_w->recommendadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['type'] = eval(ART_CATEGORY);
			$this->load->view('manage/hot_issueadd',$data);
		}
	}
	
	/**
	 * 编辑热点问题、动态公告
	 */
	function hot_issueedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_w->recommendinfo($id);
			$data['type'] = eval(ART_CATEGORY);
			$this->load->view('manage/hot_issueadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$p['url'] = urldecode($p['url']);
			$_r2 = $this->m_w->recommendedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 显示隐藏热点问题、动态公告
	 */
	function hot_issueshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_w->recommendedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 法规解读
	 */
	function law_read(){
		$p = array();
		$params=array();
		$params = $this->get_params();
		$p = $params;
		$params ['type'] = empty ( $params ['type'] ) ? '' :$params ['type'];
		$params ['name'] = empty ( $params ['name'] ) ? '' :$params ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? ' and type in(6,9,10,11) ' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_w->recommend($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['type'] = eval(ART_CATEGORY);
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_web/law_read ?type='.$params ['type'].'&name='.$params ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/law_readlist',$data);
	}
	
	/**
	 * 添加热点问题、动态公告
	 */
	function law_readadd(){
		$p = $this->get_params();
		$p['createid'] = $this->getMLId();
		if($_POST){
			$p['url'] = urldecode($p['url']);
			$r = $this->m_w->recommendadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['type'] = eval(ART_CATEGORY);
			$this->load->view('manage/law_readadd',$data);
		}
	}
	
	/**
	 * 编辑热点问题、动态公告
	 */
	function law_readedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_w->recommendinfo($id);
			$data['type'] = eval(ART_CATEGORY);
			$this->load->view('manage/law_readadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$p['url'] = urldecode($p['url']);
			$_r2 = $this->m_w->recommendedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 显示隐藏热点问题、动态公告
	 */
	function law_readshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_w->recommendedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	
}