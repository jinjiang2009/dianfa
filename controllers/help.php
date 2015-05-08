<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * @author chenxiao
 * @date 2014/10/28
 */
class help extends base {
	private $jdata = array();
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "help_model",'help' );
		//$this->load->library('pagination');
	}
	
	/**
	 * 法律援助
	 */
	function index(){
		//$this->output->cache(43200);
		$data = $this->data;
		$data['type'] = $this->help->getHelpType();
		$this->load->view('default/lawhelp',$data);
	}
	/**
	 * 法律援助栏目介绍
	 */
	function introduce(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('default/helpintroduce',$data);
	}
	
	/**
	 * 法援案件展示
	 */
	
	function caseshow(){
	
		$data = array();
		$data = $this->data;
		$get = $this->input->get();
		$perpage = 10;
		
		$conmodel ='';
		
		$url='';
		if(!empty($get)){
			unset($get['page']);
			foreach($get as $k=>$v){
				if(!empty($v)){
					$url .= $k.'='.$v.'&';
				}
			}
				$conmodel = $get;
		}
			
	$data['get'] = $conmodel;//
	$data['type'] = $this->help->getCase();
	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;//var_dump($data['current']);exit;
	$data['law'] = $this->help->getCaseshow($conmodel,$data['current'],$perpage);//var_dump($data['law']);exit;
	$this->load->library('pagination');
	$rssss = $this->help->getShow($conmodel);
	$data['total'] = $rssss['total'];//var_dump($data['total']);exit;
	$data['totalpage'] = ceil($data['total']/$perpage);//var_dump($data['totalpage']);
	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'help/caseshow?'.$url);//var_dump($config);exit;
	$this->pagination->initialize($config);
	$data['page'] = $this->pagination->create_links();
	
	
		//var_dump($_GET);exit;
		
	
		
		//$data['la'] = $this->help->getCaseshows();
		
		$this->load->view('default/show',$data);
	}
	
	
	
	
	
	/**
	 * 法律援助申请
	 */
	function apply(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('default/helpapply',$data);
	}
	/*
	 * 根据pid获得option
	 */
	function getoption(){
		$val = $this->input->post('pid',0);
		if($val!=0){
			$r = $this->help->getOptions($val);
			$str = '<option value="0">请选择</option>';
			foreach($r as $k=>$v){
				$str .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
			}
			echo $str;
		}
	}
	/*
	 * 上传
	 */
	function upload(){
		$val = $this->input->post();
		$filename = array();
		if($_FILES){
			
			$time = 0;
			foreach($_FILES['userfile']['name'] as $k=>$v){
				if(!empty($v)){
					$time++;
				}
			}
			$filename = array();
			for($i=0;$i<$time;$i++){
				
				$_FILES['a'.$i]['name'] = $_FILES['userfile']['name'][$i];
				$_FILES['a'.$i]['type'] = $_FILES['userfile']['type'][$i];
				$_FILES['a'.$i]['tmp_name'] = $_FILES['userfile']['tmp_name'][$i];
				$_FILES['a'.$i]['error'] = $_FILES['userfile']['error'][$i];
				$_FILES['a'.$i]['size'] = $_FILES['userfile']['size'][$i];

				$rs = $this->img_upload('./upload/lawhelp/'.date('Ymd',time()).'/','a'.$i);
				$filename[$i]['file_name'] = $rs['file_name'];
				$filename[$i]['file_path'] = 'upload/lawhelp/'.date('Ymd',time()).'/';
				$filename[$i]['file_ext'] = $rs['file_ext'];
			}
	  	}
	  	
	  	$filename =  serialize($filename);
	  	$typearray = array('type1'=>isset($val['type1'])?$val['type1']:0,'type2'=>isset($val['choi1'])?$val['choi1']:0,
	  						'type3'=>isset($val['type2'])?$val['type2']:0,'type4'=>isset($val['choi2'])?$val['choi2']:0,
	  						'type5'=>isset($val['type3'])?$val['type3']:0,'type6'=>isset($val['choi3'])?$val['choi3']:0,
	  						'type7'=>isset($val['type4'])?$val['type4']:0
	  				);
	  	$id = $this->help->insertTable('df_help_type',$typearray);
	  	$infoarray = array('name'=>$val['name'],'address'=>$val['address'],
	  				'email'=>$val['email'],'tel'=>$val['tel'],
	  				'content'=>$val['content'],'upload'=>$filename,
	  				'type'=>$id
	  				);
	  	$this->help->insertTable('df_help_detail',$infoarray);
		#发邮件

	  		$this->load->library('email');//加载邮件类
	  		$config['protocol'] = 'smtp';//邮件发送协议
	  		$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
	  		$config['smtp_user'] = 'jinjiang2009@126.com';
	  		$config['smtp_pass'] = 'qwe2092223';//smtp密码
	  		$this->email->initialize($config);
	  		$this->email->from('jinjiang2009@126.com','北京点法网信息技术有限公司');//来自什么邮箱
	  		$this->email->to('2479307496@qq.com,zhouqianqian@idianfa.com');//发到什么邮箱
	  		$this->email->subject('北京点法网信息技术有限公司-法律援助');//邮件主题
	  		$this->email->message("请工作人员后台查看新法律援助--援助人姓名:".$val['name']);//邮件内容
	  		$this->email->print_debugger();//返回包含邮件内容的字符串，包括EMAIL正文。用于调试
	  		$this->email->send();//发送email，根据发送结果，成功返回true,失败返回false,就可以用它判断局域
	  
	  	$conn = '信息提交成功,我们会在3个工作日内与您联系';
	  	header( "content-type:text/html;charset=utf-8" );
	  	echo "<script type=\"text/javascript\">";
	  	echo "alert(\"{$conn}\");";
	  	//echo 'parent.location.reload(true);';
	  	echo "</script>";
		
	  	exit;
	}
	/**
	 * 上传
	 */
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
	/*
	 * 下载
	*/
	function download(){
		$p = $this->get_params();
		$info = $this->help->getLawHelp($p['id']);
		$co = unserialize($info['upload']);//var_dump(filesize((dirname(__FILE__).'\a.txt')));die;
		foreach($co as $k=>$v){
			if($k>0){
			$filename=STATIC_URL.$v['file_path'].$v['file_name']; //文件名	
			$date=date("Ymd-H:i:m").$v['file_ext'];
			Header( "Content-type:  application/octet-stream ");
			Header( "Accept-Ranges:  bytes ");
			Header( "Accept-Length: " );
			header( "Content-Disposition:  attachment;  filename= {$date}");
			file_get_contents($filename);
			readfile($filename);
			}
			
		}
		
	}

	
	/**
	 * 关于我们
	 */
	function about_us(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/about_us',$data);
	}
	
	/**
	 * 产品中心
	 */
	function product(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/product',$data);
	}
	
	/**
	 * 加盟
	 */
	function join(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/join',$data);
	}
	
	/**
	 * 帮助
	 */
	function helpinfo(){
		
	}
	
	/**
	 * 声明
	 */
	function statement(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/statement',$data);
	}
	
	/**
	 * 诚邀精英
	 */
	function elite(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/elite',$data);
	}
	
	/**
	 * 网站地图
	 */
	function map(){
		
	}
	
	/**
	 * 退款流程
	 */
	function refund(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/refund',$data);
	}
	
	/**
	 * 服务流程
	 */
	function idea(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/idea',$data);
	}
	
	/**
	 * 联系我们
	 */
	function contact(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/connect',$data);
	}
	/**
	 * 在线客服
	 */
	function C_Service(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/C_Service.php',$data);
	}
	
	
	function site_info(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info',$data);
	}
	
	function site_info1(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info1',$data);
	}
	
	function site_info2(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info2',$data);
	}
	
	function site_info3(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info3',$data);
	}
	
	function site_info4(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info4',$data);
	}
	
	function info_list(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/info_list',$data);
	}
	
	function site_info0(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info0',$data);
	}
	
	function site_info5(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info5',$data);
	}
	
	function site_info6(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info6',$data);
	}
	
	function site_info7(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info7',$data);
	}
	
	function site_info8(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info8',$data);
	}
	
	function site_info9(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info9',$data);
	}	
	function site_info11(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info11',$data);
	}
	function site_info12(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info12',$data);
	}
	function site_info13(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info13',$data);
	}
	function site_info14(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info14',$data);
	}
	public function buyhelp(){
		$data = $this->data;
		$this->load->view('bottom/buyhelp.php', $data );
	
	}
	function site_info15(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info15',$data);
	}
	function site_info16(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info16',$data);
	}
	function site_info17(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info17',$data);
	}
	function site_info18(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info18',$data);
	}
	function site_info19(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info19',$data);
	}
	function site_info20(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info20',$data);
	}
	function site_info21(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info21',$data);
	}
	function site_info22(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info22',$data);
	}
	function site_info23(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info23',$data);
	}
	function site_info24(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info24',$data);
	}
	function site_info25(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info25',$data);
	}
	function site_info26(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info26',$data);
	}
	function site_info27(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info27',$data);
	}
	function site_info28(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info28',$data);
	}
	function site_info29(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info29',$data);
	}
	function site_info30(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info30',$data);
	}
	function site_info31(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info31',$data);
	}
	function site_info32(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info32',$data);
	}
	function site_info33(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info33',$data);
	}
	function site_info34(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info34',$data);
	}
	function site_info35(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info35',$data);
	}
	function site_info36(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info36',$data);
	}
	function site_info37(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info37',$data);
	}
	function site_info38(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info38',$data);
	}
	function site_info39(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info39',$data);
	}
	function site_info40(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info40',$data);
	}
	function site_info41(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info41',$data);
	}
	function site_info42(){
		$this->output->cache(43200);
		$data = $this->data;
		$this->load->view('bottom/site_info42',$data);
	}
}