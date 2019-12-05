define('act/pay', function(require, exports, module){ /**
 * ֧��ҳ��
 hudw 2015.4.15
 **/
"use strict";

var back_btn = require('common/widget/back_btn/main');
var utility = require('common/utility/index');
var ua = require('common/ua/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var coupon = require('coupon/list');
var yueyue_header = require('common/widget/yueyue_header/main');
var header = require('common/widget/header/main');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{




    var $header = $('#nav-header');

    var tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div id=\"global-header\"></div>\n\n<ul class=\"info ui-list ui-list-text pt15 pr15 pb5\">\n    <li>\n        <p>����ƣ�"
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.event_title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\n    </li>\n    <li>\n        <div>�ֻ����룺"
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n    <li>\n        <div>��۸񣺣�"
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n    <li>\n        <div>����������"
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n    <li>\n        <div>ѡ�񳡴Σ�"
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list)),stack1 == null || stack1 === false ? stack1 : stack1.table_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n    </li>\n</ul>\n\n\n\n\n<div class=\"mt30 pl15 mb10\">Ӧ�����</div>\n\n<!--֧��ģ��-->\n<ul class=\"ui-list ui-list-text \" class=\"ui-border-t\">\n    <li class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">֧���ܼۣ�<span >��<label data-role=\"pay-amount\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span></div>\n    </li>\n    <li data-role=\"coupon-money\" class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">ʹ���Ż�ȯ��<span><div class=\"ui-nowrap\" style=\"width: 200px;\" data-role=\"coupon-money-tag\"></div></span></div>\n        <div class=\"ui-edge-right\">\n            <span class=\"count-money-color\" data-role=\"coupon-money-text\"></span>\n            <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n        </div>\n    </li>\n    <li data-role=\"select-available-balance\" class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">Ǯ��<span>����<label data-role=\"available_balance\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.available_balance)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span>����<span class=\"count-money-color\" data-role=\"less-money\"></span></div>\n        <div class=\"ui-edge-right\">\n\n            <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n        </div>\n    </li>\n    <li class=\"ui-border-t\">\n        <div class=\"ui-txt-default \">����֧����<span class=\"count-money-color_v2 fb\">��<label data-role=\"need-price\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span></div>\n    </li>\n</ul>\n<ul class=\"ui-list ui-list-text ui-border-b mt20 fn-hide\" style=\"margin-bottom: 0;\"  data-role=\"must-pay-container\"></ul>\n\n<div data-role=\"other-pay-container\">\n    <div class=\"mt30 pl15 mb10\">֧����ʽ</div>\n    <ul class=\"ui-list ui-list-text \" >\n        <li class=\"ui-border-t\" data-pay-type=\"alipay_purse\" data-role=\"pay-li\">\n            <div class=\"ui-txt-default \">\n                <div class=\"pay-type\">\n                    <i class=\"icon icon-zhifubao\"></i>\n                    <div class=\"ui-list-info \">\n                        <h4 class=\"ui-nowrap\" >֧����֧��</h4>\n                        <p class=\"ui-nowrap\">�Ƽ���֧�����˺ŵ��û�ʹ��</p>\n                    </div>\n                </div>\n                <div class=\"ui-edge-right\">\n                    <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n                </div>\n            </div>\n        </li>\n        <li class=\"ui-border-t\" data-pay-type=\"tenpay_wxapp\" data-role=\"pay-li\">\n            <div class=\"ui-txt-default \">\n                <div class=\"pay-type\">\n                    <i class=\"icon icon-wx-pay\"></i>\n                    <div class=\"ui-list-info \">\n                        <h4 class=\"ui-nowrap\" >΢��֧��</h4>\n                        <p class=\"ui-nowrap\">��װ΢��5.0�����ϰ汾��ʹ��</p>\n                    </div>\n                </div>\n                <div class=\"ui-edge-right\">\n                    <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\n                </div>\n            </div>\n        </li>\n    </ul>\n\n    <div style=\"height:100px;\" class=\"bg\"></div>\n\n</div>\n\n\n\n\n<div class=\"last-container fn-hide\"></div>\n<!--֧��ģ��-->\n<div class=\"buttom-btn-wrap ui-border-t\">\n    <div class=\"pl10 text-info\">\n        ����֧����<span class=\"count-money-color_v2 red-font\" style=\"font-size: 18px;\">��<label data-role=\"need-price\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span>\n    </div>\n    <div class=\"right\">\n        <button class=\"ui-tt-pay-btn \" id=\"pay-btn\">\n            <span class=\"ui-button-content\" ><i class=\"icon icon-btn-icon-fk \"></i></span>\n            <span class=\"txt\">ȥ֧��</span>\n        </button>\n    </div>\n\n</div>";
  return buffer;
  });

    var _self = {};

    // Ĭ�ϲ���
    var defaults=
        {
            title : '����'
        };

    // ��ȡ����
    _self.order_sn = $('#order_sn').val();

    // ��ʼ��dom
    _self.$page_container = $('[data-role="page-container"]');

    // ��ʼ��ͳ�Ƶ�
    if(App.isPaiApp)
    {
        //App.analysis('moduletongji',{pid:'1220089',mid:'122OD04006'});
    }

    // ���캯��
    var pay_tt_class = function()
    {
        var self = this;

        self.init();

    };

    // ��ӷ���������
    pay_tt_class.prototype =
    {
        refresh : function()
        {
            var self = this;

            var $loading=$.loading
            ({
                content:'������...'
            });

            var content = _page_data.result_data;

            _self.$page_container.html('');

            _self.$page_container.html(tpl({data:content}));

            _self.page_params = window._page_params.result_data;

            // ��ʼ���Ż�ȯ
            _self.coupon_obj = coupon.init
            ({
                container : $('[data-role="coupon-list-container"]'),
                order_total_amount :content.total_amount,
                extend_params :
                {
                    event_id : _self.page_params.event_id,
                    enroll_id : _self.page_params.enroll_id,
                    table_id : _self.page_params.table_id,
                    module_type : 'waipai'
                },
                page : 1

            });

            // ��Ⱦͷ��
            header.init({
                ele : $("#global-header"), //ͷ����Ⱦ�Ľڵ�
                title:"֧��",
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


            // ��װ�¼�
            self.setup_event();

            $loading.loading("hide");
        },
        page_back : function()
        {

        },
        // ��ʼ��
        init : function()
        {
            var self = this;
            var $back_btn_html = back_btn.render();
            $header.prepend($back_btn_html);



            // ��װ�����¼�
            self.setup_back();
        },
        hide : function()
        {

        },
        // ��װ���˰�ť
        setup_back : function()
        {
            $('[data-role="page-back"]').on('tap',function()
            {
                if(App.isPaiApp)
                {
                    App.app_back();
                }
            });
        },
        // ��װ�¼�
        setup_event : function()
        {
            var self = this;

            _self.$pay_li = $('[data-role="pay-li"]');
            _self.$pay_btn = $('#pay-btn');
            _self.$select_ab_btn = $('[data-role="select-available-balance"]');
            _self.$less_money = $('[data-role="less-money"]');
            _self.$available_balance = $('[data-role="available_balance"]');
            _self.$need_price = $('[data-role="need-price"]');
            _self.ab = $('[data-role="available_balance"]').html();
            _self.total_price = $('[data-role="pay-amount"]').html();
            _self.$coupon_list_wrap = $('[data-role="coupon-list-wrap"]');
            _self.$coupon_money = $('[data-role="coupon-money"]');
            _self.$coupon_money_tag = $('[data-role="coupon-money-tag"]');
            _self.$coupon_money_text = $('[data-role="coupon-money-text"]');

            // ��װ�Ż�ȯͷ��
            yueyue_header.render($('[data-role="nav-header"]')[0],
                {
                    left_text : '����',
                    title:'�����Ż�ȯ',
                    right_text : 'ȷ��'
                });

            _self.$left_btn = $('[data-role="left-btn"]');
            _self.$right_btn = $('[data-role="right-btn"]');

            // Ĭ������ѡ��֧����
            _self.selected_pay_action_type ='alipay_purse';

            // Ĭ������ѡ�����
            _self.can_use_balance = true;

            // ѡ�����֧��
            _self.$select_ab_btn.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');
                var tag = $yes_tag.hasClass('icon-select-active');

                if(tag)
                {
                    $yes_tag.addClass('icon-select-no').removeClass('icon-select-active');
                }
                else
                {
                    $yes_tag.removeClass('icon-select-no').addClass('icon-select-active');
                }

                _self.can_use_balance = $yes_tag.hasClass('icon-select-active');

                var pay_items_model =
                    {
                        can_use_balance : _self.can_use_balance,
                        available_balance : _self.ab,
                        total_price : _self.total_price,
                        coupon : _self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.face_value
                    };

                // ��ʼ����
                self.count_money(pay_items_model);
            });

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

            // �ر��Ż�ȯ��
            _self.$left_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');

                if(!ua.is_yue_app)
                {
                    $('main').css('padding-top','45px');
                }
            });

            // �ر��Ż�ȯ��
            _self.$right_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');

                //Ҫʵ��ѡ���Ż�ȯʱ��Ĵ���
                var selected_coupon = _self.coupon_obj.selected_coupon;

                var pay_items_model =
                    {
                        can_use_balance : _self.can_use_balance,
                        available_balance : _self.ab,
                        total_price : _self.total_price,
                        set_pay_type : false,
                        coupon : selected_coupon && selected_coupon.face_value
                    };

                // ��ʼ����
                self.count_money(pay_items_model);

                if(selected_coupon)
                {
                    // ��ʾ�Ż���Ϣ
                    _self.$coupon_money_tag.html(selected_coupon.batch_name);
                }

                if(selected_coupon)
                {
                    _self.$coupon_money.find('[data-role="yes-tag"]').addClass('icon-select-active').removeClass('icon-select-no');
                }
                else
                {
                    _self.$coupon_money.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');
                }

                if(!ua.is_yue_app)
                {
                    $('main').css('padding-top','45px');
                }
            });

            // �����Ż�ȯ�б�
            _self.$coupon_money.on('click',function(ev)
            {
                _self.$page_container.addClass('fn-hide');
                _self.$coupon_list_wrap.removeClass('fn-hide');

                if(!ua.is_yue_app)
                {
                    $('main').css('padding-top',0);
                }
            });

            // ȷ����ť
            _self.$pay_btn.on('click',function()
            {
                //App.analysis('eventtongji',{id:'1220090'});

                if(!_self.selected_pay_action_type)
                {
                    alert('��ѡ��֧����ʽ');
                    return;
                }


                if(confirm('ȷ��֧��?'))
                {
                    var redirect_url = './sign.php?event_id='+_page_params.result_data.event_id;

                    var params =
                        {
                            third_code : _self.selected_pay_action_type,
                            redirect_url : redirect_url,
                            coupon_sn : (_self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.coupon_sn) || '',
                            available_balance : _self.ab,
                            is_available_balance : _self.$select_ab_btn.find('[data-role="yes-tag"]').hasClass('icon-select-active') ? 1 : 0

                        };

                    params = $.extend({},params,_page_params.result_data,true);

                    self.pay_action(params,
                        {
                            success :function(res)
                            {
                                var third_code = res.result_data && res.result_data.third_code;
                                var channel_return = res.result_data && res.result_data.channel_return;
                                var code = res.result_data && res.result_data.code;

                                if(code == 2)
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

            // ��ʼ������
            var pay_items_model =
                {
                    can_use_balance : _self.ab>0?true:false,
                    available_balance : _self.ab,
                    total_price : _self.total_price
                };

            // ��ʼ����
            self.count_money(pay_items_model);
        },
        count_money : function(params)
        {
            var self = this;

            var total_price = utility.float(params.total_price);

            var available_balance = utility.float(params.available_balance);

            var set_pay_type = params.set_pay_type == null ? true : false;

            var coupon = params.coupon || 0;

            // Ǯ��Ҫ�۵�Ǯ
            var less_money = 0;

            // ʹ���Ż݄���ʱ��
            if(coupon)
            {
                total_price = total_price - utility.float(coupon);
            }

            // ��Ҫ����Ǯ
            var must_pay_money = available_balance - total_price;

            // ʹ�����֧��
            if(params.can_use_balance)
            {

                less_money = must_pay_money;

                if(less_money <= 0)
                {
                    less_money = available_balance;
                }
                else
                {
                    less_money = total_price;

                    must_pay_money = 0;
                }

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-no').addClass('icon-select-active');

                // �����ȫ��Ǯ֧���˶���
                if(must_pay_money>=0)
                {
                    self.clear_select();

                    self.control_other_pay_item({show:false});

                    console.log('�����ȫ��Ǯ֧���˶���');
                }
                // ����Ǯ֧��������ʱ����Ҫ���֧��
                else
                {

                    self.control_other_pay_item({show:true});

                    // ����Ĭ��֧����ʽ�������Ż݄�������Ĭ��֧����ʽ
                    if(set_pay_type)
                    {
                        //self.pay_item_obj._select_pay_type('alipay_purse');
                        $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                            .removeClass('icon-select-no').addClass('icon-select-active');
                    }
                }
            }
            // ��ȫʹ�õ�����֧��
            else
            {
                // ��Ҫ����Ǯ�����ܼ� ��ȥ �Ż݄�
                must_pay_money = total_price;

                self.clear_select(true);

                self.control_other_pay_item({show:true});

                // ����Ĭ��֧����ʽ�������Ż݄�������Ĭ��֧����ʽ
                if(set_pay_type)
                {
                    $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                        .removeClass('icon-select-no').addClass('icon-select-active');
                }
            }

            self.must_pay_money = utility.format_float(must_pay_money,2);

            if(self.must_pay_money<0)
            {
                self.must_pay_money = self.must_pay_money * -1;
            }

            _self.$need_price.html(self.must_pay_money);
            _self.$less_money.html('-��'+less_money);

            if(coupon)
            {
                _self.$coupon_money_text.html('-��'+coupon);
            }
            else
            {
                _self.$coupon_money_text.html('');
            }



        },
        // tt ֧������
        pay_action : function(params,callback)
        {
            var $loading=$.loading
            ({
                content:'������...'
            });

            var url = 'join_again_act.php';

            utility.ajax_request
            ({
                url : window.$__ajax_domain+url,
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
        /**
         * ���ָ��ѡ����
         * @param tag
         */
        clear_select : function(tag)
        {
            var self = this;

            var $yes_tag =_self.$pay_li.find('[data-role="yes-tag"]');

            if(tag)
            {
                _self.$select_ab_btn.find('[data-role="yes-tag"]')
                    .removeClass('icon-select-active').addClass('icon-select-no');
            }
            // ������ָ����ǣ��������ѡ��
            else
            {
                $yes_tag.removeClass('icon-select-active').addClass('icon-select-no');
            }
        },
        /**
         * ���Ƶ�����֧����ʾ����
         * @param options
         */
        control_other_pay_item : function(options)
        {
            var self = this;

            if(options.show)
            {
                $('[data-role="other-pay-container"]').removeClass('fn-hide');
                $('[data-role="must-pay-container"]').removeClass('last-sec-container');
            }
            else
            {
                $('[data-role="other-pay-container"]').addClass('fn-hide');
                $('[data-role="must-pay-container"]').addClass('last-sec-container');
            }



        },
        after_pay_text : function(code) {
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
        }
    };

    // ʵ����tt֧����
    var pt_obj = new pay_tt_class();

    pt_obj.refresh();



})($,window);



/**
 * Created by hudingwen on 15/7/21.
 */
 
});