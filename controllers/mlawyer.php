<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class mlawyer extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->model ( "laywer_model",'laywer' );
    $this->load->library('pagination');
  }
  /**
   * 找律师页
   */
  public function index(){
  	$data = $this->data;
  	$get = $this->input->get();
  	$perpage = 20;
  	$tem = getIPLoc_sina(getip());
	$city = $tem?$tem:'北京';
	//$data['laywer'] = $this->laywer->getLaywer();
	$data['alpha'] = alpha();
	$this->load->model ( "judgement_model",'judge' );
	$data['region'] = $this->judge->getRegion();
	$data['allregion'] = $this->laywer->getRegion();
	$data['regionurl'] = '';
	$data['territory'] = $this->laywer->getTerritory();
	$data['territoryall'] = $this->laywer->getTerritory(true);
	$data['vip'] = array(0=>'推荐',1=>'钻石',2=>'金牌',3=>'银牌',4=>'普通');
	#分页
	$data['rerritoryurl']=0;
	$data['vipurl']=100;
	$data['regionurl']=0;
	$data['secre']=0;
	$data['office']=0;
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
		$data['rerritoryurl'] = isset($get['rerritory'])?$get['rerritory']:0;
		$data['vipurl'] = isset($get['vip'])?$get['vip']:100;
		$data['regionurl'] = isset($get['region'])?$get['region']:0;
		$data['connurl'] = isset($get['secre'])?$get['secre']:0;
		$data['officeurl'] = isset($get['office'])?$get['office']:'';
	}
	if($data['rerritoryurl']>0){
		$data['conn'] = $this->laywer->getSecondTerritory($data['rerritoryurl']);
	}
	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
	$data['law'] = $this->laywer->getConLay($conmodel,$data['current'],$perpage);
	//p($data['law']);die;
	$this->load->library('pagination');
	$rssss = $this->laywer->getConLayTotal($conmodel);
	$data['total'] = $rssss['total'];
	$data['totalpage'] = ceil($data['total']/$perpage);
	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'lawyer?'.$url);
	$this->pagination->initialize($config);
	$data['page'] = $this->pagination->create_links();
	$data['lawyer'] = $this->recomand_lawyer(7,3);//推荐律师
  	$this->load->view('default/lawyer2.php',$data);
  }
  /*
   * 律师详情页
   */
  public function detail(){
  	$data = $this->data;
  	$id = $this->input->get('id');
  	$info = $this->laywer->getLawyerById($id);
  	if(!empty($info)){
  		$data['lawyer'] = $info;
  		$data['territory'] = $this->laywer->getTerritory();
  		$data['vip'] = array(0=>'推荐',1=>'钻石',2=>'金牌',3=>'银牌',4=>'普通');
  		$data['region'] = $this->laywer->getRegion();
  		$data['judgement'] = $this->laywer->getJudgement($id);
  		$data['court'] = $this->laywer->getCourt();
  		$data['category'] = $this->laywer->getCategory();
  		//$data['contract'] = $this->laywer->getContract($id);
  		$data['article'] = $this->laywer->getArticle($id);
  		$data['contype'] = $this->laywer->getConType();
  		$this->load->model ( "lhome_model",'home' );
  		$data['question'] = $this->home->getQuestionLog($id);
  		
  		$this->load->view('default/lawyer_detail.php',$data);
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
  /*
   * 律师发表的文章
  */
  public function article(){
  	$data = $this->data;
  	$id = $this->input->get('id');
  	$info = $this->laywer->getLawyerById($id);
  	if(!empty($info)){
  		$data['lawyer'] = $info;
  		$data['territory'] = $this->laywer->getTerritory();
  		$data['vip'] = array(0=>'推荐',1=>'钻石',2=>'金牌',3=>'银牌',4=>'普通');
  		$data['region'] = $this->laywer->getRegion();
  		$data['article'] = $this->laywer->getArticleById($this->input->get('aid'));
  		$this->laywer->updataArticle($this->input->get('aid'));
  		$this->load->view('default/lawyer_article.php',$data);
  	}else{
  		$this->load->view('default/wrong.php',$data);
  	}
  }
}
