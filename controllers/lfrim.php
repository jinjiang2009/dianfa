<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class lfrim extends base{

	

  function __construct(){
    parent::__construct();
    $this->load->model ( "lhome_model",'home' );
    
  }
  /**
   * 个人资料页
   */
  public function index(){
  	$data = $this->data;
  	$data['territory'] = $this->home->getAllRerritory();
  	$data['secre'] = $this->home->getTypeRerritory();
  	if($data['login_type']==4){
  		header_output('请先登录');
  	}
  	if($_FILES){
  		$rs = $this->img_upload('./upload/uhead/'.date('Ymd',time()).'/');
  		$filename = date('Ymd',time()).'/'.$rs['file_name'];
  		$this->home->updateTable('df_member',$data['user']['id'],array('icon'=>$filename));//更新会员表
  		$this->home->updateLfrim('df_job_reply',$data['user']['ucode'],array('icon'=>$filename));//更新用户信息
  		if(empty($data['user']['icon'])){
  			
  			$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>10,'cor_id'=>'','num'=>20,'type'=>1));
  		}
  		$user = $this->session->userdata('user');//取得session的数据
  		$user['icon'] = $filename;
  		$this->session->set_userdata('user',$user);//传递一个新的用户数组到 session数组中
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
  		$la = $this->home->getLfrim($data['user']['ucode']);//获得律师的信息
  		
  		$in = 0;//p($la);die;
  		$aa = false;
  		if(empty($la['name'])&&!empty($post['realname'])){
  			$in += 10;
  			$aa = true;
  		}
  		if(empty($la['region'])&&!empty($post['region'])){
  			$in += 10;
  			$aa = true;
  		}
  		
  		if(empty($la['office'])&&!empty($post['office'])){
  			$in += 10;
  			$aa = true;
  		}
  		if(empty($la['license'])&&!empty($post['license'])){
  			$in += 10;
  			$aa = true;
  		}
  		if(empty($la['rerritory'])&&!empty($post['rerritory'])){
  			$in += 10;
  			$aa = true;
  		}
  		if(empty($la['website'])&&!empty($post['website'])){
  			$in += 10;
  			$aa = true;
  		}
  		if(empty($la['descript'])&&!empty($post['descript'])){
  			$in += 10;
  			$aa = true;
  		}
  		if($aa){
  			$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>27,'cor_id'=>'','num'=>$in,'type'=>1));
  		}
  		$arr = array(
  				//'name'=>str_replace('律师','',$post['realname']),//负责人
  				'name'=>$post['name'],//负责人
  				'office'=>$post['office'],//律所名称
  				'region'=>$post['region'],//所在城市
  				'license'=>$post['license'],//律师执业证号
  				//'rerritory'=>join(',',$post['rerritory']),//业务领域
  				'secre'=>join(',',$post['secre']),//二级擅长领域
  				'descript'=>$post['descript'],//简介
  				'website'=>$post['website'],//网址
  				);
  		$this->home->updateLfrim('df_lfrim',$data['user']['ucode'],$arr);
  	}
  	$this->load->model ( "judgement_model",'judge' );
  	$data['region'] = $this->judge->getRegion();//获得省份
  	unset($data['region'][31]);
  	unset($data['region'][32]);
  	unset($data['region'][33]);
  	$data['lawyer'] = $this->home->getLfrim($data['user']['ucode']);//获得律所的信息
  	//p($data['lawyer']);die;
  	$data['user'] = $this->session->userdata('user');
  	$this->load->view('default/lfrim_info.php',$data);
  }
  /*
   * 我的文档
  */
  public function document(){
  	$data = $this->data;
  	$post = $this->input->post();
  	$get = $this->input->get();
  	if(isset($get['id'])&&$get['id']>0){
  		
  		$this->home->updateTable('df_contract',$get['id'],array('is_show'=>2));
  		
  	}
  	if(isset($_FILES)&&$post['type']>0){
  		$rs = $this->document_upload('./upload/document/'.date('Ymd',time()).'/');
  		$filename = date('Ymd',time()).'/'.$rs['file_name'];
  		$this->home->insertTable('df_contract',array('type'=>$post['type'],'ucode'=>$data['user']['ucode'],'name'=>$post['title'],'document'=>$filename));
  		$name = $post['type']==1?'contract':'remark';
  		$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>13,'cor_id'=>'','num'=>10,'type'=>1));
  	}
  
  	$perpage = 10;
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['document'] = $this->home->getDocumentLog($data['user']['ucode'],$data['current'],$perpage);
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getDocumentLog($data['user']['ucode']));
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'lhome/document?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$data['type'] = array(1=>'合同',2=>'案例文书');
  	$this->load->view('default/lhome_document.php',$data);
  }
  /*
   * 上传文章
   */
  public function article(){
  	$data = $this->data;
  	$data['category'] = $this->home->getArticleCategory(0);
  	$post = $this->input->post();
  	if(!empty($post)){
  		$this->home->insertTable('df_article',array('title'=>$post['title'],'content'=>$post['content'],'type'=>$post['cate'],'ucode'=>$data['user']['ucode'],'source'=>$post['source']));
  		$this->home->addOne('df_lfrim','contract',$data['user']['ucode']);
  		$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>13,'cor_id'=>'','num'=>10,'type'=>1));
  		$data['user']['integral'] = $data['user']['integral']+10;
  		$this->session->set_userdata ( 'user', $data['user'] );
  	}
  	$this->load->view('default/lfrim_article.php',$data);
  }
  /*
   * 异步获取分类
   */
  public function getcategory(){
  	$pid = $this->input->post('pid');
  	$category = $this->home->getArticleCategory($pid);
  	if(!empty($category)&&$pid!=0){
  		$str = ' <select id="pidcategory" name="pidcategory">' ;
  		$str .= ' <option value="0">请选择</option>' ;
  		foreach($category as $k=>$v){
  			$str .= ' <option value="'.$v['id'].'">' ;
  			$str .= $v['name'];
  			$str .= ' </option>' ;
  		}
  		$str .= ' </select>' ;
  		echo $str;
  	}else{
  		echo 1111;
  	}
  }
  /*
   * 上传案例
  */
  public function upjudge(){
  	$data = $this->data;
  	$data['category'] = $this->home->getCategory();
  	$post = $this->input->post();
  	if(!empty($post)){
  		$court_id = '';
  		if(!empty($post['court'])){
  			$crou_r = mb_strpos ($post['court'] , '法院'  );
  			if($crou_r){
  				$this->load->model ( "login_model", 'login' );
  				$r = $this->login->court($post['court']);
  				if(empty($r)){
  					$court_id = $this->login->addcourt(array('name'=>$post['court']));
  				}else{
  					$court_id = $r[0]['id'];
  				}
  			}
  		
  		}
  		$id = $this->home->insertTable('df_judgement',array('laywer_id'=>$data['user']['ucode'],'serial'=>$post['serial'],'category'=>$post['category'],'conclude'=>$post['conclude'],'title'=>$post['title'],'lawer'=>$post['lawer'],'court'=>$court_id));
  		$this->home->insertTable('df_judgement_con',array('id'=>$id,'content'=>$post['content']));
  		$this->home->addOne('df_lfrim','remark',$data['user']['ucode']);
  		$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>13,'cor_id'=>'','num'=>10,'type'=>1));
  		$data['user']['integral'] = $data['user']['integral']+10;
  		$this->session->set_userdata ( 'user', $data['user'] );
  	}
  	$this->load->view('default/lfrim_judgement.php',$data);
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
  		//$this->load->view('default/lhome_pwd.php',$data);
  	}else{
  		$this->load->view('default/lfrim_pwd.php',$data);
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
   * 身份认证
   */
  public function identity(){
  	$data = $this->data;
  	if($data['login_type']==4){
  		header_output('请先登录');
  	}
  	
  	if($_FILES){
  		$type= $this->input->post('type');
  		if($type==1){
	  		$rs = $this->img_upload('./upload/identity/');
	  		$filename = $rs['file_name'];
	  		$this->home->updateTable('df_member',$data['user']['id'],array('idcardicon'=>$filename,'isreal'=>1));
	  		$user = $this->session->userdata('user');
	  		$user['idcardicon'] = $filename;
	  		$user['isreal'] = 1;
	  		if(empty($data['user']['idcardicon'])){
	  			$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>10,'cor_id'=>'','num'=>20,'type'=>1));
	  		}
  		}elseif($type==2){
  			$rs = $this->img_upload('./upload/lawyer/');
  			$filename = $rs['file_name'];
  			$law = $this->home->getLfrim($data['user']['ucode']);
  			$this->home->updateLfrim('df_lfrim',$data['user']['ucode'],array('icon'=>$filename));
  			$user = $this->session->userdata('user');
  			$user['lfrimicon'] = $filename;
  			if(empty($law['icon'])){
  				$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>25,'cor_id'=>'','num'=>20,'type'=>1));
  			}
  		}else{
  			$rs = $this->img_upload('./upload/lince/');
  			$filename = $rs['file_name'];
  			$law = $this->home->getLfrim($data['user']['ucode']);
  			$this->home->updateLfrim('df_lfrim',$data['user']['ucode'],array('lince_img'=>$filename));
  			$user = $this->session->userdata('user');
  			$user['linceimg'] = $filename;
  			if(empty($law['lince_img'])){
  				$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>16,'cor_id'=>'','num'=>20,'type'=>1));
  			}
  		}
  		$this->session->set_userdata('user',$user);
  	}
  	
  	 
  	$data['user'] = $this->session->userdata('user');
  	if(!isset($data['user']['lfrimicon'])||!isset($data['user']['linceimg'])){
  		
  		$te =  $this->home->getLayerIcon($data['user']['ucode']);
  		$te['icon']='';
  		$te['lince_img']='';
  		$data['user']['lfrimicon'] = $te['icon'];
  		$data['user']['linceimg'] = $te['lince_img'];
  	
  	}
  	$this->load->view('default/lfrim_identity.php',$data);
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
  /**
   * 上传
   */
  function document_upload($path){
  
  	if(!file_exists($path)){
  		mkdir($path,0777);
  	}
  	$config['file_name'] = mt_rand(1000,9999).time();
  	$config['upload_path'] = $path;
  	$config['allowed_types'] = '*';
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
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'lfrim/integral?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$data['inte'] = $this->integral_config();
  	$this->load->view('default/lfrim_integral.php',$data);
  }
  /*
   * 我的解答
  */
  public function ask(){
  	$data = $this->data;
  	$perpage = 10;
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['ask'] = $this->home->getQuestionLog($data['user']['ucode'],$data['current'],$perpage);
  
  	$this->load->model ( "question_model",'ask' );
  	$data['group'] =  $this->ask->getGroup(null);
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getQuestionLog($data['user']['ucode']));
  
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'lfrim/ask?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$this->load->view('default/lfrim_ask.php',$data);
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
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'lfrim/message?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$this->load->view('default/lfrim_message.php',$data);
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
  			$this->load->view('default/lhome_detail.php',$data);
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
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'lfrim/order?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$data['type'] = array(1=>'合同文书',2=>'咨询',3=>'产品服务');
  	$this->load->view('default/lfrim_order.php',$data);
  }
  /*
   * 我投过标的案件
   */
  public function cases(){
  	$data = $this->data;
  	$perpage = 10;
  	$data['caozuo'] = array(0=>'工作人员审核',1=>'中标',2=>'未中标');
  	$data['pricety'] = $this->get_type();
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['case'] = $this->home->getCaseLog($data['user']['ucode'],$data['current'],$perpage);
  	$this->load->library('pagination');
  	$data['total'] = count($this->home->getCaseLog($data['user']['ucode']));
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'lfrim/cases?');
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$this->load->view('default/lfrim_case.php',$data);
  }
  
  /**
   * 
   */
  /*function protocol(){
  	$data = $this->data;
   	$dd = $this->home->byUcodePotocol($data['user']['ucode']);
   	
  	if(!empty($dd[0]['protocol'])&&$dd[0]['status']==0){
  		$data['messege']="签约律师审核中";
  		$this->load->view('default/lhome_potocol.php',$data);
  		
  	}elseif($dd[0]['status']==1){
  		$data['messege']="签约律师成功";
  		$this->load->view('default/lhome_potocol.php',$data);
  		
  	}else{
  		$this->load->view('default/protocol.php',$data);
  	}
  	
  	
  }*/
  
 /* function file_open(){
    	//$wordname = time()."点法网签约律师申请表.doc";
  	$wordname = "点法网签约律师申请表.doc";
    	$headert='<html xmlns:o="urn:schemas-microsoft-com:office:office"
    	xmlns:w="urn:schemas-microsoft-com:office:word"
    	xmlns="http://www.w3.org/tr/rec-html40">';
    	$footer="</html>";
    	$content='<table width="560">
    <tbody>
        <tr style="height:47px" class="firstRow">
            <td width="560" valign="center" colspan="9" style="padding: 1px; border-width: 1px; border-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0;text-align:center">
                    <span style="font-weight: bold;font-size: 19px;font-family: 宋体">签约律师在线申请表</span>
                </p>
            </td>
        </tr>
        <tr style="height:34px">
            <td width="560" valign="center" colspan="9" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="color: rgb(0, 0, 255);font-weight: bold;font-size: 16px;font-family: 宋体">基本信息</span>
                </p>
            </td>
        </tr>
        <tr style="height:19px">
            <td width="83" valign="center" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">姓名</span>
                </p>
            </td>
            <td width="92" valign="center" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);"></td>
            <td width="70" valign="center" colspan="2" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">性别</span>
                </p>
            </td>
            <td width="86" valign="center" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);"></td>
            <td width="67" valign="center" colspan="2" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">出生日期</span>
                </p>
            </td>
            <td width="72" valign="center" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);"></td>
            <td width="90" valign="center" rowspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-width: 1px; border-top-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">&nbsp;&nbsp;&nbsp;照片</span>
                </p>
            </td>
        </tr>
        <tr style="height:24px">
            <td width="83" valign="center" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">执业地</span>
                </p>
            </td>
            <td width="92" valign="center" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="70" valign="center" colspan="2" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">所在律所</span>
                </p>
            </td>
            <td width="86" valign="center" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="67" valign="center" colspan="2" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">擅长领域</span>
                </p>
            </td>
            <td width="72" valign="center" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:24px">
            <td width="83" valign="center" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="font-size: 16px;font-family: 宋体">执业证号</span>
                </p>
            </td>
            <td width="92" valign="center" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="70" valign="center" colspan="2" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="font-size: 16px;font-family: 宋体">执业年限</span>
                </p>
            </td>
            <td width="86" valign="center" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="67" valign="center" colspan="2" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="font-size: 16px;font-family: 宋体">外语水平</span>
                </p>
            </td>
            <td width="72" valign="center" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:34px">
            <td width="560" valign="center" colspan="9" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="color: rgb(0, 0, 255);font-weight: bold;font-size: 16px;font-family: 宋体">联系方式</span>
                </p>
            </td>
        </tr>
        <tr style="height:19px">
            <td width="83" valign="center" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">手机</span>
                </p>
            </td>
            <td width="92" valign="center" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);"></td>
            <td width="70" valign="center" colspan="2" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">座机</span>
                </p>
            </td>
            <td width="86" valign="center" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);"></td>
            <td width="67" valign="center" colspan="2" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">电子邮件</span>
                </p>
            </td>
            <td width="162" valign="center" colspan="2" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);"></td>
        </tr>
        <tr style="height:21px">
            <td width="83" valign="center" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">邮编</span>
                </p>
            </td>
            <td width="92" valign="center" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="70" valign="center" colspan="2" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top: 0;margin-bottom: 0">
                    <span style="font-size: 16px;font-family: 宋体">通讯地址</span>
                </p>
            </td>
            <td width="315" valign="center" colspan="5" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:35px">
            <td width="560" valign="center" colspan="9" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="color: rgb(0, 0, 255);font-weight: bold;font-size: 16px;font-family: 宋体">教育经历</span>
                </p>
            </td>
        </tr>
        <tr style="height:23px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0;text-align:center">
                    <span style="font-size: 16px;font-family: 宋体">时间</span>
                </p>
            </td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top:0;margin-bottom:0;text-align:center">
                    <span style="font-size: 16px;font-family: 宋体">毕业院校</span>
                </p>
            </td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top:0;margin-bottom:0;text-align:center">
                    <span style="font-size: 16px;font-family: 宋体">专业</span>
                </p>
            </td>
        </tr>
        <tr style="height:23px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:23px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:23px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:32px">
            <td width="560" valign="center" colspan="9" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="color: rgb(0, 0, 255);font-weight: bold;font-size: 16px;font-family: 宋体">工作经历</span>
                </p>
            </td>
        </tr>
        <tr style="height:34px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0;text-align:center">
                    <span style="font-size: 16px;font-family: 宋体">时间</span>
                </p>
            </td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top:0;margin-bottom:0;text-align:center">
                    <span style="font-size: 16px;font-family: 宋体">工作单位</span>
                </p>
            </td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-width: 1px 1px 1px 43px; border-left-style: none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(255, 255, 255);">
                <p style="margin-top:0;margin-bottom:0;text-align:center">
                    <span style="font-size: 16px;font-family: 宋体">主要工作领域</span>
                </p>
            </td>
        </tr>
        <tr style="height:34px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:34px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:34px">
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
            <td width="187" valign="center" colspan="3" style="padding: 1px; border-left-width: 43px; border-left-style: none; border-left-color: rgb(255, 255, 255); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
        <tr style="height:32px">
            <td width="560" valign="center" colspan="9" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);">
                <p style="margin-top:0;margin-bottom:0">
                    <span style="color: rgb(0, 0, 255);font-weight: bold;font-size: 16px;font-family: 宋体">所获荣誉</span>
                </p>
            </td>
        </tr>
        <tr style="height:133px">
            <td width="560" valign="center" colspan="9" style="padding: 1px; border-left-width: 1px; border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-right-color: rgb(0, 0, 0); border-top-style: none; border-bottom-width: 1px; border-bottom-color: rgb(0, 0, 0);"></td>
        </tr>
    </tbody>
</table>
<p style="margin-top:0;margin-bottom:0">
    <span style=";font-size:14px;font-family:&#39;Times New Roman&#39;">&nbsp;</span>
</p>
<p style="margin-top:0;margin-bottom:0">
    <span style=";font-size:14px;font-family:&#39;Times New Roman&#39;">&nbsp;</span>
</p>
<p style="margin-top:0;margin-bottom:0">
    <span style=";font-size:16px;font-family:&#39;宋体&#39;">备注：</span>
</p>
<p style="text-indent:32px;margin-top:0;margin-bottom:0">
    <span style="font-size:16px;font-family:&#39;Times New Roman&#39;">1、</span><span style=";font-size:16px;font-family:&#39;宋体&#39;">点法网将随机通过相关单位核实信息，请务必填写真实情况。</span>
</p>
<p style="text-indent:32px;margin-top:0;margin-bottom:0">
    <span style="font-size:16px;font-family:&#39;Times New Roman&#39;">2、</span><span style=";font-size:16px;font-family:&#39;宋体&#39;">如有虚假信息，将取消律师的会员资格，同时追究相应责任。</span>
</p>
<p style="text-indent:32px;margin-top:0;margin-bottom:0">
    <span style=";font-size:16px;font-family:&#39;宋体&#39;">3、个人信息如有变更，请您务必及时通知点法网。</span>
</p>
<p>
    <br/>
</p>';
    	$handle = fopen('php://output', 'w');
    	header ("Content-Disposition: attachment; filename=" .$wordname );
    	header ("Content-type: application/octet-stream");
    	if (!empty($wordname))
    		fputs ($handle, $headert.$content.$footer);
    	fclose($handle);
  }*/
  
  
  /**
   * 律师协议提交
   */
  /*function protol_doc(){
  	$filename = '';
  	$data = $this->data;
  	if($_FILES['userfile']['error']==0){
  		$rs = $this->document_upload('./upload/affix/'.date('Ymd',time()).'/');
  		$filename = 'upload/affix/'.date('Ymd',time()).'/'.$rs['file_name'];
  		$p = array();
  		$p['protocol'] = $filename;
  		$uaer = $this->session->userdata('user');
  		$p['ucode'] = $uaer['ucode'];
  		$re = $this->home->protocol($p);
  		if(!empty($re)){
  			$data = $this->data;
  			$this->load->view('default/suc.php',$data);
  		}
  	}else{
  		$this->load->view('default/wrong_protool.php',$data);
  	}
  }*/
  
  function closecon(){
  	$uaer = $this->session->userdata('user');
  	$p = array();
  	$p['closecon'] = 2;
  	$p['ucode'] = $uaer['ucode'];
  	$this->home->closecon($p);
  }
 
}


