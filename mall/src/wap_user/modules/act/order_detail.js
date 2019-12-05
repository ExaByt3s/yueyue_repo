/**
 * Created by hudingwen on 15/7/21.
 */

var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var scroll = require('../common/scroll/index');
var abnormal = require('../common/widget/abnormal/index');
var items_tpl = __inline('./order_detail.tmpl');
var header = require('../common/widget/header/main');

if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

exports.init = function()
{
    var _self = $({});

    var detail_class = function()
    {
        var self = this;

        self.init();
    };

    detail_class.prototype =
    {
        refresh : function()
        {
            var self = this;

            self.render(_self._page_data);

        },
        init : function()
        {
            var self = this;

            _self._page_params = _page_params.result_data;

            _self._page_data = _page_data.result_data;

            _self.$container = $('[data-role="insert-container"]');

            App.isPaiApp && App.showtopmenu(false);

            // 安装事件
            self._setup_event();

            self.refresh();
        },
        render : function(data)
        {
            var self = this;

            var html_str = items_tpl({data:data});

            _self.$container.html(html_str);

            var btn_str = '';

            if(data.event_finish_button){var btn_str = '完成活动';}
            if(data.event_cancel_button){var btn_str = '取消活动';}
            if(data.enroll_cancel_button){var btn_str = '删除订单';}


            header.init({
                ele : $("#global-header"), //头部渲染的节点
                title:"标题内容",
                header_show : true , //是否显示头部
                mt_0_ele : $("#seller-list-page"), //如果头部隐藏，要把当前页节点margin-top改为0
                right_icon_show : true, //是否显示右边的按钮
                share_icon :
                {
                    show :false,  //是否显示分享按钮icon
                    content:""
                },
                omit_icon :
                {
                    show :false,  //是否显示三个圆点icon
                    content:""
                },
                show_txt :
                {
                    show :false,  //是否显示文字
                    content:btn_str,  //显示文字内容
                    style : 'width:80px'
                }
            });

            $('[data-role="act-status"]').on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var status = $cur_btn.attr('data-act-status');

                if(status == 'event_finish_button')
                {
                    if(confirm('是否结束活动'))
                    {
                        self.set_act_status({event_id: _self._page_data.event_id,type:'end'});
                    }
                }
                else if(status == 'event_cancel_button')
                {
                    if(confirm('是否取消活动'))
                    {
                        self.set_act_status({event_id: _self._page_data.event_id,type:'cancel'});

                    }
                }
                else if(status == 'enroll_cancel_button')
                {
                    if(confirm('是否取消报名'))
                    {
                        self.set_act_status({enroll_id: _self._page_data.enroll_id,type:'del_enroll'});
                    }
                }
            });

            if(!App.isPaiApp)
            {
               $('[data-role="act-go-scan"]').addClass('fn-hide');     
            }

            self._setup_button_event();

        },
        /**
         * 安装事件
         * @private
         */
        _setup_event : function()
        {
            var self = this;


        },
        _setup_button_event : function()
        {
            var self = this;



            $('[data-role="act-show-qr"]').on('click',function(ev)
            {
                utility.ajax_request
                ({
                    url : window.$__ajax_domain+'get_act_ticket_detail.php',
                    data :
                    {
                        event_id : _self._page_data.event_id,
                        enroll_id : _self._page_data.enroll_id
                    },
                    success : function(data)
                    {

                        var qr_arr = data.result_data.data;

                        if(qr_arr.length == 0)
                        {
                            alert('二维码不存在');

                            return;
                        }


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
                                name : '数字密码',
                                url_img : qr_arr[i].qr_code_url_img
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
                                // 处理二维码
                                var qrcode = require('../qrcode/qrcode');
                                var qrcode_obj = new qrcode({
                                    ele : $('#render_qrcode'), //渲染的节点
                                    play : "0" , //播放第几张，不传默认第一张
                                    data : 
                                    {
                                        name:  $('[data_nick_name]').attr('data_nick_name'),
                                        user_icon : $('[data_user_icon]').attr('data_user_icon'),
                                        user_id : $('[data_user_id]').attr('data_user_id'),
                                        data_arr : qr_arr
                                    }
                                    
                                })
                                // 处理二维码 end
                        }



                    },
                    error: function()
                    {
                        alert('网络异常');
                    }
                });
            });


        },

        set_act_status : function(data)
        {
            var self = this;

            data = data || {};

            self._sending = false;

            if(self._sending)
            {
                return;
            }

            utility.ajax_request
            ({
                url : window.$__ajax_domain + 'set_act_status.php',
                data : data,
                beforeSend : function()
                {
                    self._sending = true;

                    setTimeout(function()
                    {
                        _self.$loading = $.loading
                        ({
                            content:'加载中...'
                        });
                    },0);
                },
                success : function(res)
                {
                    _self.$loading.loading("hide");

                    if(res.result_data.code)
                    {
                        window.location.href = window.location.href ;
                    }
                    else
                    {
                        $.tips
                        ({
                            content:res.result_data.msg,
                            stayTime:3000,
                            type:'warn'
                        });
                    }
                },
                error : function()
                {
                    _self.$loading.loading("hide");

                    $.tips
                    ({
                        content:'网络异常',
                        stayTime:3000,
                        type:'warn'
                    });
                }
            });


        }
    };

    var detail_obj = new detail_class();


};