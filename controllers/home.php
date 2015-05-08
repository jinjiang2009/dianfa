<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class home extends base{

	

  function __construct(){
    parent::__construct();
    $this->load->model ( "home_model",'home' );
    
  }
  /**
   * 个人资料页
   */
  public function index(){
  	$data = $this->data;
  	if($data['login_type']==4){
  		header_output('请先登录');
  	}
  	if(2==$data['user']['identity']){
  		header ( "Location:".STATIC_URL.'lhome' );
  	}
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
  	$post = $this->input->post();
  	if(isset($post['param'])&&$post['param']=='用户'){
  		if(md5($post['password'])==$data['user']['pwd']){
  			$this->home->updateTable('df_member',$data['user']['id'],array('username'=>$post['newname']));
  			$user = $this->session->userdata('user');
  			$user['username'] = $post['newname'];
  			$this->session->set_userdata('user',$user);
  		}else{
  			header_output('密码错误');
  		}
  	}
  	if(isset($post['param'])&&$post['param']=='邮箱'){
  		if(md5($post['password1'])==$data['user']['pwd']){
  			$this->home->updateTable('df_member',$data['user']['id'],array('email'=>$post['newemail']));
  			$user = $this->session->userdata('user');
  			$user['email'] = $post['newemail'];
  			$this->session->set_userdata('user',$user);
  		}else{
  			header_output('密码错误');
  		}
  	}
  	if(isset($post['param'])&&$post['param']=='手机'){
  		if(md5($post['password2'])==$data['user']['pwd']){
  			$this->home->updateTable('df_member',$data['user']['id'],array('mobile'=>$post['newtel']));
  			$user = $this->session->userdata('user');
  			$user['mobile'] = $post['newtel'];
  			$this->session->set_userdata('user',$user);
  		}else{
  			header_output('密码错误');
  		}
  	}
  	if(isset($post['all'])&&$post['all']=='mod'){
  		$in = 0;//p($la);die;
  		$aa = false;
  		if(empty($data['user']['realname'])&&!empty($post['realname'])){
  			$in += 10;
  			$aa = true;
  		}
  		if(empty($data['user']['region'])&&!empty($post['region'])){
  			$in += 10;
  			$aa = true;
  		}
  		
  		if(empty($data['user']['sex'])&&$post['sex']!=0){
  			$in += 10;
  			$aa = true;
  		}
  		if($aa){
  			$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>27,'cor_id'=>'','num'=>$in,'type'=>1));
  		}
  		$arr = array(
  				'realname'=>$post['realname'],
  				'region'=>$post['region'],
  				'sex'=>$post['sex'],
  				);
  		$this->home->updateTable('df_member',$data['user']['id'],$arr);
  		$user = $this->session->userdata('user');
  		$user['realname'] = $arr['realname'];
  		$user['region'] = $arr['region'];
  		$user['sex'] = $arr['sex'];
  		$this->session->set_userdata('user',$user);
  	}
  	$this->load->model ( "judgement_model",'judge' );
  	$data['region'] = $this->judge->getRegion();
  	unset($data['region'][31]);
  	unset($data['region'][32]);
  	unset($data['region'][33]);
  	$data['user'] = $this->session->userdata('user');
  	$this->load->view('default/home_info.php',$data);
  }
  /*
   * 修改密码
   */
  public function pwd(){
  	$data = $this->data;
  	$get = $this->input->post();
  	if(isset($get['allpwd'])&&$get['allpwd']=='allpwd'){
  		$this->home->updateTable('df_member',$data['user']['id'],array('pwd'=>md5($get['password4'])));
  		$data['prompt']='密码修改成功';
  		
  		$this->load->view('default/prompt_success',$data);
  	}else{
  		$this->load->view('default/home_pwd.php',$data);
  	}
  	
  }
  /*
   * 异步验证:密码
  */
  public function verifypwd(){
  	$data = $this->data;
  	$get = $this->input->get();
  	if(!empty($get)){
  		
  		$arr = $this->home->verifyPwd($get['oldpassword'],$data['user']['id']);
  		if(!empty($arr)){
  			$msg = 9999;
  		}else{
  			$msg = 1111;
  		}
  	}else{
  		$msg = 1111;
  	}
  
  	echo $msg;
  }
  /**
   * 上传
   */
   function img_upload($path){
   
   	if(!file_exists($path)){
   		mkdir($path,0777);
   	}
  	$config['file_name'] = mt_rand(1000,9999).time();
  	$config['upload_path'] = $path;
  	$config['allowed_types'] = 'gif|jpg|png';
  	$config['max_size'] = '1000000';
  	// $config['max_width']  = '1024';
  	// $config['max_height']  = '768';
  	$this->load->library('upload', $config);
  	if ( ! $this->upload->do_upload())
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
  /*
   * 我的积分
   */
  public function integral(){
  	$data = $this->data;
  	$perpage = 10;
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['log'] = $this->home->getIntegralLog($data['user']['ucode'],$data['current'],$perpage);
  	
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getIntegralLog($data['user']['ucode']));
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'home/integral?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$data['inte'] = $this->integral_config();
  	$this->load->view('default/home_integral.php',$data);
  }
  /*
   * 我的咨询
  */
  public function ask(){
  	$data = $this->data;
  	$perpage = 10;
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['ask'] = $this->home->getAskLog($data['user']['ucode'],$data['current'],$perpage);
  	$this->load->model ( "question_model",'ask' );
  	$data['group'] =  $this->ask->getGroup(null);
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getAskLog($data['user']['ucode']));
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'home/ask?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$this->load->view('default/home_ask.php',$data);
  }
  /*
   * 系统消息
  */
  public function message(){
  	$data = $this->data;
  	$perpage = 10;
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['message'] = $this->home->getMessageLog($data['user']['ucode'],$data['current'],$perpage);
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getMessageLog($data['user']['ucode']));
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'home/message?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$this->load->view('default/home_message.php',$data);
  }
  /*
   * 系统消息详情
  */
  public function message_detail(){
  	$data = $this->data;
  	$id = $this->input->get('id',0);
  	if($id==0){
  		$this->load->view('default/wrong',$data);
  	}else{
  		$data['content'] = $this->home->getMessageCon($id);
  		if(!empty($data['content'])){
  			$this->home->updateTable('df_message',$id,array('is_read'=>2));
  			$this->load->view('default/home_detail.php',$data);
  		}else{
  			$this->load->view('default/wrong',$data);
  		}
  	}
  }
  /*
   * 我发布的案件
  */
  public function judgement(){
  	$data = $this->data;
  	$perpage = 10;
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['judgement'] = $this->home->getJudgeLog($data['user']['ucode'],$data['current'],$perpage);
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getJudgeLog($data['user']['ucode']));
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'home/judgement?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$this->load->view('default/home_judgement.php',$data);
  }
  /*
   * 我的订单
  */
  public function order(){
  
  	$data = $this->data;
  	$perpage = 10;
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['order'] = $this->home->getOrderLog($data['user']['ucode'],$data['current'],$perpage);
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getOrderLog($data['user']['ucode']));
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'home/order?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$data['type'] = array(1=>'合同文书',2=>'咨询',3=>'产品服务');
  	$this->load->view('default/home_order.php',$data);
  }
  /*
   * 选取中标律师
   */
  public function choose(){
  	$data = $this->data;
  	$id = $this->input->get('id',0);
  	if($id>0){
  		$data['id']= $id;
  		$title = $this->home->getCaseInfo($id);
  		if(!empty($title)){
  			$get = $this->input->get();
  			if(isset($get['bidid'])&&isset($get['status'])){
  				$rs = $this->home->verifyBid($get['bidid']);
  				if(!empty($rs)){
  					if($get['status']==1){
  						$re = $this->home->verifyBidStatus($id);
  						if(empty($re)){
  							$this->home->updateTable('df_bid',$get['bidid'],array('status'=>$get['status']));
  						}else{
  							header_output('只能有一位律师中标');
  						}
  					}else{
  						$this->home->updateTable('df_bid',$get['bidid'],array('status'=>$get['status']));
  					}
  					
  				}else{
  					$this->load->view('default/wrong.php',$data);
  				}
  			}
  			$data['region'] =  $this->home->getRegion();
  			$data['name'] = $title['name'];
  			$data['is_audit'] = $title['is_audit'];
  			$data['lawyer'] =  $this->home->getCaseLawyer($id);
  			$data['territory'] = $this->home->getTerritory();
  			$this->load->view('default/home_judgement_choose.php',$data);
  		}else{
  			$this->load->view('default/wrong.php',$data);
  		}
  	
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
}


