<?php
namespace Workroom_admin\Controller;
use Think\Controller;
class BargainController extends Controller{
	/*前置设置*/
	public function _initialize(){
		/*customer_id_en*/
		$customer_id=decode_wsy(I("get.customer_id_en"));
		if(!is_numeric($customer_id)){
			return false;
		}
		/*主题*/
		$map_theme['id']=$customer_id;
		$style=M()->table(DB_NAME.'.customers')->where($map_theme)->find();
		if($style['theme']){
			$theme="content".$style['theme'];
		}else{
			$theme="contentblue";
		}
		$this->assign('theme',$theme); 
		/*活动过期更新kj_activity_global表*/
		$where_activity_global['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
		$where_activity_global['isvalid']=1;
		$result_activity_global=M("activity")->where($where_activity_global)->select();
		$count=count($result_activity_global);
		for($i=0;$i<$count;$i++){
			foreach ($result_activity_global[$i] as $key => $value) {
				if($key=="activity_end_time"){
					if($value<=date("Y-m-d H:i:s") && $result_activity_global[$i]['activity_status']==2){
						$where_activity_global['id']=$result_activity_global[$i]['id'];
						$save['activity_status']=3;
						M("activity")->where($where_activity_global)->save($save);
						// /*活动过期更新kj_activity_global_summary表*/
						// M("activity_global_summary")->where($where_activity_global)->save($save);
					}
				}
			}
		}
		$http=Protocol.$_SERVER['HTTP_HOST'];
		$this->assign("http",$http);
		$now_time = date('Y-m-d H:i:s');
		$this->assign("now_time",$now_time);
		// /*单项商品活动过期更新kj_activity_part_summary表*/
		// $where_activity_part['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
		// $where_activity_part['isvalid']=1;
		// $result_activity_part=M("activity_part")->where($where_activity_part)->select();
		// $count=count($result_activity_part);
		// for($i=0;$i<$count;$i++){
		// 	foreach ($result_activity_part[$i] as $key => $value) {
		// 		if($key=="activity_end_time"){
		// 			if($value<=date("Y-m-d H:i:s")){
		// 				$where_activity_part['activity_id']=$result_activity_part[$i]['activity_id'];
		// 				$where_activity_part['product_id']=$result_activity_part[$i]['product_id'];
		// 				$save['activity_status']=3;
		// 				M("activity_part_summary")->where($where_activity_part)->save($save);
		// 			}
		// 		}
		// 	}
		// }
	}
	/*将字符串截取只剩数字*/
	public function jqsz($str){
        $str=trim($str);
        if(empty($str)){return '';}
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(is_numeric($str[$i])){
                $result.=$str[$i];
            }
        }
        return $result;
	}
	/*主目录*/
	public function main(){
		/*customer_id_en*/
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		if(I("get.option") || I("get.activity_start_time") || I("get.activity_end_time") ){
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			if(I("get.option")){
				if(I("get.option")==3){
					$where['activity_status']=3;
				}else if(I("get.option")==4){
					$where['activity_status']=4;
				}else{
					$where['activity_status']=I("get.option");
				}
			}
			if(I("get.activity_start_time")){
				$where['activity_start_time']=array("egt",I("get.activity_start_time"));
			}
			if(I("get.activity_end_time")){
				$where['activity_end_time']=array("elt",I("get.activity_end_time"));
			}
			$rowNums = M('activity')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&option=".I("get.option")."&activity_start_time=".I("get.activity_start_time")."&activity_end_time=".I("get.activity_end_time")."&customer_id_en=".$customer_id_en);
			$result=M("activity")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="activity_status"){
						if($value==2){
							$result[$i]['status']="进行中";
						}else if($value==3){
							$result[$i]['status']="已过期";
						}else if($value==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="id"){
						$select_activity_part['activity_id']=$value;
						$select_activity_part['isvalid']=1;
						$select_activity_part['customer_id']=$customer_id;
						$product_num=M("goods")->where($select_activity_part)->sum("product_num");
						if($product_num){
							$result[$i]['product_num']=$product_num;
						}else{
							$result[$i]['product_num']=0;
						}
					}
				}
			}
		}else{
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;

			$rowNums = M('activity')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en);
			$result=M("activity")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="activity_status"){
						if($value==2){
							$result[$i]['status']="进行中";
						}else if($value==3){
							$result[$i]['status']="已过期";
						}else if($value==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="id"){
						$select_activity_part['activity_id']=$value;
						$select_activity_part['isvalid']=1;
						$select_activity_part['customer_id']=$customer_id;
						$product_num=M("goods")->where($select_activity_part)->sum("product_num");
						if($product_num){
							$result[$i]['product_num']=$product_num;
						}else{
							$result[$i]['product_num']=0;
						}
					}
				}
			}
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$option=array(
			array("option_name"=>"未发布","option_id"=>1),
			array("option_name"=>"进行中","option_id"=>2),
			array("option_name"=>"已过期","option_id"=>3),
			array("option_name"=>"已终止","option_id"=>4),
			);
		$this->leftmenu = 2;
		$this->assign('option',$option);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
		$this->assign('bargain',$result);
		$this->display("bargain/main");
	}

	public function activity(){
	}

	/*新建页*/
	public function create(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		if(I("get.activity_id")){
			$activity_id=I("get.activity_id");
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			$where['id']=I("get.activity_id");
			$result=M("activity")->where($where)->select();
			$result[0]["bargain_min_price"]=$result[0]["bargain_min_price"]/100;
			$result[0]["bargain_max_price"]=$result[0]["bargain_max_price"]/100;
			$this->assign('activity_id',$activity_id);
			$this->assign('bargain',$result);
		}
		$map_coupons['customer_id']=$customer_id;
		$map_coupons['isvalid']=1;
		$coupons=M()->table(DB_NAME.".weixin_commonshop_coupons")->where($map_coupons)->select();
		$coupon_http=$this->http."/weixinpl/back_newshops/MarkPro/vouchers/coupon_add.php?op=edit&customer_id=".$customer_id;
		$this->assign("coupon_http",$coupon_http);
		$this->assign('customer_id_en',$customer_id_en);
		$this->assign('coupons',$coupons);
		$this->display("bargain/create");
	}

	/*发布活动*/
	public function release(){
		if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']='缺少customer_id！';
			$this->ajaxReturn($invoke);
			//$this->error('缺少customer_id！',"main?&customer_id_en=".$customer_id_en);
		}
		$customer_id_en=I("get.customer_id_en");
		if(empty(I("get.activity_id"))){
			$invoke['error']=1002;
			$invoke['data']='缺少activity_id！';
			$this->ajaxReturn($invoke);
			//$this->error('缺少activity_id！',"main?&customer_id_en=".$customer_id_en);
		}
		$map_goods['activity_id']=I("get.activity_id");
		$map_goods['product_num']=array("egt",1);
		$map_goods['isvalid']=1;
		$goods=M("goods")->where($map_goods)->find();
		if(!$goods){
			$invoke['error']=1001;
			$invoke['data']='你还未添加商品或者商品数量太少';
			$this->ajaxReturn($invoke);
			//$this->error('你还未添加商品或者商品数量太少',"main?&customer_id_en=".$customer_id_en);
		}
		$where['id']=I("get.activity_id");
		$data['activity_status']=2;
		$res=M("activity")->where($where)->save($data);
		if($res){
			$invoke['error']=1000;
			$invoke['data']='发布成功！';
			$this->ajaxReturn($invoke);
			//$this->success('发布成功！',"main?customer_id_en=".$customer_id_en);
		} else {
			$invoke['error']=1002;
			$invoke['data']='发布失败！';
			$this->ajaxReturn($invoke);
			//$this->error('发布失败！',"main?&customer_id_en=".$customer_id_en);
		}
	}

	/*手动终止活动*/
	public function stop(){
		if(empty(I("get.customer_id_en"))){
			$this->error('缺少customer_id！',"main?&customer_id_en=".$customer_id_en);
		}
		$customer_id_en=I("get.customer_id_en");
		if(empty(I("get.activity_id"))){
			$this->error('缺少activity_id！',"main?&customer_id_en=".$customer_id_en);
		}
		$where['id']=I("get.activity_id");
		$data['activity_status']=4;
		$res=M("activity")->where($where)->save($data);
		if($res){
			$this->success('终止成功！',"main?customer_id_en=".$customer_id_en);
		} else {
			$this->error('终止失败！',"main?&customer_id_en=".$customer_id_en);
		}
	}

	/**/
	public function upload_pic(){
		$customer_id_en=I("post.customer_id_en");
		$customer_id=decode_wsy(I("post.customer_id_en"));
		require_once '/opt/www/weixin_platform/mp/lib/image.php';
		//http://admin.weisanyun.cn/resources/000/override/201709/15045204824161593531978.jpg
		$kkk=new \image();
		$file_path=$kkk->upload_image($_FILES['pic'],$customer_id,'banner');
		//var_dump($kkk->error_no,$kkk->error_msg,$file_path,UPLOAD_PATH.$file_path);
		//exit();
		//$msg = $kkk->error_msg();
		//$this->ajaxReturn($msg);
		$invoke['error']=1000;
		//https://admin.weisanyun.cn/resources/3243/banner/201709/15046126898897436749679.png
		$invoke['data']='https://'.$_SERVER['HTTP_HOST']."/resources/".$file_path;
		$return = json_encode($invoke);
		echo $return;
		exit();
		// if(empty(I("post.customer_id_en"))){
		// 	$invoke['error']=1003;
		// 	$invoke['data']="customer_id为空！";
		// 	$return = json_encode($invoke);
		// 	echo $return;
		// 	exit();
		// }

		// $upload = new \Think\Upload();// 实例化上传类
	 //    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	 //    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	 //    $upload->rootPath  =     APP_PATH . 'Workroom_admin/upload/'; // 设置附件上传根目录
	 //    $upload->savePath  =     'bargain/'; // 设置附件上传（子）目录
	 //    // 上传文件
	 //    $info   =   $upload->uploadOne($_FILES['pic']);
	 //    if(!$info) {// 上传错误提示错误信息
	 //    	$invoke['error']=1002;
		// 	$invoke['data']="上传失败！";
		// 	$return = json_encode($invoke);
		// 	echo $return;
		// 	exit();
	 //    }else{// 上传成功
	 //    	/*
	 //    	http://shenzhen.weisanyun.cn/weixinpl/haggling/back/Application/Workroom_admin/upload/bargain/2017-07-05/595c903957e41.jpg
	 //    	*/
	 //        $invoke['error']=1000;
		// 	$invoke['data']='http://'.$_SERVER['HTTP_HOST']."/weixinpl/haggling/back/Application/Workroom_admin/upload/".$info['savepath'].$info['savename'];
		// 	$return = json_encode($invoke);
		// 	echo $return;
		// 	exit();
	 //    }
	}

	/*新建活动或者修改活动*/
	public function create_activity(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$input=I('get.','','trim');
		if(empty($input['title'])){
			if(I("get.activity_id")){
				$this->error('缺少标题',"create?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id"));
			}else{
				$this->error('缺少标题',"create?customer_id_en=".$customer_id_en);
			}
		}
		if(I("get.activity_id")){

		}else{
			// if(empty($input['logo'])){
			// 	$this->error('缺少logo',"create?customer_id_en=".$customer_id_en);
			// }
		}
		if(empty($input['a_n'])){
			//$this->error('缺少可报名活动总次数',"create?customer_id_en=".$customer_id_en);
			$input['a_n']=0;
		}else if(!is_numeric($input['a_n'])){
			$this->error('可报名活动总次数格式填写错误',"create?customer_id_en=".$customer_id_en);
		}
		if(empty($input['s_p_a_n'])){
			//$this->error('缺少可报名单个商品活动次数',"create?customer_id_en=".$customer_id_en);
			$input['s_p_a_n']=0;
		}else if(!is_numeric($input['s_p_a_n'])){
			$this->error('可报名单个商品活动次数格式填写错误',"create?customer_id_en=".$customer_id_en);
		}
		if(empty($input['p_n'])){
			//$this->error('缺少可参与活动总次数',"create?customer_id_en=".$customer_id_en);
			$input['p_n']=0;
		}else if(!is_numeric($input['p_n'])){
			$this->error('可参与活动总次数格式填写错误',"create?customer_id_en=".$customer_id_en);
		}
		if(empty($input['s_p_b_n'])){
			$input['s_p_b_n']=0;
		}else if(!is_numeric($input['p_n'])){
			$this->error('单个商品砍价总次数格式填写错误',"create?customer_id_en=".$customer_id_en);
		}
		// if(empty($input['b_min_p'])){
		// 	$this->error('缺少最小砍价值',"create?customer_id_en=".$customer_id_en);
		// }else if(!is_numeric($input['b_min_p'])){
		// 	$this->error('最小砍价值格式填写错误',"create?customer_id_en=".$customer_id_en);
		// }

		if(empty($input['is_coupon']) || $input['is_coupon']==0){
			$data['is_coupon']=0;
			$data['coupon_id']=0;
            $data['coupon_probability']=0;
		}else if($input['is_coupon']==1){
			if(empty($input['coupon_id'])){
				if(I("get.activity_id")){
					$this->error('还未选择优惠券',"create?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id"));
				}else{
					$this->error('还未选择优惠券',"create?customer_id_en=".$customer_id_en);
				}
			}
			$data['is_coupon']=1;
			$data['coupon_id']=$input['coupon_id'];
            $data['coupon_probability']=100;
		}else{
			if(empty($input['coupon_id'])){
				if(I("get.activity_id")){
					$this->error('还未选择优惠券',"create?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id"));
				}else{
					$this->error('还未选择优惠券',"create?customer_id_en=".$customer_id_en);
				}
			}
			if(empty($input['coupon_probability'])){
				if(I("get.activity_id")){
					$this->error('还未设置中将概率',"create?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id"));
				}else{
					$this->error('还未设置中将概率',"create?customer_id_en=".$customer_id_en);
				}
			}
			$data['is_coupon']=2;
			$data['coupon_id']=$input['coupon_id'];
            $data['coupon_probability']=$input['coupon_probability'];
		}

		if(empty($input['b_max_p'])){
			$this->error('缺少最大砍价值',"create?customer_id_en=".$customer_id_en);
		}else if(!is_numeric($input['b_max_p'])){
			$this->error('最大砍价值格式填写错误',"create?customer_id_en=".$customer_id_en);
		}
		if(empty($input['begintime'])){
			$this->error('缺少开始时间',"create?customer_id_en=".$customer_id_en);
		}else if(!strtotime($input['begintime'])){
			$this->error('开始时间格式填写错误',"create?customer_id_en=".$customer_id_en);
		}
		if(empty($input['endtime'])){
			$this->error('缺少结束时间',"create?customer_id_en=".$customer_id_en);
		}else if(!strtotime($input['endtime'])){
			$this->error('结束时间格式填写错误',"create?customer_id_en=".$customer_id_en);
		}
		
		// $data['activity_status']=1;
		$data['activity_title']=$input['title'];
		if($input['logo']){
			$data['activity_logo']=$input['logo'];
		}
		if($input['ruler']){
			$data['ruler']=$input['ruler'];
		}
		$data['apply_number']=$input['a_n'];//3;
		$data['apply_number_single']=$input['s_p_a_n'];//1;
		$data['play_number']=$input['p_n'];//3;
		$data['play_number_single']=$input['s_p_b_n'];//1;
		$data['bargain_min_price']=$input['b_min_p']*100;//1;
		$data['bargain_max_price']=$input['b_max_p']*100;//500;
		$data['activity_start_time']=$input['begintime'];//date("Y-m-d H:i:s",time()+1*60*60);//
		$data['activity_end_time']=$input['endtime'];//date("Y-m-d H:i:s",time()+31*60*60);//
		$data['create_time']=date("Y-m-d H:i:s");
		$data['is_care']=$input['is_care'];
		$data['isvalid']=1;
		$data['customer_id']=$customer_id;//$input['customer_id'];
		if(I("get.activity_id")){
			$where['id']=I("get.activity_id");
			$result=M("activity")->where($where)->save($data);
			$map_goods['customer_id']=$customer_id;
			$map_goods['activity_id']=I("get.activity_id");
			$map_goods['isvalid']=1;
			//$map_goods['start_time']=$input['begintime'];
			//$map_goods['end_time']=$input['endtime'];
			$res_goods=M("goods")->where($map_goods)->select();
			for($i=0;$i<count($res_goods);$i++){
				$save_goods['id']=$res_goods[$i]['id'];
				$data_goods['start_time']=$input['begintime'];
				$data_goods['end_time']=$input['endtime'];
				$data_goods['apply_number']=$input['s_p_a_n'];
				$data_goods['play_number']=$input['s_p_b_n'];
				M("goods")->where($save_goods)->save($data_goods);
			}
			if($result){
				$this->success('修改成功',"main?customer_id_en=".$customer_id_en);
			} else {
				$this->error('修改失败',"create?id=".I("get.activity_id")."&customer_id_en=".$customer_id_en);
			}
		}else{
			// date_default_timezone_set('PRC');
			// $a=$this->jqsz(date("Y-m-d H:i:s"));
			// $data['activity_id']=$a.$customer_id;
			$data['activity_status']=1;
			$result=M("activity")->data($data)->add();
			if($result){
				$this->success('添加成功',"product?customer_id_en=".$customer_id_en."&activity_id=".$result);
			} else {
				$this->error('添加失败',"create?customer_id_en=".$customer_id_en);
			}
		}
	}

	public function activity_delete(){
		if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$where['id']=I("get.id");
		$where['customer_id']=$customer_id;
		$save['isvalid']=0;
		$temp=M("activity")->where($where)->save($save);
		if($temp){
			$invoke['error']=1000;
			$invoke['data']="删除活动成功";
			$this->ajaxReturn($invoke);
		}else{
			$invoke['error']=1002;
			$invoke['data']="删除活动失败";
			$this->ajaxReturn($invoke);
		}
	}

	public function activity_summary(){
		/*customer_id_en*/
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		if(I("get.option") || I("get.activity_id") || I("get.activity_title") ){
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			if(I("get.option")){
				if(I("get.option")==3){
					$where['activity_status']=3;
				}else if(I("get.option")==4){
					$where['activity_status']=4;
				}else{
					$where['activity_status']=I("get.option");
				}
			}
			if(I("get.activity_id")){
				$where['id']=I("get.activity_id");
			}
			if(I("get.activity_title")){
				$where['activity_title']=array("like",'%'.I("get.activity_title").'%');
			}
			$rowNums = M('activity')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&option=".I("get.option")."&activity_id=".I("get.activity_id")."&activity_title=".I("get.activity_title")."&customer_id_en=".$customer_id_en);
			$result=M("activity")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="activity_status"){
						if($value==2){
							$result[$i]['status']="进行中";
						}else if($value==3){
							$result[$i]['status']="已过期";
						}else if($value==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="id"){
						$map_forward['activity_id']=$value;
						$forward=M("forward")->field("sum(forwarding_num)")->where($map_forward)->select();
						if($forward[0]['sum(forwarding_num)']){
							$result[$i]['count_forwarding']=$forward[0]['sum(forwarding_num)'];
						}else{
							$result[$i]['count_forwarding']=0;
						}

						$map_access['activity_id']=$value;
						$access=M("access")->field("sum(access_total)")->where($map_access)->find();
						if($access['sum(access_total)']){
							$result[$i]['count_click']=$access['sum(access_total)'];
						}else{
							$result[$i]['count_click']=0;
						}


						$select_action['activity_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							$result[$i]['count_user']=count($result_action);
							$map_order['activity_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();
						
							$map_action['activity_id']=$value;
							$map_action['customer_id']=$customer_id;
							$map_action['isvalid']=1;
							$temp=M("action")->field("id,latest_price,product_id")->where($map_action)->select();
							$count_buy_price=0;
							for ($k=0; $k < count($temp); $k++) { 
								$map_goods['buy_price']=array("egt",$temp[$k]['latest_price']);
								$map_goods['id']=$temp[$k]['product_id'];
								$map_goods['customer_id']=$customer_id;
								$map_goods['isvalid']=1;
								$res_goods=M("goods")->where($map_goods)->find();
								if($res_goods){
									$count_buy_price=$count_buy_price+1;
								}
							}							
							/*完成可购买价人数*/
							$result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=$count_buy_price-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
							$count_price=M("order")->where($map_order)->sum("money");
							/*累计销售额*/
							if($count_price){
								$result[$i]['count_price']=$count_price;
							}else{
								$result[$i]['count_price']=number_format(0,2);
							}
							for ($j=0; $j < count($temp); $j++) { 
								$temp[$j]=$temp[$j]['id'];
							}
							$map_bargain['action_id']=array("IN",$temp);
							$map_bargain['customer_id']=$customer_id;
							$map_bargain['isvalid']=1;
							$count_bargain=M("bargain")->where($map_bargain)->sum("bargain_price");
							/*累计砍价金额*/
							if($count_bargain){
								$result[$i]['count_bargain']=$count_bargain;
							}else{
								$result[$i]['count_bargain']=number_format(0,2);
							}

							/*关注公众号人数*/
							for($j=0;$j<count($result_action);$j++){
								$arr_user1[$j]=$result_action[$j]['user_id'];
							}
							if(!$arr_user1){
								$arr_user1=array();
							}
							$bargain=M("bargain")->where($map_bargain)->select();
							for($j=0;$j<count($bargain);$j++){
								$arr_user2[$j]=$bargain[$j]['user_id'];
							}
							if(!$arr_user2){
								$arr_user2=array();
							}
							$arr=array_merge($arr_user1,$arr_user2);
							$time=array($result[$j]['activity_start_time'],$result[$j]['activity_end_time']);
							$map_weixin_users['id']=array("IN",$arr);
							$map_weixin_users['createtime']=array("BETWEEN",$time);
							$weixin_users=M()->table(DB_NAME.".weixin_users")->where($map_weixin_users)->count();
							// echo M()->table(DB_NAME.".weixin_users")->getlastsql();
							// var_dump($weixin_users);
							if($weixin_users){
								$result[$i]['count_new_user']=$weixin_users;
							}else{
								$result[$i]['count_new_user']=0;
							}
						}else{
							$result[$i]['count_user']=0;
							$result[$i]['count_buy_price']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
							$result[$i]['count_price']=0;
							$result[$i]['count_bargain']=0;
							$result[$i]['count_new_user']=0;
						}
					}
				}
			}
		}else{
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;

			$rowNums = M('activity')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en);
			$result=M("activity")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="activity_status"){
						if($value==2){
							$result[$i]['status']="进行中";
						}else if($value==3){
							$result[$i]['status']="已过期";
						}else if($value==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="id"){
						$map_forward['activity_id']=$value;
						$forward=M("forward")->field("sum(forwarding_num)")->where($map_forward)->select();
						if($forward[0]['sum(forwarding_num)']){
							$result[$i]['count_forwarding']=$forward[0]['sum(forwarding_num)'];
						}else{
							$result[$i]['count_forwarding']=0;
						}

						$map_access['activity_id']=$value;
						$access=M("access")->field("sum(access_total)")->where($map_access)->find();
						if($access['sum(access_total)']){
							$result[$i]['count_click']=$access['sum(access_total)'];
						}else{
							$result[$i]['count_click']=0;
						}

						$select_action['activity_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							$result[$i]['count_user']=count($result_action);
							$map_order['activity_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();
						
							$map_action['activity_id']=$value;
							$map_action['customer_id']=$customer_id;
							$map_action['isvalid']=1;
							$temp=M("action")->field("id,latest_price,product_id")->where($map_action)->select();
							$count_buy_price=0;
							for ($k=0; $k < count($temp); $k++) { 
								$map_goods['buy_price']=array("egt",$temp[$k]['latest_price']);
								$map_goods['id']=$temp[$k]['product_id'];
								$map_goods['customer_id']=$customer_id;
								$map_goods['isvalid']=1;
								$res_goods=M("goods")->where($map_goods)->find();
								if($res_goods){
									$count_buy_price=$count_buy_price+1;
								}
							}							
							/*完成可购买价人数*/
							$result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=$count_buy_price-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
							$count_price=M("order")->where($map_order)->sum("money");
							/*累计销售额*/
							if($count_price){
								$result[$i]['count_price']=$count_price;
							}else{
								$result[$i]['count_price']=number_format(0,2);
							}
							for ($j=0; $j < count($temp); $j++) { 
								$temp[$j]=$temp[$j]['id'];
							}
							$map_bargain['action_id']=array("IN",$temp);
							$map_bargain['customer_id']=$customer_id;
							$map_bargain['isvalid']=1;
							$count_bargain=M("bargain")->where($map_bargain)->sum("bargain_price");
							/*累计砍价金额*/
							if($count_bargain){
								$result[$i]['count_bargain']=$count_bargain;
							}else{
								$result[$i]['count_bargain']=number_format(0,2);
							}

							/*关注公众号人数*/
							for($j=0;$j<count($result_action);$j++){
								$arr_user1[$j]=$result_action[$j]['user_id'];
							}
							if(!$arr_user1){
								$arr_user1=array();
							}
							$bargain=M("bargain")->where($map_bargain)->select();
							for($j=0;$j<count($bargain);$j++){
								$arr_user2[$j]=$bargain[$j]['user_id'];
							}
							if(!$arr_user2){
								$arr_user2=array();
							}
							$arr=array_merge($arr_user1,$arr_user2);
							$time=array($result[$j]['activity_start_time'],$result[$j]['activity_end_time']);
							$map_weixin_users['id']=array("IN",$arr);
							$map_weixin_users['createtime']=array("BETWEEN",$time);
							$weixin_users=M()->table(DB_NAME.".weixin_users")->where($map_weixin_users)->count();
							// echo M()->table(DB_NAME.".weixin_users")->getlastsql();
							// var_dump($weixin_users);
							if($weixin_users){
								$result[$i]['count_new_user']=$weixin_users;
							}else{
								$result[$i]['count_new_user']=0;
							}
						}else{
							$result[$i]['count_user']=0;
							$result[$i]['count_buy_price']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
							$result[$i]['count_price']=number_format(0,2);
							$result[$i]['count_bargain']=number_format(0,2);
							$result[$i]['count_new_user']=0;
						}
					}
				}
			}
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$option=array(
			array("option_name"=>"未发布","option_id"=>1),
			array("option_name"=>"进行中","option_id"=>2),
			array("option_name"=>"已过期","option_id"=>3),
			array("option_name"=>"已终止","option_id"=>4),
			);
		$this->leftmenu = 2;
		$this->assign('option',$option);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
		$this->assign('bargain',$result);
		$this->display("bargain/activity_summary");
	}

	public function product_summary(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		if(I("get.option") || I("get.product_id") || I("get.product_name") ){
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			if(I("get.option")){
				if(I("get.option")==3){
					$map_first['activity_status']=3;
				}else if(I("get.option")==4){
					$map_first['activity_status']=4;
				}else{
					$map_first['activity_status']=I("get.option");
				}
				//$map_first['activity_status']=I("get.option");
				$res_first=M("activity")->where($map_first)->select();
				foreach ($res_first as $key => $value) {
					foreach ($value as $key1 => $value1) {
						if($key1=="id"){
							$activity_id=$activity_id.$value1.",";
						}
					}
				}
				$activity_id=substr($activity_id,0,strlen($activity_id)-1);
				$where['activity_id']=array("IN",$activity_id);
			}
			if(I("get.product_id")){
				$where['product_no']=I("get.product_id");
			}
			if(I("get.product_name")){
				$map_second['name']=array('like', '%'.I("get.product_name").'%');
				$res_second=M()->table(DB_NAME.".weixin_commonshop_products")->where($map_second)->select();
				foreach ($res_second as $key => $value) {
					foreach ($value as $key1 => $value1) {
						if($key1=="id"){
							$product_no=$product_no.$value1.",";
						}
					}
				}
				$product_no=substr($product_no,0,strlen($product_no)-1);
				$where['product_no']=array("IN",$product_no);
			}
			$rowNums = M('goods')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&option=".I("get.option")."&product_id=".I("get.product_id")."&product_name=".I("get.product_name")."&customer_id_en=".$customer_id_en);
			$result=M("goods")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="activity_id"){
						$first['id']=$value;
						$first_res=M("activity")->where($first)->find();
						$result[$i]['activity_title']=$first_res['activity_title'];
						if($first_res['activity_status']==2){
							$result[$i]['status']="进行中";
						}else if($first_res['activity_status']==3){
							$result[$i]['status']="已过期";
						}else if($first_res['activity_status']==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="product_no"){
						$second['id']=$value;
						$second_res=M()->table(DB_NAME.".weixin_commonshop_products")->where($second)->find();
						$result[$i]['product_name']=$second_res['name'];
					}
					if($key=="id"){
						$map_forward['product_id']=$value;
						$forward=M("forward")->field("sum(forwarding_num)")->where($map_forward)->find();
						if($forward['sum(forwarding_num)']){
							$result[$i]['count_forwarding']=$forward['sum(forwarding_num)'];
						}else{
							$result[$i]['count_forwarding']=0;
						}

						$map_hits['product_id']=$result[$i]['id'];
						$hits=M("hits")->field("sum(hits)")->where($map_hits)->find();
						if($hits){
							$result[$i]['count_click']=$hits['sum(hits)'];
						}else{
							$result[$i]['count_click']="0";
						}

						$select_action['activity_id']=$result[$i]['activity_id'];
						$select_action['product_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							$result[$i]['count_user']=count($result_action);
							$map_order['product_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();
						
							$map_action['product_id']=$value;
							$map_action['customer_id']=$customer_id;
							$map_action['isvalid']=1;
							$temp=M("action")->field("id,latest_price,product_id")->where($map_action)->select();
							$count_buy_price=0;
							for ($k=0; $k < count($temp); $k++) { 
								$map_goods['buy_price']=array("egt",$temp[$k]['latest_price']);
								$map_goods['id']=$temp[$k]['product_id'];
								$map_goods['customer_id']=$customer_id;
								$map_goods['isvalid']=1;
								$res_goods=M("goods")->where($map_goods)->find();
								if($res_goods){
									$count_buy_price=$count_buy_price+1;
								}
							}
							
							for ($j=0; $j < count($temp); $j++) { 
								$temp[$j]=$temp[$j]['id'];
							}
							$map_bargain['action_id']=array("IN",$temp);
							$map_bargain['customer_id']=$customer_id;
							$map_bargain['isvalid']=1;
							$count_bargain_user=M("bargain")->where($map_bargain)->count();
							/*帮砍人数*/
							$result[$i]['count_bargain_user']=$count_bargain_user;

							/*完成可购买价人数*/
							// $result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=$count_buy_price-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
						}else{
							$result[$i]['count_user']=0;
							$result[$i]['count_bargain_user']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
						}
					}
				}
			}
		}else{
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;

			$rowNums = M('goods')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en);
			$result=M("goods")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="activity_id"){
						$first['id']=$value;
						$first_res=M("activity")->where($first)->find();
						$result[$i]['activity_title']=$first_res['activity_title'];
						if($first_res['activity_status']==2){
							$result[$i]['status']="进行中";
						}else if($first_res['activity_status']==3){
							$result[$i]['status']="已过期";
						}else if($first_res['activity_status']==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="product_no"){
						$second['id']=$value;
						$second_res=M()->table(DB_NAME.".weixin_commonshop_products")->where($second)->find();
						$result[$i]['product_name']=$second_res['name'];
					}
					if($key=="id"){
						$map_forward['product_id']=$value;
						$forward=M("forward")->field("sum(forwarding_num)")->where($map_forward)->find();
						if($forward['sum(forwarding_num)']){
							$result[$i]['count_forwarding']=$forward['sum(forwarding_num)'];
						}else{
							$result[$i]['count_forwarding']=0;
						}
						$map_hits['product_id']=$result[$i]['id'];
						$hits=M("hits")->field("sum(hits)")->where($map_hits)->find();
						if($hits){
							$result[$i]['count_click']=$hits['sum(hits)'];
						}else{
							$result[$i]['count_click']="0";
						}

						// $map_access['activity_id']=$value;
						// $access=M("access")->field("sum(access_total)")->where($map_access)->find();
						// if($access){
						// 	$result[$i]['count_click']=$access['sum(access_total)'];
						// }else{
						// 	$result[$i]['count_click']="0";
						// }
						
						$select_action['activity_id']=$result[$i]['activity_id'];
						$select_action['product_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							$result[$i]['count_user']=count($result_action);
							$map_order['product_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();
						
							$map_action['product_id']=$value;
							$map_action['customer_id']=$customer_id;
							$map_action['isvalid']=1;
							$temp=M("action")->field("id,latest_price,product_id")->where($map_action)->select();
							$count_buy_price=0;
							for ($k=0; $k < count($temp); $k++) { 
								$map_goods['buy_price']=array("egt",$temp[$k]['latest_price']);
								$map_goods['id']=$temp[$k]['product_id'];
								$map_goods['customer_id']=$customer_id;
								$map_goods['isvalid']=1;
								$res_goods=M("goods")->where($map_goods)->find();
								if($res_goods){
									$count_buy_price=$count_buy_price+1;
								}
							}

							for ($j=0; $j < count($temp); $j++) { 
								$temp[$j]=$temp[$j]['id'];
							}
							$map_bargain['action_id']=array("IN",$temp);
							$map_bargain['customer_id']=$customer_id;
							$map_bargain['isvalid']=1;
							$count_bargain_user=M("bargain")->where($map_bargain)->count();
							/*帮砍人数*/
							$result[$i]['count_bargain_user']=$count_bargain_user;

							/*完成可购买价人数*/
							// $result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=$count_buy_price-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
						}else{
							$result[$i]['count_user']=0;
							$result[$i]['count_bargain_user']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
						}
					}
				}
			}
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$option=array(
			array("option_name"=>"未发布","option_id"=>1),
			array("option_name"=>"进行中","option_id"=>2),
			array("option_name"=>"已过期","option_id"=>3),
			array("option_name"=>"已终止","option_id"=>4),
			);
		$this->leftmenu = 2;
		$this->assign('option',$option);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
		$this->assign('bargain',$result);
		$this->display("bargain/product_summary");
	}

	/**/
	public function user_summary(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$map_action['customer_id']=$customer_id;
		$map_action['isvalid']=1;
		$before=M("action")->where($map_action)->select();
		for($i=0;$i<count($before);$i++){
			$before[$i]=$before[$i]['user_id'];
		}

		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		if(I("get.weixin_userid") || I("get.weixin_name") ){
			if($before){
				$where['id']=array("IN",$before);
			}else{
				$where['id']=0;
			}
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			// if(I("get.option")){
			// 	$where['activity_status']=I("get.option");
			// }
			if(I("get.weixin_userid")){
				$where['id']=array('like', '%'.I("get.weixin_userid").'%');
				$where['weixin_name']=array('neq',""); 
			}
			if(I("get.weixin_name")){
				$where['weixin_name']=array('like', '%'.I("get.weixin_name").'%');
			}
			$rowNums = M()->table(DB_NAME.".weixin_users")->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&weixin_userid=".I("get.weixin_userid")."&weixin_name=".I("get.weixin_name")."&customer_id_en=".$customer_id_en);
			$result=M()->table(DB_NAME.".weixin_users")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="parent_id"){
						if($value==-1){
							$result[$i]['parent_name']="无推荐人";
						}else{
							$map['id']=$value;
							$res=M()->table(DB_NAME.".weixin_users")->where($map)->find();
							$result[$i]['parent_name']=$res['weixin_name'];
						}
					}
					if($key=="id"){
						$select_action['user_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							$result[$i]['count_user']=count($result_action);
							$map_order['user_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();

							$count_buy_price=M("order")->where($map_order)->sum("money");
							if($count_buy_price){
								$result[$i]['count_buy_price']=$count_buy_price;
							}else{
								$result[$i]['count_buy_price']=number_format(0,2);
							}

							$map_bargain['user_id']=$value;
							$map_bargain['isvalid']=1;
							$map_bargain['customer_id']=$customer_id;
							$count_bargain=M("bargain")->where($map_bargain)->sum("bargain_price");
							if($count_bargain){
								$result[$i]['count_bargain']=$count_bargain;
							}else{
								$result[$i]['count_bargain']=number_format(0,2);
							}
							/*完成可购买价人数*/
							// $result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=count($result_action)-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
							$buy_ratio=$count_buy/count($result_action)*100;
							$result[$i]['buy_ratio']=number_format($buy_ratio,2)."%";
							$no_buy_ratio=(count($result_action)-$count_buy)/count($result_action)*100;
							$result[$i]['no_buy_ratio']=number_format($no_buy_ratio,2)."%";
						}else{
							$result[$i]['count_user']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
							$result[$i]['buy_ratio']="0%";
							$result[$i]['no_buy_ratio']="0%";
							$result[$i]['count_buy_price']=number_format(0,2);
							$result[$i]['count_bargain']=number_format(0,2);
						}
					}
				}
			}
		}else{
			if($before){
				$where['id']=array("IN",$before);
			}else{
				$where['id']=0;
			}
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			$where['weixin_name']=array('neq',""); 

			$rowNums = M()->table(DB_NAME.".weixin_users")->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en);
			$result=M()->table(DB_NAME.".weixin_users")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="parent_id"){
						if($value==-1){
							$result[$i]['parent_name']="无推荐人";
						}else{
							$map['id']=$value;
							$res=M()->table(DB_NAME.".weixin_users")->where($map)->find();
							$result[$i]['parent_name']=$res['weixin_name'];
						}
					}
					if($key=="id"){
						$select_action['user_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							$result[$i]['count_user']=count($result_action);
							$map_order['user_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();

							$count_buy_price=M("order")->where($map_order)->sum("money");
							if($count_buy_price){
								$result[$i]['count_buy_price']=$count_buy_price;
							}else{
								$result[$i]['count_buy_price']=number_format(0,2);
							}

							$map_bargain['user_id']=$value;
							$map_bargain['isvalid']=1;
							$map_bargain['customer_id']=$customer_id;
							$count_bargain=M("bargain")->where($map_bargain)->sum("bargain_price");
							if($count_bargain){
								$result[$i]['count_bargain']=$count_bargain;
							}else{
								$result[$i]['count_bargain']=number_format(0,2);
							}
							/*完成可购买价人数*/
							// $result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=count($result_action)-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
							$buy_ratio=$count_buy/count($result_action)*100;
							$result[$i]['buy_ratio']=number_format($buy_ratio,2)."%";
							$no_buy_ratio=(count($result_action)-$count_buy)/count($result_action)*100;
							$result[$i]['no_buy_ratio']=number_format($no_buy_ratio,2)."%";
						}else{
							$result[$i]['count_user']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
							$result[$i]['buy_ratio']="0%";
							$result[$i]['no_buy_ratio']="0%";
							$result[$i]['count_buy_price']=number_format(0,2);
							$result[$i]['count_bargain']=number_format(0,2);
						}
					}
				}
			}
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$option=array(
			array("option_name"=>"未发布","option_id"=>1),
			array("option_name"=>"进行中","option_id"=>2),
			array("option_name"=>"已过期","option_id"=>3),
			array("option_name"=>"已终止","option_id"=>4),
			);
		$this->leftmenu = 2;
		$this->assign('option',$option);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
		$this->assign('bargain',$result);
		$this->display("bargain/user_summary");
	}

	/*活动编辑*/
	public function edit_activity(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$activity_id=I("get.activity_id");
		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		if(I("get.option") || I("get.activity_start_time") || I("get.activity_end_time") || I("get.everything")){
			$where['a.customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['a.isvalid']=1;
			$where['a.activity_id']=$activity_id;
			
			if(I("get.option")){
				if(I("get.option")==1){ //未发布
					$where['c.activity_status']=1;
				}else if(I("get.option")==2){ //进行中
					$where['c.activity_status']=2;
				}else if(I("get.option")==3){ //已过期
					$where['c.activity_status']=3;
				}else{ // 4 已终止
					$where['c.activity_status']=4;
				}
			}
			if(I("get.activity_start_time")){
				$where['a.start_time']=array("egt",I("get.activity_start_time"));
			}
			if(I("get.activity_end_time")){
				$where['a.end_time']=array("elt",I("get.activity_end_time"));
			}
			if(I("get.everything")){
				$where['a.id|b.name']=array("like","%".I("get.everything")."%");
			}

			$first['id']=$activity_id;
			$activity=M("activity")->where($first)->find();
			$rowNums = M('goods')->alias('a')->join('kj_activity c on a.activity_id=c.id')->join(DB_NAME.'.weixin_commonshop_products b on a.product_no=b.id')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&option=".I("get.option")."&activity_start_time=".I("get.activity_start_time")."&activity_end_time=".I("get.activity_end_time")."&customer_id_en=".$customer_id_en."&everything=".I("get.everything"));
			$result=M("goods")->alias('a')->join('kj_activity c on a.activity_id=c.id')->join(DB_NAME.'.weixin_commonshop_products b on a.product_no=b.id')->where($where)->order('a.id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="product_no"){
						$select_simulation_product['id']=$value;
						$res=M()->table(DB_NAME.".weixin_commonshop_products")->where($select_simulation_product)->find();
						$result[$i]['product_name']=$res['name'];
						$result[$i]['product_pic']=$res['default_imgurl'];
						$result[$i]['price']=number_format($res['orgin_price'],2);
					}
					if($key=="id"){
						$select_action['activity_id']=$result[$i]['activity_id'];
						$select_action['product_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							// $result[$i]['count_user']=count($result_action);
							$map_order['product_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();
						
							$map_action['product_id']=$value;
							$map_action['customer_id']=$customer_id;
							$map_action['isvalid']=1;
							$temp=M("action")->field("id,latest_price,product_id")->where($map_action)->select();
							$count_buy_price=0;
							for ($k=0; $k < count($temp); $k++) { 
								$map_goods['buy_price']=array("egt",$temp[$k]['latest_price']);
								$map_goods['id']=$temp[$k]['product_id'];
								$map_goods['customer_id']=$customer_id;
								$map_goods['isvalid']=1;
								$res_goods=M("goods")->where($map_goods)->find();
								if($res_goods){
									$count_buy_price=$count_buy_price+1;
								}
							}
							
							// for ($j=0; $j < count($temp); $j++) { 
							// 	$temp[$j]=$temp[$j]['id'];
							// }
							// $map_bargain['action_id']=array("IN",$temp);
							// $map_bargain['customer_id']=$customer_id;
							// $map_bargain['isvalid']=1;
							// $count_bargain_user=M("bargain")->where($map_bargain)->count();
							// /*帮砍人数*/
							// $result[$i]['count_bargain_user']=$count_bargain_user;

							/*完成可购买价人数*/
							// $result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=$count_buy_price-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
							// /*点击量*/
							// $result[$i]['count_click']=0;
							// /*转发次数*/
							// $result[$i]['count_forwarding']=0;
						}else{
							// $result[$i]['count_user']=0;
							// $result[$i]['count_bargain_user']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
							// $result[$i]['count_click']=0;
							// $result[$i]['count_forwarding']=0;
						}
					}
					if($key=="activity_id"){
						$map_activity['id']=$value;
						$result_activity=M("activity")->where($map_activity)->find();
						if($result_activity['activity_status']==2){
							$result[$i]['status']="进行中";
						}else if($result_activity['activity_status']==3){
							$result[$i]['status']="已过期";
						}else if($result_activity['activity_status']==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
				}
			}
		}else{
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			$where['activity_id']=$activity_id;
			$first['id']=$activity_id;
			$activity=M("activity")->where($first)->find();

			$rowNums = M('goods')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en);
			$result=M("goods")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="product_no"){
						$select_simulation_product['id']=$value;
						$res=M()->table(DB_NAME.".weixin_commonshop_products")->where($select_simulation_product)->find();
						$result[$i]['product_name']=$res['name'];
						$result[$i]['product_pic']=$res['default_imgurl'];
						//$result[$i]['price']=number_format($res['orgin_price'],2);
					}
					if($key=="id"){
						$select_action['activity_id']=$result[$i]['activity_id'];
						$select_action['product_id']=$value;
						$result_action=M("action")->where($select_action)->select();
						if($result_action){
							/*报名人数*/
							// $result[$i]['count_user']=count($result_action);
							$map_order['product_id']=$value;
							$map_order['is_pay']=1;
							$map_order['isvalid']=1;
							$map_order['customer_id']=$customer_id;
							$count_buy=M("order")->where($map_order)->count();
						
							$map_action['product_id']=$value;
							$map_action['customer_id']=$customer_id;
							$map_action['isvalid']=1;
							$temp=M("action")->field("id,latest_price,product_id")->where($map_action)->select();
							$count_buy_price=0;
							for ($k=0; $k < count($temp); $k++) { 
								$map_goods['buy_price']=array("egt",$temp[$k]['latest_price']);
								$map_goods['id']=$temp[$k]['product_id'];
								$map_goods['customer_id']=$customer_id;
								$map_goods['isvalid']=1;
								$res_goods=M("goods")->where($map_goods)->find();
								if($res_goods){
									$count_buy_price=$count_buy_price+1;
								}
							}
							
							// for ($j=0; $j < count($temp); $j++) { 
							// 	$temp[$j]=$temp[$j]['id'];
							// }
							// $map_bargain['action_id']=array("IN",$temp);
							// $map_bargain['customer_id']=$customer_id;
							// $map_bargain['isvalid']=1;
							// $count_bargain_user=M("bargain")->where($map_bargain)->count();
							// /*帮砍人数*/
							// $result[$i]['count_bargain_user']=$count_bargain_user;

							/*完成可购买价人数*/
							// $result[$i]['count_buy_price']=$count_buy_price;
							/*成功下单数*/
							$result[$i]['count_buy']=$count_buy;
							/*未领取数*/
							$result[$i]['count_no_buy']=$count_buy_price-$count_buy;
							/*进行中数*/
							$result[$i]['count_activitying']=count($result_action)-$count_buy;
							// /*点击量*/
							// $result[$i]['count_click']=0;
							// /*转发次数*/
							// $result[$i]['count_forwarding']=0;
						}else{
							// $result[$i]['count_user']=0;
							// $result[$i]['count_bargain_user']=0;
							$result[$i]['count_buy']=0;
							$result[$i]['count_no_buy']=0;
							$result[$i]['count_activitying']=0;
							// $result[$i]['count_click']=0;
							// $result[$i]['count_forwarding']=0;
						}
					}
					if($key=="activity_id"){
						$map_activity['id']=$value;
						$result_activity=M("activity")->where($map_activity)->find();
						if($result_activity['activity_status']==2){
							$result[$i]['status']="进行中";
						}else if($result_activity['activity_status']==3){
							$result[$i]['status']="已过期";
						}else if($result_activity['activity_status']==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
				}
			}
		}
		if($activity['activity_status']==2){
			$activity_status="进行中";
		}else if($activity['activity_status']==3){
			$activity_status="已过期";
		}else if($activity['activity_status']==4){
			$activity_status="已终止";
		}else{
			$activity_status="未发布";
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$option=array(
			array("option_name"=>"未发布","option_id"=>1),
			array("option_name"=>"进行中","option_id"=>2),
			array("option_name"=>"已过期","option_id"=>3),
			array("option_name"=>"已终止","option_id"=>4),
			);
		$this->leftmenu = 2;
		$this->activity_id = $activity_id;
		$this->assign('option',$option);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
    	$this->assign('activity_id',$activity_id);
    	$this->assign('activity_status',$activity_status);
		$this->assign('bargain',$result);
		$this->display("bargain/edit_activity");
	}

	public function apply(){
		/*customer_id_en*/
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$product_id=I("get.product_id");
		$activity_id=I("get.activity_id");
		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		if(I("get.option") || I("get.activity_start_time") || I("get.activity_end_time") ){
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			$where['product_id']=$product_id;
			// if(I("get.option")){
			// 	if(I("get.option")==3 || I("get.option")==4){
			// 		$where['activity_status']=array("IN","3,4");
			// 	}else{
			// 		$where['activity_status']=I("get.option");
			// 	}
			// }
			$rowNums = M('action')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&option=".I("get.option")."&customer_id_en=".$customer_id_en."&product_id=".$product_id);
			$result=M("action")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="user_id"){
						$find_weixin_users['id']=$value;
						$res_weixin_users=M()->table(DB_NAME.".weixin_users")->where($find_weixin_users)->find();
						$result[$i]['weixin_name']=$res_weixin_users['weixin_name'];
					}
					if($key=="id"){
						$find_bargain['action_id']=$value;
						$bargain_num=M("bargain")->where($find_bargain)->count();
						if($bargain_num){
							$result[$i]['bargain_num']=$bargain_num;
						}else{
							$result[$i]['bargain_num']=0;
						}
						$bargain_price=M("bargain")->where($find_bargain)->sum("bargain_price");
						if($bargain_num){
							$result[$i]['bargain_price']=number_format($bargain_price,2);
						}else{
							$result[$i]['bargain_price']=number_format(0,2);
						}
					}
					if($key=="activity_id"){
						$find_activity['id']=$value;
						$result_activity=M("activity")->where($find_activity)->find();
						if($result_activity['activity_status']==2){
							$result[$i]['status']="进行中";
						}else if($result_activity['activity_status']==3){
							$result[$i]['status']="已过期";
						}else if($result_activity['activity_status']==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="product_id"){
						$find_goods['id']=$value;
						$result_goods=M("goods")->where($find_goods)->find();
						$find_commonshop['id']=$result_goods['product_no'];
						$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
						$result[$i]['product_name']=$result_commonshop['name'];
						$result[$i]['product_price']=number_format($result_commonshop['orgin_price'],2);
						$result[$i]['buy_price']=$result_goods['buy_price'];
						$result[$i]['minimum_price']=$result_goods['minimum_price'];
						$find_order['activity_id']=$result[$i]['activity_id'];
						$find_order['product_id']=$result[$i]['product_id'];
						$find_order['user_id']=$result[$i]['user_id'];
						$find_order['is_pay']=1;
						$find_order['isvalid']=1;
						$find_order['customer_id']=$customer_id;
						$result_order=M("order")->where($find_order)->find();
						if($result_order){
							$result[$i]['buy_status']="已领取";
							$result[$i]['pay_price']=number_format($result_order['money'],2);
						}else{
							if($result[$i]['latest_price']<=$result_goods['buy_price']){
								$result[$i]['buy_status']="符合条件，未领取";
							}else{
								$result[$i]['buy_status']="未达到条件";
							}
							$result[$i]['pay_price']=number_format(0,2);
						}
					}
				}
			}
		}else{
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			$where['product_id']=$product_id;

			$rowNums = M('action')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en."&product_id=".$product_id);
			$result=M("action")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="user_id"){
						$find_weixin_users['id']=$value;
						$res_weixin_users=M()->table(DB_NAME.".weixin_users")->where($find_weixin_users)->find();
						$result[$i]['weixin_name']=$res_weixin_users['weixin_name'];
					}
					if($key=="id"){
						$find_bargain['action_id']=$value;
						$bargain_num=M("bargain")->where($find_bargain)->count();
						if($bargain_num){
							$result[$i]['bargain_num']=$bargain_num;
						}else{
							$result[$i]['bargain_num']=0;
						}
						$bargain_price=M("bargain")->where($find_bargain)->sum("bargain_price");
						if($bargain_num){
							$result[$i]['bargain_price']=number_format($bargain_price,2);
						}else{
							$result[$i]['bargain_price']=number_format(0,2);
						}
					}
					if($key=="activity_id"){
						$find_activity['id']=$value;
						$result_activity=M("activity")->where($find_activity)->find();
						if($result_activity['activity_status']==2){
							$result[$i]['status']="进行中";
						}else if($result_activity['activity_status']==3){
							$result[$i]['status']="已过期";
						}else if($result_activity['activity_status']==4){
							$result[$i]['status']="已终止";
						}else{
							$result[$i]['status']="未发布";
						}
					}
					if($key=="product_id"){
						$find_goods['id']=$value;
						$result_goods=M("goods")->where($find_goods)->find();
						$find_commonshop['id']=$result_goods['product_no'];
						$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
						$result[$i]['product_name']=$result_commonshop['name'];
						$result[$i]['product_price']=number_format($result_commonshop['orgin_price'],2);
						$result[$i]['buy_price']=$result_goods['buy_price'];
						$result[$i]['minimum_price']=$result_goods['minimum_price'];
						$find_order['activity_id']=$result[$i]['activity_id'];
						$find_order['product_id']=$result[$i]['product_id'];
						$find_order['user_id']=$result[$i]['user_id'];
						$find_order['is_pay']=1;
						$find_order['isvalid']=1;
						$find_order['customer_id']=$customer_id;
						$result_order=M("order")->where($find_order)->find();
						if($result_order){
							$result[$i]['buy_status']="已领取";
							$result[$i]['pay_price']=number_format($result_order['money'],2);
						}else{
							if($result[$i]['latest_price']<=$result_goods['buy_price']){
								$result[$i]['buy_status']="符合条件，未领取";
							}else{
								$result[$i]['buy_status']="未达到条件";
							}
							$result[$i]['pay_price']=number_format(0,2);
						}
					}
				}
			}
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$option=array(
			array("option_name"=>"未发布","option_id"=>1),
			array("option_name"=>"进行中","option_id"=>2),
			array("option_name"=>"已过期","option_id"=>3),
			array("option_name"=>"已终止","option_id"=>4),
			);
		$this->leftmenu = 2;
		$this->assign('option',$option);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
    	$this->assign('product_id',$product_id);
    	$this->assign('activity_id',$activity_id);
		$this->assign('bargain',$result);
		$this->display("bargain/apply");
	}

	public function join(){
		/*customer_id_en*/
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$product_id=I("get.product_id");
		$activity_id=I("get.activity_id");
		if(I("get.action_id")){
			$action_id=I("get.action_id");
		}
		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		if(I("get.option") || I("get.activity_start_time") || I("get.activity_end_time") ){
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			$where['product_id']=$product_id;
			// if(I("get.option")){
			// 	if(I("get.option")==3 || I("get.option")==4){
			// 		$where['activity_status']=array("IN","3,4");
			// 	}else{
			// 		$where['activity_status']=I("get.option");
			// 	}
			// }
			$rowNums = M('action')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&option=".I("get.option")."&customer_id_en=".$customer_id_en."&product_id=".$product_id);
			$result=M("action")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			// for($i=0;$i<$count;$i++){
			// 	foreach ($result[$i] as $key => $value) {
			// 		if($key=="user_id"){
			// 			$find_weixin_users['id']=$value;
			// 			$res_weixin_users=M()->table(DB_NAME.".weixin_users")->where($find_weixin_users)->find();
			// 			$result[$i]['weixin_name']=$res_weixin_users['weixin_name'];
			// 		}
			// 		if($key=="id"){
			// 			$find_bargain['action_id']=$value;
			// 			$bargain_num=M("bargain")->where($find_bargain)->count();
			// 			if($bargain_num){
			// 				$result[$i]['bargain_num']=$bargain_num;
			// 			}else{
			// 				$result[$i]['bargain_num']=0;
			// 			}
			// 			$bargain_price=M("bargain")->where($find_bargain)->sum("bargain_price");
			// 			if($bargain_num){
			// 				$result[$i]['bargain_price']=number_format($bargain_price,2);
			// 			}else{
			// 				$result[$i]['bargain_price']=number_format(0,2);
			// 			}
			// 		}
			// 		if($key=="activity_id"){
			// 			$find_activity['id']=$value;
			// 			$result_activity=M("activity")->where($find_activity)->find();
			// 			if($result_activity['activity_status']==2){
			// 				$result[$i]['status']="进行中";
			// 			}else if($result_activity['activity_status']==3){
			// 				$result[$i]['status']="已过期";
			// 			}else if($result_activity['activity_status']==4){
			// 				$result[$i]['status']="已终止";
			// 			}else{
			// 				$result[$i]['status']="未发布";
			// 			}
			// 		}
			// 		if($key=="product_id"){
			// 			$find_goods['id']=$value;
			// 			$result_goods=M("goods")->where($find_goods)->find();
			// 			$find_commonshop['id']=$result_goods['product_no'];
			// 			$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
			// 			$result[$i]['product_name']=$result_commonshop['name'];
			// 			$result[$i]['product_price']=number_format($result_commonshop['orgin_price'],2);
			// 			$result[$i]['buy_price']=$result_goods['buy_price'];
			// 			$result[$i]['minimum_price']=$result_goods['minimum_price'];
			// 			$find_order['activity_id']=$result[$i]['activity_id'];
			// 			$find_order['product_id']=$result[$i]['product_id'];
			// 			$find_order['user_id']=$result[$i]['user_id'];
			// 			$find_order['is_pay']=1;
			// 			$find_order['isvalid']=1;
			// 			$find_order['customer_id']=$customer_id;
			// 			$result_order=M("order")->where($find_order)->find();
			// 			if($result_order){
			// 				$result[$i]['buy_status']="已领取";
			// 				$result[$i]['pay_price']=number_format($result_order['money'],2);
			// 			}else{
			// 				if($result[$i]['latest_price']<=$result_goods['buy_price']){
			// 					$result[$i]['buy_status']="符合条件，未领取";
			// 				}else{
			// 					$result[$i]['buy_status']="未达到条件";
			// 				}
			// 				$result[$i]['pay_price']=number_format(0,2);
			// 			}
			// 		}
			// 	}
			// }
		}else{
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			$where['product_id']=$product_id;
			if($action_id){
				$where['action_id']=$action_id;
				$k = M('bargain')->field('count(1) as count')->where($where)->group('user_id')->select();
			}else{
				$k = M('bargain')->field('count(1) as count')->where($where)->group('action_id,user_id')->select();
			}
			$rowNums=count($k);
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
    		if($action_id){
    			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en."&product_id=".$product_id."&action_id=".$action_id);
    			$result=M("bargain")->field('action_id,user_id,max(bargain_time),sum(bargain_price),count(user_id)')->where($where)->group('user_id')->order("max(bargain_time) desc")->limit($page->firstRow,$page->show)->select();
    		}else{
    			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en."&product_id=".$product_id);
    			$result=M("bargain")->field('action_id,user_id,max(bargain_time),sum(bargain_price),count(user_id)')->where($where)->group('action_id,user_id')->order("max(bargain_time) desc")->limit($page->firstRow,$page->show)->select();
    		}
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="sum(bargain_price)"){
						$result[$i]['bargain_price']=$result[$i]['sum(bargain_price)'];
					}
					if($key=="max(bargain_time)"){
						$result[$i]['bargain_time']=$result[$i]['max(bargain_time)'];
					}
					if($key=="user_id"){
						$weixin_users['id']=$value;
						$user=M()->table(DB_NAME.".weixin_users")->where($weixin_users)->find();
						$result[$i]['weixin_name']=$user['weixin_name'];
					}
				}
				$find_goods['id']=$product_id;
				$result_goods=M("goods")->where($find_goods)->find();
				$find_commonshop['id']=$result_goods['product_no'];
				$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
				$result[$i]['product_name']=$result_commonshop['name'];
				$result[$i]['bargain_num']=$result[$i]['count(user_id)'];

				$find_action['id']=$result[$i]['action_id'];
				$result_action=M("action")->where($find_action)->find();
				$weixin_users2['id']=$result_action['user_id'];
				$user2=M()->table(DB_NAME.".weixin_users")->where($weixin_users2)->find();
				$result[$i]['action_name']=$user2['weixin_name'];

				$find_activity['id']=$result_goods['activity_id'];
				$result_activity=M("activity")->where($find_activity)->find();
				if($result_activity['activity_status']==2){
					$result[$i]['status']="进行中";
				}else if($result_activity['activity_status']==3){
					$result[$i]['status']="已过期";
				}else if($result_activity['activity_status']==4){
					$result[$i]['status']="已终止";
				}else{
					$result[$i]['status']="未发布";
				}
			}
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$option=array(
			array("option_name"=>"未发布","option_id"=>1),
			array("option_name"=>"进行中","option_id"=>2),
			array("option_name"=>"已过期","option_id"=>3),
			array("option_name"=>"已终止","option_id"=>4),
			);
		$this->leftmenu = 2;
		$this->assign('option',$option);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
    	$this->assign('product_id',$product_id);
    	$this->assign('activity_id',$activity_id);
		$this->assign('bargain',$result);
		$this->display("bargain/join");
	}

	/*商品管理页（不分页）*/
	public function product(){//
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
        $where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
		$where['isvalid']=1;
		$where['activity_id']=I("get.activity_id");
		$result=M("goods")->where($where)->select();
		$where_1['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
		$where_1['isvalid']=1;
		$where_1['id']=I("get.activity_id");
		$activity=M("activity")->where($where_1)->find();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="product_no"){
					$select_simulation_product['id']=$value;
					$res=M()->table(DB_NAME.".weixin_commonshop_products")->where($select_simulation_product)->find();
					$result[$i]['product_name']=$res['name'];
					$result[$i]['product_pic']=$res['default_imgurl'];
				}
				if($key=="start_time"){
					$result[$i]['time']=(strtotime($result[$i]['end_time'])-strtotime($value))/3600;
				}
				$result[$i]['all_apply_number']=$activity['apply_number'];
				$result[$i]['all_play_number']=$activity['play_number'];
			}
		}
		$activity_id=I("get.activity_id");
		$this->assign('activity_id',$activity_id);
		$this->assign('count',$count);
		$this->assign('customer_id_en',$customer_id_en);
		$this->assign('bargain',$result);
		$this->display("bargain/product");
	}

	/*产品分类展示方法内（无限级）*/
	public function getList($customer_id,$pid=-1,&$result=array(),$spac=0){
        $spac=$spac+2;
        $where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
		$where['isvalid']=1;
		$where['parent_id']=$pid;
        $rs=M()->table(DB_NAME.".weixin_commonshop_types")->where($where)->select();
        foreach ($rs as $key => $value) {
        	$value['name']=str_repeat('&nbsp;&nbsp',$spac).$value['name'];
            $result[]=$value;
            $this->getList($customer_id,$value['id'],$result,$spac); 
        }             
        return $result;
    }

    /*产品分类展示方法外（无限级）*/
    public function displayList($customer_id){
        $rs=$this->getList($customer_id);
        $str="<select name='product_classify' class='product_classify' ><option value='' >--请选择--</option>";

        foreach ($rs as $key => $val) {
            $str.="<option value='{$val['id']}' >{$val['name']}</option>";
        }
        $str.="</select>";
        return $str;
    }

    /***/
    function my_sort($arrays,$sort_key,$sort_order=SORT_DESC,$sort_type=SORT_NUMERIC ){  
        if(is_array($arrays)){  
            foreach ($arrays as $array){  
                if(is_array($array)){  
                    $key_arrays[] = $array[$sort_key];  
                }else{  
                    return false;  
                }  
            }  
        }else{  
            return false;  
        } 
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);  
        return $arrays;  
    } 

    /*商城商品展示*/
	public function product_show(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		vendor('lib.Page','','.class.php');
		//$this->display("bargain/product_show");
		$show = isset($_GET['show'])?$_GET['show']:10;
		$activity_id=I("get.activity_id");
		$goods['activity_id']=$activity_id;
		$goods['isvalid']=1;
		$goods['customer_id']=$customer_id;
		$pros_id=M("goods")->field("product_no,product_pro")->where($goods)->select();
		for($i=0;$i<count($pros_id);$i++){
			if($pros_id[$i]['product_pro']!=-1){
				$pross_id[]=$pros_id[$i]['product_pro'];
			}else{
				$pron_id[]=$pros_id[$i]['product_no'];
			}
		}
		if($pross_id){
			$where2['id']=array("NOT IN",$pross_id);
		}
		if($pron_id){
			$where1['id']=array("NOT IN",$pron_id);
		}
		$where1['isout']=0;
		if(I("get.product_id") || I("get.product_name") || I("get.product_classify") || I("get.product_source") || I("get.product_label")){
			$where1['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where1['isvalid']=1;
			if(I("get.product_id")){
				$where1['id']=I("get.product_id");
				$this->assign('product_id',I("get.product_id"));
			}
			if(I("get.product_name")){
				$where1['name']=array("like","%".I("get.product_name")."%");
				$this->assign('product_name',I("get.product_name"));
			}
			if(I("get.product_classify")){
				$where1['type_ids']=array("like","%,".I("get.product_classify").",%");
			}
			if(I("get.product_source")){
				if(I("get.product_source")==-1){
					$where1['is_supply_id']=array("eq",-1);
				}else{
					$where1['is_supply_id']=array("neq",-1);
				}
			}
			if(I("get.product_label")){
				if(I("get.product_label")=="isnew"){
					$where1['isnew']=1;
				}
				if(I("get.product_label")=="ishot"){
					$where1['ishot']=1;
				}
				if(I("get.product_label")=="isvp"){
					$where1['isvp']=1;
				}
				if(I("get.product_label")=="issnapup"){
					$where1['issnapup']=1;
				}
				if(I("get.product_label")=="is_virtual"){
					$where1['is_virtual']=1;
				}
				if(I("get.product_label")=="is_currency"){
					$where1['is_currency']=1;
				}
				if(I("get.product_label")=="is_guess_you_like"){
					$where1['is_guess_you_like']=1;
				}
				if(I("get.product_label")=="is_free_shipping"){
					$where1['is_free_shipping']=1;
				}
				if(I("get.product_label")=="isscore"){
					$where1['isscore']=1;
				}
				if(I("get.product_label")=="islimit"){
					$where1['islimit']=1;
				}
				if(I("get.product_label")=="is_first_extend"){
					$where1['is_first_extend']=1;
				}
				if(I("get.product_label")=="is_privilege"){
					$where1['is_privilege']=1;
				}
				if(I("get.product_label")=="link_package"){
					$where1['link_package']=1;
				}
				if(I("get.product_label")=="is_mini_mshop"){
					$where1['is_mini_mshop']=1;
				}
			}

			$where1['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where1['isvalid']=1;
			$res_1=M()->table(DB_NAME.".weixin_commonshop_products")->field('id')->where($where1)->select();
			$temp_1=array_column($res_1,'id');
			if($temp_1){
				$where2['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
				$where2['product_id']=array("IN",$temp_1);
				$count_2=M()->table(DB_NAME.".weixin_commonshop_product_prices")->where($where2)->count();
				$res_2=M()->table(DB_NAME.".weixin_commonshop_product_prices")->field('id,product_id')->where($where2)->select();
				$temp_2=array_column($res_2,'product_id');
				if($temp_2){
					$temp_2=array_unique($temp_2);
					$temp_2_new=array_diff($temp_1,$temp_2);
					if($temp_2_new){
						$where1['id']=array("IN",$temp_2_new);
						$count_1=M()->table(DB_NAME.".weixin_commonshop_products")->where($where1)->count();
						$res_3=M()->table(DB_NAME.".weixin_commonshop_products")->field('id as product_id')->where($where1)->select();
					}
				}else{
					$where1['id']=array("IN",$temp_1);
					$count_1=M()->table(DB_NAME.".weixin_commonshop_products")->where($where1)->count();
					$res_3=M()->table(DB_NAME.".weixin_commonshop_products")->field('id as product_id')->where($where1)->select();
				}
			}
			
			if($res_2 && $res_3){
				$res=array_merge_recursive($res_3,$res_2);
				$count_1=count($res_3);
			}else if(!$res_2 && $res_3){
				$new_res_3=array_column($res_3,'product_id');
				$new_res_3_=array_diff($new_res_3,$pron_id);
				foreach($new_res_3_ as $a){
					$res[]['product_id'] = $a;
				}
				$count_1=count($res);
			}else if($res_2 && !$res_3){
				$res=$res_2;
				$count_1=0;
			}
			$res=$this->my_sort($res,'product_id');
			// var_dump($res);
			$rowNums=$count_1+$count_2;
			$show = $show<$rowNums?$show:20;
    		$page = new \Page($rowNums,$show);
    		$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&product_id=".I("get.product_id")."&product_name=".I("get.product_name")."&product_classify=".I("get.product_classify")."&product_source=".I("get.product_source")."&product_label=".I("get.product_label"));
    		if(($page->firstRow+$page->show)<$rowNums){
    			for($i=$page->firstRow;$i<$page->firstRow+$page->show;$i++){
	    			if(array_key_exists("id", $res[$i])){
	    				$map_1['id']=$res[$i]['id'];
	    				$res_prices=M()->table(DB_NAME.".weixin_commonshop_product_prices")->field('now_price,storenum,id as next_id')->where($map_1)->find();
	    				$map_1_['id']=$res[$i]['product_id'];
	    				$res_product=M()->table(DB_NAME.".weixin_commonshop_products")->
	    							field('id,default_imgurl,name,'.
	    								'sell_count,createtime,'.
	    								'isnew,ishot,isvp,'.
	    								'issnapup,is_virtual,is_currency,'.
	    								'is_guess_you_like,is_free_shipping,isscore,'.
	    								'islimit,is_first_extend,is_privilege,'.
	    								'link_package,is_mini_mshop')->
	    							where($map_1_)->find();
	    				// if(!strstr($list[$i]['default_imgurl'],'http')){
	    				// 	$list[$i]['default_imgurl']=$this->http.$list[$i]['default_imgurl'];
	    				// }
	    				$list[]=array_merge_recursive($res_prices,$res_product);
	    			}else{
	    				$map_1_['id']=$res[$i]['product_id'];
	    				$list[]=M()->table(DB_NAME.".weixin_commonshop_products")->
	    						field('id,default_imgurl,name,'.
	    							'now_price,sell_count,storenum,'.
	    							'createtime,-1 as next_id,'.
	    							'isnew,ishot,isvp,'.
	    							'issnapup,is_virtual,is_currency,'.
	    							'is_guess_you_like,is_free_shipping,isscore,'.
	    							'islimit,is_first_extend,is_privilege,'.
	    							'link_package,is_mini_mshop')->
	    						where($map_1_)->find();
	    			}
				}
    		}else{
    			for($i=$page->firstRow;$i<$rowNums;$i++){
	    			if(array_key_exists("id", $res[$i])){
	    				$map_1['id']=$res[$i]['id'];
	    				$res_prices=M()->table(DB_NAME.".weixin_commonshop_product_prices")->field('now_price,storenum,id as next_id')->where($map_1)->find();
	    				$map_1_['id']=$res[$i]['product_id'];
	    				$res_product=M()->table(DB_NAME.".weixin_commonshop_products")->
	    							field('id,default_imgurl,name,'.
	    								'sell_count,createtime,'.
	    								'isnew,ishot,isvp,'.
	    								'issnapup,is_virtual,is_currency,'.
	    								'is_guess_you_like,is_free_shipping,isscore,'.
	    								'islimit,is_first_extend,is_privilege,'.
	    								'link_package,is_mini_mshop')->
	    							where($map_1_)->find();
	    				$list[]=array_merge_recursive($res_prices,$res_product);
	    			}else{
	    				$map_1_['id']=$res[$i]['product_id'];
	    				// $list[]=M()->table(DB_NAME.".weixin_commonshop_products")->
	    				// 		field('id,default_imgurl,name,'.
	    				// 			.'now_price,sell_count,storenum,createtime,-1 as next_id')->
	    				// 		where($map_1_)->
	    				// 		find();
	    				$list[]=M()->table(DB_NAME.".weixin_commonshop_products")->
	    						field('id,default_imgurl,name,'.
	    							'now_price,sell_count,storenum,'.
	    							'createtime,-1 as next_id,'.
	    							'isnew,ishot,isvp,'.
	    							'issnapup,is_virtual,is_currency,'.
	    							'is_guess_you_like,is_free_shipping,isscore,'.
	    							'islimit,is_first_extend,is_privilege,'.
	    							'link_package,is_mini_mshop')->
	    						where($map_1_)->find();
	    			}
				}
    		}
			$result=$list;
		}else{
			
			$where1['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where1['isvalid']=1;
			$res_1=M()->table(DB_NAME.".weixin_commonshop_products")->field('id')->where($where1)->select();
			$temp_1=array_column($res_1,'id');
			if($temp_1){
				$where2['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
				$where2['product_id']=array("IN",$temp_1);
				$count_2=M()->table(DB_NAME.".weixin_commonshop_product_prices")->where($where2)->count();
				$res_2=M()->table(DB_NAME.".weixin_commonshop_product_prices")->field('id,product_id')->where($where2)->select();
				$temp_2=array_column($res_2,'product_id');
				if($temp_2){
					$temp_2=array_unique($temp_2);
					$temp_2_new=array_diff($temp_1,$temp_2);
					if($temp_2_new){
						$where1['id']=array("IN",$temp_2_new);
						$count_1=M()->table(DB_NAME.".weixin_commonshop_products")->where($where1)->count();
						$res_3=M()->table(DB_NAME.".weixin_commonshop_products")->field('id as product_id')->where($where1)->select();
					}
				}else{
					$where1['id']=array("IN",$temp_1);
					$count_1=M()->table(DB_NAME.".weixin_commonshop_products")->where($where1)->count();
					$res_3=M()->table(DB_NAME.".weixin_commonshop_products")->field('id as product_id')->where($where1)->select();
				}
			}

			// var_dump($res_2);
			// var_dump($res_3);
			
			
			if($res_2 && $res_3){
				$res=array_merge_recursive($res_3,$res_2);
				$count_1=count($res_3);
			}else if(!$res_2 && $res_3){
				$new_res_3=array_column($res_3,'product_id');
				$new_res_3_=array_diff($new_res_3,$pron_id);
				foreach($new_res_3_ as $a){
					$res[]['product_id'] = $a;
				}
				$count_1=count($res);
			}else if($res_2 && !$res_3){
				$res=$res_2;
				$count_1=0;
			}
			$res=$this->my_sort($res,'product_id');
			// var_dump($res);
			$rowNums=$count_1+$count_2;
			$show = $show<$rowNums?$show:20;
    		$page = new \Page($rowNums,$show);
    		$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en);
    		if(($page->firstRow+$page->show)<$rowNums){
    			for($i=$page->firstRow;$i<$page->firstRow+$page->show;$i++){
	    			if(array_key_exists("id", $res[$i])){
	    				$map_1['id']=$res[$i]['id'];
	    				$res_prices=M()->table(DB_NAME.".weixin_commonshop_product_prices")->field('now_price,storenum,id as next_id')->where($map_1)->find();
	    				$map_1_['id']=$res[$i]['product_id'];
	    				$res_product=M()->table(DB_NAME.".weixin_commonshop_products")->
	    							field('id,default_imgurl,name,'.
	    								'sell_count,createtime,'.
	    								'isnew,ishot,isvp,'.
	    								'issnapup,is_virtual,is_currency,'.
	    								'is_guess_you_like,is_free_shipping,isscore,'.
	    								'islimit,is_first_extend,is_privilege,'.
	    								'link_package,is_mini_mshop')->
	    							where($map_1_)->find();
	    				// if(!strstr($list[$i]['default_imgurl'],'http')){
	    				// 	$list[$i]['default_imgurl']=$this->http.$list[$i]['default_imgurl'];
	    				// }
	    				$list[]=array_merge_recursive($res_prices,$res_product);
	    			}else{
	    				$map_1_['id']=$res[$i]['product_id'];
	    				$list[]=M()->table(DB_NAME.".weixin_commonshop_products")->
	    						field('id,default_imgurl,name,'.
	    							'now_price,sell_count,storenum,'.
	    							'createtime,-1 as next_id,'.
	    							'isnew,ishot,isvp,'.
	    							'issnapup,is_virtual,is_currency,'.
	    							'is_guess_you_like,is_free_shipping,isscore,'.
	    							'islimit,is_first_extend,is_privilege,'.
	    							'link_package,is_mini_mshop')->
	    						where($map_1_)->find();
	    			}
				}
    		}else{
    			for($i=$page->firstRow;$i<$rowNums;$i++){
	    			if(array_key_exists("id", $res[$i])){
	    				$map_1['id']=$res[$i]['id'];
	    				$res_prices=M()->table(DB_NAME.".weixin_commonshop_product_prices")->field('now_price,storenum,id as next_id')->where($map_1)->find();
	    				$map_1_['id']=$res[$i]['product_id'];
	    				$res_product=M()->table(DB_NAME.".weixin_commonshop_products")->
	    							field('id,default_imgurl,name,'.
	    								'sell_count,createtime,'.
	    								'isnew,ishot,isvp,'.
	    								'issnapup,is_virtual,is_currency,'.
	    								'is_guess_you_like,is_free_shipping,isscore,'.
	    								'islimit,is_first_extend,is_privilege,'.
	    								'link_package,is_mini_mshop')->
	    							where($map_1_)->find();
	    				$list[]=array_merge_recursive($res_prices,$res_product);
	    			}else{
	    				$map_1_['id']=$res[$i]['product_id'];
	    				// $list[]=M()->table(DB_NAME.".weixin_commonshop_products")->
	    				// 		field('id,default_imgurl,name,'.
	    				// 			.'now_price,sell_count,storenum,createtime,-1 as next_id')->
	    				// 		where($map_1_)->
	    				// 		find();
	    				$list[]=M()->table(DB_NAME.".weixin_commonshop_products")->
	    						field('id,default_imgurl,name,'.
	    							'now_price,sell_count,storenum,'.
	    							'createtime,-1 as next_id,'.
	    							'isnew,ishot,isvp,'.
	    							'issnapup,is_virtual,is_currency,'.
	    							'is_guess_you_like,is_free_shipping,isscore,'.
	    							'islimit,is_first_extend,is_privilege,'.
	    							'link_package,is_mini_mshop')->
	    						where($map_1_)->find();
	    			}
				}
    		}
			$result=$list;
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		/*产品分类*/
		$product_classify=$this->displayList($customer_id);
		/*产品分类*/
		/*产品来源*/
		$product_source=array(
			array("option_name"=>"平台","option_id"=>1),
			array("option_name"=>"合作商","option_id"=>-1),
			);
		/*产品来源*/
		/*产品标签*/
		$product_label=array(
			array("option_name"=>"新品","option_id"=>"isnew"),
			array("option_name"=>"热品","option_id"=>"ishot"),
			array("option_name"=>"vp品","option_id"=>"isvp"),
			array("option_name"=>"抢购","option_id"=>"issnapup"),
			array("option_name"=>"虚拟产品","option_id"=>"is_virtual"),
			array("option_name"=>"购物币产品","option_id"=>"is_currency"),
			array("option_name"=>"猜你喜欢产品","option_id"=>"is_guess_you_like"),
			array("option_name"=>"包邮","option_id"=>"is_free_shipping"),
			array("option_name"=>"积分专区","option_id"=>"isscore"),
			array("option_name"=>"限购","option_id"=>"islimit"),
			array("option_name"=>"首次推广奖励","option_id"=>"is_first_extend"),
			array("option_name"=>"特权专区","option_id"=>"is_privilege"),
			array("option_name"=>"关联礼包","option_id"=>"link_package"),
			array("option_name"=>"微信小程序","option_id"=>"is_mini_mshop"),
			);
		/*产品标签*/
		$activity_id=I("get.activity_id");
		$this->assign('activity_id',$activity_id);
		$this->leftmenu = 2;
		$this->activity_id = $activity_id;
		$this->assign('product_classify',$product_classify);
		$this->assign('product_source',$product_source);
		$this->assign('product_label',$product_label);
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
		$this->assign('bargain',$result);
		$this->display("bargain/product_show");
	}

	/*添加活动商品*/
	public function add_product(){
		if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$activity_global['id']=I("get.activity_id");
		$result_activity_global=M("activity")->where($activity_global)->find();
		$data['activity_id']=$result_activity_global['id'];
		$data['apply_number']=$result_activity_global['apply_number_single'];
		$data['play_number']=$result_activity_global['play_number_single'];
		$data['create_time']=date("Y-m-d H:i:s");
		$data['start_time']=$result_activity_global['activity_start_time'];
		$data['end_time']=$result_activity_global['activity_end_time'];
		$data['isvalid']=1;
		$data['customer_id']=$customer_id;
		if(is_array(I("get.product_id"))){
			/*批量添加活动商品*/
			//$where['id']=array('IN',I("get.product_id"));
			//$res=M()->table(DB_NAME.".weixin_commonshop_products")->where($where)->select();
			$product_id=I("get.product_id");
			$product_pro=I("get.pros_id");
			for($i=0;$i<count($product_id);$i++){
				$where1['id']=$product_id[$i];
				$res=M()->table(DB_NAME.".weixin_commonshop_products")->where($where1)->find();
				$data['product_no']=$product_id[$i];
				$where['id']=$product_pro[$i];
				$pros=M()->table(DB_NAME.".weixin_commonshop_product_prices")->where($where)->find();
				if($pros){
					$data['price']=$pros['now_price'];
					$data['product_pro']=$pros['id'];
				}else{
					$data['price']=$res['now_price'];
					$data['product_pro']=-1;
				}
				//unset($pros);
				//$data['product_pro']=$product_pro[$i];
				$data['product_num']=0;
				$data['inventory']=0;
				//$data['price']=$product_pro[$i];
				$data['buy_price']=0;
				$data['minimum_price']=0;
				$result=M("goods")->data($data)->add();
				if(!$result){
					$invoke['error']=1002;
					$invoke['data']="添加失败！";
					$this->ajaxReturn($invoke);
				}
			}
			/*批量添加活动商品*/
		}else{
			/*单个添加活动商品*/
			if(I("get.pros_id")){
				$where['id']=I("get.pros_id");
				$pros=M()->table(DB_NAME.".weixin_commonshop_product_prices")->where($where)->find();
			}
			$where['id']=I("get.product_id");
			$res=M()->table(DB_NAME.".weixin_commonshop_products")->where($where)->find();
			$second['product_no']=$res['id'];
			//$second['product_pro']=$pros['id'];
			$second['activity_id']=$result_activity_global['id'];
			$second['isvalid']=1;
			$second['customer_id']=$customer_id;
			$second_do=M("goods")->where($second)->find();
			// if($second_do){
			// 	$invoke['error']=1002;
			// 	$invoke['data']="商品".$res['name']."已存在！";
			// 	$this->ajaxReturn($invoke);
			// }
			$data['product_no']=$res['id'];
			//$data['product_origin']=$res['id'];
			$data['product_num']=0;
			$data['inventory']=0;
			if($pros){
				$data['price']=$pros['now_price'];
				$data['product_pro']=$pros['id'];
			}else{
				$data['price']=$res['now_price'];
				$data['product_pro']=-1;
			}
			$data['buy_price']=0;
			$data['minimum_price']=0;
			$result=M("goods")->data($data)->add();
			/*单个添加活动商品*/
		}
		if($result){
			$invoke['error']=1000;
			$invoke['data']="添加成功！";
		}
		$this->ajaxReturn($invoke);
	}

	/*
	*
	*/
	public function add_product_most(){

	}

	/*保存商品设置*/
	public function save_product(){//
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$input=I('get.','','trim');
		for($i=1;$i<=I("get.count");$i++){
			if(empty($input['buy_price'.$i])){
				$this->error('缺少可购买价'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			}
			// if(empty($input['minimum_price'.$i])){
			// 	$this->error('缺少minimum_price'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			// }
			if(empty($input['product_num'.$i])){
				$this->error('缺少商品数量'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			}
			// if(empty($input['time'.$i])){
			// 	$this->error('缺少活动时长'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			// }
			// if(empty($input['apply_number'.$i])){
			// 	$this->error('缺少报名者报名次数'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			// }
			// if(empty($input['play_number'.$i])){
			// 	$this->error('缺少参与者砍价次数'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			// }
		}
		$where['customer_id']=$customer_id;
		$where['isvalid']=1;
		$where['activity_id']=$input['activity_id'];
		$first_where['customer_id']=$customer_id;
		$first_where['isvalid']=1;
		$first_where['id']=$input['activity_id'];
		for($i=1;$i<=$input['count'];$i++){
			$first=M("activity")->where($first_where)->find();
			$where['id']=$input['id'.$i];
			$temp=M("goods")->where($where)->find();
			if($input['price'.$i]<$input['buy_price'.$i]){
				$data['buy_price']=0;
			}else{
				$data['buy_price']=$input['buy_price'.$i];
			}
			if($input['price'.$i]<$input['minimum_price'.$i]){
				$data['minimum_price']=0;
			}else{
				$data['minimum_price']=$input['minimum_price'.$i];
			}
			if($input['buy_price'.$i]<$input['minimum_price'.$i]){
				$data['minimum_price']=0;
			}else{
				$data['minimum_price']=$input['minimum_price'.$i];
			}
			//$data['buy_price']=$input['buy_price'.$i];
			//$data['minimum_price']=$input['minimum_price'.$i];
			$data['product_num']=$input['product_num'.$i];
			$data['inventory']=$input['product_num'.$i];
			$data['apply_number']=$input['apply_number'.$i];
			$data['play_number']=$input['play_number'.$i];
			//$data['end_time']=date('Y-m-d H:i:s',strtotime($temp['start_time'])+$input['time'.$i]*3600);
			/*限制商品活动时长不可高于活动总时长*/
			if(strtotime($data['end_time'])>strtotime($first['activity_end_time'])){
				$this->error('商品活动时长不可高于活动总时长',"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			}
			/*限制商品活动时长不可高于活动总时长*/
			$res=M("goods")->where($where)->save($data);
		}
		for($i=1;$i<=$input['count'];$i++){
			if($input['price'.$i]<$input['buy_price'.$i]){
				$this->error('可购买价不可大于市场价'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			}
			if($input['price'.$i]<$input['minimum_price'.$i]){
				$this->error('最低价不可大于市场价'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			}
			if($input['buy_price'.$i]<$input['minimum_price'.$i]){
				$this->error('最低价不可大于可购买价'.$i,"product?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
			}
		}
		$this->success('修改成功',"edit_activity?customer_id_en=".$customer_id_en."&activity_id=".I("get.activity_id")."&class=80");
	}

	/*添加活动商品*/
	public function delete_goods(){
		if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$where['customer_id']=$customer_id;
		if(is_array(I("get.product_id"))){
			/*批量删除活动商品*/
			$where['id']=array('IN',I("get.product_id"));
			$data['isvalid']=0;
			$result=M("goods")->where($where)->save($data);
			/*批量删除活动商品*/
		}else{
			/*单个删除活动商品*/
			$where['id']=I("get.product_id");
			$data['isvalid']=0;
			$result=M("goods")->where($where)->save($data);
			/*单个删除活动商品*/
		}
		if($result){
			$invoke['error']=1000;
			$invoke['data']="删除成功！";
		}else{
			$invoke['error']=1002;
			$invoke['data']="删除不成功！";
		}
		$this->ajaxReturn($invoke);
	}

	public function order(){
		/*customer_id_en*/
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		vendor('lib.Page','','.class.php');
		$show = isset($_GET['show'])?$_GET['show']:10;
		//activity_id=1&batchcode=2&phone=3&pay_status=0&goods_man=4&user_name=5&time_select=1&start_time=6&end_time=7
		if(I("get.activity_id") || I("get.batchcode") || I("get.phone") || I("get.pay_status") || I("get.goods_man") || I("get.user_name") || I("get.time_select") || I("get.start_time") || I("get.end_time") ){
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;
			if(I("get.activity_id")){
				$where['activity_id']=array("like","%".I("get.activity_id")."%");
			}
			if(I("get.batchcode")){
				$where['batchcode']=array("like","%".I("get.batchcode")."%");
			}
			if(I("get.phone")){
				$where['user_tel']=array("like","%".I("get.phone")."%");
			}
			if(I("get.pay_status")){
				$where['status']=I("get.pay_status");
			}
			if(I("get.goods_man")){
				$where['user_name']=array("like","%".I("get.goods_man")."%");
			}
			if(I("get.user_name")){
				$where['user_name']=array("like","%".I("get.goods_man")."%");
			}
			if(I("get.time_select")){
				if(I("get.time_select")==1){
					if(I("get.start_time")){
						$where['order_time']=array("egt",I("get.start_time"));
					}
					if(I("get.end_time")){
						$where['order_time']=array("elt",I("get.end_time"));
					}
				}else if(I("get.time_select")==2){
					if(I("get.start_time")){
						$where['pay_time']=array("egt",I("get.start_time"));
					}
					if(I("get.end_time")){
						$where['pay_time']=array("elt",I("get.end_time"));
					}
				}
			}
			$rowNums = M('order')->where($where)->count();
			$show = $show<$rowNums?$show:10;
			$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&activity_id=".I("get.activity_id")."&batchcode=".I("get.batchcode")."&phone=".I("get.phone")."&pay_status=".I("get.pay_status")."&goods_man=".I("get.goods_man")."&user_name=".I("get.user_name")."&time_select=".I("get.time_select")."&start_time=".I("get.start_time")."&end_time=".I("get.end_time")."&customer_id_en=".$customer_id_en);
			$result=M("order")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="delivery_status"){
						if($value==0){
							$result[$i]['delivery']="未发货";
						}else if($value==1){
							$result[$i]['delivery']="已发货";
						}else if($value==2){
							$result[$i]['delivery']="已签收";
						}
					}
					if($key=="product_id"){
						$find_goods['id']=$value;
						$res_goods=M("goods")->where($find_goods)->find();
						$find_common_goods['id']=$res_goods['product_no'];
						$res_common_goods=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_common_goods)->find();
						$result[$i]['product_pic']='https://'.$_SERVER['HTTP_HOST'].$res_common_goods['default_imgurl'];
						$result[$i]['product_name']=$res_common_goods['name'];
						$result[$i]['foreign_mark']=$res_common_goods['foreign_mark'];
						$result[$i]['bargain_price']=number_format($res_common_goods['orgin_price']-$result[$i]['money'],2);
					}
					if($key=="user_id"){
						$find_weixin_users['id']=$value;
						$res_weixin_users=M()->table(DB_NAME.".weixin_users")->where($find_weixin_users)->find();
						if($res_weixin_users['parent_id']==-1){
							$result[$i]['parent_weixin_name']='无';
						}else{
							$find_parent_weixin['id']=$res_weixin_users['parent_id'];
							$parent_weixin=M()->table(DB_NAME.".weixin_users")->where($find_parent_weixin)->find();
							$result[$i]['parent_weixin_name']=$parent_weixin['weixin_name'];
						}
						$result[$i]['weixin_name']=$res_weixin_users['weixin_name'];
						$find_weixin_address['user_id']=$value;
						$res_weixin_address=M()->table(DB_NAME.".weixin_commonshop_addresses")->where($find_weixin_address)->find();
						$result[$i]['phone']=$res_weixin_address['phone'];
						$result[$i]['name']=$res_weixin_address['name'];
					}
					if($key=="pay_style"){
						if($value==1){
							$result[$i]['payments']="微信支付";
						}else if($value==2){
							$result[$i]['payments']="零钱支付";
						}else if($value==3){
							$result[$i]['payments']="支付宝支付";
						}
					}
					if($key=="batchcode"){
						$find_order_status['batchcode']=$value;
						$res_order_status=M()->table(DB_NAME.".weixin_commonshop_orders")->where($find_order_status)->find();
						if($res_order_status['paystatus']==1){
							$result[$i]['pay_batchcode']=$res_order_status['pay_batchcode'];
							$result[$i]['paytime']=$res_order_status['paytime'];
							$result[$i]['paystyle']=$res_order_status['paystyle'];
							if($res_order_status['sendstatus']!=6){
								$result[$i]['order_status']="已支付";
							}else{
								$result[$i]['order_status']="已退款";
							}
							if($result[$i]['sendstatus']==0){
								$result[$i]['delivery']="未发货";
							}else if($result[$i]['sendstatus']==1){
								$result[$i]['delivery']="已发货";
							}else if($result[$i]['sendstatus']==2){
								$result[$i]['delivery']="已收货";
							}else if($result[$i]['sendstatus']==3){
								$result[$i]['delivery']="申请退货";
							}else if($result[$i]['sendstatus']==4){
								$result[$i]['delivery']="已退货";
							}else{
								$result[$i]['delivery']="已退货";
							}
						}else{
							$now=time();
							$can_pay=$now-strtotime($res_order_status['createtime']);
							if($can_pay>1800){
								$result[$i]['order_status']="无效订单";
							}else{
								$result[$i]['order_status']="待支付";
							}
						}
					}
				}
			}
		}else{
			$where['customer_id']=$customer_id;//decode_wsy(I("get.customer_id_en"));
			$where['isvalid']=1;

			$rowNums = M('order')->where($where)->count();
    		$show = $show<$rowNums?$show:10;
    		$page = new \Page($rowNums,$show);
			$strPage = $page->pageInfo("&customer_id_en=".$customer_id_en);
			$result=M("order")->where($where)->order('id desc')->limit($page->firstRow,$page->show)->select();
			$count=count($result);
			for($i=0;$i<$count;$i++){
				foreach ($result[$i] as $key => $value) {
					if($key=="delivery_status"){
						if($value==0){
							$result[$i]['delivery']="未发货";
						}else if($value==1){
							$result[$i]['delivery']="已发货";
						}else if($value==2){
							$result[$i]['delivery']="已签收";
						}
					}
					if($key=="product_id"){
						$find_goods['id']=$value;
						$res_goods=M("goods")->where($find_goods)->find();
						$find_common_goods['id']=$res_goods['product_no'];
						$res_common_goods=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_common_goods)->find();
						$result[$i]['product_pic']='https://'.$_SERVER['HTTP_HOST'].$res_common_goods['default_imgurl'];
						$result[$i]['product_name']=$res_common_goods['name'];
						$result[$i]['foreign_mark']=$res_common_goods['foreign_mark'];
						$result[$i]['bargain_price']=number_format($res_common_goods['orgin_price']-$result[$i]['money'],2);
					}
					if($key=="user_id"){
						$find_weixin_users['id']=$value;
						$res_weixin_users=M()->table(DB_NAME.".weixin_users")->where($find_weixin_users)->find();
						if($res_weixin_users['parent_id']==-1){
							$result[$i]['parent_weixin_name']='无';
						}else{
							$find_parent_weixin['id']=$res_weixin_users['parent_id'];
							$parent_weixin=M()->table(DB_NAME.".weixin_users")->where($find_parent_weixin)->find();
							$result[$i]['parent_weixin_name']=$parent_weixin['weixin_name'];
						}
						$result[$i]['weixin_name']=$res_weixin_users['weixin_name'];
						$find_weixin_address['user_id']=$value;
						$res_weixin_address=M()->table(DB_NAME.".weixin_commonshop_addresses")->where($find_weixin_address)->find();
						$result[$i]['phone']=$res_weixin_address['phone'];
						$result[$i]['name']=$res_weixin_address['name'];
					}
					if($key=="pay_style"){
						if($value==1){
							$result[$i]['payments']="微信支付";
						}else if($value==2){
							$result[$i]['payments']="零钱支付";
						}else if($value==3){
							$result[$i]['payments']="支付宝支付";
						}
					}
					if($key=="batchcode"){
						$find_order_status['batchcode']=$value;
						$res_order_status=M()->table(DB_NAME.".weixin_commonshop_orders")->where($find_order_status)->find();
						if($res_order_status['paystatus']==1){
							$result[$i]['pay_batchcode']=$res_order_status['pay_batchcode'];
							$result[$i]['paytime']=$res_order_status['paytime'];
							$result[$i]['paystyle']=$res_order_status['paystyle'];
							if($res_order_status['sendstatus']!=6){
								$result[$i]['order_status']="已支付";
							}else{
								$result[$i]['order_status']="已退款";
							}
							if($result[$i]['sendstatus']==0){
								$result[$i]['delivery']="未发货";
							}else if($result[$i]['sendstatus']==1){
								$result[$i]['delivery']="已发货";
							}else if($result[$i]['sendstatus']==2){
								$result[$i]['delivery']="已收货";
							}else if($result[$i]['sendstatus']==3){
								$result[$i]['delivery']="申请退货";
							}else if($result[$i]['sendstatus']==4){
								$result[$i]['delivery']="已退货";
							}else{
								$result[$i]['delivery']="已退货";
							}
						}else{
							$now=time();
							$can_pay=$now-strtotime($res_order_status['createtime']);
							if($can_pay>1800){
								$result[$i]['order_status']="无效订单";
							}else{
								$result[$i]['order_status']="待支付";
							}
						}
					}
				}
			}
		}
		$rowNums = $page->rowNums;
		$currentPage = $page->page;
		$pageNums = $page->pageNums;
		$this->leftmenu = 2;
		$this->rowNums = $rowNums;
    	$this->currentPage = $currentPage;  
    	$this->pageNums = $pageNums;       
    	$this->strPage = $strPage;
    	$this->assign('customer_id_en',$customer_id_en);
    	$this->assign('customer_id',$customer_id);
		$this->assign('bargain',$result);
		$this->display("bargain/order");
	}

	public function save_logistics(){
		if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$where['batchcode']=I("get.batchcode");
		$where['customer_id']=$customer_id;
		$temp=M("logistics")->where($where)->find();
		if($temp){
			$save['customer_remark']=I("get.remark");
			$res=M("logistics")->where($where)->save($save);
			if($res){
				$invoke['error']=1000;
				$invoke['data']="修改物流备注成功";
				$this->ajaxReturn($invoke);
			}else{
				$invoke['error']=1002;
				$invoke['data']="修改物流备注失败";
				$this->ajaxReturn($invoke);
			}
		}else{
			$where['customer_remark']=I("get.remark");
			$where['status']=1;
			$where['isvalid']=1;
			$res=M("logistics")->add($where);
			if($res){
				$invoke['error']=1000;
				$invoke['data']="添加物流备注成功";
				$this->ajaxReturn($invoke);
			}else{
				$invoke['error']=1002;
				$invoke['data']="添加物流备注失败";
				$this->ajaxReturn($invoke);
			}
		}
	}

	public function courier(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$where['batchcode']=I("get.batchcode");
		$where['customer_id']=$customer_id;
		$temp=M("logistics")->where($where)->find();
		$order=M("order")->where($where)->find();
		$find_goods['id']=$order['product_id'];
		$res_goods=M("goods")->where($find_goods)->find();
		$find_common_goods['id']=$res_goods['product_no'];
		$res_common_goods=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_common_goods)->find();
		$product_name=$res_common_goods['name'];
		$user_id=$order['user_id'];

		$add['batchcode']=I("get.batchcode");
		$add['time']=date("Y-m-d H:i:s");
		$add['operate']=3;
		$add['operate_detail']="平台发货-".I("get.logistics_company")."快递";
		$add['isvalid']=1;
		$add['customer_id']=$customer_id;
		$content='亲,您的商品\n'.$product_name.'\n已经发货啦!\n快递单号:'.I("get.logistics_num").'('.I("get.logistics_company").'快递)\n快递备注:'.I("get.logistics_remark");
		if($temp){
			$save['company']=I("get.logistics_company");
			$save['logistics_num']=I("get.logistics_num");
			$save['logistics_remark']=I("get.logistics_remark");
			$save['status']=2;
			$res=M("logistics")->where($where)->save($save);
			if($res){
				M("log")->add($add);
				$this->send_after_courier($customer_id,$user_id,$content);
				$invoke['error']=1000;
				$invoke['data']['note']="修改物流备注成功";
				$invoke['data']['time']=$add['time'];
				$invoke['data']['operate']="发货";
				$invoke['data']['operate_detail']=$add['operate_detail'];
				$invoke['data']['customer_id']=$add['customer_id'];
				$this->ajaxReturn($invoke);
			}else{
				$invoke['error']=1002;
				$invoke['data']="修改物流备注失败";
				$this->ajaxReturn($invoke);
			}
		}else{
			$where['company']=I("get.logistics_company");
			$where['logistics_num']=I("get.logistics_num");
			$where['logistics_remark']=I("get.logistics_remark");
			$where['status']=2;
			$where['isvalid']=1;
			$res=M("logistics")->add($where);
			if($res){
				M("log")->add($add);
				$this->send_after_courier($customer_id,$user_id,$content);
				$invoke['error']=1000;
				$invoke['data']['note']="添加物流备注成功";
				$invoke['data']['time']=$add['time'];
				$invoke['data']['operate']="发货";
				$invoke['data']['operate_detail']=$add['operate_detail'];
				$invoke['data']['customer_id']=$add['customer_id'];
				$this->ajaxReturn($invoke);
			}else{
				$invoke['error']=1002;
				$invoke['data']="添加物流备注失败";
				$this->ajaxReturn($invoke);
			}
		}
	}

	public function expkd(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$where['customer_id']=$customer_id;
		$where['isvalid']=1;
		$where['delivery_status']=0;
		$result=M("order")->where($where)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				$new_result[$i]['batchcode']=(string)$result[$i]['batchcode'];
				$new_result[$i]['user_name']=$result[$i]['user_name'];
				$find_goods['id']=$result[$i]['product_id'];
				$res_goods=M("goods")->where($find_goods)->find();
				$find_common_goods['id']=$res_goods['product_no'];
				$res_common_goods=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_common_goods)->find();
				$new_result[$i]['product_name']=$res_common_goods['name']."（数量：1）";
			}
		}
	}

	public function send_after_courier($customer_id,$user_id,$content){
		send($content,$user_id,$customer_id);
	}

	public function test_experot_order(){
		$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$where['customer_id']=$customer_id;
		$where['isvalid']=1;
		$where['is_pay']=1;
		$result=M("order")->where($where)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				$new_result[$i]['batchcode']=(string)$result[$i]['batchcode'];
				$new_result[$i]['user_id']=$result[$i]['user_id'];
				$new_result[$i]['user_name']=$result[$i]['user_name'];
				$find_weixin_users['id']=$new_result[$i]['user_id'];
				$res_weixin_users=M()->table(DB_NAME.".weixin_users")->where($find_weixin_users)->find();
				$new_result[$i]['weixin_name']=$res_weixin_users['weixin_name'];
				if($result[$i]['pay_style']==1){
					$new_result[$i]['payments']="微信支付";
				}else if($result[$i]['pay_style']==2){
					$new_result[$i]['payments']="零钱支付";
				}else if($result[$i]['pay_style']==3){
					$new_result[$i]['payments']="支付宝支付";
				}
				$new_result[$i]['money']=$result[$i]['money'];
				$new_result[$i]['pay_time']=(string)$result[$i]['pay_time'];
				$new_result[$i]['activity_id']=$result[$i]['activity_id'];
				$new_result[$i]['receipt_man']=$result[$i]['user_name'];
				$new_result[$i]['user_tel']=(string)$result[$i]['user_tel'];
				$new_result[$i]['address']=$result[$i]['province'].$result[$i]['city'].$result[$i]['district'].$result[$i]['addr'];
				$find_goods['id']=$result[$i]['product_id'];
				$res_goods=M("goods")->where($find_goods)->find();
				$find_common_goods['id']=$res_goods['product_no'];
				$res_common_goods=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_common_goods)->find();
				$new_result[$i]['product_name']=$res_common_goods['name'];
				$new_result[$i]['product_num']=1;
				$new_result[$i]['foreign_mark']=$res_common_goods['foreign_mark'];
				$new_result[$i]['order_time']=(string)$result[$i]['order_time'];
				if($new_result[$i]['status']==0){
					$new_result[$i]['order_status']="待支付";
				}else if($new_result[$i]['status']==1){
					$new_result[$i]['order_status']="待完成";
				}else if($new_result[$i]['status']==2){
					$new_result[$i]['order_status']="待退款";
				}else if($new_result[$i]['status']==3){
					$new_result[$i]['order_status']="已退款";
				}else if($new_result[$i]['status']==4){
					$new_result[$i]['order_status']="已完成";
				}else if($new_result[$i]['status']==5){
					$new_result[$i]['order_status']="退款失败";
				}
			}
		}
		$text = array('订单号','用户ID','姓名','微信名','支付方式','支付金额','支付时间','活动ID','收货人','电话号码','地址','产品名','数量','属性名','下单时间','订单状态');
		$this->exportexcel($new_result,$text,"砍价订单". date('Y-m-d',time()));
	}

	public function exportexcel($data=array(),$title=array(),$filename='report'){
		//header("Content-type:application/octet-stream");
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");			
		header("Accept-Ranges:bytes");
		header("Content-type:application/vnd.ms-excel");  
		header("Content-Disposition:attachment;filename=".$filename.".xls");
		header("Pragma: no-cache");
		
		header("Expires: 0"); 
		ob_clean();
		//导出xls 开始
		if (!empty($title)){
			foreach ($title as $k => $v) {
				$title[$k]=iconv("UTF-8", "GB2312",$v);
				//$title[$k]=iconv("BIG5", "GB2312",$v);
			}
			$title= implode("\t", $title);
			echo "$title\n";
		}
		if (!empty($data)){
			foreach($data as $key=>$val){
				foreach ($val as $ck => $cv) {
					//$data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
					$data[$key][$ck]= mb_convert_encoding($cv, "GBK", "UTF-8");
				}
				$data[$key]=implode("\t", $data[$key]);
			}
			echo implode("\n",$data);
		}
	}

	public function goto_mshop(){
		$this->display("bargain/test");
    }

    public function test_http(){
    	$data = I('request.','','trim');
    	if(!$data['customer_id_en']){
    		$invoke['data']='缺少customer_id_en';
    		$invoke['error']=1002;
    		$this->ajaxReturn($invoke);
    	}
    	$customer_id=decode_wsy($data['customer_id_en']);
    	$map_activity['customer_id']=$customer_id;
    	$map_activity['isvalid']=1;
		$activity=M("activity")->field("ruler")->where($map_activity)->find();
		$ruler=$activity['ruler'];
		$this->assign("ruler",$ruler);
		$this->display("bargain/test");
    }

}