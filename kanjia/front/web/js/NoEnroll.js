//获取url上的参数
idx = getPar_WSY('idx');
id = getPar_WSY('id');
var activity_id = getPar_WSY('activity_id');
console.log(activity_id);
user_id_en = getPar_WSY('user_id_en');
web_time = getPar_WSY('web_time');
var apply_id="";
var checkGuanData="";
var is_care="1";//判断是否需要关注
if(idx){id=idx}
var vm=new Vue({
	el:'#enrolled',
	data:{
		day:'',
		hour:'',
		mintue:'',
		seconds:'',
		activity_id:'',
		product_no:'',
		product_num:'',
		price:'',
		buy_price:'',
		minimum_price:'',
		name:'',
		specifications:'',
		customer_service:'',
		description:'',
		inventory:'',
		enroll_price:'',
		list:'',
		goods_expire_code:'',
		activity_expire_code:'',
		shop_name:'',
		shop_tel:'',
		shop_url:'',
		shop_logo:'',
		shop_pro_num:'',
		shop_collect_num:'',
		mshop_code:'',
		weixin_name:'',
		money:'',
		Error:'',
		ruledata:''
	},
	methods:{
		enroll:function(){
			//报名
			//检查是否关注
            checkGuan(function(res){
				console.log(res.err_code)
			if(res.err_code=="1000"){
                   jion();
			}else if(res.err_code=="1001"){
				  if(is_care=="1"){
				  	guanAlert(res.data);
                    console.log("还没有关注！");
				  }else if(is_care=="0"){
				  	jion();
				  }
 
			};
		});

		},
		shuted:function(){
			tkhide();
		},
		home:function(activity_id){
			location.href = 'index.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&activity_id='+activity_id;
		}
	}
})
/*砍价页面展示数据*************开始*/
function init(err_code,activityid){
	var vm=this.vm;
	var data={id:id,customer_id_en:customer_id_en,activity_id:activityid};
	console.log(data);
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/goods_details'
	$.ajax({
        url:url,
        data:data,
        async:true,
        type:'get',
        dataType:'json',
        success:function(res){
        	//展示
			document.getElementsByTagName('body')[0].style.visibility='visible';
			console.log(res)
			if(res.ruler!=null){
			var ruler=res.ruler.replace(/\n/g,'<br>');
        	console.log(ruler)
			vm.ruledata=ruler;
			}
			//生成二维码
			new QRCode(
				document.getElementsByClassName("QRcode")[0], 
				res.qr_code
				);  // 设置要生成二维码的链接 
			//点开二维码
			//活动时间
			end=new Date(res.data['end_time'])
			Activity_end=new Date(res.data['activity_end_time'])
			now=new Date(res.data['nowtime']);
			star=new Date(res.data['start_time'])
			//时间差
			time1=new Date(res.data['end_time'])-new Date(res.data['nowtime'])
			time2=Activity_end-new Date(res.data['nowtime'])
			//倒计时函数
			conduct();
			//数据
			vm.shop_name=res.mshop['mshop_data']['shop_name'];
			vm.shop_tel=res.mshop['mshop_data']['shop_tel'];
			vm.shop_url=res.mshop['mshop_data']['shop_url'];
			vm.shop_logo=res.mshop['mshop_data']['shop_logo'];
			vm.shop_pro_num=res.mshop['mshop_data']['shop_pro_num'];
			vm.shop_collect_num=res.mshop['mshop_data']['shop_collect_num'];
			vm.mshop_code=res.mshop['mshop_code']
			vm.list=res.img;
			vm.id=res.data['id'];
			vm.activity_id=res.data['activity_id'];
			vm.product_no=res.data['product_no'];
			vm.product_num=res.data['product_num'];
			vm.price=res.data['price'];
			vm.buy_price=res.data['buy_price'];
			vm.minimum_price=res.data['minimum_price'];
			vm.name=res.data['name'];
			vm.inventory=res.data['inventory'];/*商品库存量*/
			vm.enroll_price=(Number(res.data['price'])-Number(res.data['minimum_price'])).toFixed(2);
			var customer_service=res.data['customer_service'];/*售后保障*/
			var description=res.data['description'];/*商品详情*/
			var specifications=res.data['specifications'];/*产品规则*/
			$('.explainCXq').html(description)
			$('.explainCGg').html(specifications)
			$('.explainCSh').html(customer_service)
			//
			if(err_code==0){
				$('.FTbutton .FTbutton4').css('background','#888888')
			}
			setTimeout(function(){
				//轮播
				mySwiper = new Swiper(".swiper-container",{
				    loop:true,  //用于无限循环切换
				    autoplay:1000,//轮播间隔时间
				    speed:1000,//轮播过程时间
				    //如果需要分页器
				    pagination:".swiper-pagination",  //默认：null
				    //当参数为true时，点击分页器指示点分页器会控制Swiper切换，点击后轮播会失效
				    paginationClickable:true,
				    //加这个之后，点击autoPlay会重启
				    //用户操作swiper之后，是否禁止autoplay.默认为true:停止 ，如果设置为false,用户操作swiper之后自动切换不会停止。
				    // 每次都会重新启动autopaly.
				    autoplayDisableOnInteraction:false,
				    //拖动时变成手掌形状
				    grabCursor:true
				})
			},200)
        },
        error:function(res){
           	console.log('请求信息失败')
        },  
    });
}
//获取活动商品的信息
function HDxinxi(){
	var vm=this.vm;
	var data={
		id:id,
		customer_id_en:customer_id_en,
		user_id_en:user_id_en,
		activity_id:activity_id
	};
	console.log(data);
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/comInfo'
	$.ajax({
        url:url,
        data:data,
        async:true,
        type:'get',
        dataType:'json',
        success:function(res){
			console.log(res);
			vm.activity_expire_code=res.activity_expire_code;
			vm.goods_expire_code=res.goods_expire_code;
			activity_id=res.activity_id;
			completed_status=res.completed_status;
			apply_status=res.apply_status;
			is_care=res.is_care;
			console.log("apply_status:"+apply_status);
//			console.log(res.completed_status);
			if(completed_status==-1 || apply_status==1){
				$('.FTbutton .FTbutton4').css('background','#888888')
			}
        },
        error:function(res){
        	console.log('请求信息失败')
        },  
    });
}
HDxinxi()
//获取参与资格的信息
function xinxi(){
	var data={
		id:id,
		customer_id_en:customer_id_en,
		user_id_en:user_id_en,
		activity_id:activity_id
	};
	console.log(data);
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/checkApply'
	$.ajax({
        url:url,
        data:data, 
        async:true,
        type:'get',
        dataType:'json',
        success:function(res){
			console.log(res);
			var err_code=res.err_code
			init(err_code,data.activity_id);
        },
        error:function(res){
			console.log('请求信息失败')
        },  
    });
}
xinxi();
//倒计时
function conduct(){
	var vm=this.vm;
	if(now>=star){
		$('.Countdown1').show()
		$('.Countdown2').hide()
		if(end<=Activity_end){
			time1=time1-1000;
			var day=parseInt(time1/1000/60/60/24)
    		var hour=parseInt(time1/1000/60/60%24)
			var mintue=parseInt(time1/1000/60%60)
			var seconds=parseInt(time1/1000%60)
		}else{
			time2=time2-1000;
			var day=parseInt(time2/1000/60/60/24)
    		var hour=parseInt(time2/1000/60/60%24)
			var mintue=parseInt(time2/1000/60%60)
			var seconds=parseInt(time2/1000%60)
		}
	}else{
		$('.Countdown2').show()
		$('.Countdown1').hide()
	}
    
	if(day<10){
		day='0'+day
	}
	if(hour<10){
		hour='0'+hour
	}
	if(mintue<10){
		mintue='0'+mintue
	}
	if(seconds<10){
		seconds='0'+seconds
	}
	//倒计时
	tt=setTimeout('conduct()',1000)
	if(time1<=0||time2<=0){
		clearTimeout(tt)//清除计时器
		day=0;
		hour=0;
		mintue=0;
		seconds=0;
	}
	vm.day=day;
	vm.hour=hour;
	vm.mintue=mintue;
	vm.seconds=seconds;
}
/*报名砍价*/
function jion(){
	if(idx){
		id = idx;
	}
	var data={
		id:id,
		customer_id_en:customer_id_en,
		activity_id:activity_id,
		user_id_en:user_id_en,
		completed_status:completed_status
	};
	console.log(data);
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/shareApply'
	$.ajax({
        url:url,
        data:data,
        async:true,
        type:'get',
        dataType:'json',
        success:function(res){
			console.log(res)
			var err_code=res.err_code;
			apply_id=res.apply_id;
			if(err_code==1){
                 sureAlert();
				console.log("报名成功！");
        	}
        	if(err_code==0){
        		var t=res.data;
        		tkshow(t);
        	}
        },
        error:function(res){
        	console.log('请求信息失败')
        },  
    });
}
// 检查是否已经关注
function checkGuan(callback){
	var data={
		customer_id_en:customer_id_en,
		activity_id:activity_id,
		user_id_en:user_id_en,
	};
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/getSubscribe';
    ajax(url,data,"get",function(res){
          console.log('checkGuanData:'+JSON.stringify(res)); 
          // checkGuanData=res; 
          callback(res);        
	})
}


