/**
 * Created by nolest on 2014/11/21.
 *
 * 取消理由view
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var cancel_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var global_config = require('../../common/global_config');
    var Scroll = require('../../common/scroll');
    var m_alert = require('../../ui/m_alert/view');
    var abnormal = require('../../widget/abnormal/view');
    var choosen_group_view = require('../../widget/choosen_group/view');
    var App = require('../../common/I_APP');

    var cancel_view = View.extend
    ({
        attrs:
        {
            template: cancel_tpl
        },
        events :
        {
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="submit_btn"]' : function(ev)
            {
                var self = this;

                self._send_data();
            }
        },
        _send_data : function()
        {
            var self = this;

            var btns = self.btn_group.get_value();

            var explain = self.$('[data-role="explain_text"]').val();

            var data =
            {
                date_id : self.get("date_id"),
                reason : btns[0].text,
                remark : explain
            };

            if(self.get("cancel_wait") == 'cancel_wait')
            {
                data = $.extend(true,{},data,{type:'refund'})
            }


            utility.ajax_request
            ({
                url: global_config.ajax_url.submit_date_cancel_application,
                type : 'POST',
                data : data,
                cache: true,
                beforeSend: function (xhr, options)
                {
                    self.trigger('before:cancel_reason', xhr, options);
                },
                success: function ( response, options)
                {
                    self.trigger('success:cancel_reason', response, options);
                },
                error: function ( xhr, options)
                {
                    self.trigger('error:cancel_reason',  xhr, options);
                },
                complete: function (xhr, status)
                {
                    self.trigger('complete:cancel_reason', xhr, status);
                }
            });


        },
        _setup_events : function()
        {
            var self = this;

            self
                .on('render',function()
                {
                    self._setup_scroll();

                });

            self
                .on('success:cancel_reason',function(response,options)
                {
                    var msg = response.result_data.msg;

                    var code = response.result_data.data;

                    if(code == 1)
                    {
                        m_alert.show(msg,"right");

                        console.log(self.from_app)
                        if(self.from_app)
                        {
                            App.app_back();
                        }
                        else
                        {
                            page_control.navigate_to_page("mine/consider_details_camera/" + self.get("date_id") + "/form_cancel");
                        }

                    }
                    else
                    {
                        m_alert.show(msg,"error");
                    }

                })
                .on('error:cancel_reason',function(response,options)
                {
                    m_alert.show("提交失败，请重试","error");
                })
                .on('complete:cancel_reason',function(response,options)
                {

                })

        },
        _setup_btn : function()
        {

            var self = this;

            var list = [{selected: true,text: "时间原因"},{selected: false,text: "身体原因"},{selected: false,text: "合作原因"},{selected: false,text: "天气原因"}];

            self.btn_group = new choosen_group_view
            ({
                templateModel :
                {
                    list : list
                },
                btn_per_line : 2, //每行按钮个数
                line_margin : '0px 0px 15px 0px', //每行margin
                btn_width : '143px', //按钮宽度
                parentNode: self.$btn_container,
                is_multiply : false
                //css : "btn_reset"
            }).render();
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.$container.height(self.reset_viewport_height());

            self.view_scroll_obj = view_scroll_obj;
        },
        setup : function()
        {
            var self = this;

            self.from_app = self.get("from_app");

            self.$container = self.$('[data-role="container"]');

            self.$btn_container = self.$('[data-role="reason-type"]');

            self._setup_btn();

            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 41;
        }

    });

    module.exports = cancel_view;
});