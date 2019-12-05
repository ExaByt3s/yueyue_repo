/**
 * Created by nolest on 2014/9/9.
 *
 * 约拍邀请列表view
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var consider_tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var new_iscroll = require('../../common/new_iscroll');
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
                if(window._IS_NEW_WEB_VIEW)
                {
                    return;
                }

                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                if(window._IS_NEW_WEB_VIEW)
                {
                    App.remove_front();

                    return;
                }

                var self = this;

                if(self.get('can_back_to_mine'))
                {
                    page_control.back();
                }
                else
                {
                    page_control.navigate_to_page("mine")
                }

            },
            'tap [data-role="considering"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                self.confirm_view.hide();

                self.consider_view.show();
                //首次加载
                if(self.first_time_in_consider)
                {
                    self.consider_view.get_list();

                    self.first_time_in_consider = false;
                }
                //点击刷新
                if(self.current_type == 'consider')
                {
                    self.consider_view.refresh();
                }

                self.current_type = 'consider'

            },
            'tap [data-role="agreed"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                self.consider_view.hide();

                self.confirm_view.show();
                //首次加载
                if(self.first_time_in_confirm)
                {
                    self.confirm_view.get_list();

                    self.first_time_in_confirm = false;
                }
                //点击刷新
                if(self.current_type == 'confirm')
                {
                    self.confirm_view.refresh();
                }

                self.current_type = 'confirm'

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
            'tap [data-role="to-zone"]' : function(ev)
            {
                var self = this;

                var $panel = $(ev.currentTarget);

                var user_id = $panel.attr('data-date-user-id');

                page_control.navigate_to_page('zone/' + user_id + '/cameraman');
            },
            'tap [data-role="btn_pub_scan"]' :function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                if(App.isPaiApp)
                {
                    App.qrcodescan();
                }
            },
            //出示二维码
            'tap [data-role="btn_pub_be_scan"]':function(ev)
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

                        var pic_view_scroll_obj = new_iscroll(dialog.$('[data-role=qr]'),{
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
            'tap [data-role="cameraman_date_list"]' : function(ev)
            {
                var self = this;

                var $panel = $(ev.currentTarget);

                var from_date_id = $panel.attr('data-date-user-id');

                var item_type = $panel.attr('data-item-type');

                var user_id = $panel.attr('data-date-user-id');

                var _date_id = $panel.attr('data-date-id');

                var date_id = $panel.attr('data-to-date-id');

                var status = $panel.attr('data-date_status');

                if($(ev.target).attr('data-to-tap') == 'end' || $(ev.target).attr('data-to-tap') == 'commit')
                {
                    return;
                }

                console.log(item_type);

                if(status == 'wait')
                {
                    page_control.navigate_to_page('mine/consider_details_camera/'+date_id);
                }
                else
                {
                    page_control.navigate_to_page('mine/consider_details_camera/'+date_id);
                }



                // var data;
                // data = self.consider_view.search_data(date_id);

                /*代付款延期处理 nolest 2014.11.20
                 //如果是已拒绝不进行跳转
                 // modify hudw 2014.11.8 处理待付款
                 if(data.date_status == 'cancel' || data.pay_status ==0)
                 {
                 page_control.navigate_to_page('mine/consider_details_camera/'+_date_id);
                 }
                 else
                 {
                 utility.user.get('role') == 'model' && page_control.navigate_to_page('mine/consider_details_camera/'+date_id,data);
                 }
                 */

                //data-item-type="{{data.item_type}}" data-enroll-id="{{data.enroll_id}}" data-to-date-id
            },
            'tap [data-role="to_model_card"]' : function(ev)
            {
                var self = this;

                var $panel = $(ev.currentTarget);

                var $target = $(ev.target).attr('data-to-tap')

                if($target == 'list')
                {
                    return;
                }
                var to_date_id = $panel.attr('data-to-date-id');

                page_control.navigate_to_page('model_card/'+to_date_id);

            },
            'tap [data-role="model_date_list"]' : function(ev)
            {
                var self = this;

                var $panel = $(ev.currentTarget);

                var $target = $(ev.target).attr('data-to-tap');

                var to_date_id = $panel.attr('data-to-date-id');

                var item_type = $panel.attr('data-item-type');

                var enroll_id = $panel.attr('data-enroll-id');

                var date_id = $panel.attr('data-date-id');

                var from_date_id = $panel.attr('data-date-user-id');

                if($target == 'commit' || $target == 'commit_text' || $target =='end' || $target  == 'icon')
                {
                    return;
                }

                if($(ev.target).attr('data-enroll-id'))
                {
                    return;
                }

                page_control.navigate_to_page('mine/consider_details_camera/'+date_id);

            },
            'tap [data-role="btn_commit"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var from_date_id = $cur_btn.attr('data-from-date-id');

                var to_date_id = $cur_btn.attr('data-to-date-id');
                //约拍事件id
                var date_id = $cur_btn.attr('data-date-id');

                var can_comment = $cur_btn.attr('data-can-comment');

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
                if(can_comment)
                {
                    page_control.navigate_to_page('comment/' + to_event + "/" + date_id + "/" + send_id);
                }
            }
        },
        _setup_events : function()
        {
            var self = this;

            self.model
                // 确认活动结束
                .on("before:confirm_date:fetch",function(response,options)
                {
                    m_alert.show('发送中','loading');
                })
                .on("success:confirm_date:fetch",function(response,options)
                {
                    var response = response.result_data;

                    var msg = response.msg;

                    m_alert.hide();

                    if(response.code >0)
                    {
                        m_alert.show(msg,'right',{delay:2000});

                        var data =
                        {
                            status : self.current_status,
                            page : 1
                        };

                        //self.model.get_list(data);

                        self.confirm_view.refresh();
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

        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="container"]');

            self.is_cameraman = self.get("templateModel").is_cameraman;

            var model = new container_model();
            //考虑中view
            self.consider_view = new contain_view
            ({
                model : model,
                parentNode: self.$container,
                templateModel : {type_tag : 'consider',role : self.get("templateModel").role,is_cameraman : self.is_cameraman}
            }).render();
            //确认view
            self.confirm_view = new contain_view
            ({
                model : model,
                parentNode: self.$container,
                templateModel : {type_tag : 'confirm',role : self.get("templateModel").role,is_cameraman : self.is_cameraman}
            }).render();

            //当前view标记
            self.current_type = self.get("type");
            //首次进入标记
            self.first_time_in_confirm = true;

            self.first_time_in_consider = true;

            if(self.current_type == 'consider')
            {
                self.current_type = 'consider';

                self.$('[data-role="considering"]').addClass("cur").siblings().removeClass("cur");

                self.confirm_view.hide();

                self.consider_view.show();

                self.consider_view.get_list();

                self.first_time_in_consider = false;

            }
            else if(self.current_type == 'confirm')
            {
                self.$('[data-role="agreed"]').addClass("cur").siblings().removeClass("cur");

                self.current_type = 'confirm';

                self.confirm_view.show();

                self.consider_view.hide();

                self.confirm_view.get_list();

                self.first_time_in_confirm = false;
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