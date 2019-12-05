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

            // ��װ�¼�
            self._setup_event();

            self.refresh();
        },
        render : function(data)
        {
            var self = this;

            var html_str = items_tpl({data:data});

            _self.$container.html(html_str);

            var btn_str = '';

            if(data.event_finish_button){var btn_str = '��ɻ';}
            if(data.event_cancel_button){var btn_str = 'ȡ���';}
            if(data.enroll_cancel_button){var btn_str = 'ɾ������';}


            header.init({
                ele : $("#global-header"), //ͷ����Ⱦ�Ľڵ�
                title:"��������",
                header_show : true , //�Ƿ���ʾͷ��
                mt_0_ele : $("#seller-list-page"), //���ͷ�����أ�Ҫ�ѵ�ǰҳ�ڵ�margin-top��Ϊ0
                right_icon_show : true, //�Ƿ���ʾ�ұߵİ�ť
                share_icon :
                {
                    show :false,  //�Ƿ���ʾ����ťicon
                    content:""
                },
                omit_icon :
                {
                    show :false,  //�Ƿ���ʾ����Բ��icon
                    content:""
                },
                show_txt :
                {
                    show :false,  //�Ƿ���ʾ����
                    content:btn_str,  //��ʾ��������
                    style : 'width:80px'
                }
            });

            $('[data-role="act-status"]').on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var status = $cur_btn.attr('data-act-status');

                if(status == 'event_finish_button')
                {
                    if(confirm('�Ƿ�����'))
                    {
                        self.set_act_status({event_id: _self._page_data.event_id,type:'end'});
                    }
                }
                else if(status == 'event_cancel_button')
                {
                    if(confirm('�Ƿ�ȡ���'))
                    {
                        self.set_act_status({event_id: _self._page_data.event_id,type:'cancel'});

                    }
                }
                else if(status == 'enroll_cancel_button')
                {
                    if(confirm('�Ƿ�ȡ������'))
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
         * ��װ�¼�
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
                            alert('��ά�벻����');

                            return;
                        }


                        // �Ŵ��ά����������
                        var pic_w_h = Math.ceil(((utility.get_view_port_width() - 90)));//���������
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
                                name : '��������',
                                url_img : qr_arr[i].qr_code_url_img
                            };
                        }

                        // ���ݽṹ
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
                                // �����ά��
                                var qrcode = require('../qrcode/qrcode');
                                var qrcode_obj = new qrcode({
                                    ele : $('#render_qrcode'), //��Ⱦ�Ľڵ�
                                    play : "0" , //���ŵڼ��ţ�����Ĭ�ϵ�һ��
                                    data : 
                                    {
                                        name:  $('[data_nick_name]').attr('data_nick_name'),
                                        user_icon : $('[data_user_icon]').attr('data_user_icon'),
                                        user_id : $('[data_user_id]').attr('data_user_id'),
                                        data_arr : qr_arr
                                    }
                                    
                                })
                                // �����ά�� end
                        }



                    },
                    error: function()
                    {
                        alert('�����쳣');
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
                            content:'������...'
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
                        content:'�����쳣',
                        stayTime:3000,
                        type:'warn'
                    });
                }
            });


        }
    };

    var detail_obj = new detail_class();


};