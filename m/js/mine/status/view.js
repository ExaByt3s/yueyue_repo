/**
 * Created by nolest on 2014/9/24.
 *
 * 我的 - 活动列表
 */


define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var consider_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var contain_view = require('./contain_view');
    var m_alert = require('../../ui/m_alert/view');
    var container_model = require('./model');
    var App = require('../../common/I_APP');

    var global_config = require('../../common/global_config');

    var Dialog = require('../../ui/dialog/index');

    var qr_tpl = require('../../act/security/tpl/big_qr_code.handlebars');

    var consider_view = View.extend
    ({
        attrs:
        {
            template: consider_tpl
        },
        events :
        {
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.navigate_to_page("mine")
            },
            'tap [data-carry="unpaid"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                self.paid_view.hide();

                self.pub_view.hide();

                self.uppaid_view.show();

                //首次加载
                if(self.first_time_in_unpaid)
                {
                    self.uppaid_view.get_list();

                    self.first_time_in_unpaid = false;
                }
                //点击刷新
                if(self.current_type == 'unpaid')
                {
                    self.uppaid_view.refresh();
                }

                self.current_type = 'unpaid'

            },
            'tap [data-carry="paid"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                self.uppaid_view.hide();

                self.pub_view.hide();

                self.paid_view.show();

                //首次加载
                if(self.first_time_in_paid)
                {
                    self.paid_view.get_list();

                    self.first_time_in_paid = false;
                }
                //点击刷新
                if(self.current_type == 'confirm')
                {
                    self.paid_view.refresh();
                }

                self.current_type = 'confirm'

            },
            'tap [data-carry="pub"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                self.paid_view.hide();

                self.uppaid_view.hide();

                self.pub_view.show();
                //首次加载
                if(self.first_time_in_pub)
                {
                    self.pub_view.get_list();

                    self.first_time_in_pub = false;
                }
                //点击刷新
                if(self.current_type == 'pub')
                {
                    self.pub_view.refresh();
                }

                self.current_type = 'pub'

            },
            'tap [data-role="confirm_btn"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');

                if(confirm('是否确认结束'))
                {
                    self.model.confirm_date(event_id);
                }
            },
            'tap [data-role="btn_unpaid_pay"]' :function(ev)
            {
                //去付款

                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');
                var enroll_id = $cur_btn.attr('data-enroll-id');

                page_control.navigate_to_page('act/payment/'+event_id+'/'+enroll_id);
            },
            // 待付款 取消报名付款
            'tap [data-role="btn_unpaid_delete"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');
                var enroll_id = $cur_btn.attr('data-enroll-id');

                if(confirm('是否取消报名'))
                {
                    self.model.cancel_act(enroll_id);
                }
            },
            'tap [data-role="lists-to-info"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var enroll_id = $cur_btn.attr('data-enroll-id');

                var view_type = $cur_btn.attr('data-view-type');

                var event_id = $cur_btn.attr('data-event_id');

                var audit_status = $cur_btn.attr('data-audit_status');

                var data;

                if(audit_status == '0')
                {
                    m_alert.show('该活动在审核当中','error');

                    return;
                }

                if(audit_status == '2')
                {
                    m_alert.show('该活动未通过审核','error');

                    return;
                }
                console.log(enroll_id)
                //我发布的
                if(!enroll_id)
                {
                    page_control.navigate_to_page("mine/info/event/" + event_id);
                }
                else
                {
                    switch(view_type)
                    {
                        case 'unpaid' : data = self.uppaid_view.search_data_by_id(enroll_id);break;
                        case 'paid' : data = self.paid_view.search_data_by_id(enroll_id);break;
                        case 'pub' : data = self.pub_view.search_data_by_id(enroll_id);break;
                    }

                    page_control.navigate_to_page("mine/info/enroll/" + enroll_id,data);
                }



            },
            'tap [data-role="btn_pub_show_ewm"]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');
                var enroll_id = $cur_btn.attr('data-enroll-id');

                m_alert.show('加载二维码中...','loading');

                utility.ajax_request
                ({
                    url : global_config.ajax_url.get_act_ticket_detail,
                    data :
                    {
                        event_id : event_id,
                        enroll_id : enroll_id
                    },
                    success : function(data)
                    {
                        var qr_arr = data.result_data.data;

                        if(qr_arr.length == 0)
                        {
                            m_alert.show('二维码不存在','error');

                            return;
                        }

                        m_alert.hide({delay:-1});

                        // 放大二维码数据整合
                        var pic_w_h = Math.ceil(((utility.get_view_port_width() - 90)));//滚动外框宽高
                        var out_height = pic_w_h + 35;

                        for(var i =0;i<qr_arr.length;i++)
                        {
                            qr_arr[i] =
                            {
                                code : qr_arr[i].code,
                                qr_code : qr_arr[i].qr_code,
                                pic_w_h : pic_w_h,
                                out_height : out_height
                            };
                        }

                        // 数据结构
                        var data =
                        {
                            qr_code_arr : qr_arr,
                            pic_w_h : pic_w_h,
                            out_height : out_height
                        };

                        var html_str = qr_tpl(data);

                        // 放大二维码弹出层
                        var  dialog = new Dialog({
                            content : html_str
                        }).show();

                        var pic_view_scroll_obj = Scroll(dialog.$('[data-role=qr]'),{
                            lazyLoad: true
                        });

                        pic_view_scroll_obj.refresh();


                    },
                    error: function()
                    {
                        m_alert.show('网络异常','error');
                    }
                });
            },
            //去评价
            'tap [data-role="btn_paid_commit"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);
                //事件id
                var event_id = $cur_btn.attr('data-event-id');
                //被评价id
                var to_date_id = $cur_btn.attr('data-to-date-id');

                var table_id = $cur_btn.attr('data-table-id');

                var can_comment = $cur_btn.attr('data-can-comment');

                if(can_comment)
                {
                    page_control.navigate_to_page('comment/event/'+ event_id + '/0',{table_id : table_id});
                }
            },
            'tap [data-role="btn_pub_scan"]' : function()
            {
                if(App.isPaiApp)
                {
                    App.qrcodescan
                    ({
                        success : function(data)
                        {
                            console.log(data.type);

                            page_control.navigate_to_page(data.type);
                        }
                    });

                }

            },
            'tap [data-role="btn_pub_finnish"]' : function(ev)
            {
                //活动结束
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');

                if(confirm('是否结束活动'))
                {
                    self.model.set_event_end_act(event_id);
                }
            },
            // 取消活动
            'tap [data-role="btn_pub_cencel"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');

                if(confirm('是否取消活动'))
                {
                    m_alert.show('提交中','loading');
                    self.model.set_event_cancel_act(event_id);
                }
            }
        },
        _setup_events : function()
        {
            var self = this;

            self.model
                .on("before:confirm_date:fetch",function(response,options)
                {    // 确认活动结束
                    m_alert.show('发送中','loading');
                })
                .on("success:confirm_date:fetch",function(response,options)
                {
                    console.log(response);

                    var responses = response.result_data;

                    var msg = responses.msg;

                    //m_alert.hide();

                    if(responses.code >0)
                    {
                        m_alert.show(msg,'right',{delay:2000});

                        self.pub_view.refresh();
                    }
                    else
                    {
                        m_alert.show(msg,'error',{delay:1000});
                    }
                })
                .on("error:confirm_date:fetch",function(response,options)
                {
                    m_alert.show('网络出错','right',{delay:1000});

                })
                .on("complete:confirm_date:fetch",function(response,options)
                {
                    //m_alert.hide();
                })
                .on('before:cancel_act:fetch',function(response,options)
                {
                    m_alert.show('发送中','loading',{delay:800});
                })
                .on('success:cancel_act:fetch',function(response,options)
                {

                    var msg = response.result_data.msg;

                    if(response.result_data.code == 1)
                    {
                        console.log("in");

                        m_alert.show(msg,'right',{delay:2000});

                        setTimeout(function()
                        {
                            self.uppaid_view.refresh();
                        },500)

                    }
                    else
                    {
                        console.log("in_err");
                        m_alert.show(msg,'error',{delay:1000});
                    }

                })
                .on('error:cancel_act:fetch',function(response,options)
                {
                    m_alert.show('网络出错','right',{delay:1000});
                })
                .on('complete:cancel_act:fetch',function(response,options)
                {
                    //m_alert.hide();
                })
                // 取消活动
                .on('before:set_event_cancel_act:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:800});

                })
                .on('success:set_event_cancel_act:fetch',function(response,options)
                {

                    var responses = response.result_data;

                    var msg = responses.msg;


                    if(responses.code >0)
                    {
                        m_alert.show(msg,'right',{delay:2000});

                        self.pub_view.refresh()
                    }
                    else
                    {
                        m_alert.show(msg,'error',{delay:1000});
                    }

                })
                .on('error:set_event_cancel_act:fetch',function(response,options)
                {
                    m_alert.show('网络出错','right',{delay:1000});
                })
                .on('complete:set_event_cancel_act:fetch',function(response,options)
                {

                    //m_alert.hide();
                })
                // 结束活动
                .on('before:set_event_end_act:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:800});
                })
                .on('success:set_event_end_act:fetch',function(response,options)
                {

                    var responses = response.result_data;

                    var msg = responses.msg;

                    if(responses.code >0)
                    {
                        m_alert.show(msg,'right',{delay:1000});

                        self.pub_view.refresh()
                    }
                    else
                    {
                        m_alert.show(msg,'error',{delay:1000});
                    }
                    //m_alert.hide();
                })
                .on('error:set_event_end_act:fetch',function(response,options)
                {
                    m_alert.show('网络出错','right',{delay:1000});
                })
                .on('complete:set_event_end_act:fetch',function(response,options)
                {
                    //m_alert.hide();
                })
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="status-container"]');

            var model = new container_model();
            //待付款view
            self.uppaid_view = new contain_view
            ({
                model : model,
                parentNode: self.$container,
                templateModel : {type_tag : 'unpaid'}
            }).render();

            //已付款view
            self.paid_view = new contain_view
            ({
                model : model,
                parentNode: self.$container,
                templateModel : {type_tag : 'paid'}
            }).render();
            //我发布的view
            self.pub_view = new contain_view
            ({
                model : model,
                parentNode: self.$container,
                templateModel : {type_tag : 'pub'}
            }).render();

            //首次进入标记
            self.first_time_in_unpaid = true;

            self.first_time_in_paid = true;

            self.first_time_in_pub = true;

            if(self.get('tap_type') == 'unpaid')
            {
                //当前view标记
                self.current_type = 'unpaid';

                self.uppaid_view.get_list();

                self.$('[data-carry="unpaid"]').addClass("cur").siblings().removeClass("cur");

                self.paid_view.hide();

                self.pub_view.hide();

                self.uppaid_view.show();

                self.first_time_in_unpaid = false;
            }
            else if(self.get('tap_type') == 'paid')
            {
                self.current_type = 'paid';

                self.paid_view.get_list();

                self.$('[data-carry="paid"]').addClass("cur").siblings().removeClass("cur");

                self.paid_view.show();

                self.pub_view.hide();

                self.uppaid_view.hide();

                self.first_time_in_paid = false;
            }
            else if(self.get('tap_type') == 'pub')
            {
                self.current_type = 'pub';

                self.pub_view.get_list();

                self.$('[data-carry="pub"]').addClass("cur").siblings().removeClass("cur");

                self.paid_view.hide();

                self.pub_view.show();

                self.uppaid_view.hide();

                self.first_time_in_pub = false;
            }
            else
            {   //默认打开待付款
                self.current_type = 'unpaid';

                self.uppaid_view.get_list();

                self.first_time_in_unpaid = false;
            }

            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = consider_view;
});