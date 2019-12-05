/**
 * Created by nolest on 2014/9/18.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var global_config = require('../../../common/global_config');
    var reg_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var m_alert = require('../../../ui/m_alert/view');
    var m_select = require('../../../ui/m_select/view');
    var dialog = require('../../../ui/dialog/index');
    var App = require('../../../common/I_APP');


    var login_phone_view = View.extend
    ({

        attrs:
        {
            template: reg_tpl
        },
        events :
        {
            'swiperight' : function ()
            {
                var self = this;

                if(self.get('is_can_back'))
                {
                    return;
                }

                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="get_code"]' : function()
            {
                var self = this;

                //不为数字 || 空 || 字符串中有空格时返回
                if(!parseInt(self.enter_phone.val()) || self.enter_phone.val().trim() == '' || self.enter_phone.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('手机号不对哦！','error');

                    return
                }

                if(self.counting)
                {
                    return
                }

                self._count_down(60);

                self.get_verify_code
                ({
                    phone : self.enter_phone.val(),
                    type : 'bind_sent_verify_code'
                })
            },
            'tap [data-role="btn_login"]' : function()
            {
                var self = this;

                //手机格式
                if(self.enter_phone.val().trim() == '' || self.enter_phone.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('手机号不对哦！','error');

                    return
                }
                //验证码格式
                if(self.enter_code.val().trim() == '' || self.enter_code.val().trim().indexOf(" ") != -1)
                {
                    m_alert.show('验证码格式不对哦！','error');

                    return
                }

                m_alert.show('提交中...','loading');

                var action_type = 'verify_phone';

                self.get_verify_code
                ({
                    phone : self.enter_phone.val(),
                    verify_code : self.enter_code.val(),
                    type : action_type
                })
            },
            'tap [data-role="reg-look-look"]' : function()
            {
                page_control.navigate_to_page(global_config.default_index_route);
            },
            'tap [data-role="old-login"]' : function()
            {
                page_control.navigate_to_page('account/login/1');
            }
        },
        _count_down : function(sec)
        {
            var self = this;

            self.counting = true;

            self.count_btn.html(sec);

            self.count_Interval = setInterval(function()
            {
                sec--;

                if(sec == 0)
                {
                    self._stop_count_down();
                }
                else
                {
                    self.count_btn.html(sec);
                }
            },1000)
        },
        _stop_count_down : function()
        {
            var self = this;

            clearInterval(self.count_Interval);

            self.count_btn.html('重新获取');

            self.counting = false;
        },
        _setup_events : function()
        {
            var self = this;

            self.$('[data-role="enter_phone"]')[0].oninput = function()
            {
                if(self.enter_phone.val() != "")
                {
                    self.$btn.removeClass("ui-button-dark");

                    self.$btn.addClass("ui-button-primary").removeAttr('disabled');
                }
                else
                {
                    self.$btn.addClass("ui-button-dark").attr('disabled','disabled');

                    self.$btn.removeClass("ui-button-primary")
                }
            };
            //ui-button-primary

            self.on('render',function()
            {
                setTimeout(function()
                {
                    self._show_dialog();
                },200)


                self._setup_scroll();
            });


            self.on('success:fetch_verify_phone',function(response,options)
            {
                var response = response.result_data;

                var msg = response.msg;

                var code = response.code;

                if(code == 1)
                {
                    m_alert.show(msg,'right');

                    self._stop_count_down();

                    page_control.navigate_to_page('account/register/login_pw',
                    {
                        phone :self.enter_phone.val(),
                        verify_code : self.enter_code.val(),
                        is_from_reg : true
                    });


                }
                else
                {
                    m_alert.show(msg,'error');
                }
            })
            // 验证码触发成功
            .on('success:fetch_bind_sent_verify_code',function(response,options)
            {
                var response = response.result_data;

                var msg = response.msg;

                var code = response.code;

                self.has_reg = false;

                if(code == 1)
                {
                    m_alert.show(msg,'right');

                }
                // 得知是已经注册过的账号，直接跳到登录页
                else if (code == 2)
                {
                    m_alert.show(msg,'right',{delay : 2000});

                    self._stop_count_down();

                    self.has_reg = true;

                    setTimeout(function()
                    {
                        self.dialog_obj.show();
                    },2000);

                }
                else
                {
                    m_alert.show(msg,'error');

                    // modify hudw 2014.11.8
                    self._stop_count_down();
                }
            })
            .on('error:fetch_code',function(response,options)
            {
                m_alert.show('网络异常','error');

                self._stop_count_down();
            });

            // 提示层按钮安装
            self.dialog_obj.on('tap:button:save', function()
            {
                this.hide();

                page_control.navigate_to_page('account/login/has_reg_user_id_'+self.enter_phone.val());
            })
            .on('tap:button:close', function()
            {
                this.hide();
            })


        },
        _show_dialog : function()
        {
            var self = this;

            self.dialog_reg = new dialog({
                content: '<p class="wx_card_notice">您还没有注册</p><button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_login_btn"  data-role="button" data-name="reg-cam"><span class="ui-button-content">注册摄影师</span></button><button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_white"  data-role="button" data-name="reg-model"><span class="ui-button-content">注册模特</span></button><p class="wx_card_gray">注册太麻烦，想要直接约？</p><p class="wx_card_gray wx_card_gray_mgb">拨打4000-82-9003，我们帮你！</p>',
                mask_is_close_btn : false //点击半透明层是否关闭dialog 默认是
            }).show();

            self.dialog_reg
                .on('tap:button:reg-cam',function()
                {
                    this.hide();

                    //window.location.href = 'http://app.yueus.com/';
                })
                .on('tap:button:reg-model', function()
                {
                    this.hide();

                    self.dialog_reg_model = new dialog({
                        content: '<p class="wx_card_notice wx_long_notice">微信版约约是摄影师专用平台，一经注册，账号将默认为摄影师。模特请下载APP，或关注微信服务号(yueusmt)，开启时间变现的旅程。</p><button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_login_btn wx_card_gray_mgb"  data-role="button" data-name="download"><span class="ui-button-content">下载约yue APP</span></button>',
                        mask_is_close_btn : false //点击半透明层是否关闭dialog 默认是
                    }).show();

                    self.dialog_reg_model
                        .on('tap:button:download',function()
                        {
                            window.location.href = 'http://app.yueus.com/';
                        })

                })
        },
        _setup_dialog : function()
        {
            var self = this;

            // 已注册过的提示层安装 hudw 2014.11.21
            self.dialog_obj = new dialog({
                content: '<p>这个号码已经注册过了</p><br/>你可以选择',
                buttons: [{
                    name: 'save',
                    text: '登录'
                },{
                    name: 'close',
                    text: '返回'
                }]
            });
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,{
                is_hide_dropdown : true
            });

            self.view_scroll_obj = view_scroll_obj;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.counting = false;

            self.count_btn = self.$('[data-role="get_code"]');

            self.enter_phone = self.$('[data-role="enter_phone"]');

            self.enter_code = self.$('[data-role="verify_code"]');

            self.$btn = self.$('[data-role="btn_login"]');

            self._setup_dialog();

            self._setup_events();

            //self._show_dialog();

            if(self.get('is_can_back'))
            {
                self.$('[data-role="page-back"]').addClass('fn-invisible');

            }


        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        get_verify_code : function(data)
        {
            var self = this;

            utility.ajax_request
            ({
                data : data,
                url : global_config.ajax_url.reg,
                success : function(response)
                {
                    switch(data.type)
                    {
                        case 'bind_sent_verify_code' :
                            self.trigger('success:fetch_bind_sent_verify_code',response);
                            break;
                        case 'verify_phone' :
                            self.trigger('success:fetch_verify_phone',response);
                            break;
                    }


                },
                error : function()
                {
                    self.trigger('error:fetch_code');
                }
            });
        }
    });

    module.exports = login_phone_view;
});