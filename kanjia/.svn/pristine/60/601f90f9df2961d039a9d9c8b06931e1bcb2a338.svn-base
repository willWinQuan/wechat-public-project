$('.wsy_nav li').click(function(){
	$(this).addClass('WSY_buttontj')
	$(this).siblings().removeClass('WSY_buttontj')
});
/*删除*/
$('.evaluateNav li:nth-child(3)').click(function(){
	$('.WSYLabel div').addClass('WSYLabelD')
	$('.WSYLabel div').removeClass('WSYLabelX')
	$('.WSYLabel div').attr('contenteditable','false')
	if($('.WSYLabel div img').length<=0){
		$('.WSYLabel div').append('<img src="img/del.png" />')
	};
	$('.WSYLabel div').click(function(){
		if($('.WSYLabel div img').length>0){
			$(this).remove()
		}
	})
})
/*编辑*/
$('.evaluateNav li:nth-child(2)').click(function(){
	$('.WSYLabel div').addClass('WSYLabelX')
	$('.WSYLabel div img').remove()
	$('.WSYLabel div').attr('contenteditable','true')
})
/*添加*/
$('.evaluateNav li:nth-child(1)').click(function(){
	$('.WSYLabel div').removeClass('WSYLabelX')
	$('.WSYLabel div').removeClass('WSYLabelD')
	$('.WSYLabel div img').remove()
	$('.WSYLabel div').attr('contenteditable','false')
	if($('.evaluate input').val()!=''){
		var text=$('.evaluate input').val()
		$('.WSYLabel').append('<div class="WSYLabelDiv"></div>')
		$('.WSYLabel div:last-child').html(text)
		
	}
	$('.evaluate input').val('')
})
/*导航*/
$('.WSY_columnnav a').click(function(){
	if($(this).hasClass('white1')){
		
	}else{
		$(this).addClass('white1').siblings().removeClass('white1')
	}
})
/*艺人管理*/
$('.yirenGl tr').mouseout(function(){
	$(this).css('background','#f8f8f8')
})
$('.yirenGl tr').mouseover(function(){
	$(this).css('background','#e4e4e4')
})
/*切换*/
/*$('.WSY_columnnav a').click(function(){
	var index=$(this).prevAll().length;
	$('.zong .zong_t').eq(index).show()
	$('.zong .zong_t').eq(index).siblings().hide()
	$('.details ').hide()
})*/

$('.xiangqing').click(function(){
	$('.details ').show()
	$('.zong .zong_t').hide()
	$('.examine').hide()
});
$('.shenhe').click(function(){
	$('.examine').show()
	$('.zong .zong_t').hide()
	$('.details ').hide()
});

/*
$('.WSY_columnnav a').click(function(){
	var te=$(this).html()	
	location.href=""+te+".html";
});
$('.xiangqing').click(function(){
	location.href="详情.html";
});

$('.shenhe').click(function(){
	location.href="审核详情.html";
});*/


/*弹框*/
$('.yirenFormTe').click(function(){
	$('.yirenIdBox').show();
	$('.mask').show()
	$('.yrGl').css({'position':'absolute','z-index':'-9999999'});
	$('body').css({ 
             "overflow-x":"hidden",
             "overflow-y":"hidden"       
         });
})
/*保存*/
$('.yrBc').click(function(){
	$('body').css({ 
             "overflow-x":"auto",
             "overflow-y":"auto"       
         });
    $('.yirenIdBox').hide();
	$('.mask').hide()
})
/*取消*/
$('.yrQx').click(function(){
	$('body').css({ 
             "overflow-x":"auto",
             "overflow-y":"auto"       
         });
    $('.yirenIdBox').hide();
	$('.mask').hide()
})
/*
$('.wsy_nav li').click(function(){
	var text=$(this).html();
	if(text=='评价标签'){
		$('.evaluate').show()
		$('.shenqing').hide()
		$('.yhSz').hide()
		$('.Sfbq').hide()
	}
	if(text=='申请设置'){
		$('.evaluate').hide()
		$('.shenqing').show()
		$('.Sfbq').hide()
		$('.yhSz').hide()
	}
	if(text=='用户设置'){
		$('.evaluate').hide()
		$('.shenqing').hide()
		$('.Sfbq').hide()
		$('.yhSz').show()
	}
	if(text=='支付设置'){
		$('.evaluate').hide()
		$('.shenqing').hide()
		$('.yhSz').hide()
		$('.Sfbq').show()
	}
});
*/
/*滑动按钮*/
$('.box1').click(function(){
			if($(this).hasClass('right')){
				$(this).removeClass('right')
				$(this).animate({left:'0'})
			}else{
				$(this).addClass('right')
				$(this).animate({left:'28px'})
			}
});
/*编辑分类*/
$('.yirenClass .Flxg').click(function(){
	$('.classAdmini').hide()
	$('.flBj').show()
});
/*添加分类*/
$('.yirenFormTe').click(function(){
	$('.classAdmini').hide()
	$('.flTj').show()
});

