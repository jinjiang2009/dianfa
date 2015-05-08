<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Judge extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->library('pagination');
    $this->load->model ( "judgement_model",'judge' );
  }
  /**
   * 裁判文书页
   */
  public function index(){
  	$data = $this->data;
  	$get = $this->input->get();
  	$data['post'] = $get;
  	#搜索条件
  	$conmodel = '';
  	$url = '';
  	$data['regionurl']='';
  	$data['amanuensisurl']='';
  	$data['institutionurl']='';
  	$data['categoryurl']='';
  	if(!empty($get)){
  		unset($get['page']);
  		foreach($get as $k=>$v){
  			if(!empty($v) && $v !=0 && $v!=''){
  				$url .= $k.'='.$v.'&';
  			}
  		}
  		$conmodel = $get;
  		$data['regionurl'] = isset($get['region'])?$get['region']:0;
  		$data['amanuensisurl'] = isset($get['amanuensis'])?$get['amanuensis']:0;
  		$data['institutionurl'] = isset($get['institution'])?$get['institution']:0;
  		$data['categoryurl'] = isset($get['category'])?$get['category']:0;
  	}
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$perpage = 10;
  	$data['category'] = $this->judge->getCategory();
	$data['civil'] = $this->judge->getCivil();
  	$data['judge'] = $this->judge->getJudge($conmodel,$data['current'],$perpage);                          
  	if(!empty($data['judge'])){
	  	$ids = join(',',array_keys($data['judge']));
	  	$data['content'] =  $this->judge->getContent($ids);
	  	$data['court'] = $this->judge->getCourt();
	  	#分页
	  	$this->load->library('pagination');
	  	$ttt = $this->judge->getJudgeTotal($conmodel);
	  	$data['total'] = $ttt['total'];
	  	$data['totalpage'] = ceil($data['total']/$perpage);
	  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'judge?'.$url);
	  	$this->pagination->initialize($config);
	  	$data['page'] = $this->pagination->create_links();
  	}
  	#右边栏目相关内容
  	$this->load->model ( "laywer_model",'laywer' );
  	$data['territory'] = $this->laywer->getTerritory();
  	$data['panlaw'] = true;
  	$data['panlawyer'] = true;
  	if(isset($get['title'])&&!empty($get['title'])){
  		$data['law'] = $this->judge->getLaw($get['title']);
  		if(!empty($data['law'])){
  			$data['panlaw'] = false;
  		}else{
  			$data['law'] = $this->judge->getLaw();
  		}
  		
  		$data['tit'] = $get['title'];
  		$data['lawyer_id'] = $this->judge->getJudgeLawyer($get['title']);
  		
  		if(empty($data['lawyer_id'])){
  			$data['lawyer'] = $this->judge->getLawyer();
  		}else{
  			$data['panlawyer'] = false;
  			$tem = '(';
  			foreach($data['lawyer_id'] as $k=>$v){
  				$tem .= '"'.$v['laywer_id'].'",';
  			}
  			$tem .= ' "1111" )';
  			
  			$data['lawyer'] = $this->judge->getLawyer($tem);
  			
  		}
  	}else{
  		$data['lawyer'] = $this->judge->getLawyer();
  		$data['law'] = $this->judge->getLaw();
  	}
  	//p($data['law']);die;
  	$this->load->view('default/judge.php',$data);
  }
  /*
   * 详情页
   */
  public function detail(){
  	$data = $this->data;
  	$id = $this->input->get('id');
  	$tem = $this->judge->getJudge(array('id'=>$id));
  	
  	if(!empty($tem)){
  		$data['judge'] = $tem[$id];
  		$rss = $this->judge->getContent($id);
  		$data['content'] = $rss[$id]['content'];
  		$data['category'] = $this->judge->getCategory();
  		$data['court'] = $this->judge->getAllCourt();
  		//$data['amanuensis'] = $this->judge->getAllAmanuensis();
  		//$data['institution'] = $this->judge->getAllInstitution();
  		#常用法律法规
  		$data['process'] = array(0=>'初审',1=>'初审',2=>'终审',3=>'再审');
  		$everid = '1,957,1143,5463';
  		$this->load->model ( "law_model",'m_l' );
  		$data['normal'] = $this->m_l->everydayLaw($everid);
  		#相关法律法规
  		$data['each'] = $this->judge->getJudge(array('category'=>$data['judge']['category']),1,4);
  		
  		$this->load->view('default/judge_detail.php',$data);
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
  /*
   * 导数据
   
  public function data(){
  	$data = $this->judge->getJudgeById(255738);
  	unset($data['id']);
  	$id = $this->judge->insert('df_ju',$data);
  	echo $id;
  }
  */
}