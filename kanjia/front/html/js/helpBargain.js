//导航选择项
$('.headerLi').click(function(){
	$('.headerLi div').removeClass('choice')
	$(this).children('div').addClass('choice')
	var id=$(this).index()
	if(id==0){
		$('.img1').css('background-image','url(img/kj_04.png)')
		$('.img2').css('background-image','url(img/kj_06.png)')
		$('.img3').css('background-image','url(img/kj_08.png)')
		$('.img4').css('background-image','url(img/kj_10.png))')
	}
	if(id==1){
		$('.img1').css('background-image','url(img/kj_041.png)')
		$('.img2').css('background-image','url(img/kj_061.png)')
		$('.img3').css('background-image','url(img/kj_08.png)')
		$('.img4').css('background-image','url(img/kj_10.png))')
	}
	if(id==2){
		$('.img1').css('background-image','url(img/kj_041.png)')
		$('.img2').css('background-image','url(img/kj_06.png)')
		$('.img3').css('background-image','url(img/kj_081.png)')
		$('.img4').css('background-image','url(img/kj_10.png)')
	}
	if(id==3){
		$('.img1').css('background-image','url(img/kj_041.png)')
		$('.img2').css('background-image','url(img/kj_06.png)')
		$('.img3').css('background-image','url(img/kj_08.png)')
		$('.img4').css('background-image','url(img/kj_101.png)')
	}
})