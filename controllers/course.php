<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class course extends base{

	

  function __construct(){
    parent::__construct();
		$this->load->model ( "contract_model",'m_c' );
    
  }
	/**
	 * 
	 * @param int $t 分类		
	 * @param int $l 数量
	 * @param int $c (1、视频2、新闻分类)
	 */
	function onlineCourse($t,$l,$c=1){
			return $this->m_c->onlineCourse($t,$l,$c);
	}
	
	/**
	 * 培训列表
	 */
	function train(){
		
		$data = $this->data;
		$data['list_1'] = $this->onlineCourse(3,14,2);//业务培训
		$data['list_2'] = $this->m_c->onlineCourse2(3,6,6);
		$data['list_3'] = $this->m_c->onlineCourse2(3,6,5);
		//$data['list_4'] = $this->m_c->onlineCourse2(3,14,7);
		//$data['list_5'] = $this->m_c->onlineBusiness(1,7);
		//$data['list_6'] = $this->m_c->onlineBusiness(2,7);
		$data['list_7'] = $this->m_c->onlineBusiness(3,7);
		$data['list_8'] = $this->m_c->onlineBusiness(4,7);
		$data['type'] = $this->m_c->getTraimGroup();
		$this->load->view('default/n_train',$data);
	}
	
	/**
	 * eglish
	 */
	function eglish(){
		$data = array();
		$data = $this->data;
		$data['list'] = $this->onlineCourse(3,100,1);//法律英语
		$this->load->view('default/n_eglish2',$data);
	}
	/**
	 * eglish
	 */
	function practice(){
		$data = array();
		$data = $this->data;
		$data['list'] = $this->onlineCourse(4,100,1);

		$this->load->view('default/practice',$data);
	}
	/**
	 * eglish
	 */
	function exam(){
		$data = array();
		$data = $this->data;
		$data['list'] = $this->onlineCourse(5,100,1);
		$this->load->view('default/exam',$data);
	}
	
	/**
	 * 法院
	 */
	function court(){
		$data = array();
		$data = $this->data;
		$data['list'] =  $this->onlineCourse(2,100,1);//模拟法庭
		$this->load->view('default/n_court',$data);
	}
	
	/**
	 * 专家讲堂
	 */
	function room(){
		$data = array();
		$data = $this->data;
		$data['list'] = $this->onlineCourse(1,6,1);//专家讲堂
		$this->load->view('default/n_room',$data);
	}
	
	/**
	 * 专家论证
	 */
	function argument(){
		$data = array();
		$data = $this->data;

		$data['list_1'] = $this->m_c->onlineCourse2(2,14,1);
		$data['list_2'] = $this->m_c->onlineCourse2(2,6,2);
		$data['list_3'] = $this->m_c->onlineCourse2(2,6,3);
		$data['list_4'] = $this->m_c->onlineCourse2(2,14,4);
		$this->load->view('default/n_rgument2',$data);
	}
	
	function info(){
		$data = array();
		$data = $this->data;
		$p =array();
		$p=$this->get_params();
		$data['info'] = $this->m_c->onlineCourseInfo($p);
		$this->load->view('default/n_info',$data);
	}
	
 
}


