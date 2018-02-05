//导航选择项
customer_id_en=getPar_WSY('customer_id_en');
user_id_en = getPar_WSY('user_id_en');
apply_id = getPar_WSY('apply_id');
activity_id = getPar_WSY('activity_id');
var t1,t2,t3,t4;
var allData,ingData,compelteData,overdueData;

getAllData()
showDOM(allData);
t1=setInterval('getAllData()',1000);

$('.headerLi').click(function(){
	$('.headerLi div').removeClass('choice')
	$(this).children('div').addClass('choice')
	var id=$(this).index();
	if(id==0){
		$(".content").css("display","none");
		$('.headerLi div .img1').css('background-image','url(img/kj_04.png)')
		$('.headerLi div .img2').css('background-image','url(img/kj_06.png)')
		$('.headerLi div .img3').css('background-image','url(img/kj_08.png)')
		$('.headerLi div .img4').css('background-image','url(img/kj_10.png)')
		$('.content li').remove()
		clearInterval(t1)
		if(t3!=''){
			clearInterval(t3)
		}
		if(t2!=''){
			clearInterval(t2)
		}
		if(t4!=''){
            clearInterval(t4)
		}
		getAllData();
		showDOM(allData);
		t1=setInterval('getAllData()',1000);
	}
	if(id==1){
		$(".content").css("display","none");
		$('.headerLi div .img1').css('background-image','url(img/kj_041.png)')
		$('.headerLi div .img2').css('background-image','url(img/kj_061.png)')
		$('.headerLi div .img3').css('background-image','url(img/kj_08.png)')
		$('.headerLi div .img4').css('background-image','url(img/kj_10.png)')
		$('.content li').remove()
		clearInterval(t1)
		if(t3!=''){
			clearInterval(t3)
		}
		if(t2!=''){
			clearInterval(t2)
		}
		if(t4!=''){
            clearInterval(t4)
		}
		getIngData();
		showDOM(ingData);
		t2=setInterval('getIngData()',1000);

	}
	if(id==2){
		$(".content").css("display","none");
		$('.headerLi div .img1').css('background-image','url(img/kj_041.png)')
		$('.headerLi div .img2').css('background-image','url(img/kj_06.png)')
		$('.headerLi div .img3').css('background-image','url(img/kj_081.png)')
		$('.headerLi div .img4').css('background-image','url(img/kj_10.png)')
		$('.content li').remove()
		clearInterval(t1)
		if(t3!=''){
			clearInterval(t3)
		}
		if(t2!=''){
			clearInterval(t2)
		}
		if(t4!=''){
            clearInterval(t4)
		}
		getCompelteData()
		showDOM(compelteData);
		t3=setInterval('getCompelteData()',1000);
		
	}
	if(id==3){
		$(".content").css("display","none");
		$('.headerLi div .img1').css('background-image','url(img/kj_041.png)')
		$('.headerLi div .img2').css('background-image','url(img/kj_06.png)')
		$('.headerLi div .img3').css('background-image','url(img/kj_08.png)')
		$('.headerLi div .img4').css('background-image','url(img/kj_101.png)')
		$('.content li').remove()
		clearInterval(t1)
		if(t3!=''){
			clearInterval(t3)
		}
		if(t2!=''){
			clearInterval(t2)
		}
		if(t4!=''){
            clearInterval(t4)
		}
		getOverdueData()
		showDOM(overdueData);
		t4=setInterval('getOverdueData()',1000);
		
	}
})

//==============获取数据==================
//全部
function getAllData(callback){
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/join',
	    data={customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id};
	$.ajax({
	        url:url,
	        data:data,
	        async:false,
	        type:'get',
	        dataType:'json',
	        success:function(res){
				console.log(res)
				xunhuan(res);
				allData=res;
	        },
	        error:function(res){
	            console.log('请求信息失败！！！');
	        },
	});
}

//进行中
function getIngData(){
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/joining',
	    data={customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id};
	$.ajax({
	        url:url,
	        data:data,
	        async:false,
	        type:'get',
	        dataType:'json',
	        success:function(res){
				console.log(res)
				xunhuan(res);
				ingData=res;
	        },
	        error:function(res){
	            console.log('请求信息失败！！！');
	        },
	});
}

// 已完成
function getCompelteData(){
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/join_buy',
	    data={customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id};
	$.ajax({
	        url:url,
	        data:data,
	        async:false,
	        type:'get',
	        dataType:'json',
	        success:function(res){
				console.log(res)
				xunhuan(res);
				compelteData=res;
	        },
	        error:function(res){
	            console.log('请求信息失败！！！');
	        },
	});

}

// 已过期
function getOverdueData(){
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/join_ed',
	    data={customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id};
	$.ajax({
	        url:url,
	        data:data,
	        async:false,
	        type:'get',
	        dataType:'json',
	        success:function(res){
				console.log(res)
				xunhuan(res);
				overdueData=res;
	        },
	        error:function(res){
	            console.log('请求信息失败！！！');
	        },
	});

}


