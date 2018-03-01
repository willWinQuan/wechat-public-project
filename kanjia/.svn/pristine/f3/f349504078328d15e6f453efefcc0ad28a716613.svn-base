var customer_id_en = '',
	user_id_en = '',
	web_time='',
    activity_id = '';

/*检测商家customer_id*/
function init_WSY(){
	customer_id_en = getPar_WSY('customer_id_en');
	if(!customer_id_en){
        alert('缺少customer_id_en');
//		xiandan_msg('缺少customer_id_en');
		return false;
	}
}

/**获取get参数 (url查询字符串GET键值)**/
function getPar_WSY(par){
	var local_url = document.location.href; 
	var get = local_url.indexOf(par+'=');
	if(get==-1){
		return false;   
	}   
	var get_par = local_url.slice(par.length+get+1);    
	var nextPar = get_par.indexOf('&');
	if(nextPar!=-1){
		get_par = get_par.slice(0,nextPar);
	}
	return get_par;
}

/** 网页授权 识别用户(获取用户user_id) **/
//product_id 用于识别砍价页面商品id
function get_user_id_WSY(id,apply_id,idx){
    console.log(id+'==='+apply_id+'==='+idx);
    if(typeof(window.localStorage)=='undefined'){
    	alert('您的浏览器版本太低'); //浏览器不支持localStorage
    	return false;
    }
    var web_time_local = '';
    user_id_en = getPar_WSY('user_id_en');
    web_time = getPar_WSY('web_time');
    web_time_local = localStorage.getItem('web_time');
    if(!web_time_local){
		web_time_local = get_randchar_WSY();
        localStorage.setItem('web_time',web_time_local);
    }
	console.log('user_id_en:'+user_id_en,'web_time:'+web_time,'web_time_local:'+web_time_local);
    if(!activity_id){activity_id = getPar_WSY('activity_id');}
    if(!user_id_en||!web_time||web_time!=web_time_local){
        var re_url = '';
        if(!id&&!apply_id&&!idx){//
            re_url = HOST_WSY+'/weixinpl/haggling/back/index.php/home/wx/getinfo?customer_id_en='+customer_id_en+'&web_time='+web_time_local+'&activity_id='+activity_id;   
        }
        if(id&&apply_id){//...
            re_url = HOST_WSY+'/weixinpl/haggling/back/index.php/home/wx/getinfo?customer_id_en='+customer_id_en+'&web_time='+web_time_local+'&activity_id='+activity_id+'&id='+id+'&apply_id='+apply_id;
        }
        if(idx&&apply_id){//分享授权
            re_url = HOST_WSY+'/weixinpl/haggling/back/index.php/home/wx/getinfo?customer_id_en='+customer_id_en+'&web_time='+web_time_local+'&activity_id='+activity_id+'&idx='+idx+'&apply_id='+apply_id;
        }
        if(!id&&idx&&!apply_id){//二维码授权
            re_url = HOST_WSY+'/weixinpl/haggling/back/index.php/home/wx/getinfo?customer_id_en='+customer_id_en+'&web_time='+web_time_local+'&activity_id='+activity_id+'&idx='+idx;
        }
		console.log(re_url);
        location.href = re_url;
		return;
    };
}

function get_randchar_WSY(){
    var timestamp = new Date().getTime(), 
        randchars = String(Math.random());
    return timestamp+randchars.substr(2,4);
}

/*设置COOKIE*/
function setCookie(name,value){
	if(value=="clear"){
		var Days = -1;
	}else{
		var Days = 7;
	}
    var exp = new Date();
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
/*获取COOKIE*/
function getCookie(name){
    var arr,reg = new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg)){
    	return unescape(arr[2]);
    }else{
    	return null;
    }    
}

function chuxian_gz(){
    $('.mask2').show();
    var html="<div id='chuxian_gz' style='position: fixed;top: 20%;left: 15%;width: 5rem;z-index: 99999;'>"+
             "<img id='close-zhe' src='img/close_btn.png'/><img id='weiMa' src='"+gongzong_pic+"'/>"+
             "<span>长按识别二维码<br/>即可进入公众号关注</span></div>";
	$("body").prepend(html);
}

function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
                "SymbianOS", "Windows Phone",
                "iPad", "iPod"];
    var flag = true;
    for (varv=0;v<Agents.length;v++){
        if (userAgentInfo.indexOf(Agents[v])>0){
            flag = false;
        }
    }
    return flag;
}

//填写提示弹出框函数 by chq 20170622
function xiandan_msg(msg){ 
   	 $(".erweima_alert").show();
   	 $(".xiandan-alert span").html(msg);
   	 $(".xiandan-alert button").click(function(){
   	  	$(".erweima_alert").hide();
   	 });
   	 $(".close").click(function(){
   	  	$(".erweima_alert").hide();
   	 });
};
/*公共ajax*/
function ajax(url,data,type,callback){
	$.ajax({
	   url:url,
	   data:data,
	   type:type,            
	   dataType:'json',        
	   success:function(res){
		   //console.log(JSON.stringify(res));
		   callback(res);
	   },
	   error:function(err){
	   	   //alert('网络异常');
	   	   callback(false);
	   } 
	});	
}

//用户访问量统计
function access(){
	var url = HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/access_count',
	    list = {customer_id_en:customer_id_en,activity_id:activity_id};
    ajax(url,list,'get',function(res){
    	if(!res.err_code){
    		console.log('访问统计出错:'+res.data);
    	}else{
    		console.log('访客:'+res.data);
    	}
    });  
}

//商品点击量统计
function hits(activity_id,id){
    //console.log(activity_id+'=='+id);
	var url = HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/goods_hits',
	    list = {customer_id_en:customer_id_en,activity_id:activity_id,id:id};
    ajax(url,list,'get',function(res){
    	if(!res.err_code){
    		console.log(res.data); //
    	}else{
    		console.log('hits:'+res.data);
    	}
    });
}
function get_navigation(column) {
    $.ajax({
        url:HOST_WSY+'/weixinpl/back_newshops/Base/personalization/navigation/navigation_ajax_vue.php',
        type:'POST', //GET
        data:{
            column:column,customer_id:customer_id_en
        },
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(re){
            console.log(re);

            //alert(456);
            $('body').append(re.data);
        },
        error:function(xhr,textStatus){
            //alert(789);
            console.log('错误')
            console.log(xhr)
            console.log(textStatus)
        }
    })
}
function get_bottom_label(column){
    $.ajax({
        url:HOST_WSY+'/weixinpl/back_newshops/Base/personalization/bottom_label/label_ajax_vue.php',
        type:'POST', //GET
        data:{
            column:column,customer_id:customer_id_en
        },
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(re){
            console.log(re);
            $('body').append(re.data);
        },
        error:function(xhr,textStatus){
            console.log('错误')
            console.log(xhr)
            console.log(textStatus)
        }
    })
}