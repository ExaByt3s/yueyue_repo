define('order/list', function(require, exports, module){ var $ = require('components/zepto/zepto.js');

var App = require('common/I_APP/I_APP');

var utility = require('common/utility/index');

var abnormal = require('common/widget/abnormal/index');

var fastclick = require('components/fastclick/fastclick.js');

var list_tmp = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n<div class=\"child\" order_id=\"";
  if (helper = helpers.order_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.order_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" order_sn=\"";
  if (helper = helpers.order_sn) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.order_sn); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" goods_id=\"";
  if (helper = helpers.detail_list) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.detail_list); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" >\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.code_list), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.detail_list), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n\n    <div class=\"item_info top-info ui-border-b\" data-role=\"nav-to-seller\" data-seller-user-id=\"";
  if (helper = helpers.seller_user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.seller_user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" >\n        <span>";
  if (helper = helpers.seller_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.seller_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\n        <i class=\"icon-allow-grey\"></i>\n        ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.status), "==", "8", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.status), "==", "8", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n    <a href=\"./detail.php?order_sn=";
  if (helper = helpers.order_sn) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.order_sn); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"dib w-100\" >\n        <div class=\"item_info\" data-role=\"item_info\">\n             \n                <div class=\"pics\">\n                    <i data-lazyload-url=\"";
  if (helper = helpers.goods_images) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_images); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"  class=\"img image-img min-height\"></i>\n                    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.activity_list), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                </div>\n                <div class=\"notice_contain\">\n                    <div class=\"notice ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.activity_list), {hash:{},inverse:self.noop,fn:self.program(12, program12, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" >";
  if (helper = helpers.goods_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.activity_list), {hash:{},inverse:self.noop,fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    <div class=\"o_price\">￥";
  if (helper = helpers.total_amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.total_amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + " ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.activity_list), {hash:{},inverse:self.noop,fn:self.program(17, program17, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</div>\n                </div>\n            \n        </div>\n    </a>\n    <div class=\"r_pay ui-border-t\">\n        <div class=\"info\">\n            <p class=\"tex\">实付：</p>\n            <p class=\"re_price\">￥";
  if (helper = helpers.pending_amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.pending_amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\n        </div>\n    </div>\n    <div class=\"btns ui-border-t\">\n        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.btn_action), {hash:{},inverse:self.noop,fn:self.program(19, program19, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n</div>\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <div data-role-code=\"contain\" style=\"display: none\" >\n        <input type=\"hidden\" data-code-url=\"";
  if (helper = helpers.qr_code_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.qr_code_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n        <input type=\"hidden\" data-code-number=\"";
  if (helper = helpers.code_sn) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.code_sn); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n        <input type=\"hidden\" data-code-name=\"";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n        <input type=\"hidden\" data-code-img-url=\"";
  if (helper = helpers.qr_code_url_img) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.qr_code_url_img); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    </div>\n    ";
  return buffer;
  }

function program4(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <input type=\"hidden\" data-goods-id=\"";
  if (helper = helpers.goods_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    ";
  return buffer;
  }

function program6(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.is_buyer_comment), "==", "0", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.is_buyer_comment), "==", "0", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  return buffer;
  }
function program7(depth0,data) {
  
  
  return "\n            <span class=\"status color-fe9920\">待评价</span>\n            ";
  }

function program9(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                        ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.is_official), "==", "1", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.is_official), "==", "1", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    ";
  return buffer;
  }
function program10(depth0,data) {
  
  
  return "\n                        <div class=\"ui-list-item-tips\">\n                            <i class=\"ui-tips-content\">官方</i>\n                            <i class=\"ui-list-item-tips-triangle\"></i>\n                        </div>\n                        ";
  }

function program12(depth0,data) {
  
  
  return "mb15";
  }

