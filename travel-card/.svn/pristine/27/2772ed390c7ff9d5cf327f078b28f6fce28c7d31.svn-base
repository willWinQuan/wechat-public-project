/*--------------------分享-------------------------------*/
var appId,
timestamp,
nonceStr,
signature,
debug,
share_type=4,
new_share_url,//微信分享链接
share_url,//app分享链接
imgUrl;


var getshare_val=(function(){
	var data={
		
	};
	var url='/o2o/web/index.php?m=travel_card&a=getapi_param';
	getJSON('POST',data,url).then(function(res){
	   if(res.errcode !=1000){
//			alert(JSON.stringify(res.errmsg));
			return;
		};
        
		debug=false;
		appId=res.appId;
		timestamp=res.timestamp;
		nonceStr=res.nonceStr;
		signature=res.signature;
		new_share_url=res.new_share_url;
		share_url=res.linkurl;
		imgUrl=res.cover_img,
		         
		wx.config({
                debug:false,               
                appId:res.appId,          
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
        
        //判断是否未定义
		if(typeof(debug)=="undefined"){
			debug="";
		}
		if(typeof(share_type_true)=="undefined"){//是否需要分享按钮
			share_type=3;
		}

        //调用分享集成函数
		new_share(debug,appId,timestamp,nonceStr,signature,share_url,imgUrl,share_type,new_share_url);
	
	}).catch(function(errContent){
		console.log(errContent);
	});	
	
})();




var app_share_data = {};


function new_share(debug,appId,timestamp,nonceStr,signature,share_url,imgUrl,share_type,new_share_url){
/*参数说明
debug：调试模式，true开启， false关闭
share_url：分享的链接
title：分享的标题
desc：分享内容（仅分享给用户）
imgUrl：分享的LOGO
new_share_url 分享QQ QQ空间 QQ微博的链接

share_type:菜单类型
	-1：显示所有，除去复制链接以及查看公众号。
	1 ：只显示 发送给朋友，分享到朋友圈，收藏，刷新，调整字体，投诉。
	2 ：只显示 发送给朋友，分享到朋友圈，分享到QQ，分享到QQ空间，收藏，刷新，调整字体，投诉。
	3 : 只显示收藏，刷新，调整字体，投诉。
*/
//	alert(new_share_url)
	//app分享
	app_share_data = {
			appId: appId,
			timestamp: timestamp,
			nonceStr: nonceStr,
			signature: signature,
			share_url: share_url,
//			title: title,
//			desc: desc,
			imgUrl: imgUrl,
			share_type: share_type,
			new_share_url: new_share_url
		}
	
	//app分享
	var showmenuarr;
	switch(share_type){

		case -1:
			showmenuarr=["menuItem:share:QZone","menuItem:share:qq","menuItem:originPage","menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:appMessage","menuItem:share:timeline","menuItem:refresh","menuItem:favorite"];
			break;
		case 1:
			showmenuarr=["menuItem:share:appMessage","menuItem:share:timeline","menuItem:refresh","menuItem:favorite"];
			break;
		case 2:
			showmenuarr=["menuItem:share:appMessage","menuItem:share:timeline","menuItem:refresh","menuItem:favorite","menuItem:share:QZone","menuItem:share:qq"];
			break;
		case 3:
			showmenuarr=["menuItem:refresh","menuItem:favorite"];
			break;
		case 4:
			showmenuarr=["menuItem:share:appMessage","menuItem:share:timeline","menuItem:refresh","menuItem:favorite",,"menuItem:share:qq","menuItem:share:QZone"];
			break;
		default :
			showmenuarr=["menuItem:share:QZone","menuItem:share:qq","menuItem:originPage","menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:appMessage","menuItem:share:timeline","menuItem:refresh","menuItem:favorite"];
			break;
	}
	
	window.wx && wx.config({
                debug: debug,
                appId: appId,
                timestamp: timestamp,
                nonceStr: nonceStr,
                signature: signature,
                jsApiList: [
                    "previewImage",			//微信预览接口
					'onMenuShareTimeline',
					'onMenuShareAppMessage',
					'onMenuShareQQ',
					'onMenuShareWeibo',
					'onMenuShareQZone',
					'showMenuItems',
					'hideMenuItems'
                ]
            });

		// window.scrollTo(0,0);	//影响到产品搜索列表，暂时先注释了

		wx.ready(function () {
			// 在这里调用 API
				wx.hideMenuItems({
					menuList: [
						"menuItem:share:qq",//隐藏分享到QQ
						"menuItem:share:QZone",//隐藏分享到QQ空间	
						'menuItem:copyUrl', // 复制链接
						"menuItem:originPage",//原网页
						"menuItem:readMode",//阅读模式
						"menuItem:openWithQQBrowser",//在QQ浏览器中打开
						"menuItem:openWithSafari", //在Safari中打开
						"menuItem:share:appMessage",
						"menuItem:share:timeline",
					], // 要隐藏的菜单项，所有menu项见附录3

					success: function () { 
					},
					cancel: function () { 
					}			
				});
			wx.showMenuItems({
				/*menuList: [
					"menuItem:share:appMessage",
					"menuItem:share:timeline",				
					"menuItem:refresh"				
				],*/
				menuList: showmenuarr,
				success: function () { 
				},
				cancel: function () { 
				}			
			});
				
			wx.onMenuShareTimeline({
//				title: title, // 分享标题
				link: share_url, // 分享链接
				imgUrl: imgUrl, // 分享图标
				success: function () { 
					// 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
				
			wx.onMenuShareAppMessage({
//				title: title, // 分享标题
//				desc: desc, // 分享描述
				link: share_url, // 分享链接
				imgUrl: imgUrl, // 分享图标
				type: '', // 分享类型,music、video或link，不填默认为link
				dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				success: function () { 
					// 用户确认分享后执行的回调函数
					
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
			
			wx.onMenuShareQQ({
//				title: title, // 分享标题
//				desc: desc, // 分享描述
				link: new_share_url, // 分享链接
				imgUrl: imgUrl, // 分享图标
				success: function () { 
				   // 用户确认分享后执行的回调函数
				},
				cancel: function () { 
				   // 用户取消分享后执行的回调函数
				}
			});
			
			wx.onMenuShareWeibo({
//				title: title, // 分享标题
//				desc: desc, // 分享描述
				link: new_share_url, // 分享链接
				imgUrl: imgUrl, // 分享图标
				success: function () { 
				   // 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
			
			wx.onMenuShareQZone({
//				title: title, // 分享标题
//				desc: desc, // 分享描述
				link: new_share_url, // 分享链接
				imgUrl: imgUrl, // 分享图标
				success: function () { 
				   // 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
			
		});
	 
}


	

