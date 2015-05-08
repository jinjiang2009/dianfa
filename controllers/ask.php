<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ask extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->model ( "question_model",'ask' );
    $this->load->library('pagination');
  }
  /**
   * 咨询中心页
   */
  public function index(){
	
  	$data = $this->data;
  	$get = $this->input->get();
  	$data['uurltitle'] = isset($get['title'])?$get['title']:'';
  	$data['uurlcon'] = isset($get['content'])?$get['content']:'';
  	$data['group'] = $this->ask->getGroup(null);
  	$data['typegroup'] = $this->ask->getGroup(1);
  	#去除大的分类
  	unset($data['typegroup'][0]);
  	$data['temp'] = array();
  	foreach($data['typegroup'] as $k=>$v){
  		$data['temp'][$k] = array_keys($v);
  	}
  	$this->load->model ( "judgement_model",'judge' );
  	$data['region'] = $this->judge->getRegion();
  	unset($data['region'][31]);
  	unset($data['region'][32]);
  	unset($data['region'][33]);
  	$data['nslove'] = $this->ask->getQuestion('id',1,' order by createtime desc ',' limit 10');
  	$data['ask'] = $this->ask->getQuestion('id',2,' order by createtime desc ',' limit 1000 ');
  	$data['alluser'] = $this->ask->getAllUser();
  	$this->load->view('default/ask.php',$data);
  }
 /*
  * 异步验证咨询中心的数据
  */
  public function verify(){
  	$post = $this->input->post();
  	$re = $this->ask->verifyTitle($post['title']);
  	if(empty($re)){
	  	session_start();
	  	if($_SESSION["helloweba_char"]==$post['code']){
		  	$data = $this->data;
		  	if($data['login_type']==4){
		  		$post['uid'] = 0 ;
		  	}else{
		  		$post['uid'] = $data['user']['ucode'] ;
		  		
		  		$this->integral_change(array('ucode'=>$post['uid'],'cause'=>12,'cor_id'=>'','num'=>10,'type'=>1));
		  	}
		  	unset($post['code']);
		  	$post['createtime'] = date('Y-m-d',time());
		  	$id = $this->ask->insertTable('df_question',$post);
		  	if($id>0){
		  		$msg['success'] = 1;
		  	}else{
		  		$msg['success'] = 2;
		  	}
	  	}else{
	  		$msg['success'] = 3;
	  	}
  	}else{
  		$msg['success'] = 4;
  	}
  	echo json_encode($msg);
  }
  /*
   * 验证咨询标题
   */
  public function verifytitle(){
  	header("Content-type:text/html;charset=utf-8");
  	$post = $this->input->post('title',0);
  	$title = urldecode($post);
  	if($title){
  		$re = $this->ask->verifyTitle(urldecode($post));
  		if(empty($re)){
  			echo 9999;
  		}else{
  			echo 1111;
  		}
  	}else{
  		echo 9999;
  	}
  }
  /*
   * 资讯列表页
   */
  public function asklist(){
  	$data = $this->data;
  	$data['group'] = $this->ask->getGroup(null);
  	$data['typegroup'] = $this->ask->getGroup(1);
  	unset($data['typegroup'][0]);
  	$get = $this->input->get();
  	$data['title'] = isset($get['title'])?$get['title']:'';
  	$perpage = 20;
  	$data['alpha'] = alpha();
  	$this->load->model ( "judgement_model",'judge' );
  	$data['region'] = $this->judge->getRegion();
  	$data['regionurl'] = '';
  	#分页
  	$data['groupurl']='';
  	$conmodel = '';
  	$url = '';
  	if(!empty($get)){
  		unset($get['page']);
  		foreach($get as $k=>$v){
  			if(!empty($v)){
  				$url .= $k.'='.$v.'&';
  			}
  		}
  		$conmodel = $get;
  		$data['groupurl'] = isset($get['group'])?$get['group']:'';
  		$data['regionurl'] = isset($get['region'])?$get['region']:'';
  	}
  	$data['recom'] = $this->ask->getQuestion(null,2,' order by createtime desc ',' limit 10 ',1);
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$data['question'] = $this->ask->getQuestionList($conmodel,$data['current'],$perpage);
  	//p($data['law']);die;
  	$this->load->library('pagination');
  	$resss = $this->ask->getQuestionTotal($conmodel);
  	$data['total'] = $resss['total'];
  	$data['totalpage'] = ceil($data['total']/$perpage);
  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'ask/asklist?'.$url);
  	$this->pagination->initialize($config);
  	$data['page'] = $this->pagination->create_links();
  	$data['lawyer'] = $this->ask->getRecomLawyer();
  	$this->load->model ( "laywer_model",'laywer' );
  	$data['territory'] = $this->laywer->getTerritory();
  	$this->load->view('default/asklist.php',$data);
  }
  /*
   * 咨询详情页
   */
  public function detail(){
  	$data = $this->data;
  	$get = $this->input->get();
  	$data['type'] = $get['done'];//$get['done'];
  	$data['qid'] = $get['id'] ;//$get['id'];
  	$data['question'] = $this->ask->getQueById($get['id']);
  	//$tem = $data['question']['content'];echo $tem;die;
  	if(!empty($data['question'])){
  		$data['addjudge'] = $this->ask->judgeAdd($data['qid']);
  		$data['question']['content'] = trim(strip_tags( html_entity_decode($data['question']['content'])));
		#计算相差天数
  		$d1= strtotime($data['question']['createtime']);
  		$d2= time();
  		$data['day'] = round(($d2-$d1)/3600/24);
  		$tem = $this->ask->getWho($data['question']['uid']);
  		$data['who'] = isset($tem['username'])?$tem['username']:'游客';
  		$data['answer'] = $this->ask->getAnswer($data['question']['id']);
  		$this->ask->updateQuestionClick($data['qid']);
  		#获得相关咨询
  		$data['solved'] = $this->ask->getQue(2,$data['question']['group_id']);
  		$data['nosolved'] = $this->ask->getQue(1,$data['question']['group_id']);
  		$this->load->view('default/askdetail.php',$data);
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  	
  }
  /*
   * 回答问题
   */
  public function answer(){
  	$data = $this->data;
  	$post = $this->input->post();
  	$c = $post['ctime'];
  	$title = $post['title'] ; 
  	$email = $post['email'] ;
  	unset($post['ctime']);
  	unset($post['email']);
  	unset($post['title']);
  	$post['ucode'] = $data['user']['ucode'];
  	if($this->ask->insertTable('df_answer',$post)){
  		$this->ask->updateQuestion($post['qid']);
  		$this->load->model ( "lhome_model",'home' );
  		$this->home->addOne('df_lawyer','atotal',$data['user']['ucode']);
  		if($c<8){
  			$this->integral_change(array('ucode'=>$data['user']['ucode'],'cause'=>18,'cor_id'=>'','num'=>5,'type'=>1));
  		}
  		echo 9999;
  		$this->load->library('email');//加载邮件类
  		$config['protocol'] = 'smtp';//邮件发送协议
  		$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
  		$config['smtp_user'] = 'jinjiang2009@126.com';
  		$config['smtp_pass'] = 'qwe2092223';//smtp密码
  		$this->email->initialize($config);
  		$this->email->from('jinjiang2009@126.com','北京点法网信息技术有限公司');//来自什么邮箱
  		$this->email->to($email);//发到什么邮箱
  		$this->email->subject('点法网--您咨询的问题已有回复,请注意查看');//邮件主题
  		$content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  		<html xmlns="http://www.w3.org/1999/xhtml">
  		<head>
  		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  		<title>点法网</title>
  		</head>
  		<body style="margin:0;padding:0;">
  		<div style="line-height:26px;font-size:12px;padding:5px;margin:10px auto;width:686px;border:2px #325e7f solid;border-radius:2px;">
  		<strong style="background-color:#0b6bab;color:#fff;font-size:14px;display:block;line-height:30px;text-indent:1em;">点法网免费法律咨询</strong>
  		<p style="margin:0;padding-top:12px;font-size:16px;">您好！您在点法网提出的法律问题，已经有律师为您解答了，快去看看吧！</p>
  		您的提问：<a style="font-size:16px;" target="_blank" href="http://www.idianfa.com/ask/detail?id='.$post['qid'].'&done=2" title="点击查看答案">'.$title.'</a><br />
  		<p style="margin:0;">该解答仅代表律师个人观点。若是您对该解答不甚满意，你可以咨询网站的在线法务或致电点法网：010-84585012。</p>
  		<p style="margin:0;">欢迎您<a target="_blank" href="http://www.idianfa.com/register?type=1">注册</a>点法网，我们提供法律咨询、案件委托、文书制作等法律服务，满足您的各种需求。</p>
  		<p>指尖上的法律，一点就成；一站式法律解决方案；金牌律师在线。</p>
  		<p style="">微信号：dianfawang<img src="http://www.idianfa.com/views/default/images/ew.gif" width="80" />；新浪微博：北京点法网<img src="http://www.idianfa.com/views/default/images/ewb.png" width="80" />。</p>
  		<p style="margin:0;overflow:hidden;"><a href="http://www.idianfa.com" target="_blank" style="float:right;margin:8px;"><img src="http://www.idianfa.com/views/default/images/logo_03.png" width="" /></a></p>
  		</div>
  		</body>
  		</html>
  		' ;
  		$this->email->message($content);//邮件内容
  		$this->email->print_debugger();//返回包含邮件内容的字符串，包括EMAIL正文。用于调试
  		$this->email->send();//发送email，根据发送结果，成功返回true,失败返回false,就可以用它判断局域
  		
  	}
  }
  /*
   * 补充问题
   */
  public function add(){
  	$data = $this->data;
  	$tem['status'] = 9999;
  	if($data['login_type']==4){
  		$tem['status'] = 9999;
  		
  	}else{
	  	$post = $this->input->post();
	  	if(isset($post['qid'])&&isset($post['con'])){
	  		$time = date('Y-m-d h:i:s',time());
	  		$rs = $this->ask->insertTable('df_ask_add',array('content'=>$post['con'],'qid'=>$post['qid'],'createtime'=>$time));
	  		if($rs){
	  			$this->ask->updateAdd($post['qid']);
	  			$tem['status'] = 1111;
	  			$tem['time'] = $time;
	  		}else{
	  			$tem['status'] = 9999;
	  		}
	  	}else{
	  		$tem['status'] = 9999;
	  	}
  	}
  	echo json_encode($tem);
  	die;
  }
//   public function test(){
//   	$mem = new Memcache;
//   	$mem->connect("114.215.140.141", 11211);
  	
//   	//保存数据
//   	$mem->set('key1', 2342);
//   	$val = $mem->get('key1');
//   	echo "Get key1 value: " . $val;
  	
//   }
}
