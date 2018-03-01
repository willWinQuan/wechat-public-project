/**---------------图片上传-------------**/
var list = {customer_id_en:customer_id_en,activity_id:activity_id};
var apply_id = getPar_WSY('apply_id');
var id = getPar_WSY('id');
var idx = getPar_WSY('idx');
if(idx){id=idx}
console.log(list)
console.log(id);
(function(){
    var url = HOST_WSY+'/weixinpl/haggling/back/index.php/home/wx/WxDisplay';
    $.ajax({
        url:url,
        data:list,
        type:'get',
        dataType:'json',
        success:function(res){
            console.log(res);
            wx.config({
                debug:false,               
                appId:res.appid,          
                timestamp:res.timestamp, 
                nonceStr:res.nonceStr,    
                signature:res.signature, 
                jsApiList:[ /*所有要调用的API都要加到这个列表中*/
                    'checkJsApi',    //判断当前客户端版本是否支持指定JS接口
                    'getLocation',   //获取地理位置
                    'chooseImage', //拍照或从手机相册中选图接口
                    'uploadImage', //上传图片接口
                    'onMenuShareAppMessage', //分享给朋友
                    'onMenuShareTimeline', //分享到朋友圈
                    'onMenuShareWeibo', //分享到腾讯微博
                    'onMenuShareQQ',  //分享到QQ
                    'previewImage', //预览图片接口
                ]     
            });
        }
        /*error:function(x){
            alert('请求失败！！！');
        }*/
    });
})();

/***分享内容***/
var share_title = ''; //'砍价狂欢日,买,买,买啊！',                            //分享标题
var share_desc = '';  //'各种价廉物美商品任你选,欢迎邀请朋友一起砍价购买！',  //分享描述
var share_img = '';   //HOST_WSY+'/weixinpl/haggling/front/web/img/adkj.jpg'; //分享图标
var share_type = ''; 
var title = '';
var data_information = '';
var exp_user_id = getPar_WSY('user_id_en');
var url_information = HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/information';
var share_url1 = HOST_WSY+'/weixinpl/common_shop/jiushop/forward.php?type=bargainIndex&customer_id_en='+customer_id_en+'&activity_id='+activity_id+'&exp_user_id='+exp_user_id+'&customer_id='+customer_id_en; //分享链接
var share_url2 = HOST_WSY+'/weixinpl/common_shop/jiushop/forward.php?type=bargainParticipator&customer_id_en='+customer_id_en+'&activity_id='+activity_id+'&exp_user_id='+exp_user_id+'&customer_id='+customer_id_en+'&idx='+id+'&apply_id='+apply_id;
//var share_url = HOST_WSY+'/weixinpl/haggling/front/web/participator.html?customer_id_en='+customer_id_en+'&activity_id='+activity_id+'&idx='+id+'&apply_id='+apply_id;  //分享链接
//var share_url = HOST_WSY+'/weixinpl/common_shop/jiushop/forward.php?type=bargainIndex&customer_id_en='+customer_id_en+'&activity_id='+activity_id+'&idx='+id+'&apply_id='+apply_id;

if(getPar_WSY('idx') && getPar_WSY('apply_id')){
    data_information = {customer_id_en:customer_id_en,activity_id:getPar_WSY("activity_id"),product_id:idx,action_id:apply_id};
    sharefn();
}else if(getPar_WSY('idx') && !getPar_WSY('apply_id')){
    data_information = {customer_id_en:customer_id_en,activity_id:getPar_WSY("activity_id"),product_id:idx};
    sharefn();
}else if(!getPar_WSY('idx') && getPar_WSY('apply_id')){
    data_information = {customer_id_en:customer_id_en,activity_id:getPar_WSY("activity_id"),action_id:apply_id};
    sharefn();
}else{
    data_information = {customer_id_en:customer_id_en,activity_id:getPar_WSY("activity_id")};
    sharefn();
}

function sharefn(){
	console.log(data_information);
$.ajax({
    url:url_information,
    data:data_information,
    //async:false,
    type:'get',
    dataType:'json',
    success:function(res){
        console.log(res);
        share_title = res.data.share_title;//'砍价狂欢日,买,买,买啊！';                        //分享标题
        share_desc = res.data.share_desc;//'各种价廉物美商品任你选,欢迎邀请朋友一起砍价购买！';  //分享描述
        share_img = res.data.share_img; //HOST_WSY+'/weixinpl/haggling/front/web/img/adkj.jpg'; //分享图标
        share_url = res.data.share_url;
        title=res.data.title;
        console.log(share_url);
        if(share_url.indexOf('index.html')!=-1){
            console.log(0)
            share_url=share_url1;
        }
        // if(share_url.indexOf('NoEnroll.html')!=-1){share_url=share_url1;}
        if(share_url.indexOf('participator.html')!=-1){
            console.log(1)
            share_url=share_url2;
        }
		console.log(share_url);
		console.log(share_title);
		console.log(share_desc);
		console.log(share_img);
        share();
    }
});
}

//活动标题赋值
$(function(){
	$('#index-title').html(title);
})

