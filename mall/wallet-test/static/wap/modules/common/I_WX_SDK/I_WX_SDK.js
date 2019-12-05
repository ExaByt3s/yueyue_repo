define('common/I_WX_SDK/I_WX_SDK', function(require, exports, module){ /**
* 微信 接口
*/
var utility = require('common/utility/index');
//微信debug模式开关 true | false
var WeiXinDebugMode = false;

var WeiXinSDK =
{
    version : 'default'
};

// Config_data格式 ：
// Config_data =
// {
//      appId:appId，
//      timestamp:timestamp，
//      nonceStr:nonceStr，
//      signature:signature
// }
WeiXinSDK.setConfig = function(Config_data)
{

    var data = Config_data;

    wx.config({
        debug: WeiXinDebugMode,   // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: data.appId,        // 必填，公众号的唯一标识
        timestamp: data.timestamp,// 必填，生成签名的时间戳
        nonceStr: data.nonceStr,  // 必填，生成签名的随机串
        signature: data.signature,// 必填，签名，见附录1
        jsApiList: [
            'checkJsApi',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow',
            'scanQRCode',
            'chooseWXPay',
            'openProductSpecificView'
        ] // 必填，需要使用的JS接口列表

    });
};

//ready回调
WeiXinSDK.ready = function(callback)
{
    wx.ready(function()
    {
        var Api = this;

        if(typeof callback == 'function')
        {
            callback.call(this,Api);
        }
    });

};

WeiXinSDK.ShareToFriend = function(Share_data)
{
    var data = Share_data;

    console.log(Share_data);

    wx.onMenuShareAppMessage
    ({
        title: Share_data.title, // 分享标题
        desc: Share_data.desc, // 分享描述
        link: Share_data.link, // 分享链接
        imgUrl: Share_data.imgUrl, // 分享图标
        type: Share_data.type, // 分享类型,music、video或link，不填默认为link
        dataUrl: Share_data.dataUrl, // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // 用户确认分享后执行的回调函数
            if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
        }
    })
};

WeiXinSDK.ShareTimeLine = function(Share_data)
{
    var data = Share_data;

    wx.onMenuShareTimeline({
        title: Share_data.title, // 分享标题
        link: Share_data.link, // 分享链接
        imgUrl: Share_data.imgUrl, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
            if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
        }
    });
};

WeiXinSDK.ShareQQ = function(Share_data)
{
    var data = Share_data;

    wx.onMenuShareQQ({
        title: Share_data.title, // 分享标题
        desc: Share_data.desc, // 分享描述
        link: Share_data.link, // 分享链接
        imgUrl: Share_data.imgUrl, // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
            if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
            if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
        }
    });
};

WeiXinSDK.isWeiXin = function ()
{
    return /MicroMessenger/i.test(navigator.userAgent);
};

WeiXinSDK.hideOptionMenu = function()
{
    wx.hideOptionMenu();
};

WeiXinSDK.showOptionMenu = function()
{
    wx.showOptionMenu();

};

WeiXinSDK.getLocation = function(options)
{
    wx.getLocation({
        success: function (res) {
            //alert("success" + JSON.stringify(res))
            if(options.success && typeof options.success === 'function')options.success(res);
        },
        cancel: function (res) {
            //alert("cancel" + JSON.stringify(res))
            if(options.cancel && typeof options.cancel === 'function')options.cancel(res);
        },
        fail : function(res){
            //alert("fail" + JSON.stringify(res))
            if(options.fail && typeof options.fail === 'function')options.fail(res);
        }
    });
};

/**
 * 调起微信Native的图片播放组件。
 * 这里必须对参数进行强检测，如果参数不合法，直接会导致微信客户端crash
 *
 * @param {String} curSrc 当前播放的图片地址
 * @param {Array} srcList 图片地址列表
 */
WeiXinSDK.imagePreview = function (curSrc, srcList)
{

    if (!curSrc || !srcList || srcList.length == 0) {
        return;
    }
    wx.previewImage
    ({
        current: curSrc, // 当前显示图片的http链接
        urls: srcList // 需要预览的图片http链接列表
    });
};

