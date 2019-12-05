define('pay_tt', function(require, exports, module){ /**
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


    App.isPaiApp && App.showtopmenu(false);

    var $header = $('#nav-header'); 

    var tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list), {hash:{},inverse:self.noop,fn:self.programWithDepth(2, program2, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n";
  return buffer;
  }
function program2(depth0,data,depth1) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n    <ul class=\"info ui-list ui-list-text pt15 pr15 pb5\">\r\n    	<li>\r\n    		<p>��Ʒ���ƣ�";
  if (helper = helpers.goods_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n    	</li>\r\n        <li>\r\n            <div>���";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.prices_spec), {hash:{},inverse:self.program(5, program5, data),fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</div>\r\n        </li>\r\n        <li>\r\n            <div>ȫ���"
    + escapeExpression(((stack1 = ((stack1 = (depth1 && depth1.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\r\n        </li>\r\n        <li>\r\n            <div>���ڣ�";
  if (helper = helpers.service_time_str) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.service_time_str); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n        </li>\r\n    </ul>\r\n    ";
  return buffer;
  }
function program3(depth0,data) {
  
  var stack1, helper;
  if (helper = helpers.prices_spec) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.prices_spec); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  return escapeExpression(stack1);
  }

function program5(depth0,data) {
  
  
  return "��";
  }

function program7(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.activity_list), {hash:{},inverse:self.noop,fn:self.programWithDepth(8, program8, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n";
  return buffer;
  }
function program8(depth0,data,depth1) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\r\n    <ul class=\"info ui-list ui-list-text pt15 pr15 pb5\">\r\n        <li>\r\n            <p>����ƣ�";
  if (helper = helpers.activity_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.activity_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n        </li>\r\n        <li>\r\n            <div>ʱ��/���Σ�"
    + escapeExpression((helper = helpers.formatDateTime || (depth0 && depth0.formatDateTime),options={hash:{},data:data},helper ? helper.call(depth0, "Y.m.d H:i", (depth0 && depth0.service_start_time), options) : helperMissing.call(depth0, "formatDateTime", "Y.m.d H:i", (depth0 && depth0.service_start_time), options)))
    + " ";
  if (helper = helpers.stage_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.stage_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n        </li>\r\n        <li>\r\n            <div>ȫ���"
    + escapeExpression(((stack1 = ((stack1 = (depth1 && depth1.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\r\n        </li>\r\n        <li>\r\n            <div>����������";
  if (helper = helpers.quantity) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.quantity); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n        </li>\r\n        <li>\r\n            <div>�ֻ����룺";
  if (helper = helpers.service_cellphone) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.service_cellphone); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n        </li>\r\n    </ul>\r\n    ";
  return buffer;
  }

function program10(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.payment_list), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n";
  return buffer;
  }
function program11(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n    <ul class=\"info ui-list ui-list-text pt15 pr15 pb5\">\r\n        <li>\r\n            <div class=\"ui-avatar-icon ui-avatar-icon-m\">\r\n                <i style=\"background-image:url(";
  if (helper = helpers.seller_icon) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.seller_icon); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + ")\"></i>\r\n            </div>\r\n            <div class=\"payment-info\">\r\n                <div class=\"name f16\">";
  if (helper = helpers.seller_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.seller_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n                <div class=\"num\">";
  if (helper = helpers.seller_user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.seller_user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n            </div>\r\n        </li>\r\n    </ul>\r\n    ";
  return buffer;
  }

function program13(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    <li class=\"ui-border-t vertical\">\r\n        <div class=\"ui-txt-default \">��ʹ���Żݣ�</div>\r\n        <div class=\"ui-txt-default \">\r\n            <i class=\"icon icon-pay-cu-36x36\"></i>\r\n            ";
  stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_promotion_info)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n        </div>\r\n    </li>\r\n    ";
  return buffer;
  }

function program15(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    <li class=\"ui-border-t vertical\">\r\n        <div class=\"ui-txt-default \">��ʹ���Żݣ�</div>\r\n        <div class=\"ui-txt-default \">\r\n            <i class=\"icon icon-pay-cu-36x36\"></i>\r\n            ";
  stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.activity_promotion_info)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n        </div>\r\n    </li>\r\n    ";
  return buffer;
  }

function program17(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    <input type=\"hidden\" value=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.no_allow_coupon_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" id=\"no_allow_coupon_text\" />\r\n    ";
  return buffer;
  }

function program19(depth0,data) {
  
  
  return "\r\n        <li class=\"ui-border-t\" data-pay-type=\"alipay_purse\" data-role=\"pay-li\">\r\n            <div class=\"ui-txt-default \">\r\n                <div class=\"pay-type\">\r\n                    <i class=\"icon icon-zhifubao\"></i>\r\n                    <div class=\"ui-list-info \">\r\n                        <h4 class=\"ui-nowrap\" >֧����֧��</h4>\r\n                        <p class=\"ui-nowrap\">�Ƽ���֧�����˺ŵ��û�ʹ��</p>\r\n                    </div>\r\n                </div>\r\n                <div class=\"ui-edge-right\">\r\n                    <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n                </div>\r\n            </div>\r\n        </li>\r\n        <li class=\"ui-border-t\" data-pay-type=\"tenpay_wxapp\" data-role=\"pay-li\">\r\n            <div class=\"ui-txt-default \">\r\n                <div class=\"pay-type\">\r\n                    <i class=\"icon icon-wx-pay\"></i>\r\n                    <div class=\"ui-list-info \">\r\n                        <h4 class=\"ui-nowrap\" >΢��֧��</h4>\r\n                        <p class=\"ui-nowrap\">��װ΢��5.0�����ϰ汾��ʹ��</p>\r\n                    </div>\r\n                </div>\r\n                <div class=\"ui-edge-right\">\r\n                    <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n                </div>\r\n            </div>\r\n        </li>\r\n        ";
  }

function program21(depth0,data) {
  
  
  return "\r\n            <li class=\"ui-border-t\" data-pay-type=\"tenpay_wxpub\" data-role=\"pay-li\">\r\n                <div class=\"ui-txt-default \">\r\n                    <div class=\"pay-type\">\r\n                        <i class=\"icon icon-wx-pay\"></i>\r\n                        <div class=\"ui-list-info \">\r\n                            <h4 class=\"ui-nowrap\" >΢��֧��</h4>\r\n                            <p class=\"ui-nowrap\">��װ΢��5.0�����ϰ汾��ʹ��</p>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"ui-edge-right\">\r\n                        <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n                    </div>\r\n                </div>\r\n            </li>\r\n        ";
  }

function program23(depth0,data) {
  
  
  return "\r\n        <li class=\"ui-border-t\" data-pay-type=\"alipay_wap\" data-role=\"pay-li\">\r\n                <div class=\"ui-txt-default \">\r\n                    <div class=\"pay-type\">\r\n                        <i class=\"icon icon-zhifubao\"></i>\r\n                        <div class=\"ui-list-info \">\r\n                            <h4 class=\"ui-nowrap\" >֧����֧��</h4>\r\n                            <p class=\"ui-nowrap\">�Ƽ���֧�����˺ŵ��û�ʹ��</p>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"ui-edge-right\">\r\n                        <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n                    </div>\r\n                </div>\r\n            </li>\r\n        ";
  }

  buffer += "<div id=\"global-header\"></div>\r\n\r\n";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.detail_list), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.activity_list), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.payment_list), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n<div class=\"mt30 pl15 mb10\">Ӧ�����</div>\r\n\r\n<!--֧��ģ��-->\r\n<ul class=\"ui-list ui-list-text \" class=\"ui-border-t\">\r\n    <li class=\"ui-border-t\">\r\n        <div class=\"ui-txt-default \">֧���ܼۣ�<span >��<label data-role=\"pay-amount\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span></div>\r\n    </li>\r\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_promotion_info), {hash:{},inverse:self.noop,fn:self.program(13, program13, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.activity_promotion_info), {hash:{},inverse:self.noop,fn:self.program(15, program15, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = helpers.unless.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_allow_coupon), {hash:{},inverse:self.noop,fn:self.program(17, program17, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    \r\n    <li data-role=\"coupon-money\" class=\"ui-border-t\">\r\n        <div class=\"ui-txt-default \">ʹ��/�һ��Ż�ȯ��<span><div class=\"ui-nowrap\" style=\"width: 200px;\" data-role=\"coupon-money-tag\">\r\n            \r\n        </div></span></div>\r\n        <div class=\"ui-edge-right\">\r\n            <span class=\"count-money-color\" data-role=\"coupon-money-text\"></span>\r\n            <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n        </div>\r\n    </li>\r\n    <li data-role=\"select-available-balance\" class=\"ui-border-t\">\r\n		<div class=\"ui-txt-default \">Ǯ��<span>����<label data-role=\"available_balance\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.available_balance)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span>����<span class=\"count-money-color\" data-role=\"less-money\"></span></div>\r\n		<div class=\"ui-edge-right\">\r\n\r\n			<i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n		</div>\r\n	</li>\r\n    <li class=\"ui-border-t\">\r\n        <div class=\"ui-txt-default \">����֧����<span class=\"count-money-color_v2 fb\">��<label data-role=\"need-price\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span></div>\r\n    </li>\r\n</ul>\r\n<ul class=\"ui-list ui-list-text ui-border-b mt20 fn-hide\" style=\"margin-bottom: 0;\"  data-role=\"must-pay-container\"></ul>\r\n\r\n<div data-role=\"other-pay-container\" class=\"fn-hide\">\r\n    <div class=\"mt30 pl15 mb10\">֧����ʽ</div>\r\n    <ul class=\"ui-list ui-list-text \" >\r\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_yueyue_app), {hash:{},inverse:self.noop,fn:self.program(19, program19, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_weixin), {hash:{},inverse:self.noop,fn:self.program(21, program21, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_zfb_wap), {hash:{},inverse:self.noop,fn:self.program(23, program23, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n    </ul>\r\n\r\n   \r\n\r\n</div>\r\n\r\n\r\n\r\n<div style=\"height:55px;\"></div>\r\n<div class=\"last-container fn-hide\"></div>\r\n<!--֧��ģ��-->\r\n<div class=\"buttom-btn-wrap ui-border-t\">\r\n    <div class=\"pl10 text-info\">\r\n        ����֧����<span class=\"count-money-color_v2 red-font\" style=\"font-size: 18px;\">��<label data-role=\"need-price\" id=\"bottom-need-price\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_amount)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</label></span>\r\n    </div>\r\n    <div class=\"right\">\r\n        <button class=\"ui-tt-pay-btn ui-button  ui-button-inline-block  ui-button-size-x ui-button-bg-ff6 \" id=\"pay-btn\">\r\n            <span class=\"ui-button-content\" style=\"padding:0px\" ><i class=\"icon icon-btn-icon-fk \"></i></span>\r\n            <span class=\"txt\">ȥ֧��</span>\r\n        </button>\r\n    </div>\r\n\r\n\r\n</div>";
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

            
            var content = window._page_data;  

            _self.$page_container.html('');
            
            _self.$page_container.html(tpl(content));

            if(content.data.request_id){_self.request_id = content.data.request_id;}
            if(content.data.total_amount){_self.order_total_amount = content.data.total_amount;}
            if(content.data.pay_amount){_self.order_pay_amount = content.data.pay_amount;}
            if(content.data.batch_name){_self.batch_name = content.data.batch_name;}
            if(content.data.is_allow_coupon){_self.is_allow_coupon = content.data.is_allow_coupon;}
            if(content.data.coupon_amount){_self.coupon_amount = content.data.coupon_amount;}


            // ��ʼ���Ż�ȯ
            _self.coupon_obj = coupon.init
            ({
                container : $('[data-role="coupon-list-container"]'),
                order_sn : _self.order_sn,
                order_total_amount :_self.order_total_amount,
                order_pay_amount : _self.order_pay_amount,
                page : 1,
                extend_params :
                {
                    module_type : 'mall_order'
                },
                success : function()
                {
                    // ======= Ҫʵ��ѡ���Ż�ȯʱ��Ĵ��� =======
                    var selected_coupon = _self.coupon_obj.selected_coupon;

                    var pay_items_model =
                    {
                        can_use_balance : _self.can_use_balance,
                        available_balance : _self.ab,
                        total_price : _self.total_price,
                        set_pay_type : false,
                        coupon : selected_coupon && selected_coupon.coupon_sn,
                        request_count : false
                    };

                    // ��ʼ����
                    self.count_money(pay_items_model);

                    if(_self.batch_name && _self.is_allow_coupon)
                    {
                        // ��ʾ�Ż���Ϣ
                        _self.$coupon_money_tag.html(_self.batch_name);

                        _self.$coupon_money.find('[data-role="yes-tag"]').addClass('icon-select-active').removeClass('icon-select-no');

                        _self.$coupon_money.find('[data-role="coupon-money-text"]').html('-��'+_self.coupon_amount);
                    }
                    else
                    {
                        _self.$coupon_money_tag.html('');

                        _self.$coupon_money.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');

                        _self.$coupon_money.find('[data-role="coupon-money-text"]').html('');

                    }

                    // ======= Ҫʵ��ѡ���Ż�ȯʱ��Ĵ��� =======
                }

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

            /*utility.ajax_request
            ({
                url : window.$__config.ajax_url.get_tt_pay_info,
                data :
                {
                    order_sn : _self.order_sn
                },                      
                success : function(res)
                {
                    

                    

                    $loading.loading("hide");
                },
                error : function()
                {
                    $loading.loading("hide");

                    $.tips
                    ({
                        content : '�����쳣',
                        stayTime:3000,
                        type:"warn"
                    });
                }
            });*/
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


            var loc = window.location;
            // ����origin����������
            if (!loc.origin)
            {
                loc.origin = loc.protocol + '//' + loc.hostname + (loc.port ? (':' + loc.port) : '');
            }



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
            if(App.isPaiApp)
            {
                _self.selected_pay_action_type ='alipay_purse';
            }
            else if(ua.is_weixin)
            {
                _self.selected_pay_action_type = 'tenpay_wxpub';
            }
            else
            {
                _self.selected_pay_action_type = 'alipay_wap';
            }


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
                    coupon : _self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.coupon_sn
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

            // ���ȷ����Ȼ��ر��Ż�ȯ��
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
                    coupon : selected_coupon && selected_coupon.coupon_sn
                };

                // ��ʼ����
                self.count_money(pay_items_model);

                if(selected_coupon)
                {
                    // ��ʾ�Ż���Ϣ
                    _self.$coupon_money_tag.html(selected_coupon.batch_name);

                    _self.$coupon_money_text.html("-��"+selected_coupon.face_value);

                    _self.$coupon_money.find('[data-role="yes-tag"]').addClass('icon-select-active').removeClass('icon-select-no');
                }
                else
                {
                    _self.$coupon_money_tag.html('');

                    _self.$coupon_money_text.html('');

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
                var no_c_txt = $('#no_allow_coupon_text').val();
                if(no_c_txt)
                {
                    var dialog = utility.dialog({
                        "title" : "" ,
                        "content" : no_c_txt,
                        "buttons" : ["ȷ��"]
                    });

                    return;
                }

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
                    var redirect_url = window.location.href.replace('pay.php','success.php');

                    var coupon = '';

                    if(_self.is_allow_coupon)
                    {
                        coupon = (_self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.coupon_sn) || '';
                    }

                    var params = 
                    {
                        third_code : _self.selected_pay_action_type,
                        order_sn  : _self.order_sn || '',
                        redirect_url : redirect_url,
                        coupon_sn : coupon,
                        available_balance : _self.ab,
                        is_available_balance : _self.$select_ab_btn.find('[data-role="yes-tag"]').hasClass('icon-select-active') ? 1 : 0,
                        total_amount : _self.order_total_amount 
                    };


                    if($('#submit_token').val())
                    {
                        window.location.href = '../notice/index.php?type=pay';

                        return;
                    }


                    // ΢�Ź��ں�֧��
                    if ( _self.selected_pay_action_type == 'tenpay_wxpub')
                    {
                        var wx_pub_pay_params = params;

                        delete wx_pub_pay_params.redirect_url;

                        wx_pub_pay_params.redirect_url = loc.origin+loc.pathname+'success.php?order_sn='+_self.order_sn;

                        wx_pub_pay_params = $.extend(wx_pub_pay_params,{type:'mall_order'});

                        wx_pub_pay_params = $.param(wx_pub_pay_params);

                        window.location.href = 'http://yp.yueus.com/m/wx_pay_jump.php?'+wx_pub_pay_params;

                        return;
                    }


                    if ( _self.selected_pay_action_type == 'alipay_wap')
                    {
                        params.redirect_url = loc.origin+loc.pathname+'success.php?order_sn='+_self.order_sn;
                    }


                    self.pay_action(params,
                    {
                        success :function(res)
                        {
                            var third_code = res.result_data && res.result_data.third_code;
                            var channel_return = 'success.php?order_sn='+res.result_data.order_sn;
                            var result = res.result_data && res.result_data.result;
                            
                            if(result == 1)
                            {
                                switch(third_code)
                                {
                                    // ֧����֧��������App�ӿ�
                                    case 'alipay_purse':
                                        App.alipay
                                        ({
                                            alipayparams : res.result_data.request_data,
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
                                                $('#submit_token').val(new Date().getTime());

                                                window.location.href = channel_return;
                                            }
                                        });
                                        break;
                                    // ΢��֧��
                                    case 'tenpay_wxapp':                                    
                                        App.wxpay(JSON.parse(res.result_data.request_data),function(data)
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
                                                $('#submit_token').val(new Date().getTime());

                                                window.location.href = channel_return;
                                            }
                                        });
                                        break;

                                    // ��֧ͨ����֧��
                                    case 'alipay_wap':
                                        if(result == 1)
                                        {
                                            window.location.href = res.result_data.request_data;
                                        }
                                        else
                                        {
                                            var text = self.after_pay_text(result);

                                            $.tips
                                            ({
                                                content:text,
                                                stayTime:3000,
                                                type:'warn'
                                            });
                                        }
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

                                $('#submit_token').val(new Date().getTime());

                                window.location.href = channel_return;
                            }
                            
                            

                            

                            
                        },
                        error : function()
                        {
                            
                        }
                    });
                }
            });

            
        },
        count_action : function(options)
        {
            var self = this;

            options = options || {};

            utility.ajax_request
            ({
                url : window.$__ajax_domain+'get_pay_amount.php',
                type : 'POST',
                data : options.data,
                beforeSend : function()
                {
                    self.$loading = $.loading
                    ({
                        content:'������...'
                    });
                },
                success : function(res)
                {
                    self.$loading.loading("hide");

                    if(res.result_data.code > 0)
                    {
                        options.success.call(this,res.result_data);
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

                    

                    console.log(res.result_data);
                },
                error : function()
                {
                    self.$loading.loading("hide");

                    $.tips
                    ({
                        content:'�����쳣',
                        stayTime:3000,
                        type:'warn'
                    });

                    options.error.call(this);
                }
            })
        },
        count_render : function(data)
        {
            var self = this;

            var $bottom_need_price = $('#bottom-need-price');

            // ����Ǯ�����
            _self.$available_balance.html(data.available_balance);
            // Ҫ֧����Ǯ
            _self.$less_money.html('-��'+data.use_balance);
            // ��Ҫ֧����Ǯ
            _self.$need_price.html(data.pending_amount);

            $bottom_need_price.html('');
            
            setTimeout(function()
            {
                $bottom_need_price.html(data.pending_amount);
            },200);

            _self.order_total_amount = data.total_amount;

            _self.is_allow_coupon = data.is_allow_coupon;

            _self.batch_name = data.batch_name;

            var is_use_third_party_payment = data.is_use_third_party_payment;
            var can_use_balance = data.is_available_balance;
               
            if(can_use_balance)
            {
                // ʹ�����֧��
                
                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-no').addClass('icon-select-active');

                
                // ����Ǯ֧��������ʱ����Ҫ���֧��
                if(is_use_third_party_payment)
                {

                    self.control_other_pay_item({show:true});

                    // ����Ĭ��֧����ʽ�������Ż݄�������Ĭ��֧����ʽ
                    if(true)
                    {
                        //self.pay_item_obj._select_pay_type('alipay_purse');
                        $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                            .removeClass('icon-select-no').addClass('icon-select-active');
                    }
                }
                // �����ȫ��Ǯ֧���˶���
                else
                {
                    self.clear_select();

                    self.control_other_pay_item({show:false});

                    console.log('�����ȫ��Ǯ֧���˶���');
                }
            }
            else
            {
                // ʹ�õ�����֧�� 
                
                self.clear_select(true);

                self.control_other_pay_item({show:true});

                // ����Ĭ��֧����ʽ�������Ż݄�������Ĭ��֧����ʽ
                if(true)
                {
                    $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                        .removeClass('icon-select-no').addClass('icon-select-active');
                }
                
            }
        },
        count_money : function(params)
        {
            var self = this;

            var total_price = utility.float(params.total_price);

            var available_balance = utility.float(params.available_balance);

            var set_pay_type = params.set_pay_type == null ? true : false;

            var coupon = params.coupon || 0;

            var request_count = params.request_count == null ? true : false;

            // Ǯ��Ҫ�۵�Ǯ
            var less_money = 0;

            if(!request_count)
            {
                setTimeout(function()
                {
                    self.count_render(window._page_data.data);
                },300);
                return;
            }

            // ����ȥ���������true�Żᷢ��
            self.count_action
            ({
                data : 
                {
                    order_sn : _self.order_sn,
                    is_available_balance : params.can_use_balance?1:0,
                    coupon_sn : coupon
                },
                success : function(res)
                {
                    var data = res.data;

                    setTimeout(function()
                    {
                        self.count_render(data);
                    },300);
                    
                    
                },
                error : function()
                {

                }
            });

            

        },
        // tt ֧������
        pay_action : function(params,callback)
        {
            var $loading=$.loading
            ({
                content:'������...'
            });

            utility.ajax_request
            ({
                url : window.$__config.ajax_url.pay_tt_action,
                data : params,          
                type : 'POST',  
                success : function(res)
                {
                    $loading.loading("hide");
                                        
                    if(res.result_data && res.result_data.result>0)
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



 
});