<?php
class 	Weixincomm{
	/**
	 * 获取微信用户的ACCESS_TOKEN
	 */
	public function getWxAccessToken($customer_id){
		if(!$customer_id){
			$info['error_code'] = 1002;
			$info['data'] = '鉴权失败';
			return $this->ajaxReturn($info);
		}
		$where['customer_id']= $customer_id;
		$str = $this->common($customer_id);
		$result =  explode('-',$str);
		$appid = $result[0];
		$appsecret = $result[1];
		$resultdata =  M()->table(DB_NAME.".weixin_menus")->field('access_token,expires_token_time,get_token_time')->where($where)->select();
		$time = time();
		if($resultdata[0]['expires_token_time']<=$time){
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid="
					.$appid."&secret=".$appsecret;
			$res2=$this->http_curl($url,'get');
			if($res2['access_token']){
				$data['expires_token_time']=time()+7200;
				$data['get_token_time']=time();
				$data['access_token']= $res2['access_token'];
				$where['customer_id'] =$customer_id;
				$where['isvalid'] =1;
				$result_save =  M()->table(DB_NAME.".weixin_menus")->where($where)->save($data);
				if($result_save){
					return $res2['access_token'];
				}else{
					$info['error_code'] = 1001;
					$info['data'] = '设置access_toke失败';
					return $this->ajaxReturn($info);
				}
			}
		}else{
			return $resultdata[0]['access_token'];
		}


	}
	//获取tiket函数
	public  function  getjsapitiket($customer_id){
		if(!$customer_id){
			$info['error_code'] = 1002;
			$info['data'] = '鉴权失败';
			return $this->ajaxReturn($info);
		}
		$where['customer_id']= $customer_id;
		$str = $this->common($customer_id);
		$result =  explode('-',$str);
		$appid = $result[0];
		$appsecret = $result[1];
		$resultdata =  M()->table(DB_NAME.".weixin_menus")->field('jsapi_ticket,jsapi_time,jsapi_expire_time')->where($where)->select();
		$time = time();
		
	
		if($resultdata[0]['jsapi_expire_time']<=$time){
			$aceess_token = $this->getWxAccessToken($customer_id);
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $aceess_token . "&type=jsapi";
			$res2 = $this->http_curl($url, 'get');
			//将重新获取到的aceess_token存到session
			if($res2['ticket']){
				$data['jsapi_expire_time']=time()+7200;
				$data['jsapi_time']=time();
				$data['jsapi_ticket']=$res2['ticket'];
				$where['customer_id'] =$customer_id;
				$where['isvalid'] =1;
				$result_save =  M()->table(DB_NAME.".weixin_menus")->where($where)->save($data);
				if($result_save){
					return $res2['ticket'];
				}else{
					$info['error_code'] = 1001;
					$info['data'] = '设置access_toke失败';
					return $this->ajaxReturn($info);
				}
			}
		}else{
			return $resultdata[0]['jsapi_ticket'];
		}
	}
	
	//获取十六位随机码
	public  function getRandCode($num=16){
		$array=array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
				'a','c','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
				'0','1','2','3','4','5','6','7','8','9',
		);
		$tmpstr ='';
		$max=count($array);
		for($i=1;$i<=$num;$i++){
			$key=rand(0,$max-1);
			$tmpstr .= $array[$key];
		}
		return  $tmpstr;
	}
	//微信js-sdk 页面参数
	public function weixinDisplay($customer_id){
		if(!$customer_id){
			$info['error_code'] = 1002;
			$info['data'] = '鉴权失败';
			return $this->ajaxReturn($info);
		}
		//$_SESSION['customer_id_open']=$customer_id; 暂时关闭zee
		$timestamp = time();
		$nonecestr=$this->getRandCode();
		$jsapi_ticket =$this->getjsapitiket($customer_id);
		$url 	=$_SERVER['HTTP_REFERER'];
		$signature=sha1("jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonecestr."&timestamp=".$timestamp."&url=".$url);
		$str = $this->common($customer_id);
		$result =  explode('-',$str);
		$appid = $result[0];
		$info=array('appid'=>$appid,'timestamp'=>$timestamp,'nonecestr'=>$nonecestr,'signature'=>$signature);
		return  $info;
	}

	//获取 appid 何 APPsecret 公共函数
	public function common($customer_id){
		/*$type =I('type');

		$customer_id = decode_wsy( I('customer_id'));
		$code = I('code');*/
		$c['customer_id'] = $customer_id;
		$result=M()->table(DB_NAME.'.weixin_menus')->field('appid,appsecret')->where($c)->select();
		$appid = $result[0]['appid'];
		$appsecret = $result[0]['appsecret'];

		return   $appid.'-'.$appsecret;
	}

	//curl 配置
	public   function http_curl($url,$type='get',$res='json',$arr=''){
		//1.初始化curl
		$ch = curl_init();
		//2.设置curl的参数
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		if($type == 'post'){
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
		}
		//3.采集
		$output =curl_exec($ch);
		//4.关闭
		curl_close($ch);
		if($res=='json'){
			if(curl_error($ch)){
				//请求失败，返回错误信息
				return curl_error($ch);
			}else{
				//请求成功，返回错误信息
				return json_decode($output,true);
			}
		}
		return $output;
	}
	
}

