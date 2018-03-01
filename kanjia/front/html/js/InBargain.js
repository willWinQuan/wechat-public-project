//轮播
var mySwiper = new Swiper(".swiper-container",{
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

/*点击收起规则说明*/
$('.ruleTitle img').click(function(){
	$('.ruleImg').toggle()
	$('.ruleExplain').toggle()
	var img=$(this).attr('src')
	if(img=='img/kj_32.png'){
		$(this).attr('src','img/kj_33.png')
	}else{
		$(this).attr('src','img/kj_32.png')
	}
})

//tab切换
function query(obj){
	$('.explaintext').removeClass('xuanze')
	var index=$(obj).index('.explaintext')
	$(obj).addClass('xuanze')
	$(obj).siblings().removeClass('xuanze')
	$('.explainC1').eq(index).show()
	$('.explainC1').eq(index).siblings().hide()
}
$('.FTbutton3').click(function(){
	$('.payTk').css('visibility','visible')
	$('.payTk').css('bottom','0')
})
$('.payTkEsc').click(function(){
	$('.payTk').css('visibility','hidden')
	$('.payTk').css('bottom','-5rem')
})