function showDOM(res){
	$('.content li').remove()
	if(res.data==null || res.data.length==0 || res.data==false){

		$(".content").empty();
		var html='<div class="list-nodata">'+
			   '<img  src="img/kj_30.png" />'+
			   '<span>没有数据...</span>'+
			   '</div>';
		$('.content').append(html);	 

	
	   }else{
	   	
		var let=res.data.length;
	  $(".list-nodata").remove();
		   	for(var i=0;i<let;i++){
	   		var content=$('.content');
	   		console.log(res.data[i]['apply_id']);
	   		content.append('<li onclick="openParticipator('+res.data[i]['product_id']+','+res.data[i]['apply_id']+','+res.data[i]['activity_id']+')">'+
				'<div class="img"></div>'+
				'<div class="information">'+
					'<p class="xx_1">'+
						'<font>【'+res.data[i]['status']+'】</font>'+
						'<span>'+res.data[i]['product_name']+'</span>'+
					'</p>'+
					'<div class="xx_c">'+
						'<p class="xx_2">'+
							'<font class="title">最低价</font>'+
							'<span>￥<font>'+res.data[i]['min_price']+'</font></span>'+
						'</p>'+
						'<p class="xx_3">'+
							'(已帮<font class="card_id">'+res.data[i]['weixin_name']+'</font>砍价￥<font class="money">'+res.data[i]['bargain_price']+'</font>)'+
						'</p>'+
					'</div>	'+			
					'<div class="xx_z">'+
						'<div class="xx_4">'+
							'<div class="xx_4Jd"></div>'+
							'<div class="img1"></div>'+
							'<div class="img2"></div>'+
							'<div class="img3"></div>'+						
							'<div class="xx_4JdImg"></div>'+
						'</div>'+
						'<div class="xx_5">'+
							'<font class="linq">继续帮砍</font>'+
						'</div>'+
					'</div>'+
				'</div>'+
				'<p class="clear"></p>'+
			'</li>')
	   		$('.content li .img').eq(i).css('background-image','url('+res.data[i]['product_pic']+')')
	   	}  
	}  	
}

// 跳转报名页面
function  openParticipator(id,applyId,ativityId){
	// location.href='enrolled.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&id='+id+'&apply_id='+apply_id+'&activity_id='+ativityId+'&web_time='+web_time+'';
	location.href='participator.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&web_time='+web_time+'&activity_id='+ativityId+'&idx='+id+'&apply_id='+applyId+''
}

function xunhuan(res){
	if(res.data==null || res.data.length==0 || res.data==false){

		$(".content").empty();
		var html='<div class="list-nodata">'+
			   '<img  src="img/kj_30.png" />'+
			   '<span>没有数据...</span>'+
			   '</div>';
		$('.content').append(html);	 

	
	}else{
		var let=res.data.length;
	$(".list-nodata").remove();
            for(var i=0;i<let;i++){
            	
	        // 距离最低价还有
			var jlminprice=Number(res.data[i]['latest_price'])-Number(res.data[i]['min_price']);
			// 已砍了多少  orgin_price为原价
			var defprice=Number(res.data[i]['orgin_price'])-Number(res.data[i]['latest_price']);
			// 可购买价到最低价
			var chajia=Number(res.data[i]['buy_price'])-Number(res.data[i]['min_price']);
			// 原价到可购买价
			var buyprice=Number(res.data[i]['orgin_price'])-Number(res.data[i]['buy_price']);
			
			
			//前一半与后一半的计算速度不一样
			if(defprice>0){
				$('.xx_4 .img1').eq(i).css('border-color','#FD7D24');
				$('.xx_4 .img1').eq(i).css('background-image','url(img/kj_22.png)')
			}
			if(Number(res.data[i]['latest_price'])<=Number(res.data[i]['buy_price'])){
				$('.xx_4 .img2').eq(i).css('border-color','#FD7D24');
				$('.xx_4 .img2').eq(i).css('background-image','url(img/kj_12.png)');
			}
            if(defprice<buyprice){
//				$('.jinduXx_Gm').hide()
				var wit=(defprice/buyprice*100/2)+'%';
				console.log(wit);
				$('.xx_4JdImg').width(wit)
				setTimeout(function(){
					$(".content").css("display","block");
				},1000)
			}
			if(defprice>=buyprice){
//				$('.jinduXx_Gm').show();
				var wit=(50+((Number(res.data[i]['buy_price'])-Number(res.data[i]['latest_price']))/chajia*100)/2)+'%';
				console.log(wit);
				$('.xx_4JdImg').width(wit);
				setTimeout(function(){
					$(".content").css("display","block");
				},1000)
			};
			if(Number(res.data[i]['latest_price'])<=Number(res.data[i]['min_price'])){
				$('.xx_4 .img3').eq(i).css('border-color','#FD7D24')
				$('.xx_4 .img3').eq(i).css('background-image','url(img/kj_42.png)');
				
			}
			//是否已完成
			if(res.data[i]['status']=='已购买'||res.data[i]['status']=='已过期'){
				$('.xx_z').eq(i).hide()
				$('.xx_5').eq(i).hide()
				
			}
			
			
			//是否可以支付
			// if(Number(res.data[i]['latest_price'])<=Number(res.data[i]['buy_price'])){
			// 	$('.xx_5').eq(i).hide()
			// }
	    }  
	}  	
}