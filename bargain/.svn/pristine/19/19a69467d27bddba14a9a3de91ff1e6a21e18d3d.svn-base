user_id_en = getPar_WSY('user_id_en');
web_time = getPar_WSY('web_time');
activity_id = getPar_WSY('activity_id');
$(function(){
	//计算屏幕高度
	var het=$(window).height();
	$('#content').height(het)
	
	console.log($(window).width())
})
//=======默认展示数据========
var vm=new Vue({
	el:'#footer',
	data:{
		url1:HOST_WSY+'/weixinpl/haggling/front/web/mybargainWares.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&web_time='+web_time+'&activity_id='+activity_id+'',
		url2:HOST_WSY+'/weixinpl/haggling/front/web/bargainWares.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&web_time='+web_time+'&activity_id='+activity_id+''
	}
})