function share(){
wx.ready(function(){
    /*判断当前客户端版本是否支持指定JS接口*/
    wx.checkJsApi({
        jsApiList:[ //需要检测的JS接口列表
           'getLocation' 
        ], 
        success:function(res){ //返回JOSN (以键值对的形式返回,可用的api值true,不可用为false)
            //alert(JSON.stringify(res));
            //alert(JSON.stringify(res.checkResult.getLocation));
            if(res.checkResult.getLocation==false){
                alert('你的微信版本太低,不支持微信JS接口,请升级到最新的微信版本!');
            }
        }
    });
    //分享到朋友圈
    wx.onMenuShareTimeline({
        title:share_title,  //分享标题
        link:share_url,     //分享链接
        imgUrl:share_img,   //分享图标
        success:function(){ //用户确认分享后执行的回调函数
            if(getPar_WSY('id')==getPar_WSY('activity_id')){
                product_id=-1
            }else{
                product_id=getPar_WSY('id')
            }
            var data_forward={
                customer_id_en:getPar_WSY('customer_id_en'),
                activity_id:getPar_WSY('activity_id'),
                product_id:product_id,
            }
            var url_forward=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/forward'
            $.ajax({
                url:url_forward,
                data:data_forward,
                async:false,
                type:'get',
                dataType:'json',
                success:function(res){
                }
            });
        },
        cancel:function(){  //用户取消分享后执行的回调函数
            console.log('cancel');
        }
    });
    //分享给朋友
    wx.onMenuShareAppMessage({
        title:share_title,  //分享标题
        desc:share_desc,    //分享描述
        link:share_url,     //分享链接
        imgUrl:share_img,   //分享图标
        type:'link',        //分享类型,music、video或link,不填默认为link
        dataUrl:'link',     //如果type是music或video,则要提供数据链接,默认为空
        success:function(){ //用户确认分享后执行的回调函数
            console.log(share_url);
            // if(getPar_WSY('id')==getPar_WSY('activity_id')){
            //     product_id=-1
            // }else{
            //     product_id=getPar_WSY('id')
            // }
            // var data_forward={
            //     customer_id_en:getPar_WSY('customer_id_en'),
            //     activity_id:getPar_WSY('activity_id'),
            //     product_id:product_id
            // }
            // var url_forward=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/forward'
            // $.ajax({
            //     url:url_forward,
            //     data:data_forward,
            //     async:false,
            //     type:'get',
            //     dataType:'json',
            //     success:function(res){
            //     }
            // });
        },
        cancel:function(){ //用户取消分享后执行的回调函数
            console.log('cancel');
        }
    });

    //分享到QQ
    wx.onMenuShareQQ({
        title:share_title, //分享标题
        desc:share_desc, //分享描述
        link:share_url, //分享链接
        imgUrl:share_img, //分享图标
        success:function(){ 
           //用户确认分享后执行的回调函数
           console.log('success');
        },
        cancel: function () { 
           //用户取消分享后执行的回调函数
           console.log('cancel');
        }
    });
    
    //分享到腾讯微博
    wx.onMenuShareWeibo({
        title:share_title, //分享标题
        desc:share_desc, //分享描述
        link:share_url, //分享链接
        imgUrl:share_img, //分享图标
        success:function(){ 
            //用户确认分享后执行的回调函数
            console.log('success');
        },
        cancel: function () { 
            //用户取消分享后执行的回调函数
            console.log('cancel');
        }
    });

    //获取地理位置
    /*wx.getLocation({
        type:'wgs84', //默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
        success:function(res){
            latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
            longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
            var speed = res.speed; // 速度，以米/每秒计
            var accuracy = res.accuracy; // 位置精度
            //alert(longitude+'---'+latitude);
        }
    });*/
});
}

function picture_upload(obj){
    wx.chooseImage({
        count: 1, // 默认9
        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
        success: function (res) {
            var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
            // 上传照片
            wx.uploadImage({
                localId: '' + localIds,
                isShowProgressTips: 1,
                success: function(res) {
                	var ids=$(obj).attr('data-id')
                    var id=$(obj).children('img').attr('id');
                    
                    serverId = res.serverId;
                    lit={
                        photo_id_en:id,
                        serverId:serverId,
                        user_id_en:user_id_en,
                        customer_id_en:customer_id_en,
                        class:ids
                    };
                    $.post(HOST_WSY+'/weixinpl/yiren/back/index.php/home/index/test_img/serverId',lit,function(data){                        
                        //申请页面
                        $(obj).children('.img').attr('id',data.photo_id_en);
                        $(obj).children('.img').attr('src', data.url);
                        //我的艺人上传图片
                        $(obj).parent(".my_artist_B").children(".wrapper").children("ul").prepend("<li><img src="+data.url+" pic_id="+data.photo_id_en+"></li>");
                        var ele = $(".wrapper ul");
                            ele.width((ele.find("li img").length) * (ele.find("li").width()+5));
                        var myScroll = new IScroll('.wrapper', {scrollX: true, scrollY: false});
                        //我的信息上传头像
                        $(obj).children('#tx').attr('src', data.url);
                    });
                }
            });
        }
    });
}
function yr_pic_look_picture(obj){
    var imgsurl=new Array();
    for(var i=0;i<$("#picture ul li").length;i++){
        imgsurl[i]=$("#picture ul li").eq(i).children("img").eq(1).attr("src");
    }
    var nowurl=$(obj).attr("src");
    console.log(imgsurl);
    console.log(nowurl);
    console.log(obj);
    console.log($(obj).index());
    wx.previewImage({  
        current: nowurl,  
        urls: imgsurl  
    });               
}
function xq_pic_look_picture(obj){
    var imgsurl=new Array();
    for(var i=0;i<$("#picture ul li").length;i++){
        imgsurl[i]=$("#picture ul li").eq(i).children("img").attr("src");
    }
    var nowurl=$(obj).attr("src");
    console.log(imgsurl);
    console.log(nowurl);
    console.log(obj);
    console.log($(obj).index());
    wx.previewImage({  
        current: nowurl,  
        urls: imgsurl  
    });               
} 
