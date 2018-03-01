var customer_id = '',
	user_id_en = '',
	web_time = '',
	HOST_WSY = 'http://shenzhen.weisanyun.cn';
	
function init_WSY(){
	customer_id = getPar_WSY('customer_id');
	if(!customer_id){
		var str=check_input()
		if(str!=HOST_WSY+"/weixinpl/yiren/back/index.php/Workroom_admin/base/apply_set.html"){
			alert('缺少customer_id');
			return false;
		}
	}
}

function check_input(){
	var str=document.referrer;
	var num=str.indexOf("?") 
	str=str.substr(0,num);
	return str;
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

/**
 * 访问验证(识别用户)
 * 获取用户user_id
 */
function get_user_id_WSY(){
    if(typeof(window.localStorage)=='undefined'){
    	alert('您的浏览器版本太低'); //浏览器不支持localStorage
    	return false;
    }
    user_id_en = getPar_WSY('user_id_en');
    web_time = getPar_WSY('web_time');

    var web_time_local = localStorage.getItem('web_time');
    if(!web_time_local){
		var web_timestamp = get_randchar_WSY();
		web_time_local=web_timestamp;
        localStorage.setItem('web_time',web_time_local);
    }

	console.log('user_id_en:'+user_id_en,'web_time:'+web_time,'web_time_local:'+web_time_local);
	//alert('user_id_en:'+user_id_en,'web_time:'+web_time,'web_time_local:'+web_time_local);
	
    if(!user_id_en||!web_time||web_time!=web_time_local){
        window.location.href = HOST_WSY+'/weixinpl/yiren/back/index.php/home/comm/getbaseinfo/customer_id_en/'+customer_id_en+'/web_time/'+web_time_local;
		return false;
    }
    return false;
}

function get_randchar_WSY(){
    var timestamp = new Date().getTime(), //ms
        randchars = String(Math.random());
    return timestamp+randchars.substr(2);
}

