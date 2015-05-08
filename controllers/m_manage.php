<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/20
 * @desc 后台管理
 */
class m_manage extends m_base {
	private $jdata = array();
	/**
	 * @desc 图片类型
	 * @var array
	 */
	private $image_type = array('gif','jpg','jpeg','png');
	
	/**
	 *@desc 图片大小 2M
	 *@var int
	 */
	private $max_size = 2097152;
	function __construct() {
		parent::__construct ();
		$this->judgeUser();//用户登陆
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "m_login_model",'m_l' );
		$this->load->model ( "m_config_model",'m_c' );
		$this->load->model ( "m_data_model",'m_d' );
	}
	
	/**
	 * 管理登陆页
	 */
	public function index(){
		$this->load->view('manage/index');
	}
	
	
	/**
	 * 左侧
	 */
	public function left(){
			$data['left'] = array(
					array('c'=>'m_manage','a'=>'right','name'=>'公告栏'),
					array('c'=>'m_manage','a'=>'editPwd','name'=>'修改密码')
					);
		$this->load->view('manage/left',$data);
		
	}
	
	/**
	 * 右侧
	 */
	public function right(){
		$this->load->view('manage/homepage');
	}
	
	/**
	 * 顶部
	 */
	public function top(){
		$data = array();
		$data ['user'] = $this->getMLId(true);
		$desc = $this->judgeUser(true);
		$data ['menu'] = $this->m_l->getmenu($desc[2]);
		$this->load->view('manage/top',$data);
	}
	
	/**
	 * 修改密码
	 */
	function editPwd(){
		if(!$_POST){
			$this->load->view('manage/editpwd');
		}else{
			$pwd_o = md5($this->input->post('pwd_o'));
			$pwd_n = md5($this->input->post('pwd_n'));
			$id = $this->getMLId();
			$_r = $this->m_l->byIdInfo($id);

			if(empty($_r)){
				$this->jdata["success"] = 3;
				$this->json_out($this->jdata);
			}

			if($pwd_o!=$_r[0]['pwd']){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$r = $this->m_l->byIdpwd(array('pwd'=>$pwd_n,'id'=>$id));
			if($r){
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 *地区联动
	 */
	function region(){
		$p = array();
		$p = $this->get_params();
		$p['uuid'] = empty($p['uuid'])?'':$p['uuid'];
		$re = $this->m_c->region($p['uuid']);
		if (empty($re)){
			$this->jdata["success"] = 0;
			$this->json_out($this->jdata);
		}
		$this->jdata["success"] = 1;
		$this->jdata["message"] = $re;
		$this->json_out($this->jdata);
	}
	
	/**
	 *案由
	 */
	function category(){
		$p = array();
		$p = $this->get_params();
		$p['id'] = empty($p['id'])?'':$p['id'];
		$re = $this->m_c->categorys($p['id']);
		if (empty($re)){
			$this->jdata["success"] = 0;
			$this->json_out($this->jdata);
		}
		$this->jdata["success"] = 1;
		$this->jdata["message"] = $re;
		$this->json_out($this->jdata);
	}
	
	/**
	 * 查询法院
	 */
	function search_court(){
		$p = $this->get_params();
		$name = urldecode($p['court']);
		$re = $this->m_c->search_court($name);
		if (empty($re)){
			$this->jdata["success"] = 0;
			$this->json_out($this->jdata);
		}
		$this->jdata["success"] = 1;
		$this->jdata["message"] = $re;
		$this->json_out($this->jdata);
	}
	
	/**
	 * 查询律师
	 */
	function search_lawyer(){
		$p = $this->get_params();
		$name = urldecode($p['name']);
		if(empty($name)){
			$this->jdata["success"] = 1;
			$this->jdata["message"] = array();
			$this->json_out($this->jdata);
		}
		$re = $this->m_c->search_lawyer($name);
		if (empty($re)){
			$this->jdata["success"] = 0;
			$this->json_out($this->jdata);
		}
		$this->jdata["success"] = 1;
		$this->jdata["message"] = $re;
		$this->json_out($this->jdata);
	}
	
	/**
	 * 异步上传图片
	 */
	function upimg(){
			if ($_FILES ['fileImage'] ['error'] == 0) {
				$up_filename = $_FILES['fileImage']['name'];
				$tmp = explode(".", $up_filename);
				$suffix = trim(array_pop($tmp));
				if(!in_array(strtolower($suffix), $this->image_type)){
					$this->jdata["success"] = 5;
					$this->jdata["message"] = "上传的图片格式不正确";
					$this->json_out ($this->jdata);
				}
				$filesize = $_FILES['fileImage']['size'];
				if($filesize >= $this->max_size){
					$this->jdata["success"] = 6;
					$this->jdata["message"] = "上传的图片不能大于2M";
					$this->json_out($this->jdata);
				}
				
				$path='upload/img/'.date("Ymd");
				if(!file_exists($path))
				{
					//检查是否有该文件夹，如果没有就创建，并给予最高权限
					mkdir("$path", 0700);
				}//END IF
				
				if($_FILES["fileImage"]["name"])
				{
					$today=date("YmdHis"); //获取时间并赋值给变量
					$file2 = $path.'/'.$today.rand(100000, 999999).'.'.$suffix; //图片的完整路径
					$flag=1;
				}//END IF
				if($flag) $result=move_uploaded_file($_FILES["fileImage"]["tmp_name"],$file2);
				$oimg = $this->input->get('oimg');
				if (!empty($oimg))
					unlink($this->input->get('oimg'));
				$this->jdata["success"] = 1;
				$this->jdata["message"] = "上传成功";
				$this->jdata["img"] = $file2; 
				$this->json_out($this->jdata);
				//特别注意这里传递给move_uploaded_file的第一个参数为上传到服务器上的临时文件
			}//END IFb	
	}
	
	/**
	 * 读取word
	 */
	function readword(){
		//$type = empty($this->input->get('type'))?'':$this->input->get('type');
		$file = BASE_ROOT.$this->input->get('name');
		if(is_file($file)) {

            header("Content-Type: application/force-download");

            header("Content-Disposition: attachment; filename=".basename($file));

            readfile($file);

            exit;

        }else{

            echo "文件不存在！2";

            exit;

        }
	}
	
	
}