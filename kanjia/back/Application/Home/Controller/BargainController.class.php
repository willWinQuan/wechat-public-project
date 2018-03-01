<?php
namespace Home\Controller;
use Think\Controller;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST, GET, OPTIONS');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header('Content-type:text/html;charset=utf-8');
class BargainController extends Controller{

	/*统一测试函数*/
	public function test(){
		// $result=$this->jqsz("2017-07-01 10:00:00");
		// echo $result;

		// $result=$this->yzb(100);
		// echo $result;
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

	/*将数字位数固定*/
	public function yzb($int){
		$newint=sprintf("%02d",$int);
		return $newint;
	}

	/*模拟生成global*/
	public function add_global(){
		date_default_timezone_set('PRC');
		$a=$this->jqsz(date("Y-m-d H:i:s"));
		$data['activity_id']=$a."1234";
		$data['activity_title']="hello world";//$input['title'];
		$data['activity_logo']="https://exp.bdstatic.com/static/common-jquery/widget/search-box/img/logo_83ae7e2.png";//$input['logo'];
		$data['apply_number']=3;//$input['a_n'];
		$data['single_product_apply_number']=1;//$input['s_p_a_n'];
		$data['play_number']=3;//$input['p_n'];
		$data['single_product_bargain_number']=1;//$input['s_p_b_n'];
		$data['bargain_min_price']=1;//$input['b_min_p'];
		$data['bargain_max_price']=500;//$input['b_max_p'];
		$data['is_coupon']=1;//$input['is_coupon'];
		$data['coupon_id']=1000100;//$input['coupon_id'];
		$data['coupon_probability']=0;//$input['coupon_probability'];
		$data['activity_create_time']=date("Y-m-d H:i:s",time()+1*60*60);//$input['activity_create_time'];
		$data['activity_end_time']=date("Y-m-d H:i:s",time()+31*60*60);//$input['activity_end_time'];
		$data['isvalid']=1;
		$data['customer_id']=1234;//$input['customer_id'];
		M("activity_global")->data($data)->add();
	}

	/*模拟展示global*/
	public function global_display(){
		$result=M("activity_global")->where(true)->select();
		$this->ajaxReturn($result);
	}

	/*模拟修改global(内不含同步修改part)*/
	public function update_global(){
		$where['activity_id']="201706261830341234";
		/*for example*/
		$data['activity_end_time']=date("Y-m-d H:i:s",time()+31*60*60);
		/*for example*/
		M("activity_global")->where($where)->save($data);
	}

	/*模拟修改global(内含同步修改part)*/
	public function update_global_and_part(){
		$where['activity_id']="201706261830341234";
		/*for example*/
		$data['activity_end_time']=date("Y-m-d H:i:s",time()+31*60*60);
		/*for example*/
		M("activity_global")->where($where)->save($data);
		/*修改activity_global是否同步修改activity_part（是）*/
		M("activity_part")->where($where)->save($data);
		/*修改activity_global是否同步修改activity_part（是）*/
	}

	/*模拟商品选取页*/
	public function product_select(){
		$result=M("simulation_product")->where(true)->select();
		$this->ajaxReturn($result);
	}

	/*模拟生成part*/
	public function add_part(){
		/*检查本次活动是否已经添加过同类商品*/
		$check_product['product_id']=100005060;
		$check_product['activity_id']="201706261830341234";
		$check_product['isvalid']=1;
		$result_product=M("activity_part")->where($check_product)->find();
		if($result_product){
			$this->error('已经添加过同类商品',U('part_display'));
			exit();
		}
		/*检查本次活动是否已经添加过同类商品*/

		/*activity_id是添加activity_part数据的必须条件*/
		$where['activity_id']="201706261830341234";//I("post.activity_id");
		$temp=M("activity_global")->where($where)->find();
		/*activity_id是添加activity_part数据的必须条件*/
		$data['activity_id']=$temp['activity_id'];
		$data['product_id']=100005060;//$input['product_id'];
		$data['inventory']=0;//$input['inventory'];
		$data['apply_number']=$temp['apply_number'];
		$data['single_product_apply_number']=$temp['single_product_apply_number'];
		$data['play_number']=$temp['play_number'];
		$data['single_product_bargain_number']=$temp['single_product_bargain_number'];
		$data['bargain_min_price']=$temp['bargain_min_price'];
		$data['bargain_max_price']=$temp['bargain_max_price'];
		$data['is_coupon']=$temp['is_coupon'];
		$data['coupon_id']=$temp['coupon_id'];
		$data['coupon_probability']=$temp['coupon_probability'];
		$data['activity_create_time']=$temp['activity_create_time'];
		$data['activity_end_time']=$temp['activity_end_time'];
		$data['isvalid']=1;
		$data['customer_id']=$temp['customer_id'];
		$result=M("activity_part")->data($data)->add();
		if($result){
			$this->success('添加成功,是否继续添加？',"product_select");
		} else {
			$this->error('添加失败');
		}
	}

	/*模拟展示part*/
	public function part_display(){
		$where['activity_id']="201706261830341234";
		$result=M("activity_part")->where($where)->select();
		$this->ajaxReturn($result);
	}

	/*模拟修改part*/
	public function update_part(){
		/*for example*/
		$where['id']=13;//I("post.id");
		$data['product_id']=100005060;//I("post.product_id");
		if(I("get.inventory")){
			$get['inventory']=I("get.inventory");
			$result_inventory_part=M("activity_part")->where($where)->find();
			/*检查商品总库存是否足够*/
			$check_inventory['product_id']=100005060;
			$result_inventory_product=M("simulation_product")->where($check_inventory)->find();
			$temp=$result_inventory_product['inventory']+$result_inventory_part['inventory'];
			if($temp<$get['inventory']){
				$this->error('商品库存不足',U("product_select"));
			}
			$save_inventory['inventory']=$get['inventory']-$result_inventory_part['inventory'];
			M("simulation_product")->where($check_inventory)->save($save_inventory);
		}
		$data['inventory']=I("get.inventory");
		/*检查商品总库存是否足够*/
		/*for example*/
		M("activity_part")->where($where)->save($data);
	}

	/*活动汇总*/
	public function activity_simulate(){
		$where['customer_id']=1234;//decode_wsy(I("get.customer_id_en"));
		$where['isvalid']=1;
		$result=M("activity_global")->where($where)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="activity_end_time"){
					if($value<date("Y-m-d H:i:s")){
						$where['activity_id']=$result[$i]['activity_id'];
						$save['activity_status']=0;
						M("activity_global")->where($where)->save($save);
						$result[$i]['status']="已结束";
					}else{
						$result[$i]['status']="进行中";
					}
				}
			}
		}
		var_dump($result);
	}

}

?>