<?php
class Cate{
	/**
	 * Created by Zend
	 * Author: Tung(binn)
	 * Date: 2017/4/22
	 */
	static public function lt($list,$pid=0,$space=0,&$a=array(),$catename='service_name'){
		$space +=4;
		foreach($list as $v){
			if($v['parent_id']==$pid){
				if($v['parent_id']==0){$space = 0;}$v[$catename] = str_repeat('&nbsp;',$space).'|--'.$v[$catename];
				$a[] = $v;self::lt($list,$v['id'],$space,$a);
			}
		} return $a;
	}
	static public function tch($list,$pid){
		$con=[];
		foreach($list as $v){if($v['parent_id']==$pid){$con[] = $v;
		$con = array_merge($con,self::tch($list,$v['id']));}}
		return $con;
	}
	static public function shl($cate,$pid='parent_id',$catename='service_name',$level_id=null,$strCate=''){
		$strCate .= "<select name=$pid><option value='0'>一级分类</option>";
		foreach($cate as $v){
			if($v['id']==$level_id){$strCate .= "<option value='{$v['id']}' selected>".$v[$catename]."</option>";}
			else{$strCate .= "<option value='{$v['id']}'>".$v[$catename]."</option>";}
		} return $strCate.="</select>";
	}
	static public function pare($id,&$a=array(),$catename='service_name',$table='yr_service'){
		$sql = "select id,parent_id,$catename from $table where id=$id";
		$arr = mysql_fetch_assoc(_mysql_query($sql)); 
		if(!empty($arr)){
			$a[] = $arr;
			self::pare($arr['parent_id'],$a); 
		} return $a;
	}
	static public function nch($list,$pid,&$a=array()){
		foreach($list as $v){if($v['parent_id']==$pid){$a[] = $v;}}
		return $a;
	}
	static public function shx($cate,$level_id=null,$pid='parent_id',$catename='service_name',$strCate=''){
		$strCate .= "<select name=$pid><option value='0'>一级分类</option>";
		foreach($cate as $v){
			if($v['id']==$level_id){$strCate .= "<option value='{$v['id']}' selected>".$v[$catename]."</option>";}
			else{$strCate .= "<option value='{$v['id']}'>".$v[$catename]."</option>";}
		} return $strCate.="</select>";
	}
	
}

