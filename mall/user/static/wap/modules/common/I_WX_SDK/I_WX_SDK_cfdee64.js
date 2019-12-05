define("common/I_WX_SDK/I_WX_SDK",function(e,n,c){var o=e("common/utility/index"),s=!1,t={version:"default"};t.setConfig=function(e){var n=e;wx.config({debug:s,appId:n.appId,timestamp:n.timestamp,nonceStr:n.nonceStr,signature:n.signature,jsApiList:["checkJsApi","chooseImage","previewImage","uploadImage","onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","hideMenuItems","showMenuItems","hideAllNonBaseMenuItem","showAllNonBaseMenuItem","getNetworkType","openLocation","getLocation","hideOptionMenu","showOptionMenu","closeWindow","scanQRCode","chooseWXPay","openProductSpecificView"]})},t.ready=function(e){wx.ready(function(){var n=this;"function"==typeof e&&e.call(this,n)})},t.ShareToFriend=function(e){console.log(e),wx.onMenuShareAppMessage({title:e.title,desc:e.desc,link:e.link,imgUrl:e.imgUrl,type:e.type,dataUrl:e.dataUrl,success:function(){e.success&&"function"==typeof e.success&&e.success()},cancel:function(){e.cancel&&"function"==typeof e.cancel&&e.cancel()}})},t.ShareTimeLine=function(e){wx.onMenuShareTimeline({title:e.title,link:e.link,imgUrl:e.imgUrl,success:function(){e.success&&"function"==typeof e.success&&e.success()},cancel:function(){e.cancel&&"function"==typeof e.cancel&&e.cancel()}})},t.ShareQQ=function(e){wx.onMenuShareQQ({title:e.title,desc:e.desc,link:e.link,imgUrl:e.imgUrl,success:function(){e.success&&"function"==typeof e.success&&e.success()},cancel:function(){e.cancel&&"function"==typeof e.cancel&&e.cancel()}})},t.isWeiXin=function(){return/MicroMessenger/i.test(navigator.userAgent)},t.hideOptionMenu=function(){wx.hideOptionMenu()},t.showOptionMenu=function(){wx.showOptionMenu()},t.getLocation=function(e){wx.getLocation({success:function(n){e.success&&"function"==typeof e.success&&e.success(n)},cancel:function(n){e.cancel&&"function"==typeof e.cancel&&e.cancel(n)},fail:function(n){e.fail&&"function"==typeof e.fail&&e.fail(n)}})},t.imagePreview=function(e,n){e&&n&&0!=n.length&&wx.previewImage({current:e,urls:n})},t.chooseWXPay=function(e,n,c){e.appId&&delete e.appId,e.timestamp=e.timeStamp;var o={success:function(e){var c=0,o="http://www.yueus.com/mobile_app/log/save_log.php?err_level=1&url="+encodeURIComponent(window.location.href),s=new Image;"chooseWXPay:ok"==e.errMsg?c=1:"chooseWXPay:cancel"==e.errMsg?c=10:"chooseWXPay:fail"==e.errMsg?(c=-3,s.src=o+"&err_str="+encodeURIComponent(e.errMsg),console.log("url="+window.location.href+"&err_str="+e.errMsg),alert("支付失败:"+e.err_msg)):(s.src=o+"&err_str="+encodeURIComponent(e.errMsg),console.log("url="+window.location.href+"&err_str="+e.errMsg),alert("支付失败，由于网络问题提交失败，请点击左上角关闭并重新进入")),"function"==typeof n&&n.call(this,{code:c})},fail:function(e){console.log(e),"function"==typeof c&&c.call(this,e)},complete:function(){},cancel:function(){}};e=$.extend({},e,o),wx.chooseWXPay(e)},t.chooseImage=function(e){wx.chooseImage({count:e.count,sizeType:["original","compressed"],sourceType:["album","camera"],success:function(n){e.success&&"function"==typeof e.success&&e.success(n)},cancel:function(n){e.cancel&&"function"==typeof e.cancel&&e.cancel(n)},fail:function(n){e.fail&&"function"==typeof e.fail&&e.fail(n)}})},t.upload_imgs=function(e){function n(e){wx.uploadImage({localId:e.localId[c],isShowProgressTips:s,success:function(s){var i={localId:s.localId,media_id:s.serverId};t.push(i),e.success_single&&"function"==typeof e.success_single&&e.success_single(s,c,o),c++,c>=e.localId.length?e.success_all&&"function"==typeof e.success_all&&e.success_all(t,c,o):n(e)},cancel:function(n){e.cancel_single&&"function"==typeof e.cancel_single&&e.cancel_single(n,c,o)},fail:function(n){e.fail_single&&"function"==typeof e.fail_single&&e.fail_single(n,c,o)}})}var c=0,o=e.localId.length,s=e.isShowProgressTips||1;n(e);var t=[]},t.downloadImage=function(e){if(!e.media_id)throw"serverId/media_Id error!";wx.downloadImage({serverId:e.media_id,isShowProgressTips:e.isShowProgressTips||1,success:function(n){n.localId;e.success&&"function"==typeof e.success&&e.success(n)},cancel:function(n){e.cancel&&"function"==typeof e.cancel&&e.cancel(n)},fail:function(n){e.fail&&"function"==typeof e.fail&&e.fail(n)}})},t.chooseImages_and_uploadImages_and_downloadImages=function(e){var n=e.upload_type||"pics",c=e.choose_trigger_str||"chooseImages",s=parseInt(e.choose_count)||9,i=e.choose_success||function(){},u=e.choose_cancel||function(){},l=e.choose_fail||function(){},a=e.upload_trigger_str||"uploadImages",f=e.upload_success||function(){},r=e.upload_cancel||function(){},p=e.upload_fail||function(){},g=e.upload_success_all||function(){},d=e.get_trigger_str||"getImagesUrl",_=e.get_imgUrl_beforeSend||function(){},m=e.get_imgUrl_success||function(){},h=e.get_imgUrl_error||function(){},y=e.get_imgUrl_complete||function(){},w=[],I=[];$(t).on(c,function(){var e=[];t.chooseImage({count:s,success:function(n){e=n.localIds,w=e,i&&"function"==typeof i&&i(n)},cancel:function(e){u&&"function"==typeof u&&u(e)},fail:function(e){l&&"function"==typeof l&&l(e)}})}),$(t).on(a,function(){0!=w.length&&t.upload_imgs({localId:w,success_single:function(e,n){e.serverId;f&&"function"==typeof f&&f(e,n,w.length)},cancel_single:function(e,n){r&&"function"==typeof r&&r(e,n,w.length)},fail_single:function(e,n){p&&"function"==typeof p&&p(e,n,w.length)},success_all:function(e,n){I=e,g&&"function"==typeof g&&g(e,n,w.length)}})}),$(t).on(d,function(){0!=I.length&&o.ajax_request({url:window.$__ajax_domain+"wx_image.php",data:{obj_list:I,upload_type:n},dataType:"JSON",type:"POST",cache:!1,beforeSend:function(){_&&"function"==typeof _&&_()},success:function(e){m&&"function"==typeof m&&m(e)},error:function(e){h&&"function"==typeof h&&h(e)},complete:function(){y&&"function"==typeof y&&y()}})})},c.exports=t});