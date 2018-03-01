<?php
/**
*修改人1：郑培强 Copyby邓继勇
*修改时间：2017-06-15
*修改内容：添加阿里大鱼短信发送方法，并在common目录添加了alidayu_sdk文件夹里的所有文件，里面都是阿里大鱼的sdk文件，没做任何修改与备注
*/
//require_once('utility.php'); 
/*阿里大鱼短信需要的文件*/
//include "alidayu_sdk/TopSdk.php";
/*阿里大鱼短信方法需要：时区设置*/
//date_default_timezone_set('Asia/Shanghai'); 
//namespace Vendor\lib;
class Sendmsg
{   

	public function sendMessage($tel,$content,$connect=null){
		$map_first['isvalid']=true;
		$res=M()->table(DB_NAME.'.sms_config')->field('sms_account,sms_password')->where($map_first)->find();
		$username = $res['sms_account'];
		$pwd=$res['sms_password'];
		//$result['Code']=$username;
		//return $result;

        //$connect是对象，lunpan2版用
        /*
		 $query  = "SELECT sms_account,sms_password FROM sms_config WHERE isvalid=true limit 1";   //查找平台短信配置的账号密码，由数据库控制，不由config控制
		 if(is_null($connect)){
            $result = _mysql_query($query) or die("L310 Query failed: " . mysql_error());
         }else{
            //$result = _mysql_query($connect,$query) or die("L311 Query failed: " . mysql_error());
            $result = $connect->query($query);
         }
         
		 while($row    = mysql_fetch_object($result)){
			 $username = $row -> sms_account;
			 $pwd      = $row -> sms_password;
		 }
		 */
		 
         $charset='utf-8';//网站编码，使用短信功能网站的编码，可以为gb2312，gbk，utf-8
         $gateId = '0'; // 通道ID，默认为1
         
         $content= $charset=='utf-8'? $content : iconv($charset,'utf-8',$content);
         $SendTime = ''; // 定时发送时间 格式：YYYY-MM-DD HH:MM:SS，立即发送为空即可

         $http = "http://www.sms800.cc:6630/WebAPI/doodsms.asmx/SendSmsExt";
         $file = $http."?user=".$username."&pwd=".$pwd."&chid=".$gateId."&mobiles=".$tel."&contents=".$content."&sendtime=".$SendTime;
         $xml=simplexml_load_file($file);
         return $xml;
    }


