
/*检测商家customer_id*/
var init_WSY=(function(){
	var customer_id = getPar_WSY('customer_id');
	if(!customer_id){
//      alert('缺少customer_id_en');
		console.log('缺少customer_id')
//		xiandan_msg('缺少customer_id_en');
		return false;
	}
})();


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
	return decodeURI(get_par);
};

/*****************************************************api*****************************************************/
/*****************************************************api*****************************************************/
//检测浏览器是否支持pormise
var checkPormise=(function(){
	try{
		new Promise(function () {});
		console.log('支持pormise');
	}catch(err){
		alert('该浏览器版本太低，请升级浏览器版本或更换浏览器使用该产品！');
		console.log('不支持promise');
		console.log(err);
		return false;
	};
})();

//ajax-promise
function getJSON(type,data,url) {
    return new Promise(function(resolve, reject) {
    	$.ajax({
    		type:type,
    		url:url,
    		async:true,
    		data:data,
    		success:function(res){  
    			var res=JSON.parse(res);
                resolve(res); 
    		},
    		error :function(err){
    			var errContent='接口请求失败';
    			reject(errContent);
    		}
    	});
    })
  }; 
