/**
 * Created by nolest on 2014/10/27.
 *
 * 活动状态详情view
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var info_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/new_iscroll');
    var m_alert = require('../../ui/m_alert/view');
    var insert_tpl = require('./tpl/insert.handlebars');
    var App = require('../../common/I_APP');
    var global_config = require('../../common/global_config');
    var Dialog = require('../../ui/dialog/index');
    var qr_tpl = require('../../act/security/tpl/big_qr_code.handlebars');
    var new_iscroll = require('../../common/new_iscroll');

    var info_view = View.extend
    ({
        attrs:
        {
            template: info_tpl
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
            'tap [data-role="title"]' :function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event_id');

                if(event_id != 0)
                {
                    //从活动详情页点击 带参数 隐藏“报名”按钮 nolest 2015-1-30
                    page_control.navigate_to_page("act/detail/" + event_id,{form_info_title: true})
                }
            },
            'tap [data-role="finish-tap-right"]' : function(ev)
            {
                //活动结束
                var self = this;

                if(confirm('是否结束活动'))
                {
                    self.model.set_event_end_act(self.event_id);
                }
            },
            'tap [data-role="cancel-tap-right"]' : function()
            {
                //活动取消
                var self = this;

                if(confirm('是否取消活动'))
                {
                    self.model.set_event_cancel_act(self.event_id);
                }
            },
            'tap [data-role="quit-tap-right"]': function()
            {
                //取消参加
                var self = this;

                if(confirm('是否取消报名'))
                {
                    self.model.cancel_act(self.enroll_id);
                }
            },
            'tap [data-role="act-go-pay"]' : function(ev)
            {
                //去支付
                var self = this;

                page_control.navigate_to_page('act/payment/'+ self.event_id+'/'+ self.enroll_id);
            },
            'tap [data-role="act-show-qr"]' : function(ev)
            {
                var self = this;
                //出示二维码

                m_alert.show('加载二维码中...','loading');

                utility.ajax_request
                ({
                    url : global_config.ajax_url.get_act_ticket_detail,
                    data :
                    {
                        event_id : self.event_id,
                        enroll_id : self.enroll_id
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
                                qr_code : qr_arr[i].qr_code_url,
                                pic_w_h : pic_w_h,
                                out_height : out_height,
                                url : qr_arr[i].qr_code_url,
                                number : qr_arr[i].code,
                                name : '数字密码'
                            };
                        }

                        // 数据结构
                        var data =
                        {
                            qr_code_arr : qr_arr,
                            pic_w_h : pic_w_h,
                            out_height : out_height
                        };

                        if(App.isPaiApp)
                        {
                            App.qrcodeshow(qr_arr);
                        }
                        else
                        {
                            var html_str = qr_tpl(data);

                            var interval_num;
                            // 放大二维码弹出层
                            var  dialog = new Dialog({
                                content : html_str
                            })
                                .on('after:show',function()
                                {
                                    interval_num = setInterval(function()
                                    {
                                        utility.ajax_request
                                        ({
                                            url : global_config.ajax_url.get_scan_code,
                                            data :
                                            {
                                                code : qr_arr[0].code
                                            },
                                            success : function(data)
                                            {
                                                if(data.result_data.data == 1)
                                                {
                                                    clearInterval(interval_num);

                                                    dialog.hide();

                                                    self._refresh();
                                                    console.log('已扫');
                                                }
                                                else if(data.result_data.data == 0)
                                                {
                                                    console.log('未扫');
                                                }
                                            },
                                            error : function(data)
                                            {
                                                console.log('网络超时')
                                            }
                                        })
                                    },1500);

                                })
                                .on('after:hide',function()
                                {
                                    clearInterval(interval_num);
                                })
                                .show();

                            var pic_view_scroll_obj = new_iscroll(dialog.$('[data-role=qr]'),{
                                lazyLoad: true
                            });

                            pic_view_scroll_obj.refresh();
                        }



                    },
                    error: function()
                    {
                        m_alert.show('网络异常','error');
                    }
                });
            },
            'tap [data-role="act-go-commit"]' : function(ev)
            {
                //去评价
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                 page_control.navigate_to_page('comment/event/'+ self.event_id + '/0',{table_id : self.table_id});
            },
            'tap [data-role="act-go-scan"]' : function(ev)
            {
                var self = this;
                //扫描
                /*
                if(App.isPaiApp)
                {
                    App.qrcodescan();
                }
                */
                page_control.navigate_to_page('act/signin/'+ self.event_id);
            }
        },
        _setup_events : function()
        {
            var self = this;
            self
                .on('render',function()
                {
                    self._setup_scroll();
                });
            self.model
                .on('success:fetch',function(response,options)
                {
                    console.log(response.result_data.data)

                    self._show_parts(response.result_data.data);
                })
                .on('error:fetch',function(response,options)
                {
                    m_alert.show('网络异常','error',{delay:800});
                })
                .on('success:pub_fetch',function(response,options)
                {
                    console.log(response.result_data.data)
                    self._show_parts(response.result_data.data);
                })
                .on('error:pub_fetch',function(response,options)
                {
                    m_alert.show('网络异常','error',{delay:800});
                })
                //完成活动
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

                        self._refresh();
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
                //取消活动
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
                        m_alert.show(msg,'right',{delay:1000});

                        self._refresh();
                    }
                    else
                    {
                        m_alert.show(msg,'error',{delay:1000});
                    }
                    //m_alert.hide();
                })
                .on('error:set_event_cancel_act:fetch',function(response,options)
                {
                    m_alert.show('网络出错','right',{delay:1000});
                })
                .on('complete:set_event_cancel_act:fetch',function(response,options)
                {
                    //m_alert.hide();
                })
                //取消参加
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
                            page_control.back();
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

        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container);

            self.view_scroll_obj = view_scroll_obj;
        },
        _show_parts : function(data)
        {
            var self = this;

            if(utility.user.get("user_id") != data.event_organizers)
            {
                console.log("参与者")
                //参与者
                data = $.extend(true,{},data,{joiner : true});
            }
            else
            {
                console.log("组织者")
                //组织者
                data = $.extend(true,{},data,{organizers : true});
            }

            self.event_id = data.event_id;

            self.enroll_id = data.enroll_id;

            self.table_id = data.table_id;

            console.log(data);
            var html = insert_tpl
            ({
                data : data
            });

            if(data.event_finish_button){self.$('[data-role="finish-tap-right"]').removeClass("fn-hide")}
            if(data.event_cancel_button){self.$('[data-role="cancel-tap-right"]').removeClass("fn-hide")}
            if(data.enroll_cancel_button){self.$('[data-role="quit-tap-right"]').removeClass("fn-hide")}

            self.$insert_container.html(html);

        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="info-container"]');

            self.$insert_container = self.$('[data-role="insert-container"]');

            var arr = self.get("params_arr");

            if(arr[0] == 'event')
            {
                //点击订单列表 我发布的
                m_alert.show('加载中','loading',{delay:800});

                self.model.get_pub_info(arr[1]);
            }
            else if(arr[0] == 'enroll')
            {
                //点击订单列表 待付款、已付款
                m_alert.show('加载中','loading',{delay:800});

                self.model.get_info(arr[1]);
            }

            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        _refresh : function()
        {
            var self = this;

            var arr = self.get("params_arr");

            if(arr[0] == 'event')
            {
                //点击订单列表 我发布的
                m_alert.show('加载中','loading',{delay:800});

                self.model.get_pub_info(arr[1]);
            }
            else if(arr[0] == 'enroll')
            {
                //点击订单列表 待付款、已付款
                m_alert.show('加载中','loading',{delay:800});

                self.model.get_info(arr[1]);
            }

            self.$('[data-tap-tap]').addClass("fn-hide");
        }
    });

    module.exports = info_view;
});