function program14(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.activity_list), {hash:{},inverse:self.noop,fn:self.program(15, program15, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    ";
  return buffer;
  }
function program15(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                        <div class=\"o_price mb5\">"
    + escapeExpression((helper = helpers.formatDateTime || (depth0 && depth0.formatDateTime),options={hash:{},data:data},helper ? helper.call(depth0, "Y.m.d H:i", (depth0 && depth0.service_start_time), options) : helperMissing.call(depth0, "formatDateTime", "Y.m.d H:i", (depth0 && depth0.service_start_time), options)))
    + " ";
  if (helper = helpers.stage_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.stage_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                        ";
  return buffer;
  }

function program17(depth0,data) {
  
  var stack1, helper;
  if (helper = helpers.prices_spec) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.prices_spec); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  return escapeExpression(stack1);
  }

function program19(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(20, program20, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.request), "==", "close", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.request), "==", "close", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(22, program22, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.request), "==", "pay", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.request), "==", "pay", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n            ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(24, program24, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.request), "==", "cancel", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.request), "==", "cancel", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n            ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(26, program26, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.request), "==", "refund", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.request), "==", "refund", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(28, program28, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.request), "sign", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.request), "sign", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(33, program33, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.request), "==", "delete", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.request), "==", "delete", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n            ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(35, program35, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.request), "==", "appraise", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.request), "==", "appraise", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  return buffer;
  }
function program20(depth0,data) {
  
  
  return "\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-aaa bad\" data-action-type=\"close\" data-role=\"close\">\n                <span >关闭</span>\n            </button>\n            ";
  }

function program22(depth0,data) {
  
  
  return "\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-ff6 good\" data-action-type=\"pay\" data-role=\"pay\">\n                <span >支付</span>\n            </button>\n            ";
  }

function program24(depth0,data) {
  
  
  return "\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-aaa bad\" data-action-type=\"cancel\" data-role=\"cancel\">\n                <span >取消订单</span>\n            </button>\n            ";
  }

function program26(depth0,data) {
  
  
  return "\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-aaa bad\" data-action-type=\"refund\" data-role=\"refund\">\n                <span >申请退款</span>\n            </button>\n            ";
  }

function program28(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-ff6 good\" data-action-type=\"ewm\" data-role=\"ewm\" >\n                <span >\n                    ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.program(31, program31, data),fn:self.program(29, program29, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.order_type), "activity", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.order_type), "activity", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                </span>\n            </button> \n            ";
  return buffer;
  }
function program29(depth0,data) {
  
  
  return "活动签到";
  }

function program31(depth0,data) {
  
  
  return "出示二维码";
  }

function program33(depth0,data) {
  
  
  return "\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-aaa bad\" data-action-type=\"delete\" data-role=\"delete\">\n                <span >删除订单</span>\n            </button>\n            ";
  }

function program35(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n            <a href=\"../comment/index.php?order_sn=";
  if (helper = helpers.order_sn) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.order_sn); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"dib\">\n                <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-ff6 good\" data-action-type=\"comment\" data-role=\"comment\">\n                    <span >评价</span>\n                </button>\n            </a>\n            ";
  return buffer;
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.list), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  return buffer;
  });

var qrcode_widget = require('qrcode');

var list_item_class = require('list'); 

var swiper = require('common/widget/swiper/1.0.0/swiper3.07.min'); //3.07.min

var header = require('common/widget/header/main');        

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

// 做兼容
var APP = App;


// 订单列表类
var order_list_class = function(options)
{
    var self = this;

    options = options || {};

    self.$el = options.$el || $('body');

    var type_id = options.type_id;
    var status = options.status;

    self.type_id = type_id;
    self.status = status;
    self.type = 'daifukuan';

    self.name = options.name;
    self.user_icon = options.user_icon;
    self.user_id = options.user_id;
    self.pay_url = options.pay_url;

    self.init(options);



};

