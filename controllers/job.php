<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class job extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->model ( "job_model",'job' );
    $this->load->library('pagination');
  }
  /**
   * 人才中心页
   */
  public function index(){
  	$data = $this->data;
  	$data['type'] = $this->input->get('type',0)?$this->input->get('type',0):1;
	$post = $this->input->post();
	if(isset($post['title'])&&!empty($post['title'])){
		$post['reply'] = 1;
		$post['ucode'] = $data['user']['ucode'];
		$post['name'] = $data['user']['username'];
		$content = $post['content'];
		unset($post['content']);
		$id = $this->job->insertTable('df_job',$post);
		$this->job->insertTable('df_job_con',array('content'=>$content,'job_id'=>$id));
	}
  	if($data['type']>0){
	  	$perpage = 10;
	  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
	  	$data['job'] = $this->job->getJob($data['type'],$data['current'],$perpage);
	  	if(!empty($data['job'])){
		  	$data['reply'] = $this->job->getReply(join(',',array_keys($data['job'])));
		  	$this->load->library('pagination');
		  	$data['total'] = count($this->job->getJob($data['type']));
		  	$data['totalpage'] = ceil($data['total']/$perpage);
		  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'job?type='.$data['type'].'&');
		  	$this->pagination->initialize($config);
		  	$data['page'] = $this->pagination->create_links();
	  	}
	  	$data['ad'] = $this->job->getAd();
	  	$this->load->view('default/job.php',$data);
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
  /*
   * 人才详情页
   */
  public function detail(){
  	$data = $this->data;
  	$data['id'] = $this->input->get('id',0);
  	if($data['id']>0){
  		$data['jobcon'] = $this->job->getJobCon($data['id']);
  		$con = $this->input->post('content',0);
  		if(!empty($con)){
  			$this->job->insertTable('df_job_reply',array('content'=>$con,'job_id'=>$data['id'],'ucode'=>$data['user']['ucode'],'name'=>$data['user']['username'],'icon'=>$data['user']['icon']));
  		}
  		$perpage = 10;
  		$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  		$data['job'] = $this->job->getJobReply($data['id'],$data['current'],$perpage);
  		
  		if(!empty($data['job'])){
  			$data['jobcon'] = $this->job->getJobCon($data['id']);
  			$this->load->library('pagination');
  			$data['total'] = count($this->job->getJobReply($data['id']));
  			$data['totalpage'] = ceil($data['total']/$perpage);
  			$config = $this->page_config($data['total'],$perpage,STATIC_URL.'job/detail?id='.$data['id'].'&');
  			$this->pagination->initialize($config);
  			$data['page'] = $this->pagination->create_links();
  		}
  		$this->load->view('default/jobdetail.php',$data);
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
  /**
   * 图片上传
   */
  public function upload(){
  	require_once 'JSON.php';
  	$save_url = date("Ymd"). "/";
  	$data = $this->img_upload('./upload/job/'.$save_url);
  	header('Content-type: text/html; charset=UTF-8');
  	$json = new Services_JSON();
  	$file_url = STATIC_URL.'upload/job/'.$save_url.$data['file_name'];
  	echo $json->encode(array('error' => 0, 'url' => $file_url));
  
  	
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
  	if ( ! $this->upload->do_upload('imgFile'))
  	{
  		$error = $this->upload->display_errors();
  
  	}
  	else
  	{
  		$data = $this->upload->data();
  		
  		return $data;
  	}
  }


}
