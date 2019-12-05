define('common/I_WX/I_WX', function(require, exports, module){ /**
* ΢�� �ӿ�
*/

//΢��debugģʽ���� true | false
var WeiXinDebugMode = false;

var WeiXinSDK =
{
    version : 'default'
};

// Config_data��ʽ ��
// Config_data =
// {
//      appId:appId��
//      timestamp:timestamp��
//      nonceStr:nonceStr��
//      signature:signature
// }
WeiXinSDK.setConfig = function(Config_data)
{

    var data = Config_data;

    wx.config({
        debug: WeiXinDebugMode,   // ��������ģʽ,���õ�����api�ķ���ֵ���ڿͻ���alert��������Ҫ�鿴����Ĳ�����������pc�˴򿪣�������Ϣ��ͨ��log���������pc��ʱ�Ż��ӡ��
        appId: data.appId,        // ������ںŵ�Ψһ��ʶ
        timestamp: data.timestamp,// �������ǩ����ʱ���
        nonceStr: data.nonceStr,  // �������ǩ���������
        signature: data.signature,// ���ǩ��������¼1
        jsApiList: [
            'checkJsApi',
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
        ] // �����Ҫʹ�õ�JS�ӿ��б�

    });
};

//ready�ص�
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
        title: Share_data.title, // �������
        desc: Share_data.desc, // ��������
        link: Share_data.link, // ��������
        imgUrl: Share_data.imgUrl, // ����ͼ��
        type: Share_data.type, // ��������,music��video��link������Ĭ��Ϊlink
        dataUrl: Share_data.dataUrl, // ���type��music��video����Ҫ�ṩ�������ӣ�Ĭ��Ϊ��
        success: function () {
            // �û�ȷ�Ϸ����ִ�еĻص�����
            if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
        },
        cancel: function () {
            // �û�ȡ�������ִ�еĻص�����
            if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
        }
    })
};

WeiXinSDK.ShareTimeLine = function(Share_data)
{
    var data = Share_data;

    wx.onMenuShareTimeline({
        title: Share_data.desc, // �������
        link: Share_data.link, // ��������
        imgUrl: Share_data.imgUrl, // ����ͼ��
        success: function () {
            // �û�ȷ�Ϸ����ִ�еĻص�����
            if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
        },
        cancel: function () {
            // �û�ȡ�������ִ�еĻص�����
            if(Share_data.cancel && typeof Share_data.cancel === 'function')Share_data.cancel();
        }
    });
};

WeiXinSDK.ShareQQ = function(Share_data)
{
    var data = Share_data;

    wx.onMenuShareQQ({
        title: Share_data.title, // �������
        desc: Share_data.desc, // ��������
        link: Share_data.link, // ��������
        imgUrl: Share_data.imgUrl, // ����ͼ��
        success: function () {
            // �û�ȷ�Ϸ����ִ�еĻص�����
            if(Share_data.success && typeof Share_data.success === 'function')Share_data.success();
        },
        cancel: function () {
            // �û�ȡ�������ִ�еĻص�����
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
                //֧���ɹ�
                code = 1;
            }
            else if( res.errMsg == 'chooseWXPay:cancel' )
            {
                //֧��������
                code = 10;
            }
            else if( res.errMsg == 'chooseWXPay:fail' )
            {
                //֧��ʧ��
                code = -3;

                img.src = err_log_src+'&err_str='+encodeURIComponent(res.errMsg);

                console.log('url='+window.location.href+'&err_str='+res.errMsg);

                alert("֧��ʧ��:"+res.err_msg);

            }
            else
            {
                img.src = err_log_src+'&err_str='+encodeURIComponent(res.errMsg);

                console.log('url='+window.location.href+'&err_str='+res.errMsg);

                alert("֧��ʧ�ܣ��������������ύʧ�ܣ��������Ͻǹرղ����½���");
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


module.exports = WeiXinSDK; 
});