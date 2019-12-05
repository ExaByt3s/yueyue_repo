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
    var global_config = require('../../common/global_config');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');
    var qr_tpl = require('../../act/security/tpl/big_qr_code.handlebars');
    var Dialog = require('../../ui/dialog/index');
    var footer_tpl = require('./tpl/footer.handlebars');


    var consider_view = View.extend
    ({
        attrs:
        {
            template: consider_details_tpl
        },
        events :
        {
            'swiperight' : function ()
            {
                var self = this;

                if(self.get("is_form_cancel"))
                {
                    page_control.navigate_to_page("mine/consider/can_back_to_mine")
                }
                else
                {
                    page_control.back();
                }



            },
            'tap [data-role="comment"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var from_date_id = $cur_btn.attr('data-from-date-id');

                var to_date_id = $cur_btn.attr('data-to-date-id');
                //约拍事件id
                var date_id = $cur_btn.attr('data-date-id');

                var to_event;

                var send_id;

                if(utility.user.get("role") == 'model')
                {
                    to_event = 'cameraman';
                    send_id = from_date_id;
                }
                else
                {
                    to_event = 'model';
                    send_id = to_date_id;
                }

                page_control.navigate_to_page('comment/' + to_event + "/" + date_id + "/" + send_id);

            },
            //出示二维码
            'tap [data-role="show_ewm"]':function(ev)
            {
                var self = this;

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

                        if(!qr_arr || qr_arr.length == 0)
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
                                                    console.log('已扫');
                                                    console.log(self.from_app);
                                                    if(self.from_app)
                                                    {
                                                        console.log(1)

                                                        clearInterval(interval_num);

                                                        App.app_back();
                                                    }
                                                    else
                                                    {
                                                        console.log(2)

                                                    }
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




                            var pic_view_scroll_obj = Scroll(dialog.$('[data-role=qr]'),{
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
            'tap [data-role="scan"]' :function(ev)
            {
                var self = this;

                if(App.isPaiApp)
                {
                    App.qrcodescan
                    ({
                        is_nav_page : false,
                        success : function(data)
                        {
                            console.log('qr scan success');

                            App.app_back();
                        }
                    });
                }
            },
            'tap [data-role="agreement"]' : function(ev)
            {
                //接受邀请
                var self = this;

                m_alert.show('提交中...','loading');

                var $cur_btn = $(ev.currentTarget);

                var date_id = $cur_btn.attr('data-date-id');

                var data =
                {
                    date_id :date_id,
                    status : 'confirm',
                    user_id : utility.user.get("user_id")
                };

                utility.ajax_request
                ({
                    url : global_config.ajax_url.accept_invite,
                    data : data,
                    success : function(data)
                    {
                        m_alert.show("提交成功","right");

                        if(self.from_app)
                        {
                            App.app_back();
                        }
                        else
                        {
                            self._refresh();
                        }

                    },
                    error: function()
                    {
                        m_alert.show('网络异常','error');
                    }
                });

            },
            /**
             * 拒绝
             */
            'tap [data-role="refuse"]' : function()
            {
                //拒绝邀请
                var self = this;

                page_control.navigate_to_page("mine/consider_details_model/refuse",self.data_cache)
            },
            'tap [data-role="confirm"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');

                if(confirm('是否确认结束'))
                {
                    m_alert.show('提交中...','loading');

                    utility.ajax_request
                    ({
                        url : global_config.ajax_url.set_event_end_act,
                        data:
                        {
                            event_id : event_id
                        },
                        cache : false,
                        success : function(data)
                        {
                            m_alert.show('提交成功','right');

                            if(self.from_app)
                            {
                                App.app_back();
                            }
                            else
                            {
                               self._refresh();
                            }

                        },
                        error: function()
                        {
                            m_alert.show('网络异常','error');
                        }
                    });
                }

            },
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                if(self.get("is_form_cancel"))
                {
                    page_control.navigate_to_page("mine/consider/can_back_to_mine")
                }
                else
                {
                    page_control.back();
                }
            },
            'tap [data-role="cam-dead"]' : function(ev)
            {
                var self = this;

                var $tap = $(ev.currentTarget);

                if(self.get('date_role_is_model'))
                {
                    var user_id = $tap.attr('data-to_date_id');

                    if(App.isPaiApp)
                    {
                        App.nav_to_app_page
                        ({
                            page_type : 'model_card',
                            user_id : user_id
                        });
                    }
                    else
                    {
                        page_control.navigate_to_page('model_card/'+user_id);
                    }


                }
                else
                {
                    var user_id = $tap.attr('data-from_date_id');

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


                }
            },
            'tap [data-role="date_cancel"]' : function()
            {
                var self = this;

                // app 统计事件
                App.isPaiApp && App.analysis('eventtongji',global_config.analysis_event['date_cancel']);

                if(self.cancel_wait)
                {
                    page_control.navigate_to_page('mine/date_cancel_reason/'+ self.date_id + '/cancel_wait');
                }
                else
                {
                    page_control.navigate_to_page('mine/date_cancel_reason/'+ self.date_id);
                }


            },
            'tap [data-role="btn_talk"]' : function()
            {
                var self = this;
                //模特发起私聊摄影师
                console.log("模特发起私聊摄影师");

                var data =
                {
                    senderid : utility.login_id,
                    receiverid : self.cameraman_id,
                    sendername : utility.user.get('nickname'),
                    receivername : self.cameraman_nickname,
                    sendericon : utility.user.get('user_icon'),
                    receivericon : self.cameraman_user_icon
                };

                console.log(data);

                if(!App.isPaiApp)
                {
                    console.warn('no App');

                    return;
                }

                App.chat(data);
            },
            'tap [data-role="msg"]' : function()
            {
                var self = this;
                //摄影师发起私聊模特
                console.log("摄影师发起私聊模特");


                var data =
                {
                    senderid : utility.login_id,
                    receiverid : self.model_id,
                    sendername : utility.user.get('nickname'),
                    receivername : self.model_nickname,
                    sendericon : utility.user.get('user_icon'),
                    receivericon : self.model_user_icon
                };

                console.log(data);

                if(!App.isPaiApp)
                {
                    console.warn('no App');

                    return;
                }

                App.chat(data);
            },
            'tap [data-role="force_refund"]' : function()
            {
                if(confirm('你好，选择强制退款，系统将会把约拍费用的70%的金额退还到你的钱包，另外30%将给予模特作为补偿，请再次确认。'))
                {
                    //强制退款
                    var self = this;

                    m_alert.show('提交中...','loading');

                    utility.ajax_request
                    ({
                        url: global_config.ajax_url.force_refund,
                        type : 'POST',
                        data :
                        {
                            date_id : self.get("date_id")
                        },
                        cache: true,
                        beforeSend: function (xhr, options)
                        {
                            self.trigger('before:force_refund', xhr, options);
                        },
                        success: function ( response, options)
                        {
                            self.trigger('success:force_refund', response, options);
                        },
                        error: function ( xhr, options)
                        {
                            self.trigger('error:force_refund',  xhr, options);
                        },
                        complete: function (xhr, status)
                        {
                            self.trigger('complete:force_refund', xhr, status);
                        }
                    });
                }
            },
            'tap [data-role="agree"]' : function()
            {
                //同意取消
                var self = this;

                m_alert.show('提交中...','loading');

                utility.ajax_request
                ({
                    url: global_config.ajax_url.update_agree_status,
                    type : 'POST',
                    data :
                    {
                        date_id : self.get("date_id"),
                        agree_status : 'agree'
                    },
                    cache: true,
                    beforeSend: function (xhr, options)
                    {
                        self.trigger('before:agree', xhr, options);
                    },
                    success: function ( response, options)
                    {
                        self.trigger('success:agree', response, options);
                    },
                    error: function ( xhr, options)
                    {
                        self.trigger('error:agree',  xhr, options);
                    },
                    complete: function (xhr, status)
                    {
                        self.trigger('complete:agree', xhr, status);
                    }
                });

            },
            'tap [data-role="not_agree"]' : function()
            {
                //不同意取消
                var self = this;

                m_alert.show('提交中...','loading');

                utility.ajax_request
                ({
                    url: global_config.ajax_url.update_agree_status,
                    type : 'POST',
                    data :
                    {
                        date_id : self.get("date_id"),
                        agree_status : 'disagree'
                    },
                    cache: true,
                    beforeSend: function (xhr, options)
                    {
                        self.trigger('before:disagree', xhr, options);
                    },
                    success: function ( response, options)
                    {
                        self.trigger('success:disagree', response, options);
                    },
                    error: function ( xhr, options)
                    {
                        self.trigger('error:disagree',  xhr, options);
                    },
                    complete: function (xhr, status)
                    {
                        self.trigger('complete:disagree', xhr, status);
                    }
                });

            },
            'tap [data-role="step_container"]' : function()
            {
                var self = this;


            }

        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self._setup_scroll();
            });

            utility.user.
                on('before:get_date_by_date_id:fetch',function()
                {
                    //m_alert.show('加载中...','loading',{delay:-1});

                }).
                on('success:get_date_by_date_id:fetch',function(response)
                {
                    m_alert.hide({delay:-1});

                    self._render_items(response.result_data.data);

                }).
                on('error:get_date_by_date_id:fetch',function()
                {
                    m_alert.show('网络异常','error');
                });

            //拒绝事件
            self
                .on('success:force_refund',function(response,options)
                {
                    var msg = response.result_data.msg;

                    var code = response.result_data.data;

                    if(code == 1)
                    {
                        m_alert.show(msg,"right");

                        if(self.from_app)
                        {
                            App.app_back();
                        }
                        else
                        {
                            self._refresh();
                        }
                    }
                    else
                    {
                        m_alert.show(msg,"error");
                    }

                })
                .on('error:force_refund',function(response,options)
                {
                    m_alert.show("提交失败，请重试","error");
                })
                .on('complete:force_refund',function(response,options)
                {

                })

            //同意事件
            self
                .on('success:agree',function(response,options)
                {
                    var msg = response.result_data.msg;

                    var code = response.result_data.data;

                    if(code == 1)
                    {
                        m_alert.show(msg,"right");

                        if(self.from_app)
                        {
                            App.app_back();
                        }
                        else
                        {
                            self._refresh();
                        }
                    }
                    else
                    {
                        m_alert.show(msg,"error");
                    }

                })
                .on('error:agree',function(response,options)
                {
                    m_alert.show("提交失败，请重试","error");
                })
                .on('complete:agree',function(response,options)
                {

                });

            //不同意事件
            self
                .on('success:disagree',function(response,options)
                {
                    var msg = response.result_data.msg;

                    var code = response.result_data.data;

                    if(code == 1)
                    {
                        m_alert.show(msg,"right");

                        if(self.from_app)
                        {
                            App.app_back();
                        }
                        else
                        {
                            self._refresh();
                        }
                    }
                    else
                    {
                        m_alert.show(msg,"error");
                    }

                })
                .on('error:disagree',function(response,options)
                {
                    m_alert.show("提交失败，请重试","error");
                })
                .on('complete:disagree',function(response,options)
                {

                })

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
                self._refresh();

            });
        },
        _refresh : function()
        {

            var self = this;

            self.$('[data-role="date_cancel"]').addClass("fn-hide");

            self.$('[data-role="date_cancel_notice"]').addClass("fn-hide");

            self.$('[data-role="btn_talk_container"]').addClass("fn-hide");

            utility.user.get_date_by_date_id(self.get('date_id'));

            var view_port_height = self.reset_viewport_height()+1;

            self.$container.height(view_port_height);

            self.view_scroll_obj.refresh();

        },
        _render_items : function(data)
        {
            var self = this;

            var has_btn = data.show_button_status || data.show_cancel_button_status;

            self.data_cache = data;

            data = $.extend(true,{},data,{total_price : data.date_price * data.date_hour,date_role_is_model : self.get('date_role_is_model'),from_app:self.from_app,has_btn:has_btn})



            $.each(data.date_log,function(i,obj)
            {
                if(obj.truth && obj.truth == '1')
                {
                    obj.is_truth = true
                }
                else if(obj.truth && obj.truth == '2')
                {
                    obj.is_truth = false
                }

                if(obj.time_sense && obj.time_sense == '1')
                {
                    obj.is_time_sense = true
                }
                else if(obj.time_sense && obj.time_sense == '2')
                {
                    obj.is_time_sense = false
                }
            });

            console.log(data);

            var html_str = items_tpl(data);

            var footer_str = footer_tpl(data);

            self.$list_container.html(html_str);

            self.date_id = data.date_id;

            self.model_nickname = data.model_nickname;
            self.model_user_icon = data.model_user_icon;

            self.cameraman_nickname = data.cameraman_nickname;
            self.cameraman_user_icon = data.cameraman_user_icon;

            self.cameraman_id = data.from_date_id;
            self.model_id = data.to_date_id;

            //摄影师申请退款按钮
            if(data.show_submit_refund_button == 1 && utility.user.get("role") == 'cameraman')
            {
                self.$('[data-role="date_cancel"]').removeClass("fn-hide");

                self.$('[data-role="date_cancel_notice"]').removeClass("fn-hide");
            }
            //模特同意前退款 设置参数
            if(data.show_submit_refund_button == 1 && data.date_status == 'wait' && data.pay_status == '1' && !self.from_app && utility.user.get("role") == 'cameraman')
            {

                self.cancel_wait = true;

            }

            if(data.show_button_status || data.show_cancel_button_status)
            {
                var view_port_height = self.reset_viewport_height();

                self.$container.height(view_port_height - 50);

                self.view_scroll_obj.refresh();
            }

            if(data.cancel_status == 1 && data.cancel_log && data.cancel_log.length == 1 && utility.user.get("role") == 'model' && !self.from_app)
            {   //列表中的私聊一下按钮
                self.$('[data-role="btn_talk_container"]').removeClass("fn-hide");

                var view_port_height = self.reset_viewport_height();

                self.$container.height(view_port_height - 50);

                self.view_scroll_obj.refresh();
            }

            self.$footer_container.html(footer_str);

            if(self.from_app)
            {
                self.$('[data-role="btn_talk_container"]').addClass('fn-hide');

                self.$('[data-role="msg"]').addClass('fn-hide');
            }



            self.view_scroll_obj.refresh();

            self._drop_reset();
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="content-body"]');

            self.$list_container = self.$('[data-role="list"]');

            self.$footer_container = self.$('[data-role="footer-contain"]');

            self.from_app = self.get("from_app");

            self._setup_events();

            utility.user.get_date_by_date_id(self.get('date_id'));

            console.log(self.get('date_role_is_model'))
        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height()+1;

            self.$container.height(view_port_height);

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav');
        }
    });

    module.exports = consider_view;
});