WeiXinSDK.chooseWXPay = function(data,success,fail)
{
    if(data.appId)
    {
        delete data.appId;
    }

    data.timestamp = data.timeStamp;

    var callback =
    {
        success: function(res)
        {
            var code = 0;

            var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?err_level=1&url='+encodeURIComponent(window.location.href);

            var img = new Image();

            if( res.errMsg == 'chooseWXPay:ok' )
            {
                //支付成功
                code = 1;
            }
            else if( res.errMsg == 'chooseWXPay:cancel' )
            {
                //支付过程中
                code = 10;
            }
            else if( res.errMsg == 'chooseWXPay:fail' )
            {
                //支付失败
                code = -3;

                img.src = err_log_src+'&err_str='+encodeURIComponent(res.errMsg);

                console.log('url='+window.location.href+'&err_str='+res.errMsg);

                alert("支付失败:"+res.err_msg);

            }
            else
            {
                img.src = err_log_src+'&err_str='+encodeURIComponent(res.errMsg);

                console.log('url='+window.location.href+'&err_str='+res.errMsg);

                alert("支付失败，由于网络问题提交失败，请点击左上角关闭并重新进入");
            }


            if (typeof success == "function")
            {
                success.call(this, {code : code});
            }
        },
        fail: function(res)
        {
            console.log(res);



            if (typeof fail == "function")
            {
                fail.call(this, res);
            }
        },complete : function(res)
        {

            debugger;
        },
        cancel : function(res)
        {

            debugger;
        }
    };

    data = $.extend({},data,callback);

    wx.chooseWXPay(data);

};

WeiXinSDK.chooseImage = function(options)
{
    wx.chooseImage({
        count: options.count, // 默认9
        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
        success: function (res) {
            //alert("success" + JSON.stringify(res))
            if(options.success && typeof options.success === 'function')options.success(res);
            //var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图
        },
        cancel: function (res) {
            //alert("cancel" + JSON.stringify(res))
            if(options.cancel && typeof options.cancel === 'function')options.cancel(res);
        },
        fail : function(res){
            //alert("fail" + JSON.stringify(res))
            if(options.fail && typeof options.fail === 'function')options.fail(res);
        }
    });
}

WeiXinSDK.upload_imgs = function(options)
{
    var index = 0;

    var pics_length = options.localId.length;

    var ProgressTips =  options.isShowProgressTips || 1;// 默认为1，显示进度提示

    uploadSingle(options);

    var media_list = [];

    function uploadSingle(options)
    {
        wx.uploadImage({
            localId: options.localId[index], // 需要上传的图片的本地ID，由chooseImage接口获得
            isShowProgressTips: ProgressTips,
            success: function (res) {
                // alert("success" + JSON.stringify(res))
                var obj =
                {
                    localId : res.localId,
                    media_id : res.serverId
                }

                media_list.push(obj);

                if(options.success_single && typeof options.success_single === 'function')options.success_single(res,index,pics_length);

                index++;

                if(index >= options.localId.length)
                {
                    //结束所有传图后的回调
                    if(options.success_all && typeof options.success_all === 'function')options.success_all(media_list,index,pics_length);
                }
                else
                {
                    uploadSingle(options);
                }
            },
            cancel: function (res) {
                //alert("cancel" + JSON.stringify(res))
                if(options.cancel_single && typeof options.cancel_single === 'function')options.cancel_single(res,index,pics_length);
            },
            fail : function(res){
                //alert("fail" + JSON.stringify(res))
                if(options.fail_single && typeof options.fail_single === 'function')options.fail_single(res,index,pics_length);
            }
        });
    }
}

WeiXinSDK.downloadImage = function(options)
{
    if(!options.media_id)throw 'serverId/media_Id error!';

    wx.downloadImage({
        serverId: options.media_id, // 需要下载的图片的服务器端ID，由uploadImage接口获得
        isShowProgressTips: options.isShowProgressTips || 1, // 默认为1，显示进度提示
        success: function (res) {
            var localId = res.localId; // 返回图片下载后的本地ID
            if(options.success && typeof options.success === 'function')options.success(res);
        },
        cancel: function (res) {
            //alert("cancel" + JSON.stringify(res))
            if(options.cancel && typeof options.cancel === 'function')options.cancel(res);
        },
        fail : function(res){
            //alert("fail" + JSON.stringify(res))
            if(options.fail && typeof options.fail === 'function')options.fail(res);
        }
    });
}

