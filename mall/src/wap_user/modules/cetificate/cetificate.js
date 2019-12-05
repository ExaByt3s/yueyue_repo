/**
 * Created by hudingwen on 15/7/13.
 */
"use strict";


var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var utility = require('../common/utility/index');
var header = require('../common/widget/header/main');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{

    var _self = $({});

    var lv = $('#lv').val();

    // ��ת��ʾ�İ�
    if(window.__cetificate_tips)
    {
        alert(window.__cetificate_tips);
    }

    var v2_class = function()
    {
        var self = this;

        self.init();
    };

    v2_class.prototype =
    {
        init : function()
        {
            var self = this;

            // ��Ⱦͷ��
            header.init({
                ele : $("#global-header"), //ͷ����Ⱦ�Ľڵ�
                title:"V2�ȼ���֤",
                header_show : true , //�Ƿ���ʾͷ��
                right_icon_show : false, //�Ƿ���ʾ�ұߵİ�ť

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
                    content:"�༭"  //��ʾ��������
                }
            });

            self.setup_event();
        },
        setup_event : function()
        {
            var $btn_shoot = $('[data-role="btn-shoot"]');
            var $btn_shoot_retry = $('[data-role="btn-shoot-retry"]');
            var $v2_container = $('[data-role="photo"]');
            var $v2_form = $('[data-role="form"]');
            var $submit_v2 = $('[data-role="submit-v2"]');

            _self.$loading = {};

            // ��������
            $btn_shoot.on('click',function()
            {
                $v2_container.addClass('fn-hide');
                $v2_form.removeClass('fn-hide');

                start_upload
                ({
                    img_obj : $v2_form.find('[data-role="images"]')
                });
            });

            // �����ϴ�
            $btn_shoot_retry.on('click',function()
            {
                start_upload
                ({
                    img_obj : $v2_form.find('[data-role="images"]')
                });
            });

            // �ύ���
            $submit_v2.on('click',function()
            {
                var name = $('[data-role="name"]');
                var id = $('[data-role="id"]');
                var error_tips = '';

                if(!name.val())
                {
                    error_tips = '����д�����ʵ����';
                }
                if(!id.val())
                {
                    error_tips = '������������֤��';
                }

                if(error_tips)
                {
                    $.tips
                    ({
                        content:error_tips,
                        stayTime:3000,
                        type:'warn'
                    });
                    return;
                }

                utility.ajax_request
                ({
                    url : window.$__ajax_domain+'add_id.php',
                    data : {
                        name : name.val(),
                        id_code : id.val(),
                        img : $v2_form.find('[data-role="images"]').attr('src')
                    },
                    type : 'POST',
                    beforeSend : function()
                    {
                        _self.$loading=$.loading
                        ({
                            content:'������...'
                        });
                    },
                    success : function(res)
                    {
                        _self.$loading.loading("hide");

                        if(res.result_data.code == 1)
                        {
                            var type = 'success';

                            $.tips
                            ({
                                content:res.result_data.msg,
                                stayTime:3000,
                                type:type
                            });

                            if(res.result_data.data.url)
                            {
                                window.location.href = res.result_data.data.url;
                            }

                        }
                        else
                        {
                            var type = 'warn';

                            $.tips
                            ({
                                content:res.result_data.msg,
                                stayTime:3000,
                                type:type
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
            });
        }
    };

    var v3_class = function()
    {
        var self = this;

        self.init();
    };

    v3_class.prototype =
    {
        init : function()
        {
            var self = this;

            _self.$pay_li = $('[data-role="pay-li"]');
            _self.$pay_btn = $('#pay-btn');
            _self.$select_ab_btn = $('[data-role="select-available-balance"]');
            _self.$recharge_btn = $('[data-role="btn-recharge"]');

            // Ĭ������ѡ��֧����
            _self.selected_pay_action_type ='alipay_purse';

            // Ĭ������ѡ�����
            _self.can_use_balance = true;

            self.setup_event();

            // ��Ⱦͷ��
            header.init({
                ele : $("#global-header"), //ͷ����Ⱦ�Ľڵ�
                title:"V3�ȼ���֤",
                header_show : true , //�Ƿ���ʾͷ��
                right_icon_show : false, //�Ƿ���ʾ�ұߵİ�ť

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
                    content:"�༭"  //��ʾ��������
                }
            });
        },
        pay_action : function(params,callback)
        {
            var $loading=$.loading
            ({
                content:'������...'
            });

            utility.ajax_request
            ({
                url : window.$__ajax_domain+'pay_recharge.php',
                data : params,
                type : 'POST',
                success : function(res)
                {
                    $loading.loading("hide");

                    if(res.result_data && res.result_data.code>0)
                    {

                        // ֧���ɹ�
                        callback.success.call(this,res);

                        var type = 'success';
                    }
                    else
                    {
                        // ֧��ʧ��

                        var type = 'warn';

                        $.tips
                        ({
                            content:res.result_data.message,
                            stayTime:3000,
                            type:type
                        });
                    }



                },
                error : function()
                {
                    callback.error.call(this);

                    $loading.loading("hide");

                    $.tips
                    ({
                        content:'�����쳣',
                        stayTime:3000,
                        type:'warn'
                    });
                }
            });
        },
        after_pay_text : function(code)
        {
            var str = '';

            switch (utility.int(code)) {
                case 1:
                case -2:
                case -1:
                    str = '֧���ɹ�';
                    break;
                case 0:
                    str = '��������';
                    break;
                case -3:
                    str = '֧��ʧ��';
                    break;
                case -4:
                    str = '֧��ȡ��';
                    break;
            }

            return str;
        },
        setup_event : function()
        {
            var self = this;

            // ѡ��֧����ʽѡ��
            _self.$pay_li.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

                var tag = $yes_tag.hasClass('icon-select-active');

                // �������ѡ�еģ������Ժ���չ
                _self.$pay_li.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');

                $yes_tag.addClass('icon-select-active').removeClass('icon-select-no');

                var pay_type =  $cur_btn.attr('data-pay-type');

                // �����Ƿ�ѡ��֧����ʽ
                _self.selected_pay_action_type =pay_type;

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-active');

                if(_self.can_use_balance)
                {
                    _self.$select_ab_btn.find('[data-role="yes-tag"]').addClass('icon-select-active');
                }

            });

            // �����ֵ
            _self.$recharge_btn.on('click',function()
            {

                if(!_self.selected_pay_action_type)
                {
                    alert('��ѡ��֧����ʽ');
                    return;
                }

                if(confirm('ȷ��֧��?'))
                {
                    var redirect_url = window.location.href;

                    var params =
                        {
                            third_code : _self.selected_pay_action_type,
                            redirect_url : redirect_url,
                            amount : $('#price').val(),
                            type : 'recharge'
                        };

                    self.pay_action(params,
                        {
                            success :function(res)
                            {
                                var third_code = res.result_data && res.result_data.third_code;
                                var channel_return = res.result_data && res.result_data.channel_return ;
                                var result = res.result_data && res.result_data.code;

                                if(result == 1)
                                {
                                    switch(third_code)
                                    {
                                        // ֧����֧��������App�ӿ�
                                        case 'alipay_purse':
                                            App.alipay
                                            ({
                                                alipayparams : res.result_data.data,
                                                payment_no : res.result_data.payment_no
                                            },function(data)
                                            {
                                                var result = utility.int(data.result);

                                                var text = self.after_pay_text(result);

                                                $.tips
                                                ({
                                                    content:text,
                                                    stayTime:3000,
                                                    type:'success'
                                                });

                                                if(result == 1 || result ==-1 || result == -2)
                                                {
                                                    window.location.href = channel_return;
                                                }
                                            });
                                            break;
                                        // ΢��֧��
                                        case 'tenpay_wxapp':
                                            App.wxpay(JSON.parse(res.result_data.data),function(data)
                                            {
                                                var result = utility.int(data.result);

                                                var text = self.after_pay_text(result);

                                                $.tips
                                                ({
                                                    content:text,
                                                    stayTime:3000,
                                                    type:'success'
                                                });

                                                if(result == 1 || result ==-1 || result == -2)
                                                {
                                                    window.location.href = channel_return;
                                                }
                                            });
                                            break;
                                    }
                                }
                                else
                                {
                                    $.tips
                                    ({
                                        content:'���֧���ɹ�',
                                        stayTime:3000,
                                        type:'success'
                                    });

                                    window.location.href = channel_return;
                                }






                            },
                            error : function()
                            {

                            }
                        });
                }
            });
        }
    };

    if(lv == 2)
    {
        var v2_obj = new v2_class();
    }
    else
    {
        var v3_obj = new v3_class();
    }


    /**
     * ��ͼ
     * @param options
     */
    function start_upload(options)
    {
        options = options || {};

        if(App.isPaiApp)
        {
            App.upload_img
            ('multi_img',{
                is_async_upload : 0,
                max_selection : 1,
                is_zip : 1

            },function(data)
            {
                var img = data.imgs[0].url;

                options.img_obj.attr('src',img);

                var big_img = utility.matching_img_size(img,'320');

                var new_big_img = new Image();
                new_big_img.src = big_img;
                new_big_img.onload = function()
                {
                    var oldWidth = this.width;
                    var oldHeight = this.height;

                    var newWidth = utility.get_view_port_width() - 30;
                    var newHeight = (newWidth * oldHeight) / oldWidth;

                    options.img_obj.attr('src',big_img).css('min-height',newHeight+'px');
                }
            });
        }
        else
        {
            //todo Ԥ����Appʹ�ô�ͼ����
        }
    }





})($,window);