	public function remindMsgNum($customer_id,$mobile,$remind){

		$map_first['customer_id']=$customer_id;
		$map_first['isvalid']=true;
		$res=M()->table(DB_NAME.'.sms_settings')->field('acount,isremainder,phone')->where($map_first)->find();
		$acount = $res['acount'];
		$isremainder=$res['isremainder']; 
		$phone = $res['phone'];

		/*
		$query="select acount,isremainder,phone from sms_settings where isvalid=true and customer_id=".$customer_id;
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		$acount=0;
		while ($row = mysql_fetch_object($result)) {			
			$acount = $row->acount;
			$isremainder=$row->isremainder; 
			$phone = $row->phone;
		}
		*/	     

					//发给商家提示短信剩余条数
                if($acount==11||$acount==6||$acount==1 && $isremainder==1){
                	$map_second['customer_id']=$customer_id;
                	$res_second=M()->table(DB_NAME.'.weixin_baseinfos')->field('weixin_name')->where()->find();
                	$weixin_name=$res_second['weixin_name'];

                    /*
                    $query3="SELECT weixin_name  FROM weixin_baseinfos WHERE customer_id=".$customer_id;         
                    $result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());  
                    while ($row3 = mysql_fetch_object($result3)) {			
                    $weixin_name = $row3->weixin_name;
                    } 
                    */
                    
                    if($acount==11){
                        $content="【".$weixin_name."】剩余短信条数:10条";
                    }else if($acount==6){
                        $content="【".$weixin_name."】剩余短信条数:5条";
                    }else if($acount==1){
                        $content="【".$weixin_name."】剩余短信条数:0条";
                    }        
                    //$commUtil = new CommonUtiliy();
                    $charset="utf-8";
                    $xml= $this->sendMessage($phone,$content);
                    //echo $xml->Code;
                    $status = $xml->Code;
                    //$status=1;
                    $result= $charset=='utf-8'? $xml->Result : iconv('utf-8', 'gb2312', $xml->Result);

                    $save['customer_id']=$customer_id;
                    $save['phone']=$phone;
                    $save['content']=$content;
                    $save['status']=$status;
                    $save['isvalid']=true;
                    $save['createtime']=date('Y-m-d H:i:m');
                    $save['result']=$result;
                    M()->table(DB_NAME.'.sendlogs')->add($save);
                    /*
                    $query2="insert into sendlogs(customer_id,phone,content,status,isvalid,createtime,result) values(".$customer_id.",'".$phone."','".$content."',".$status.",true,now(),'".$result."')";
                    _mysql_query($query2);
                    */            
                }                
            
            //用户发送短信功能
                //$commUtil2 = new CommonUtiliy();
                $charset2="utf-8";
                $xml2= $this->sendMessage($mobile,$remind);

                //echo $xml->Code;
                $status2 = $xml2->Code;
                
                //$status=1;
				//echo $status2;
                $result2= $charset2=='utf-8'? $xml2->Result : iconv('utf-8', 'gb2312', $xml2->Result);

                $save_second['customer_id']=$customer_id;
                $save_second['phone']=$mobile;
                $save_second['content']=$remind;
                $save_second['status']=$status2;
                $save_second['isvalid']=true;
                $save_second['createtime']=date('Y-m-d H:i:m');
                $save_second['result']=$result2;
                M()->table(DB_NAME.'.sendlogs')->add($save_second);

                /*
                $query2="insert into sendlogs(customer_id,phone,content,status,isvalid,createtime,result) values(".$customer_id.",'".$mobile."','".mysql_real_escape_string($remind)."',".$status2.",true,now(),'".$result2."')";
				//echo $query2;
                _mysql_query($query2); 
                */

                $map_third['customer_id']=$customer_id;
                $map_third['isvalid']=true;
                $result=M()->table(DB_NAME.'.sms_settings')->where($map_third)->find();
                $data_third['acount']=$result['acount']-1;
                $result=M()->table(DB_NAME.'.sms_settings')->where($map_third)->save($data_third);

                /*
                $sql="update sms_settings set acount=acount-1 where isvalid=true and customer_id=".$customer_id;
                _mysql_query($sql);
                */
                if(0 < $status2){
                    //短信返回正数就是成功
                    return true;
                }else{
                    //短信返回负数就是失败
                    return false;
                }
 
        
	}
    /**
	*作者：邓继勇
	*方法/函数描述：调用阿里大鱼接口发送短信
	*方法/函数名：remindMsgNumNew()
	*@参数：int $customer_id 商家ID
	*@参数：string $mobile 接收短信的手机号
	*@参数：string $content 包含$product、$code、$min...等参数
	*说明：只写了验证码模板的短信，以后需要拓展其他模板，可根据短信模板ID：$sms_template_code的值来做if判断，并在下行备注好修改信息
	*/
	public function remindMsgNumNew($customer_id,$mobile,$remind){
		$remind_msg = explode("_",$remind);
		$product     = $remind_msg[0];//功能名/产品名
		$code        = $remind_msg[1];//验证码
		$min         = $remind_msg[2];//有效时间，例：$min='1分钟';
	    $query="select appkey,secret,sms_sign_name,sms_template_code from aliyun_sms_config_t where isvalid=true and customer_id=".$customer_id;
	    $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	    while ($row = mysql_fetch_object($result)) {			
		    $appkey            = $row->appkey;//阿里大鱼的appkey
			$secret            = $row->secret;//阿里大鱼appkey的秘钥
			$sms_sign_name     = $row->sms_sign_name;//短信签名
			$sms_template_code = $row->sms_template_code;//短信模板ID
	    }
	
	    define("appkey",$appkey);
	    define("secret",$secret);
	
	    $c = new TopClient;
	    $c->appkey = appkey;
	    $c->secretKey = secret;
	    $c->format= "json";
	    $req = new AlibabaAliqinFcSmsNumSendRequest;
	    $req->setSmsType("normal");//短信类型，常规
	    $req->setSmsFreeSignName($sms_sign_name);
	    $req->setRecNum($mobile);//手机号
	    $req->setSmsParam("{\"code\":'".$code."',\"product\":'".$product."',\"min\":'".$min."'}");//短信模板变量,code：验证码；product:产品名；min:有效时间
	    $req->setSmsTemplateCode($sms_template_code);
	    $resp = $c->execute($req);  
		//var_dump($resp);//调试返回信息用
		
		//插入发送日志
        $content="【".$sms_sign_name."】您正在使用".$product."，验证码：".$code."，".$min."内有效。";//短信内容
        $query2="insert into sendlogs(customer_id,phone,content,isvalid,createtime) values(".$customer_id.",'".$mobile."','".$content."',true,now())";
        _mysql_query($query2); 	
        return $resp;
		
	}	 

