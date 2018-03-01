var id= getPar_WSY('id');
//全局变量
var let=0;
//tab切换
$('.explaintext').click(function(){
	$('.explaintext').removeClass('xuanze')
	var index=$(this).attr('data-id');
	$(this).addClass('xuanze')
	$(this).siblings().removeClass('xuanze')
	$('.explainC1').eq(index).show()
	$('.explainC1').eq(index).siblings().hide()
})

function code(){
	$(document).bind('touchmove',function(event) { 
    	event.preventDefault(); 
    })
	 $(document.body).css({
	   "overflow-x":"hidden",
	   "overflow-y":"hidden"
 	});
	var let=$('body').height();
	$('.mask').height(let);
	$('.QRcode').show()
}
//关闭二维码
function shut(){
	$('.QRcode').hide();
	$(document).unbind('touchmove');
	$('.mask').height(let)
	 $(document.body).css({
	   "overflow-x":"auto",
	   "overflow-y":"auto"
 	});
}
//关闭提示
function guanbi(){
	$('.promptBox1').hide();
	$('.instructions').hide();
	$(document).unbind('touchmove');
	$('.mask').height(let)
	 $(document.body).css({
	   "overflow-x":"auto",
	   "overflow-y":"auto"
 	});
}
//支付框
function payOpen(){
	$(document).bind('touchmove',function(event) { 
		event.preventDefault(); 
	})
	$(document.body).css({
		"overflow-x":"hidden",
		"overflow-y":"hidden"
	});
	var let=$('body').height();
	$('.mask').height(let);
	$('.payTk').css('visibility','visible')
	$('.payTk').css('bottom','0')
}
function payEsc(){
	$('.payTk').css('visibility','hidden')
	$('.payTk').css('bottom','-5rem')
	$('.mask').height(let)
	$(document).unbind('touchmove');
	$('.mask').height(let)
	 $(document.body).css({
	   "overflow-x":"auto",
	   "overflow-y":"auto"
 	});
}
//个人中心
function personal(){
	location.href = '/weixinpl/mshop/personal_center.php?customer_id='+customer_id_en;
}
//视频
function interflow(){
	var t='商家未提供语音及视频'
	$(document).bind('touchmove',function(event) { 
	    event.preventDefault(); 
	})
	$(document.body).css({
		"overflow-x":"hidden",
		"overflow-y":"hidden"
	});
	var let=$('body').height();
	$('.mask').height(let);
	$('.promptBox2').show();
	$('.promptBox2 .BoxB p').html(t)
}
//公共弹框
//
function tkshow(t){
	var let=$('body').height();
	$('.mask').height(let);
	$('.promptBox2').show();
	$('.promptBox2 .BoxB p').html(t)
	$(document).bind('touchmove',function(event) { 
	    event.preventDefault(); 
	})
	$(document.body).css({
		"overflow-x":"hidden",
		"overflow-y":"hidden"
	});
}
function tkhide(){
	var let=0;
	$('.promptBox2').hide();
	$('.promptBox').hide();
	$(document).unbind('touchmove');
	$('.mask').height(let)
	$(document.body).css({
		"overflow-x":"auto",
		"overflow-y":"auto"
	});
}
function tkshow2(t){
	var let=$('body').height();
	$('.mask').height(let);
	$('.promptBox').show();
	$('.BoxC p').html(t)
	$(document).bind('touchmove',function(event) { 
	    event.preventDefault(); 
	})
	$(document.body).css({
		"overflow-x":"hidden",
		"overflow-y":"hidden"
	});
}