/*审核管理二级菜单*/
$('.ShGl').mouseover(function(){
	$('.ShGl div').show();
});
$('.ShGl').mouseleave(function(){
	$('.ShGl div').hide();
});
/*放大图片*/

$('#show_picture .item_sf img').click(function(){
	$("html,body").animate({scrollTop:0}, 500);
	$('.mask').show();
	var lent=$('#show_picture .item_sf img').length;
	var b=Array();
	for(var i=0;i<lent;i++){
		var id=$('#show_picture .item_sf img').eq(i).attr('src');
		b.push(id)
	}
	var num = $(this).index('#show_picture .item_sf img');
	var lents=lent-1;
	$('#ZS #img').attr('src',b[num])
	$('#ZS').show()
	//上一张图片
		$('#ZS div .img1').click(function(){
			if(num>0){
				num--;
				$('#ZS #img').attr('src',b[num])
			}else{
				num=lents;
				$('#ZS #img').attr('src',b[num])
			}
		})
	//下一张图片	
	$('#ZS div .img2').click(function(){
		if(num<lents){
			num++;
			$('#ZS #img').attr('src',b[num])
		}else{
			num=0;
			$('#ZS #img').attr('src',b[num])
		}
		
	})
})

function big(obj){
	$("html,body").animate({scrollTop:0}, 500);
	$('.mask').show();
	var lent=$('.examineImgs.JXpic').length;
	var b=Array();
	for(var i=0;i<lent;i++){
		var id=$('.examineImgs.JXpic').eq(i).attr('src');
		b.push(id)
	}
	var num = $(obj).index('.examineImgs.JXpic');
	var lents=lent-1;
	$('#ZS #img').attr('src',b[num])
	$('#ZS').show()
	//上一张图片
		$('#ZS div .img1').click(function(){
			if(num>0){
				num--;
				$('#ZS #img').attr('src',b[num])
			}else{
				num=lents;
				$('#ZS #img').attr('src',b[num])
			}
		})
	//下一张图片	
	$('#ZS div .img2').click(function(){
		if(num<lents){
			num++;
			$('#ZS #img').attr('src',b[num])
		}else{
			num=0;
			$('#ZS #img').attr('src',b[num])
		}
		
	})
}
$('.mask').click(function(){
	$('#ZS').hide()
	$('.yirenIdBox').hide();
	$('.mask').hide()
})
/*视频审核*/
function shenhe(obj){
	if($(obj).html()=="待审核"){
		$('#videoTk').show()
	}
	var id=$(obj).attr('video_id')
	//通过
	$('.TG').click(function(){
		var status=1;
		video(status,id)
		$(obj).html('已通过')
	})
	//不通过
	$('.NoTG').click(function(){
		var status=2;
		video(status,id)
		$(obj).html('审核未通过')
	})
	//取消
	$('.ESC').click(function(){
		var status=0;
		video(status,id)
		$(obj).html('待审核')
	})
}
function video(status,id){
	data={
		customer_id:customer_id,
		id:id,
		status:status
	}
	$.ajax({
	    	url: HOST_WSY+'/weixinpl/yiren/back/index.php/workroom_admin/Artist/check_video',
	    	type:'get',
	    	data:data,
	    	dataType:'json',
	    	success: function (res) {
	    		console.log(res);
	    	}
	});
}

$('#videoTk font').click(function(){
	$('#videoTk').hide()
})

