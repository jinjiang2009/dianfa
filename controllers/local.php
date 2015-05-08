<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class local extends base{
		private $jdata = array();
		private $limit = 20;
		function __construct(){
			parent::__construct();
		
			$this->jdata["success"] = 0;
			$this->jdata["message"] = "";
			
			$this->load->model ( "home_model",'m_h' );
			$this->load->model ( "lhome_model",'home' );
			$this->load->model ( "m_data_model",'m_d' );
			$this->load->library('pagination');
		}

	function index(){
	
		$data = $this->data;
		$filename = array();
		if($_FILES){
			
			$time = 0;
			foreach($_FILES['userfile']['name'] as $k=>$v){
				if(!empty($v)){
					$time++;
				}
			}
			$filename = array();
			$docArray = array();
			for($i=0;$i<$time;$i++){
				$_FILES['a'.$i]['name'] = $_FILES['userfile']['name'][$i];
				$_FILES['a'.$i]['type'] = $_FILES['userfile']['type'][$i];
				$_FILES['a'.$i]['tmp_name'] = $_FILES['userfile']['tmp_name'][$i];
				$_FILES['a'.$i]['error'] = $_FILES['userfile']['error'][$i];
				$_FILES['a'.$i]['size'] = $_FILES['userfile']['size'][$i];

				$rs = $this->img_upload('./upload/local/'.date('Ymd',time()).'/','a'.$i);//var_dump($rs);exit;
				$filename[$i]['file_name'] = $rs['file_name'];
				$filename[$i]['file_path'] = 'upload/local/'.date('Ymd',time()).'/';
				$filename[$i]['file_ext'] = $rs['file_ext'];
				#入库
				//$docArray['doc'.$i] = $filename[$i]['file_path'].$filename[$i]['file_name'];
				//var_dump($docArray);echo "<br/>";	
				//$upload .= $filename[$i]['file_path'].$filename[$i]['file_name'].",";
				$tem = $i+1;
				$docArray['upload'.$tem] = $filename[$i]['file_path'].$filename[$i]['file_name'];
			}
				//$docArray["upload"] = rtrim($upload,",");
				$docArray['type'] = $_POST['type'];	
				//var_dump($docArray);echo "<br/>";
				$r = $this->m_d->localadd($docArray);

				header( "content-type:text/html;charset=utf-8" );
				echo "<script type=\"text/javascript\">";
				echo "alert(\"提交成功,工作人员会在3个工作日内与您取得联系\");";
				echo "</script>";
					$this->load->library('email');//加载邮件类
	  		$config['protocol'] = 'smtp';//邮件发送协议
	  		$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
	  		$config['smtp_user'] = 'jinjiang2009@126.com';
	  		$config['smtp_pass'] = 'qwe2092223';//smtp密码
	  		$this->email->initialize($config);
	  		$this->email->from('jinjiang2009@126.com','北京点法网信息技术有限公司');//来自什么邮箱
	  		$this->email->to('wunana@idianfa.com,yunchuan@gaotonglaw.com');//发到什么邮箱
	  		$this->email->subject('华夏大数据法商联合会申请入会材料');//邮件主题
	  		$this->email->message("请工作人员后台查看'网站控制'=>'华夏发商会'");//邮件内容
	  		$this->email->print_debugger();//返回包含邮件内容的字符串，包括EMAIL正文。用于调试
	  		var_dump($this->email->send());//发送email，根据发送结果，成功返回true,失败返回false,就可以用它判断局域
	  	}

	  	$filename =  serialize($filename);
		$this->load->view('default/local',$data);
		
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
	
	
	
	
}