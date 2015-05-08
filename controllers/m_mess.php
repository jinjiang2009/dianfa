<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/29
 * @desc 后台管理
 */
class m_mess extends m_base {
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
	
	function index(){
	
		$data = array();
		$data['left'] = $this->getLeftMenu();
		$this->load->view('manage/left',$data);
	}
	
	function hot_message(){
		$p = $this->get_params();
		$pa = $p;
		$pa ['title'] = empty ( $pa ['title'] ) ? '' :$pa ['title'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['title'] = empty ( $p ['title'] ) ? '' : ' and title like "%'.urldecode($p ['title']).'%"';
		$r = $this->m_d->messagelist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['category'] = $this->m_c->messageshow();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_mess/hot_message?type='.'&title='.$pa ['title'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/hot_messagelist',$data);
	}
	
	function hot_messageadd(){
		$p = $this->get_params();
		if($_POST){
			//var_dump($_POST);exit;
			//$p['createid'] = $this->getMLId();
			$p['content'] =urldecode($p['content']);
			$p['title'] =urldecode($p['title']);
			$p['sort'] =urldecode($p['sort']);
			$p['createtime'] =urldecode($p['createtime']);			
			
			$r = $this->m_d->messageadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			
			$this->load->view('manage/hot_messageadd',$data);
		}
	}
	
	
	
	function hot_messageedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_w->messageinfo($id);
			$data['type'] = eval(ART_CATEGORY);
			$this->load->view('manage/hot_messageadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['content'] =urldecode($p['content']);
			$p['title'] =urldecode($p['title']);
			$p['sort'] =urldecode($p['sort']);
			$p['createtime'] =urldecode($p['createtime']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			//$p['url'] = urldecode($p['url']);
			$_r2 = $this->m_w->messageedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	
	function hot_messageshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_w->messageedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	function local(){
		$p = $this->get_params();
		$pa = $p;
		$pa ['upload'] = empty ( $pa ['upload'] ) ? '' :$pa ['upload'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		//$p ['upload'] = empty ( $p ['upload'] ) ? '' : ' and upload like "%'.urldecode($p ['upload']).'%"';
		$r = $this->m_d->locallist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		//$data['category'] = $this->m_c->messageshow();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_mess/local?type='.'&upload='.$pa ['upload'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/locallist',$data);
	}
	public function arguement(){
		$p = $this->get_params();
		$pa = $p;
		$pa ['upload'] = empty ( $pa ['upload'] ) ? '' :$pa ['upload'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		//$p ['upload'] = empty ( $p ['upload'] ) ? '' : ' and upload like "%'.urldecode($p ['upload']).'%"';
		$r = $this->m_d->arguementlist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		//$data['category'] = $this->m_c->messageshow();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_mess/arguement?type='.'&upload='.$pa ['upload'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/arguement',$data);
		
	}
	public function lfrim(){
		$p = $this->get_params();
		$pa = $p;
		$pa ['upload'] = empty ( $pa ['upload'] ) ? '' :$pa ['upload'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		//$p ['upload'] = empty ( $p ['upload'] ) ? '' : ' and upload like "%'.urldecode($p ['upload']).'%"';
		$r = $this->m_d->lfrim($p);
		//var_dump($r);exit;
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		//$data['category'] = $this->m_c->messageshow();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_mess/lfrim?type='.'&upload='.$pa ['upload'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/lfrim',$data);
	}
}