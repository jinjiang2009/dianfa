<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class article extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->library('pagination');
    $this->load->model ( "judgement_model",'judge' );
  }
  /*
   * 文章首页
   */
  public function index(){
  	$data = $this->data;
  	$data['a'] = array(1=>'shiping',2=>'anli',3=>'fatiao',4=>'haoshu',5=>'lunwen');
  	$data['type'] = $this->judge->getArticleType();
  	$data['ptype'] = $this->judge->getArticleType(true);	
  	$data['article'] = $this->judge->getAllArticle();
  	$data['r'] = $this->judge->getRec();
  	$data['mess'] = $this->judge->mess();
  	$this->load->view('default/article1.php',$data);
  }
  /**
   * 文章详情页
   */
  public function detail(){
  	$data = $this->data;
  	$this->load->model ( "lhome_model",'home' );
  	$data['categoryurl'] = $this->home->getArticleCategory(0);
  	$get = $this->input->get();
  	$data['post'] = $get;
  	#搜索条件
  	$conmodel = '';
  	$url = '';
  	
  	$data['category'] = 0;
  	if(!empty($get)){
  		unset($get['page']);
  		foreach($get as $k=>$v){
  			if(!empty($v) && $v !=0 && $v!=''){
  				$url .= $k.'='.$v.'&';
  				$data[$k] = $v;
  			}
  		}
  		$conmodel = $get;
  		
  	}
  	$data['is'] = false;
  	if(isset($data['category'])){
  		$conmodel['type']  = $data['category'];
  		
  	}
  	if(isset($data['pidcategory'])){
  		$conmodel['type'] =  $data['pidcategory'];
  		$data['is'] = true;
  		$data['pidcategoryurl'] = $this->home->getArticleCategory($data['category']);
  	}
  	$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
  	$perpage = 10;
  	$data['article'] = $this->judge->getArticle($conmodel,$data['current'],$perpage);
  	if(!empty($data['article'])){
	  	#分页
	  	$this->load->library('pagination');
	  	$ttt = $this->judge->getArticleTotal($conmodel);
	  	$data['total'] = $ttt['total'];
	  	$data['totalpage'] = ceil($data['total']/$perpage);
	  	$config = $this->page_config($data['total'],$perpage,STATIC_URL.'article/detail?'.$url);
	  	$this->pagination->initialize($config);
	  	$data['page'] = $this->pagination->create_links();
  	}
  	
  	$this->load->view('default/article.php',$data);
  }
 
}