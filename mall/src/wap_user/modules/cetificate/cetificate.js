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

    // 跳转提示文案
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

            // 渲染头部
            header.init({
                ele : $("#global-header"), //头部渲染的节点
                title:"V2等级认证",
                header_show : true , //是否显示头部
                right_icon_show : false, //是否显示右边的按钮

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
                    content:"编辑"  //显示文字内容
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

            // 立即拍照
            $btn_shoot.on('click',function()
            {
                $v2_container.addClass('fn-hide');
                $v2_form.removeClass('fn-hide');

                start_upload
                ({
                    img_obj : $v2_form.find('[data-role="images"]')
                });
            });

            // 重新上传
            $btn_shoot_retry.on('click',function()
            {
                start_upload
                ({
                    img_obj : $v2_form.find('[data-role="images"]')
                });
            });

            // 提交审核
            $submit_v2.on('click',function()
            {
                var name = $('[data-role="name"]');
                var id = $('[data-role="id"]');
                var error_tips = '';

                if(!name.val())
                {
                    error_tips = '请填写你的真实姓名';
                }
                if(!id.val())
                {
                    error_tips = '请输入你的身份证号';
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
                            content:'发送中...'
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
                            content:'网络异常',
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

            // 默认设置选中支付宝
            _self.selected_pay_action_type ='alipay_purse';

            // 默认设置选中余额
            _self.can_use_balance = true;

            self.setup_event();

            // 渲染头部
            header.init({
                ele : $("#global-header"), //头部渲染的节点
                title:"V3等级认证",
                header_show : true , //是否显示头部
                right_icon_show : false, //是否显示右边的按钮

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
                    content:"编辑"  //显示文字内容
                }
            });
        },
        pay_action : function(params,callback)
        {
            var $loading=$.loading
            ({
                content:'发送中...'
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

                        // 支付成功
                        callback.success.call(this,res);

                        var type = 'success';
                    }
                    else
                    {
                        // 支付失败

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
                        content:'网络异常',
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
                    str = '支付成功';
                    break;
                case 0:
                    str = '其它错误';
                    break;
                case -3:
                    str = '支付失败';
                    break;
                case -4:
                    str = '支付取消';
                    break;
            }

            return str;
        },
        setup_event : function()
        {
            var self = this;

            // 选择支付方式选中
            _self.$pay_li.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

                var tag = $yes_tag.hasClass('icon-select-active');

                // 清空所有选中的，用于以后扩展
                _self.$pay_li.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');

                $yes_tag.addClass('icon-select-active').removeClass('icon-select-no');

                var pay_type =  $cur_btn.attr('data-pay-type');

                // 设置是否选中支付方式
                _self.selected_pay_action_type =pay_type;

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-active');

                if(_self.can_use_balance)
                {
                    _self.$select_ab_btn.find('[data-role="yes-tag"]').addClass('icon-select-active');
                }

            });

            // 点击充值
            _self.$recharge_btn.on('click',function()
            {

                if(!_self.selected_pay_action_type)
                {
                    alert('请选择支付方式');
                    return;
                }

                if(confirm('确认支付?'))
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
                                        // 支付宝支付，调用App接口
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
                                        // 微信支付
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
                                        content:'余额支付成功',
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
     * 传图
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
            //todo 预留非App使用传图功能
        }
    }





})($,window);