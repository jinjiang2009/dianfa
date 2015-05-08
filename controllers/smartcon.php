<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Smartcon extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->library('pagination');
    $this->load->model ( "judgement_model",'judge' );
  }
  /**
   * 自制合同页
   */
  public function index($id=0){
  	$data = $this->data;
  	if(isset($id)&&$id!=0){
  		$data['id'] = $id;
  		$data['ju'] = true;
  		$data['content'] = $this->judge->getContractContent($id);
  	}else{
  		$data['ju'] = false;
  	}
  	
  	$data['contract'] = $this->judge->getAllContract();
  	$data['ttype'] = $this->judge->getAllType();
  	$this->load->view('default/smart.php',$data);
  }
  /**
   * view页
   */
  public function view(){
  	$data = $this->data;
  
  	$this->load->view('templates/contract.php',$data);
  }
  /*
   * 下载合同
   */
  public function download($id=0){
  	$data = $this->data;
  	if(empty($data['user'])){
  		$data['prompt']='未登录请登录后再下载';
  		$this->load->view('default/prompt',$data);
  		return false;
  	}
  	$data['content'] = $this->judge->getContractContent($id);
  	if(!empty($data['content'])){
	  	$wordname = $data['content']['name'].".doc";
	  	$headert='<html xmlns:o="urn:schemas-microsoft-com:office:office"
	  	xmlns:w="urn:schemas-microsoft-com:office:word"
	  	xmlns="http://www.w3.org/tr/rec-html40">';
	  	$footer="</html>";
	  	$handle = fopen('php://output', 'w');
	  	header ("Content-Disposition: attachment; filename=" .$wordname );
	  	header ("Content-type: application/octet-stream");
	  	if (!empty($wordname))
	  		fputs ($handle, $headert.$data['content']['content'].$footer);
	  	fclose($handle);
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
  public function test($id){echo $id;die;
  	$a = array('a'=>123,'b'=>456,'c'=>array('name'=>232,'title'=>'sfd'));
  	print_r(json_encode($a));die;
  	$this->load->library('email');//加载邮件类
  	$config['protocol'] = 'smtp';//邮件发送协议
  	$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
  	$config['smtp_user'] = 'jinjiang2009@126.com';
  	$config['smtp_pass'] = 'qwe2092223';//smtp密码
  	$this->email->initialize($config);
  	$this->email->from('jinjiang2009@126.com','北京点法网信息技术有限公司');//来自什么邮箱
  	$this->email->to('250817526@qq.com');//发到什么邮箱
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
					您的提问：<a style="font-size:16px;" target="_blank" href="http://www.idianfa.com/ask/detail?id=112416&done=2" alt="点击查看答案">我的朋友欠我钱我应该怎么办?</a><br /><br /><br />
					<p style="margin:0;">该解答仅代表律师个人观点。若是您对该解答不甚满意，你可以咨询网站的在线法务或致电点法网：84585012。</p>
				    <p style="margin:0;">欢迎您<a target="_blank" href="http://www.idianfa.com/register?type=1">注册</a>点法网，我们提供法律咨询、案件委托、文书制作等法律服务，满足您的各种需求。</p>
				    <p>指尖上的法律，一点就成；一站式解决方案；金牌律师在线。</p>
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