    /**
	*作者：邓继勇
	*方法/函数描述：调用阿里大鱼接口发送短信------------OEM版本（无customer_id）
	*方法/函数名：remindMsgNumNew()
	*@参数：string $mobile 接收短信的手机号
	*@参数：string $content 包含$product、$code、$min...等参数
	*说明：只写了验证码模板的短信，以后需要拓展其他模板，可根据短信模板ID：$sms_template_code的值来做if判断，并在下行备注好修改信息
	*/
	public function remindMsgNumNewOEM($mobile,$remind){
		$remind_msg = explode("_",$remind);
		$product     = $remind_msg[0];//功能名/产品名
		$code        = $remind_msg[1];//验证码
		$min         = $remind_msg[2];//有效时间，例：$min='1分钟';
	    $query="select appkey,secret,sms_sign_name,sms_template_code from aliyun_sms_config_oem where isvalid=true limit 0,1";
	    $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	    while ($row = mysql_fetch_object($result)) {			
		    $appkey            = $row->appkey;//阿里大鱼的appkey
			$secret            = $row->secret;//阿里大鱼appkey的秘钥
			$sms_sign_name     = $row->sms_sign_name;//短信签名
			$sms_template_code = $row->sms_template_code;//短信模板ID
	    }
	
	    define("appkey",$appkey);
	    define("secret",$secret);
	
	    $c = new TopClient;
	    $c->appkey = appkey;
	    $c->secretKey = secret;
	    $c->format= "json";
	    $req = new AlibabaAliqinFcSmsNumSendRequest;
	    $req->setSmsType("normal");//短信类型，常规
	    $req->setSmsFreeSignName($sms_sign_name);
	    $req->setRecNum($mobile);//手机号
	    $req->setSmsParam("{\"code\":'".$code."',\"product\":'".$product."',\"min\":'".$min."'}");//短信模板变量,code：验证码；product:产品名；min:有效时间
	    $req->setSmsTemplateCode($sms_template_code);
	    $resp = $c->execute($req);  
		//var_dump($resp);//调试返回信息用
		
		//插入发送日志
        $content="【".$sms_sign_name."】您正在使用".$product."，验证码：".$code."，".$min."内有效。";//短信内容
        //无customer_id无法插入
        //$query2="insert into sendlogs(customer_id,phone,content,isvalid,createtime) values(".$customer_id.",'".$mobile."','".$content."',true,now())";
        //_mysql_query($query2); 	
        return $resp;
		
	}	    

}