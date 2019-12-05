/**
 * Created by nolest on 2014/9/10.
 *
 *
 *
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var consider_details_tpl = require('./tpl/main.handlebars');
    var items_tpl = require('./tpl/items.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var App = require('../../common/I_APP');

    var m_alert = require('../../ui/m_alert/view');


    var consider_view = View.extend
    ({
        attrs:
        {
            template: consider_details_tpl
        },
        events :
        {
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="nav-to-zone"]' :function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var user_id = $cur_btn.attr('data-user-id');

                if(App.isPaiApp)
                {
                    App.nav_to_app_page
                    ({
                        page_type : 'cameraman_card',
                        user_id : user_id
                    });
                }
                else
                {
                    page_control.navigate_to_page('zone/'+user_id+'/cameraman');
                }


            },
            'tap [data-role="receive"]' : function()
            {
                var self = this;

                var data =
                {
                    date_id :self.templateModel.date_id,
                    status : 'confirm',
                    user_id : utility.user.get("user_id")
                };

                self.model.send_request(data);

            },
            /**
             * 拒绝
             */
            'tap [data-role="refuse"]' : function()
            {
                var self = this;

                page_control.navigate_to_page("mine/consider_details_model/refuse",self.templateModel)
            },
            /**
             * 私信
             */
            'tap [data-role="msg"]' : function()
            {
                var self = this;

                var date_user = self.templateModel;


                var data =
                {
                    senderid : utility.login_id,
                    receiverid : utility.int(date_user.from_date_id),
                    sendername : utility.user.get('nickname'),
                    receivername : date_user.cameraman_nickname,
                    sendericon : utility.user.get('user_icon'),
                    receivericon : date_user.cameraman_user_icon
                };

                console.log(data)

                if(!App.isPaiApp)
                {
                    console.warn('no App');


                    return;
                }



                App.chat(data);
            },
            'tap [data-role="cam-dead"]' : function(ev)
            {
                var $tap = $(ev.currentTarget);

                page_control.navigate_to_page('zone/'+$tap.attr('data-from_date_id')+'/cameraman');
            }

        },
        _setup_events : function()
        {
            var self = this;

            self
                .on('render',function()
                {
                    self._setup_scroll();
                })
            self.model
                .on('before:fetch',function(response,options)
                {
                    m_alert.show('发送中...','loading');
                })
                .on('success:fetch',function(response,options)
                {
                    //m_alert.hide();

                    if(response.result_data.data > 0)
                    {
                        m_alert.show('你已接受约拍邀请','right');

                        page_control.navigate_to_page('mine/consider_details_model/receive',self.templateModel);
                    }
                    else
                    {
                        m_alert.show('接收失败','error');
                    }

                })
                .on('complete:fetch',function(response,options)
                {
                    //m_alert.hide();
                })
                .on('error:fetch',function(response,options)
                {
                    m_alert.show('网络异常，请重试','right',{delay:2000});
                });

            utility.user.
                on('before:get_date_by_date_id:fetch',function()
                {
                    m_alert.show('加载中...','loading',{delay:-1});
                }).
                on('success:get_date_by_date_id:fetch',function(response)
                {
                    m_alert.hide({delay:-1});

                    self.templateModel = response.result_data.data;

                    // modify hudw 2014.11.8
                    // 确认状态
                    var is_confirm = 0;
                    var is_cancel = 0;
                    var is_wait =0;
                    if(self.templateModel.date_status == 'confirm')
                    {
                        is_confirm = 1;
                    }
                    else if(self.templateModel.date_status == 'cancel')
                    {
                        is_cancel = 1;
                    }
                    else if(self.templateModel.date_status == 'wait')
                    {
                        is_wait = 1;

                        self.$('[data-role="footer"]').removeClass('fn-hide');
                    }

                    self.templateModel = $.extend(true,{},self.templateModel,
                    {
                        total_price : self.templateModel.date_price * self.templateModel.date_hour,
                        is_confirm : is_confirm,
                        is_cancel : is_cancel,
                        is_wait : is_wait
                    });



                    var html_str = items_tpl(self.templateModel);

                    self.$content_container.html(html_str);

                    self.reset_viewport_height();

                    self._drop_reset();


                }).
                on('error:get_date_by_date_id:fetch',function()
                {
                    m_alert.show('网络异常','error');
                });

        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.$content_container = self.$('[data-role="inside"]');

            self._setup_events();


            utility.user.get_date_by_date_id(self.get('date_id'));


        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : false
                });

            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.on('dropload',function(e)
            {
                utility.user.get_date_by_date_id(self.get('date_id'));

            });
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            self.delegateEvents();

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') -41;
        }
    });

    module.exports = consider_view;
});