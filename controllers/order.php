<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class order extends base{
  function __construct(){
    parent::__construct();
    $this->load->model ( "order_model",'order' );
    header("Content-type: text/html; charset=utf-8");
  }
  /*
   * 确认订单
   */
  public function confim(){
  	session_start();
  	if($_SESSION['ver']==$this->input->post('ver')){
  		$_SESSION['ver'] = '';
	  	$data = $this->data;
	  	if($data['login_type']==4){
	  		header_output('请先登录');die;
	  	}
	  	$post = $this->input->post();
	  	if(isset($post['moneyprice'])&&isset($post['subject'])
	  		&&isset($post['mobile'])&&isset($post['email'])
	  		&&isset($post['address'])&&isset($post['contracttime'])
	  			&&isset($post['type'])
	  	)
	  	{
	  		$filename = '';
	  		if($_FILES['userfile']['error']==0){
	  			$rs = $this->document_upload('./upload/affix/'.date('Ymd',time()).'/');
	  			$filename = 'upload/affix/'.date('Ymd',time()).'/'.$rs['file_name'];
	  		}
	  		$data['order_code'] = $this->c_order();
	  		$data['price'] = $post['moneyprice'];
	  		$data['subject'] = $post['subject'];
	  		if(isset($post['dcon_status'])){
	  			$dcon_status = $post['dcon_status'];
	  			$dcontract_type = $post['dcontract_type'];
	  			$dtheir = $post['dtheir'];
	  			$tem = array(1=>'起草',2=>'审核');
	  			$co = '文书合同'.$tem[$post['dcon_status']];
	  		}else{
	  			$dcon_status = '';
	  			$dcontract_type = '';
	  			$dtheir = '';
	  			$co = '法律产品';
	  		}
	  		$arr = array('money'=>$post['moneyprice'],
	  				'mobile'=>$post['mobile'],
	  				'email'=>$post['email'],
	  				'address'=>$post['address'],
	  				'contract_type'=>$dcontract_type,
	  				'their'=>$dtheir,
	  				'con_status'=>$dcon_status,
	  				'cor_id'=>isset($post['cor_id'])?$post['cor_id']:'',
	  				'content'=>$post['content'],
	  				'adjunct'=>$filename,
	  				'ucode'=>$data['user']['ucode'],
	  				'order_code'=>$data['order_code'],
	  				'type'=>$post['type'],
	  				'moneytype'=>$post['mmoneytype']
	  				);
	  		$data['mmoneytype'] = $post['mmoneytype'];
	  		$id = $this->order->insertTable('df_order',$arr);
	  		$data['orderid'] = $id;
	  		if($post['moneyprice']==0){
	  			$data['tt'] = 2;
	  			$data['prompt']='产品需求添加成功，我们律师会在1小时内与你联系';
	  			$this->load->view('default/prompt_ok',$data);
	  			
	  		}else{
	  			$this->load->view('default/order.php',$data);
	  		}
	  		
// 	  		#发邮件

	  		$this->load->library('email');//加载邮件类
	  		$config['protocol'] = 'smtp';//邮件发送协议
	  		$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
	  		$config['smtp_user'] = 'jinjiang2009@126.com';
	  		$config['smtp_pass'] = 'qwe2092223';//smtp密码
	  		$this->email->initialize($config);
	  		$this->email->from('jinjiang2009@126.com','北京点法网信息技术有限公司');//来自什么邮箱
	  		$this->email->to('2479307496@qq.com,zhouqianqian@idianfa.com');//发到什么邮箱
	  		$this->email->subject('北京点法网信息技术有限公司-订单-'.$co);//邮件主题
	  		$this->email->message("请工作人员后台查看新订单,订单号:".$data['order_code']);//邮件内容
	  		$this->email->print_debugger();//返回包含邮件内容的字符串，包括EMAIL正文。用于调试
	  		$this->email->send();//发送email，根据发送结果，成功返回true,失败返回false,就可以用它判断局域
	
	  	}else{
	  		$this->load->view('default/wrong.php',$data);
	  	}
  	}else{
  		header_output('请勿重复提交订单');
  		$this->load->view('default/wrong.php',$data);
  	}
  }
  /**
   * 订单页
   */
  public function index(){
  	$data = $this->data;
	$get = $this->input->get();
	if(isset($get['id'])&&$get['id']>0&&isset($get['subject'])){
		$re = $this->order->verorder($get['id']);
		if(!empty($re)&&isset($re['moneytype'])&&$re['moneytype']!=3){
			$data['subject'] = $get['subject'];
			$data['orderid'] = $data['id'] = $get['id'];
			if(trim($re['money'])==0.00){
				#法律产品多价格下的匹配
				$money = $this->order->getMoney($re['cor_id']);
				if(!empty($money)){
					if(strstr($money['unit_price'],';')){
						$data['moneytype'] = 2;
						preg_match_all("/\d+/i", $money['unit_price'], $matches);
						$data['moneyarr'] = $matches[0];
						$data['price'] = explode(';',$money['unit_price']);
						$this->order->updateTable('df_order',$get['id'],array('moneytype'=>2));
					}else{
						$data['moneytype'] = 1;
						preg_match("/\d+/i", $money['unit_price'], $matches);
						
						$data['price'] = $matches[0];
						$this->order->updateTable('df_order',$get['id'],array('money'=>$data['price']));
					}
					
				}
			}else{
				$data['moneytype'] = 1;
				$data['price'] = $re['money'];
			}
			
			$data['order_code'] = $data['ordernum'] = $re['order_code'];
	  		$this->load->view('default/order.php',$data);
		}else{
			$this->load->view('default/wrong.php',$data);
		}
	}else{
		$this->load->view('default/wrong.php',$data);
	}
  }
 /*
  * 结算
  */
  public function finalorder(){
  	$data = $this->data;
  	if($data['login_type']==4){
  		header_output('请先登录');die;
  	}
  	$post = $this->input->post();
  	if(isset($post['orderid'])&&isset($post['subject'])&&isset($post['pay']))
  	{
  		$bank = array(18=>'BOCBTB',19=>'ICBCBTB',20=>'CMBBTB',21=>'CCBBTB',22=>'ABCBTB',23=>'SPDBB2B',
						  1=>'BOCB2C',2=>'ICBCB2C',3=>'CMB',4=>'CCB',5=>'ABC',6=>'SPDB',7=>'CIB',8=>'GDB',9=>'CMBC',
						  10=>'CITIC',11=>'HZCBB2C',12=>'SHBANK',13=>'NBBANK',14=>'SPABANK',15=>'BJBANK',16=>'POSTGC',
						  17=>'COMM'
							);
  		$re = $this->order->verorder($post['orderid']);
  		if(!empty($re)){
  			if($post['pay']==99){
  				header("location:".STATIC_URL.'third_party/payment/alipayapi.php?ordernum='.$re['order_code'].'&subject='.$post['subject'].'&price='.$re['money']);
  			}else{
  				header("location:".STATIC_URL.'third_party/intertbank/alipayapi.php?ordernum='.$re['order_code'].'&subject='.$post['subject'].'&price='.$re['money'].'&bank='.$bank[$post['pay']]);
  			}
  		}else{
  			$this->load->view('default/wrong.php',$data);
  		}
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
  /*
   * 支付成功页面
   */
  public function success(){
  	$data = $this->data;
  	if($data['login_type']==4){
  		header_output('请先登录');die;
  	}
  	$ordercode = $this->input->get('orderid',0);
  	if($ordercode>0){
  		$rs = $this->order->verorderCode($ordercode);
  		if(!empty($rs)){
  			$data['ordernum'] = $ordercode;
  			$this->order->updateTable('df_order',$rs['id'],array('status'=>2));
  			$this->load->view('default/sucpay.php', $data );
  		}
  	}
  }
  /*
   * 支付宝异步验证页面
  */
  public function notify(){
  	$data = $this->data;
  	if($data['login_type']==4){
  		header_output('请先登录');die;
  	}
  	$ordercode = $this->input->get('orderid',0);
  	if($ordercode>0){
  		$rs = $this->order->verorderCode($ordercode);
  		if(!empty($rs)&&$rs['status']==2){
  			$data['ordernum'] = $ordercode;
  			$this->load->view('default/sucpay.php', $data );
  		}else{
  			$data['id'] = $rs['id'];
  			$rs = $this->order->getSubject($rs['cor_id']);
  			$data['subject'] = $rs['name'];
  			$this->load->view('default/orderw.php', $data );
  		}
  	}else{
  		$this->load->view('default/wrong.php', $data );
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
  	$config['allowed_types'] = 'doc|xls|jpg|png|jpeg|docx|xlsx';
  	$config['max_size'] = '1000000';
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
 
 
}


