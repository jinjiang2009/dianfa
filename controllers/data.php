
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Data extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('data_model','group');
	}
	/*
	 * 洗q_group表的数据到df_group
	 */
	public function insertgroup(){
		$group = $this->group->getGroup();
		foreach($group as $key=>$val){
			$arr = array(
					'id'=>$val['g_id'],
					'pid'=>$val['p_id'],
					'title'=>$val['g_title']
			);
			$this->group->insertTable($arr,'df_group');
			
		}
	}
	/*
	 * 洗questions表的数据到df_question
	*/
	function insertquestion(){
		set_time_limit(0);
		$group = $this->group->getQuestion();
		foreach($group as $key=>$val){
			$arr = array(
					'id'=>$val['q_id'],
					'group_id'=>$val['g_id'],
					'title'=>$val['q_title'],
					'content'=>$val['q_content']
			);
			$this->group->insertTable($arr,'df_question');
	
		}
		echo 11;die;
	}
	/*
	 * 洗questions表的数据到df_question
	*/
	function insertanswer(){
		set_time_limit(0);
		$group = $this->group->getAnswer();
		foreach($group as $key=>$val){
			$arr = array(
					'id'=>$val['a_id'],
					'qid'=>$val['q_id'],
					'content'=>$val['a_content'],
					'createtime'=>$val['a_time']
			);
			$this->group->insertTable($arr,'df_answer');
	
		}
		echo 11;die;
	}
	/*
	 * question表时间错误修正
	 */
	function modifyanswer(){
		set_time_limit(0);
		$time = $this->group->getQuestionTime();
		foreach($time as $key=>$val){
			$arr = array(
					'createtime'=>$val['q_time']
			);
			$this->group->updateTable('df_question',$val['q_id'],$arr);
	
		}
		echo 11;die;
	}
	/*
	 * 洗legal_judgment_main表的数据到df_judgement
	*/
	function insertjudgement(){
		set_time_limit(0);
		$group = $this->group->getJudgement();
		foreach($group as $key=>$val){
			$arr = array(
					'createtime'=>$val['create'],
					'serial'=>$val['serial'],
					'title'=>$val['title'],
					'court'=>$val['court'],
					'content'=>$val['content'],
			);
			$this->group->insertTable($arr,'df_judgement');
	
		}
		echo 11;die;
	}
	/*
	 * 洗legal_court_main表的数据到df_court
	*/
	function insertcourt(){
		set_time_limit(0);
		$group = $this->group->getCourt();
		foreach($group as $key=>$val){
			$arr = array(
					'region'=>$val['region'],
					'address'=>$val['address'],
					'telephone'=>$val['telephone'],
					'name'=>$val['name'],
					'description'=>$val['description'],
					'createtime'=>$val['create'],
					'uuid'=>$val['uuid'],
			);
			$this->group->insertTable($arr,'df_court');
	
		}
		echo 11;die;
	}
	/*
	 * 洗df_judgement表的court字段数据
	*/
	function updatejudgement(){
		set_time_limit(0);
		$group = $this->group->getDfCourt();
		foreach($group as $key=>$val){
			
			$this->group->updateJudgement($val['id'],$val['uuid']);
	
		}
		echo 11;die;
	}
	/*
	 * 恢复df_judgement表court数据
	 */
	function recoverjudgement(){
		set_time_limit(0);
		$group = $this->group->getJudgement();
		foreach($group as $key=>$val){
			$arr = array(
					'court'=>$val['court'],	
			);
			$this->group->updateTable('df_judgement',$val['id'],$arr);
	
		}
		echo 11;die;
	}
	/*
	 * 洗legal_system_region表的数据到df_region
	*/
	function insertregion(){
		set_time_limit(0);
		$group = $this->group->getRegion();
		foreach($group as $key=>$val){
			$arr = array(
					'uuid'=>$val['uuid'],
					'name'=>$val['name'],
					'pid'=>$val['pid'],
					'type'=>$val['type'],
					'abbreviation'=>$val['abbreviation'],
			);
			$this->group->insertTable($arr,'df_region');
	
		}
		echo 11;die;
	}
	/*
	 * 洗laws表的数据到df_law
	*/
	function insertlaw(){
		set_time_limit(0);
		$category = eval(ART_CATEGORY);
		$tem1 = array_flip($category['law_cat']['child_category']);
		$tem2 = array_flip($category['promulgation_department']['child_category']);
		$tem3 = array_flip($category['time_effect']['child_category']);
		$tem4 = array_flip($category['effect_level']['child_category']);
		$group = $this->group->getLaw();
		foreach($group as $key=>$val){
			
			$law_cat = $tem1[$val['law_cat']]?$tem1[$val['law_cat']]:0;
			$promulgation = $tem2[$val['promulgation_department']]?$tem2[$val['promulgation_department']]:0;
			$time_effect = $tem3[$val['time_effect']]?$tem3[$val['time_effect']]:0;
			$effect_level = $tem4[$val['effect_level']]?$tem4[$val['effect_level']]:0;
		
			$arr = array(
					'name'=>$val['law_name'],
					'promulgation_date'=>$val['promulgation_date'],
					'effective_date'=>$val['effective_date'],
					'cat'=>$law_cat,
					'promulgation'=>$promulgation,
					'order_content'=>$val['order_content'],
					'law_content'=>$val['law_content'],
					'time_effect'=>$time_effect,
					'effect_level'=>$effect_level,
					'catalog'=>$val['catalog'],
					'law_subtitle'=>$val['law_subtitle'],
					'law_introtitle'=>$val['law_introtitle'],
			);
			$this->group->insertTable($arr,'df_law');
	
		}
		echo 11;die;
	}
	/*
	 * 洗legal_judgment_main表的审结日期和审理程序数据到df_judgement
	*/
	function modifyjudgement(){
		set_time_limit(0);
		$group = $this->group->getJudgement();
		foreach($group as $key=>$val){
			$arr = array(
					'process'=>$val['process'],
					'conclude'=>$val['conclude'],
			);
			$this->group->updateTable('df_judgement',$val['id'],$arr);;
	
		}
		echo 11;die;
	}
	/*
	 * 洗df_question表times,默认为1
	 */
	function questiontimes(){
		set_time_limit(0);
		$question = $this->group->getQuesTimes();
		foreach($question as $k=>$v){
			$arr= array('times'=>$v['tot'],'is_solve'=>2);
			$this->group->updateTable('df_question',$v['id'],$arr);;
		}
		echo 11;die;
	}
	/*
	 * 跑grab_lawer表数据到df_lawyer
	 */
	function grablawyer(){
		$lawyer =  $this->group->getLawyer();
		$regin = $this->group->getRegion();
		$qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
		
		foreach($lawyer as $k=>$v){
			$na = '';
			foreach($regin as $a=>$b){
				if(strpos($v['address'],$b['name'])){
					$na = $b['uuid'];break;
				}
			}
			$arr = array('ucode'=>strtoupper(md5(uniqid(mt_rand(), true))),
					     'name'=>str_replace('律师','',trim($v['name'])),
						 'region'=>$na,
						 'court'=> str_replace($qian,$hou,$v['court']),
						'is_grab'=>2
					);
			$this->group->insertTable($arr,'df_lawyer');
		}
		echo 11;die;
	}
	/*
	 * 跑df_bjcourt表数据到df_judgement
	 */
	function javasc(){
		set_time_limit(0);
		$group = $this->group->getbjJudgement();
// 		str_replace('<script language="javascript">',' ',$group[0]['content']);
// 		str_replace('document.getElementById("cc").innerHTML=unescape(',' ',$group[0]['content']);
// 		str_replace(')',' ',$group[0]['content']);
// 		str_replace('</script>',' ',$group[0]['content']);
	//
		//echo strpos($group[0]['content'],"</script>");die;
		p($this->unescapecto("<html>\n\n<head>\n<meta http-equiv=Content-Type content=\"text/html; charset=x-cp20936\">\n<meta name=Generator content=\"Microsoft Word 11 (filtered)\">\n<title>\u5317\u4EAC\u5E02\u5E73\u8C37\u533A\u4EBA\u6C11\u6CD5\u9662</title>\n\n<style>\n<!--\n /* Font Definitions */\n @font-face\n\t{font-family:\u5B8B\u4F53;\n\tpanose-1:2 1 6 0 3 1 1 1 1 1;}\n@font-face\n\t{font-family:\u4EFF\u5B8B_GB2312;\n\tpanose-1:2 1 6 9 3 1 1 1 1 1;}\n@font-face\n\t{font-family:Calibri;}\n@font-face\n\t{font-family:\"\\@\u5B8B\u4F53\";\n\tpanose-1:2 1 6 0 3 1 1 1 1 1;}\n@font-face\n\t{font-family:\"\\@\u4EFF\u5B8B_GB2312\";\n\tpanose-1:2 1 6 9 3 1 1 1 1 1;}\n /* Style Definitions */\n p.MsoNormal, li.MsoNormal, div.MsoNormal\n\t{margin:0cm;\n\tmargin-bottom:.0001pt;\n\ttext-align:justify;\n\ttext-justify:inter-ideograph;\n\tfont-size:10.5pt;\n\tfont-family:\"Times New Roman\";}\np.MsoHeader, li.MsoHeader, div.MsoHeader\n\t{margin:0cm;\n\tmargin-bottom:.0001pt;\n\ttext-align:center;\n\tlayout-grid-mode:char;\n\tborder:none;\n\tpadding:0cm;\n\tfont-size:9.0pt;\n\tfont-family:\"Times New Roman\";}\np.MsoFooter, li.MsoFooter, div.MsoFooter\n\t{margin:0cm;\n\tmargin-bottom:.0001pt;\n\tlayout-grid-mode:char;\n\tfont-size:9.0pt;\n\tfont-family:\"Times New Roman\";}\np.MsoAcetate, li.MsoAcetate, div.MsoAcetate\n\t{margin:0cm;\n\tmargin-bottom:.0001pt;\n\ttext-align:justify;\n\ttext-justify:inter-ideograph;\n\tfont-size:9.0pt;\n\tfont-family:\"Times New Roman\";}\n /* Page Definitions */\n @page Section1\n\t{size:21.0cm 841.95pt;\n\tmargin:3.0cm 70.9pt 70.9pt 3.0cm;}\ndiv.Section1\n\t{page:Section1;}\n-->\n</style>\n\n</head>\n\n<body bgcolor=white lang=ZH-CN style='text-justify-trim:punctuation'>\n\n\n\n<p class=MsoNormal align=center style='margin-bottom:24.0pt;text-align:center'><span\nstyle='font-size:22.0pt;font-family:\u5B8B\u4F53'>\u5317\u4EAC\u5E02\u5E73\u8C37\u533A\u4EBA\u6C11\u6CD5\u9662</span></p>\n\n<p class=MsoNormal align=center style='margin-bottom:12.0pt;text-align:center;\nline-height:32.0pt'><b><span style='font-size:26.0pt;font-family:\u5B8B\u4F53'>\u6C11\u4E8B\u88C1\u5B9A\u4E66</span></b></p>\n\n<p class=MsoNormal align=right style='margin-bottom:12.0pt;text-align:right;\nline-height:19.0pt'><span style='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\uFF08</span><span\nlang=EN-US style='font-size:16.0pt'>2014</span><span style='font-size:16.0pt;\nfont-family:\u4EFF\u5B8B_GB2312'>\uFF09\u5E73\u6C11\u521D\u5B57\u7B2C</span><span lang=EN-US style='font-size:16.0pt'>02056</span><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u53F7</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u539F\u544A\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\u80A1\u4EFD\u6709\u9650\u516C\u53F8\u5317\u4EAC\u5E73\u8C37\u652F\u884C\uFF0C\u4F4F\u6240\u5730\u5317\u4EAC\u5E02\u5E73\u8C37\u533A\u6587\u5316\u5357\u8857<span\nlang=EN-US>19</span>\u53F7\u3002\u7EC4\u7EC7\u673A\u6784\u4EE3\u7801<span lang=EN-US>:10292003-5</span>\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u8D1F\u8D23\u4EBA\u738B\u9896\u54F2\uFF0C\u884C\u957F\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u59D4\u6258\u4EE3\u7406\u4EBA\u738B\u5251\u6CE2\uFF0C\u5317\u4EAC\u5E02\u541B\u6CF0\u5F8B\u5E08\u4E8B\u52A1\u6240\u5F8B\u5E08\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u59D4\u6258\u4EE3\u7406\u4EBA\u6797\u6D69\uFF0C\u5317\u4EAC\u5E02\u541B\u6CF0\u5F8B\u5E08\u4E8B\u52A1\u6240\u5F8B\u5E08\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u88AB\u544A\u5E74\u56DB\u6770\uFF0C\u7537\uFF0C<span\n lang=EN-US>1954</span>\u5E74<span lang=EN-US>5</span>\u6708<span lang=EN-US>17</span>\u65E5\u51FA\u751F\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u88AB\u544A\u9648\u4FDD\u82F1\uFF0C\u5973\uFF0C<span\n lang=EN-US>1956</span>\u5E74<span lang=EN-US>10</span>\u6708<span lang=EN-US>18</span>\u65E5\u51FA\u751F\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u672C\u9662\u5728\u5BA1\u7406\u539F\u544A\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\u80A1\u4EFD\u6709\u9650\u516C\u53F8\u5317\u4EAC\u5E73\u8C37\u652F\u884C\uFF08\u4EE5\u4E0B\u7B80\u79F0\u5E73\u8C37\u652F\u884C\uFF09\u4E0E\u88AB\u544A\u5E74\u56DB\u6770\u3001\u9648\u4FDD\u82F1\u501F\u6B3E\u5408\u540C\u7EA0\u7EB7\u4E00\u6848\u4E2D\uFF0C\u539F\u544A\u5E73\u8C37\u652F\u884C\u4E8E<span lang=EN-US>2014</span>\u5E74<span lang=EN-US>7</span>\u6708<span lang=EN-US>23</span>\u65E5\u5411\u672C\u9662\u63D0\u51FA\u64A4\u8BC9\u7533\u8BF7\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u672C\u9662\u8BA4\u4E3A\uFF0C\u539F\u544A\u5E73\u8C37\u652F\u884C\u4EE5\u5176\u9700\u8865\u5145\u76F8\u5173\u8BC1\u636E\u4E3A\u7531\u800C\u63D0\u51FA\u7684\u64A4\u8BC9\u7533\u8BF7\uFF0C\u7B26\u5408\u6CD5\u5F8B\u89C4\u5B9A\uFF0C\u5E94\u4E88\u51C6\u8BB8\u3002\u4F9D\u7167\u300A\u4E2D\u534E\u4EBA\u6C11\u5171\u548C\u56FD\u6C11\u4E8B\u8BC9\u8BBC\u6CD5\u300B\u7B2C\u4E00\u767E\u56DB\u5341\u4E94\u6761\u4E4B\u89C4\u5B9A\uFF0C\u88C1\u5B9A\u5982\u4E0B\uFF1A</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u51C6\u8BB8\u539F\u544A\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\u80A1\u4EFD\u6709\u9650\u516C\u53F8\u5317\u4EAC\u5E73\u8C37\u652F\u884C\u64A4\u56DE\u8D77\u8BC9\u3002</span></p>\n\n<p class=MsoNormal style='text-indent:32.0pt;line-height:34.4pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u6848\u4EF6\u53D7\u7406\u8D39\u4E09\u5341\u4E94\u5143\uFF0C\u7531\u539F\u544A\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\u80A1\u4EFD\u6709\u9650\u516C\u53F8\u5317\u4EAC\u5E73\u8C37\u652F\u884C\u8D1F\u62C5\uFF08\u5DF2\u4EA4\u7EB3\uFF09\u3002</span></p>\n\n<p class=MsoNormal align=right style='margin-top:48.0pt;margin-right:30.35pt;\nmargin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt;text-align:right;\nline-height:150%'><span style='font-size:16.0pt;line-height:150%;font-family:\n\u4EFF\u5B8B_GB2312'>\u5BA1<span lang=EN-US>&nbsp; </span>\u5224<span lang=EN-US>&nbsp; </span>\u957F<span\nlang=EN-US>&nbsp;&nbsp;&nbsp; </span>\u5F20\u6811\u6CE2<span lang=EN-US><br>\n</span>\u4EBA\u6C11\u966A\u5BA1\u5458<span lang=EN-US>&nbsp;&nbsp;&nbsp; </span>\u738B\u80DC\u53CB<span lang=EN-US><br>\n</span>\u4EBA\u6C11\u966A\u5BA1\u5458<span lang=EN-US>&nbsp;&nbsp;&nbsp; </span>\u5434\u798F\u987A</span></p>\n\n<p class=MsoNormal align=right style='margin-top:36.0pt;margin-right:30.35pt;\nmargin-bottom:24.0pt;margin-left:0cm;text-align:right;text-indent:45.95pt'><span\nstyle='font-size:16.0pt;font-family:\u4EFF\u5B8B_GB2312'>\u4E8C\u25CB\u4E00\u56DB\u5E74\u4E03\u6708\u4E8C\u5341\u4E09\u65E5</span></p>\n\n<p class=MsoNormal align=right style='margin-right:30.35pt;text-align:right;\ntext-indent:45.95pt;line-height:30.0pt'><span style='font-size:16.0pt;\nfont-family:\u4EFF\u5B8B_GB2312'>\u4E66<span lang=EN-US>&nbsp; </span>\u8BB0<span lang=EN-US>&nbsp;\n</span>\u5458<span lang=EN-US>&nbsp;&nbsp;&nbsp; </span>\u5F90<span lang=EN-US>&nbsp; </span>\u7545</span></p>\n\n\n\n</body>\n\n</html>\n"));die;
		
	}
	/*
	 * 跑localhost df_law表到192.168.0.110
	 */
	public function dflaw(){
		set_time_limit(0);
		$lawarr = eval(ART_CATEGORY);
		$law = $this->group->getlawl();
		$time = array_flip($lawarr['time_effect']['child_category']);
		$effect = array_flip($lawarr['effect_level']['child_category']);
		foreach($law as $k=>$v){
			$arr = array(
					'name'=>str_replace('_找法网','',$v['name']),
					'promulgation_date'=>$v['promulgation_date'],
					'effective_date'=>$v['effective_date'],
					'time_effect'=>isset($time[$v['time_effect']])?$time[$v['time_effect']]:0,
					'effect_level'=>isset($effect[$v['effect_level']])?$effect[$v['effect_level']]:0,
					'content'=>$v['content'],
					
					);
			$this->group->insertTable($arr,'df_law');
		}
		echo 11;die;
	}
	/*
	 * 跑法规颁布部门
	 */
	public function lawpro(){
		$law = $this->group->getLawList(1);
		p($law);
	}
	/*
	 * 跑localhost test表数据到192.168.0.110 test
	 */
	public function test(){
		set_time_limit(0);
		for($i=1;$i<41583;$i++){
			$group = $this->group->gettest($i);
			$this->group->insertTable(array('content'=>$group['content'],'title'=>$group['title'],'category'=>$group['category']),'test');
		}
		echo 11;
	}
	/*
	 * memcache 缓存
	 */
	public function memcache(){echo 11;die;
		echo Phpinfo();die;
		$memcache = new memcache;p($memcache);die;
		$memcache -> connect('114.215.140.141', 11211) or die("连接失败");
		$memcache -> set('name', array('一个','两个'));
		$val = $memcache->get('name');
		print_r($val);
		$memcache -> close();
		
	}
	/*
	 * 跑localhost dianfa表legal_office_main数据到192.168.0.110 df_office
	*/
	public function df_office(){
		set_time_limit(0);
		$office = $this->group->getOffice();
		foreach($office as $k=>$v){
			$r = $this->group->veriOffice($v['title']);
			p($r);die;
			if(empty($r)){
				$this->group->insertTable(array('title'=>$v['title'],'tel'=>$v['tel'],'address'=>$v['address']),'df_office');
			}
		}
		echo 11;
	}
	/*
	 * 合并judgement数据
	 */
	function union(){
		set_time_limit(0);
		
		for($i=1520000;$i<1565319;$i++){
			$ju = $this->group->getJu($i);
			if(isset($ju['content'])){
				$content = $ju['content'];
				unset($ju['cor']);
				unset($ju['content']);
				unset($ju['id']);
				$id = $this->group->insert('df_ju',$ju);
				$con = array('id'=>$id,'content'=>$content);
				$this->group->insert('df_jud_con',$con);
				//echo 22;
			}
		}
		echo 11;die;
	}
	function done($i,$n){
		for($i;$i<$n;$i++){
			$ju = $this->group->getJu($i);
			$content = $ju['content'];
			unset($ju['cor']);
			unset($ju['content']);
			unset($ju['id']);
			$id = $this->group->insert('df_ju',$ju);
			$con = array('id'=>$id,'content'=>$content);
			$this->group->insert('df_jud_con',$con);
			//echo 22;die;
		}
	}
	function unescape($str) {
		$str = rawurldecode($str);
		preg_match_all("/%u.{4}|&#x.{4};|&#\d+;|.+/U",$str,$r);
		$ar = $r[0];
		foreach($ar as $k=>$v) {
			if(substr($v,0,2) == "%u")
				$ar[$k] = iconv("UCS-2","utf-8",pack("H4",substr($v,-4)));
			elseif(substr($v,0,3) == "&#x")
			$ar[$k] = iconv("UCS-2","utf-8",pack("H4",substr($v,3,-1)));
			elseif(substr($v,0,2) == "&#") {
				$ar[$k] = iconv("UCS-2","utf-8",pack("n",substr($v,2,-1)));
			}
		}
		return join("",$ar);
	}
	function js_unescape($str)
	{
		$ret = '';
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			if ($str[$i] == '%' && $str[$i+1] == 'u') {
				$val = hexdec(substr($str, $i+2, 4));
				if ($val < 0x7f) $ret .= chr($val);
				else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
				else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
				$i += 5;
			} else if ($str[$i] == '%') {
				$ret .= urldecode(substr($str, $i, 3));
				$i += 2;
			} else $ret .= $str[$i];
		}
		return $ret;
	}
	function unescapecto($str)
	{
		$ret = '';
		$len = strlen($str);
		for ($i = 0; $i < $len; $i ++)
		{
			if ($str[$i] == '%' && $str[$i + 1] == 'u')
				{
				$val = hexdec(substr($str, $i + 2, 4));
				if ($val < 0x7f)
					$ret .= chr($val);
					else
					if ($val < 0x800)
					$ret .= chr(0xc0 | ($val >> 6)) .
				chr(0x80 | ($val & 0x3f));
				else
					$ret .= chr(0xe0 | ($val >> 12)) .
					chr(0x80 | (($val >> 6) & 0x3f)) .
				chr(0x80 | ($val & 0x3f));
					$i += 5;
				} else
						if ($str[$i] == '%')
						{
						$ret .= urldecode(substr($str, $i, 3));
	$i += 2;
	} else
	$ret .= $str[$i];
	}
	return $ret;
	}
}