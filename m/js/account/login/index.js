/**
 * 登录
 * hudw 2014.9.3
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var ua = require('../../frame/ua');
    var utility = require('../../common/utility');
    var login_view = require('./view');
    var mine = require('../../mine/model');
    //var guide = require('../../ui/guide/view');
    //var first_guide_tpl = require('../../ui/guide/tpl/guide_model.handlebars');
    var global_config = require('../../common/global_config');
    var WeixinApi = require('../../common/I_WX');
    var m_alert = require('../../ui/m_alert/view');

    page_control.add_page([function()
    {
        return{
            title : '登录',
            route :
            {
                'account/login(/:type)' : 'account/login'
            },
            transition_type : 'slide',
            dom_not_cache : true,
            ignore_exist : true,
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


                if(WeixinApi.isWexXin() && !utility.wei_xin.is_authority())
                {
                    m_alert.show('请求授权中','loading');

                    var url = utility.wei_xin.url_head + $(self.el).attr('data-page-url');

                    var url2 = utility.wei_xin.url_head + $(self.el.nextSibling).attr('data-page-url');

                    if(/login/.test(url2) || /reg/.test(url2))
                    {
                        url2 = utility.wei_xin.url_head + "mine";
                    }

                    console.log("url:"+url);
                    console.log("url2:"+url2);

                    utility.ajax_request
                    ({
                        url: global_config.ajax_url.auth_act,
                        type : 'POST',
                        data :
                        {
                            url : url,
                            url2 : url2,
                            op : 'snsapi_base'
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

                var type_params = route_params_arr[0];

                var mine_obj = new mine();

                // ios系统增加 手机号码输入
                var is_iphone = ua.isIDevice?1 : 0;

                var login_view_obj = new login_view
                ({
                    templateModel :
                    {
                        is_iphone : is_iphone
                    },
                    model : mine_obj,
                    parentNode : self.$el,
                    type_params : type_params
                }).render();





            },
            page_before_show : function()
            {
                var self = this;


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