//关注二维码弹框
function guanAlert(pic){
	$(document).bind('touchmove',function(event) { 
    	event.preventDefault(); 
    })
	 $(document.body).css({
	   "overflow-x":"hidden",
	   "overflow-y":"hidden"
 	});
	var let=$('body').height();
	$('.mask').height(let);
      
      $(".guanCodeImg").remove();
      $(".guanCodeContent").remove();
     $(".guanCode").css({"display":"block"});
     var html="<img src='"+pic+"' class='guanCodeImg'/><span class='guanCodeContent'>温馨提示：长按二维码关注后就可以报名了哦...</span>";
     $(".guanCode").prepend(html);
}

//确定报名弹出框
function sureAlert(){
	$(document).bind('touchmove',function(event) { 
    	event.preventDefault(); 
    })
	 $(document.body).css({
	   "overflow-x":"hidden",
	   "overflow-y":"hidden"
 	});
	var let=$('body').height();
	$('.mask').height(let);

	$(".sureGuanAlert").css({"display":"block"});
	$(".sureContent").remove();
	var html='<span class="sureContent">报名成功！</span>'
	$(".sureGuanAlert").prepend(html);
}

// 关闭关注二维码弹框
function shutGuanCode(){
	$('.guanCode').hide();
	$(document).unbind('touchmove');
	var let=0;
	$('.mask').height(let)
	 $(document.body).css({
	   "overflow-x":"auto",
	   "overflow-y":"auto"
 	});
}
// 关闭确定报名弹框
function shutSureGuan(){
	$('.sureGuanAlert').hide();
	$(document).unbind('touchmove');
	var let=0;
	$('.mask').height(let)
	 $(document.body).css({
	   "overflow-x":"auto",
	   "overflow-y":"auto"
 	});

location.href = 'enrolled.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&id='+id+'&apply_id='+apply_id+'&activity_id='+activity_id+'&web_time='+web_time+'';
}
/*砍价页面展示数据*************结束*/
/*优惠券*/
function prompt(){
	var vm=this.vm;
	var data = {customer_id_en:customer_id_en,activity_id:activity_id}
	console.log(data);
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/test_coupon'
	$.ajax({
        url:url,
        data:data,
        async:true,
        type:'get',
        dataType:'json',
        success:function(res){
        	/*console.log(res)*/
        	vm.Error=res.error;
        	vm.weixin_name=res.data['weixin_name'];
        	vm.money=res.data['money'];
        	setTimeout('prompt()',1500)
        },
        error:function(res){
            console.log('请求信息失败！！！');
        },  
    });
}
prompt();
//