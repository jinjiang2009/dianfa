<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class online extends base{

	

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
		$data = array();
		$data = $this->data;
		$data['list_1'] = $this->onlineCourse(3,14,2);//业务培训
		$data['list_2'] = $this->m_c->onlineCourse2(3,6,6);
		$data['list_3'] = $this->m_c->onlineCourse2(3,6,5);
		$data['list_4'] = $this->m_c->onlineCourse2(3,14,7);
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
	 * 法院
	 */
	function court(){
		$data = array();
		$data = $this->data;
		$data['list'] =  $this->onlineCourse(2,3,1);//模拟法庭
		$this->load->view('default/n_court',$data);
	}
	
	/**
	 * 专家讲堂
	 */
	function room(){
		$data = array();
		$data = $this->data;
		$data['list'] = $this->onlineCourse(1,12,1);//专家讲堂
		$this->load->view('default/n_room',$data);
	}
	
	/**
	 * 专家论证
	 */
	function argument(){
		$data = $this->data;
		$post = $this->input->post();
		if(!empty($post)){
			$this->m_c->getApplication('df_application',$post);
			if($_FILES){
				$rs = $this->img_upload('./upload/uhead/'.date('Ymd',time()).'/');
				$filename = date('Ymd',time()).'/'.$rs['file_name'];
				if(empty($data['user']['icon'])){
					$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>10,'cor_id'=>'','num'=>20,'type'=>1));
				}
				$this->home->updateTable('df_member',$data['user']['id'],array('icon'=>$filename));
				$this->home->updateUcodeTable('df_job_reply',$data['user']['ucode'],array('icon'=>$filename));
				$user = $this->session->userdata('user');
				$user['icon'] = $filename;
				$this->session->set_userdata('user',$user);
			}
		}
			$data['list_1'] = $this->m_c->onlineCourse2(2,14,1);
			$data['list_2'] = $this->m_c->onlineCourse2(2,6,2);
			$data['list_3'] = $this->m_c->onlineCourse2(2,6,3);
			$data['list_4'] = $this->m_c->onlineCourse2(2,14,4);
			$data['experts'] = $this->m_c->getExperts();
		$this->load->view('default/n_rgument2',$data);
	}
	
	function img_upload($path,$name){
	
		if(!file_exists($path)){
			mkdir($path,0777);
		}
		$config['file_name'] = mt_rand(1000,9999).time();
		$config['upload_path'] = $path;
		$config['allowed_types'] = '*';
		$config['max_size'] = '100000000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload($name))
		{
			$error = $this->upload->display_errors();
	
			header_output($error);
	
		}
		else
		{
			$data = $this->upload->data();
	
			return $data;
		}
	}
	function info(){
		$data = $this->data;
		$p =array();
		$p=$this->get_params();
		$data['info'] = $this->m_c->onlineCourseInfo($p);
		$this->load->view('default/n_info',$data);
	}
	function expert(){
		$data = $this->data;
		$data['experts'] = $this->m_c->getAllExperts();
		$data['type'] = $this->m_c->getExpertType();
		$this->load->view('default/expert',$data);
	}
	
 
}