order_list_class.prototype = 
{
    init : function(options)
    {
        var self = this;

        App.isPaiApp && App.showtopmenu(false,{show_bar:true});

        //头部返回按钮
        var $back = $('#back');
        $back.on('click',function()
        {
            if(App.isPaiApp)
            {
                App.app_back();
                return;
            }

            if(document.referrer)
            {

                window.history.back();
            }
            else
            {
                window.location.href = "http://yp.yueus.com/mall/user/"+window.__test__str+"index.php" ;
            }
        });
        

        // 列表队列
        self.list_queue = {};

        // 默认显示待付款
        self.refresh(self.type,
        {
            params : 
            {
                type_id : self.type_id,
                status : self.status,
                page : 1
            }
            
        });

        self.setup_top_bar();
        
        
    },
    create_list : function(type)
    {
        var self = this;

        var list_wrapper_html = '<div class="order_list_container" data-role="'+type+'-list-container" ></div>';

        self.$el.find('[data-role="order-list-wrapper"]').append(list_wrapper_html);

        return self.$el.find('[data-role="'+type+'-list-container"]');
    },
    refresh : function(type,options)
    {
        var self = this;

        options = options || {};

        var $list_container_by_type = self.$el.find('[data-role="'+type+'-list-container"]');

        self.$el.find('.order_list_container').addClass('fn-hide');
        $list_container_by_type.removeClass('fn-hide');

        // 已经存在的列表，直接调用刷新方法
        if(self.list_queue && typeof(self.list_queue[type]) != 'undefined')
        {
            if(self.list_queue[type].$el.rq_status == 'error')
            {
                self.list_queue[type].refresh();
            }

            return;
            //self.list_queue[type].refresh();
        }
        else
        {
            // 渲染列表
            var AJAX_URL = window.$__ajax_domain+'get_order_list.php';            
            // 不存在的列表，就创建
            var $list_container = self.create_list(type);

            // 列表组件
            var list_obj = new list_item_class(
            {
                //渲染目标
                ele : $list_container, 
                //请求地址 
                url : AJAX_URL,
                //传递参数
                params : options.params,
                //模板
                template : list_tmp, 
                //lz是否开启参数
                is_open_lz_opts : false  
            });

            self.list_queue[type] = list_obj;

            self.list_queue[type].$el.on('list_render:after',function(events,$list_container,data,$list)
            {
                
                self.setup_event();

                var $list_container_by_type = self.$el.find('[data-role="'+type+'-list-container"]');

                // 处理红点显示隐藏
                if(!$list_container_by_type.find('[data-role="abnormal"]').hasClass('fn-hide'))
                {
                    $('[data-list-type="'+type+'"]').find('.red_dot').addClass('fn-hide');
                }
                
               
            });


        }
        
    },
    setup_top_bar : function()
    {
        var self = this;

        //栏目切换
        self.$el.find('[data-role="tap"]').on('click',function()
        {
            $(this).addClass('cur').siblings().removeClass('cur');

            var type = $(this).attr('data-list-type');
            var status = $(this).attr('data-status');

            self.type = type;
            self.status = status

            
            self.refresh(type,
            {
                params : 
                {
                    type_id : self.type_id,
                    status : self.status,
                    page : 1
                }
                
            });

            //self.ajax_control();

        });
    },
    setup_event : function($list_obj)
    {
        var self = this;

        var ajax_control_type;//请求类型

        $list_obj = self.$el;

        // 取消订单
        $list_obj.find('[data-role="cancel"]').off('click');
        $list_obj.find('[data-role="cancel"]').on('click',function()
        {
            ajax_control_type = 'cancel';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认取消？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn: $con.parents('.child').attr('order_sn'),type:ajax_control_type}
                self.ajax_control(data);
            });
        });

        // 关闭订单
        $list_obj.find('[data-role="close"]').off('click');
        $list_obj.find('[data-role="close"]').on('click',function(){
            ajax_control_type = 'close';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认关闭？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn: $con.parents('.child').attr('order_sn'),type:ajax_control_type}
                self.ajax_control(data);
            });
        });

        // 支付
        $list_obj.find('[data-role="pay"]').off('click');
        $list_obj.find('[data-role="pay"]').on('click',function(){

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '去支付？'
            });

            dialog.on('confirm',function(event,args)
            {
                utility.ajax_request
                ({
                    url: window.$__ajax_domain+'order_list_pay_judge.php',
                    data : {order_sn: $con.parents('.child').attr('order_sn')},
                    dataType: 'json',
                    type: 'POST',
                    cache: false,
                    beforeSend: function()
                    {
                        window.$loading = $.loading
                        ({
                            content:'请求支付中...'
                        });
                    },
                    success: function(data)
                    {
                        $loading.loading("hide");

                        if(data.result_data.data.result == 1){
                            //成功 后刷页
                            if(data.result_data.data.message != '')
                            {
                                $.tips
                                ({
                                    content:data.result_data.data.message,
                                    stayTime:3000,
                                    type:'success'
                                });
                            }

                            window.location.href = self.pay_url + $con.parents('.child').attr('order_sn');
                        }
                        else{
                            $.tips
                            ({
                                content:data.result_data.data.message,
                                stayTime:3000,
                                type:'warn'
                            });
                        }
                    },
                    error: function(data)
                    {
                        $loading.loading("hide");

                        $.tips
                        ({
                            content:'网络异常',
                            stayTime:3000,
                            type:'warn'
                        });
                    }
                });
            });
        });
        
        // 申请退款
        $list_obj.find('[data-role="refund"]').off('click');
        $list_obj.find('[data-role="refund"]').on('click',function(){
            ajax_control_type = 'refund';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认申请退款？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn:$con.parents('.child').attr('order_sn'),type:ajax_control_type}
                self.ajax_control(data);
            });

        });

        // 出示二维码    
        $list_obj.find('[data-role="ewm"]').off('click');
        $list_obj.find('[data-role="ewm"]').on('click',function()
        {
            var this_parent = $(this).parents('.child');
            var qrcodes = [];
            $.each(this_parent.find('[data-role-code="contain"]'),function(i,obj)
            {
                var inner_obj =
                {
                    url : $(obj).find('[data-code-url]').attr('data-code-url'),
                    number : $(obj).find('[data-code-number]').attr('data-code-number'),
                    name : $(obj).find('[data-code-name]').attr('data-code-name'),
                    url_img : $(obj).find('[data-code-img-url]').attr('data-code-img-url')
                }

                qrcodes.push(inner_obj);
            })

            console.log(qrcodes);

            if(APP.isPaiApp)
            {
                APP.qrcodeshow(qrcodes,0,function(){});
            }
            else
            {
                // 处理二维码
                    
                var qrcode_obj = new qrcode_widget
                ({
                    ele : $('#render_qrcode'), //渲染的节点
                    data : 
                    {
                        name : self.name,
                        user_icon : self.user_icon,
                        user_id : self.user_id,
                        data_arr : qrcodes
                    }
                    
                })
                // 处理二维码 end
            }
        });
        

        // 删除
        $list_obj.find('[data-role="delete"]').off('click');
        $list_obj.find('[data-role="delete"]').on('click',function(){
            ajax_control_type = 'delete';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认删除？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn:$con.parents('.child').attr('order_sn'),type:ajax_control_type}

                self.ajax_control(data);

            });

        });

        $list_obj.find('[data-role="nav-to-seller"]').off('click');
        $list_obj.find('[data-role="nav-to-seller"]').on('click',function()
        {
            var seller_user_id = $(this).attr('data-seller-user-id');
            if(App.isPaiApp)
            {
                App.nav_to_app_page
                ({
                    page_type : 'seller_user',
                    seller_user_id : seller_user_id
                });
            }
            else
            {
                window.location.href = '../seller/?seller_user_id='+seller_user_id;
            }
        });

        
    },
    //ajax请求
    ajax_control : function (data,success,error,complete)
    {
        var self = this;

        utility.ajax_request
        ({
            url: window.$__ajax_domain+'order_list_control.php',
            data : data,
            cache: false,
            beforeSend: function()
            {

                window.$loading = $.loading
                ({
                    content:'发送中...'
                });
            },
            success: function(data)
            {
                $loading.loading("hide");


                if(data.result_data.data.result == 1){
                    //成功 后刷页
                    $.tips
                    ({
                        content:data.result_data.data.message,
                        stayTime:3000,
                        type:'success'
                    });

                    self.list_queue[self.type].refresh();
                    
                }
                else{
                    $.tips
                    ({
                        content:data.result_data.data.message,
                        stayTime:3000,
                        type:'warn'
                    });
                }
            },
            error: function(data)
            {
                $loading.loading("hide");

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

exports.init = function(options)
{
    return new order_list_class(options);
}

 
});