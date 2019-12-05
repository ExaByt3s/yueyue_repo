/**
 * 公共顶栏
 */
define(function(require, exports, module) 
{
    var $ = require('jquery');
    var Handlebars = require('handlebars');
    var Cookie = require('matcha/cookie/1.0.0/cookie');
    var account_tpl = require('./tpl/share.handlebars');
    // var pop_wx_tpl = require('./tpl/pop_wx.handlebars');


        
    // 初始化
    exports.init = function(options) 
    {
        var options = options || {};        
        var $container = options.container || {};

        var erweima = options.erweima || {};
        
        var user_id = options.user_id || {};

        // var pop_wx_tpl_str = pop_wx_tpl();
        // console.log(pop_wx_tpl_str);

		erweima = encodeURIComponent(erweima);

        var pop_wx_tpl = '<div class="pop-wei-xin" style="display:none" id="pop-wei-xin"><div class="close" id="pop-wei-xin-close"></div><div class="box1"><img src="http://www.yueus.com/qrcode.php?url='+erweima+'"/></div><div class="box2 font_wryh">微信扫一扫，马上分享去朋友圈</div></div>' ;

        $('body').append(pop_wx_tpl);

        var html_str = account_tpl();
        $container.html(html_str);

        //立即约她
        $('#btn_wx').click(function() {
                $('#fade').show();
                $('#pop-wei-xin').show()
        });

        $('#pop-wei-xin-close').click(function() {
                $('#fade').hide();
                $('#pop-wei-xin').hide()
        });


        $('#share-data-wrap span').click(function(ev) {
            var  target = $(ev.target);
            var data = target.attr('data');

            // switch (data)
            // {
            //     case 'sina': 
            //         2;
            //         break;
            // }     
            var go_url = 'http://www.yueus.com/'+ user_id ;	

            var go_url = encodeURIComponent(go_url) ;


            var url = 'http://www.yueus.com/module/share.php?user_id='+user_id+'&sign='+data+'&url='+go_url;
            if (data !== 'weixin') 
            {
                window.open(url)
            }
            console.log(url);
        });
        return this;
    };

    
});