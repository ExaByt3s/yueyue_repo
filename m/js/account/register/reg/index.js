/**
 * hudw 2014.11.05
 * 注册页面
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var ua = require('../../../frame/ua');
    var utility = require('../../../common/utility');
    var reg_view = require('./view');
    //var guide = require('../../../ui/guide/view');
    //var first_guide_tpl = require('../../../ui/guide/tpl/guide_camera.handlebars');
    var global_config = require('../../../common/global_config');
    var WeixinApi = require('../../../common/I_WX');
    var m_alert = require('../../../ui/m_alert/view');


    page_control.add_page([function()
    {
        return{
            title : '注册',
            route :
            {
                'account/register/reg(/:is_can_back)' : 'account/register/reg'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(page_view,route_params_arr,route_params_obj)
            {

                var self = this;


                self
                    .on('success:login:fetch',function(response)
                    {
                        window.location.href = response.result_data.auth_url
                    })
                    .on('error:login:fetch',function()
                    {
                        m_alert.show('请求失败，请重试','error');
                    });

                console.log(utility.wei_xin.url_head + $(self.el.nextSibling).attr('data-page-url'));

                var url = utility.wei_xin.url_head + $(self.el).attr('data-page-url');

                var url2 = utility.wei_xin.url_head + $(self.el.nextSibling).attr('data-page-url');

                if(/login/.test(url2) || /reg/.test(url2))
                {
                    url2 = utility.wei_xin.url_head + "mine";
                }

                if(!url2)
                {
                    url2 = url;
                }

                // 用于强制跳转的
                if(window._force_jump_link)
                {
                    url2 = window._force_jump_link;
                }

                console.log("url:"+url);
                console.log("url2:"+url2);

                if(WeixinApi.isWexXin() && (!utility.wei_xin.is_authority() || utility.wei_xin.weixin_authority_scope() != 'snsapi_userinfo'))
                {
                    m_alert.show('请求授权中','loading');

                    utility.ajax_request
                    ({
                        url: global_config.ajax_url.auth_act,
                        type : 'POST',
                        data :
                        {
                            url : url,
                            url2 : url2,
                            op : 'snsapi_userinfo'
                        },
                        cache: false,
                        beforeSend: function (xhr, options)
                        {
                            self.trigger('before:login:fetch', xhr, options);
                        },
                        success: function ( response, options)
                        {
                            self.trigger('success:login:fetch', response, options);
                        },
                        error: function ( xhr, options)
                        {
                            self.trigger('error:login:fetch',  xhr, options);
                        },
                        complete: function (xhr, status)
                        {
                            self.trigger('complete:login:fetch', xhr, status);
                        }
                    });



                    return
                }

                var is_can_back = utility.int(route_params_arr[0]);

                // ios系统增加 手机号码输入
                var is_iphone = ua.isIDevice?1 : 0;

                var reg_view_obj = new reg_view
                ({
                    templateModel :
                    {
                        is_iphone : is_iphone
                    },
                    model : utility.user,
                    parentNode : self.$el,
                    is_can_back : is_can_back
                }).render();


            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            }
        }
    }]);

})