WeiXinSDK.chooseImages_and_uploadImages_and_downloadImages = function(options)
{
    var upload_type = options.upload_type || "pics",
        choose_trigger_str = options.choose_trigger_str || "chooseImages",
        choose_count = parseInt(options.choose_count) || 9,
        choose_success = options.choose_success || function(){},
        choose_cancel = options.choose_cancel || function(){},
        choose_fail = options.choose_fail || function(){},
        upload_trigger_str = options.upload_trigger_str || "uploadImages",
        upload_success = options.upload_success || function(){},
        upload_cancel = options.upload_cancel || function(){},
        upload_fail = options.upload_fail || function(){},
        upload_success_all = options.upload_success_all || function(){},
        get_trigger_str = options.get_trigger_str || "getImagesUrl",
        get_imgUrl_beforeSend = options.get_imgUrl_beforeSend || function(){},
        get_imgUrl_success = options.get_imgUrl_success || function(){},
        get_imgUrl_error = options.get_imgUrl_error || function(){},
        get_imgUrl_complete = options.get_imgUrl_complete || function(){}

    var choosen_list = [];

    var media_list = [];

    $(WeiXinSDK).on(choose_trigger_str,function()
    {
        var pic_list = [];

        WeiXinSDK.chooseImage
        ({
            count : choose_count, //选图张数，拍照默认1张
            success : function(res)
            {
                //alert("success" + JSON.stringify(res))
                //res.sourceType 两种情况 : 'album' 相册  || 'camera' 相机
                //res.localIds 回调的图片数组
                pic_list = res.localIds;

                choosen_list = pic_list;

                if(choose_success && typeof choose_success === 'function')choose_success(res);
            },
            cancel : function(res)
            {
                if(choose_cancel && typeof choose_cancel === 'function')choose_cancel(res);
            },
            fail : function(res)
            {
                if(choose_fail && typeof choose_fail === 'function')choose_fail(res);
            }
        });
    })

    $(WeiXinSDK).on(upload_trigger_str,function()
    {
        if(choosen_list.length != 0)
        {
            WeiXinSDK.upload_imgs
            ({
                localId : choosen_list, //传入localId数组[]
                success_single : function(resa,index)
                {
                    var serverId = resa.serverId; // 返回图片的服务器端ID
                    //alert("success" + JSON.stringify(resa))
                    if(upload_success && typeof upload_success === 'function')upload_success(resa,index,choosen_list.length);
                },
                cancel_single : function(resa,index)
                {
                    if(upload_cancel && typeof upload_cancel === 'function')upload_cancel(resa,index,choosen_list.length);
                },
                fail_single : function(resa,index)
                {
                    if(upload_fail && typeof upload_fail === 'function')upload_fail(resa,index,choosen_list.length);
                },
                success_all : function(media_obj_list,index)
                {
                    media_list = media_obj_list
                    //返回对象数组[{localId:"",mediaId:""},{},{}] mediaId用于下载该图
                    //alert("all" + JSON.stringify(media_obj_list))
                    if(upload_success_all && typeof upload_success_all === 'function')upload_success_all(media_obj_list,index,choosen_list.length);

                }
            })
        }
    })

    $(WeiXinSDK).on(get_trigger_str,function()
    {
        if(media_list.length != 0)
        {
            utility.ajax_request
            ({
                url: window.$__ajax_domain + 'wx_image.php',
                data : {obj_list:media_list,upload_type:upload_type},
                dataType: 'JSON',
                type: 'POST',
                cache: false,
                beforeSend: function()
                {
                    if(get_imgUrl_beforeSend && typeof get_imgUrl_beforeSend === 'function')get_imgUrl_beforeSend();
                },
                success: function(data)
                {
                    if(get_imgUrl_success && typeof get_imgUrl_success === 'function')get_imgUrl_success(data);
                },
                error: function(err)
                {
                    if(get_imgUrl_error && typeof get_imgUrl_error === 'function')get_imgUrl_error(err);
                },
                complete: function()
                {
                    if(get_imgUrl_complete && typeof get_imgUrl_complete === 'function')get_imgUrl_complete();
                }
            });

        }
    })
}

module.exports = WeiXinSDK; 
});