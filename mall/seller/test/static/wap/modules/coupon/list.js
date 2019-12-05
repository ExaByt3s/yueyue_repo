define('coupon/list', function(require, exports, module){ /**
 * 优惠券
 hudw 2015.5.5
 **/

/**
 * @require modules/coupon/list-coupon.scss
 */

"use strict";

var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var item_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, self=this, functionType="function", escapeExpression=this.escapeExpression;

function program1(depth0,data,depth1) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n    <div class=\"widget-coupon ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0._class_for_available), {hash:{},inverse:self.program(4, program4, data),fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " ";
  stack1 = helpers['if'].call(depth0, (depth1 && depth1.no_choose_btn), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" data-coupon_sn=\"";
  if (helper = helpers.coupon_sn) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.coupon_sn); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\r\n        <div class=\"left-border\">\r\n            <div class=\"inside\"></div>\r\n        </div>\r\n        <div class=\"price\" data-role=\"to_ticket_details_price\">\r\n            <div class=\"type\">";
  if (helper = helpers.scope_module_type_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.scope_module_type_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n            <div class=\"contain\">\r\n                <div class=\"contain-left\">";
  if (helper = helpers.coin) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.coin); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n                <div class=\"contain-right\">";
  if (helper = helpers.face_value) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.face_value); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n            </div>\r\n        </div>\r\n        <div class=\"split\">\r\n            <div class=\"top\"></div>\r\n            <div class=\"mid\"><div class=\"mid-in\"></div></div>\r\n            <div class=\"bottom\"></div>\r\n        </div>\r\n        <div class=\"details\" data-role=\"to_ticket_details\">\r\n            <div class=\"notice\">";
  if (helper = helpers.batch_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.batch_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</div>\r\n            <div class=\"date\">";
  if (helper = helpers.start_time_str) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.start_time_str); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "-";
  if (helper = helpers.end_time_str) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.end_time_str); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n        </div>\r\n        <div class=\"choosen_btn\" data-role=\"choosen_btn\">\r\n            <div class=\"tap\" data-role=\"tap\">\r\n                <div class=\"tap-in\">\r\n                    <i class=\"icon icon-right-24x16 fn-hide\"></i>\r\n                    <label class=\"ui-yue-checkbox\">\r\n                        <i data-role=\"cb-btn\" class=\"no-selected\" type=\"checkbox\" checked=\"\" ></i>\r\n                    </label>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        <div class=\"right-border\">\r\n            <div class=\"inside\"></div>\r\n        </div>\r\n    </div>\r\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "";
  return buffer;
  }

function program4(depth0,data) {
  
  
  return "unavailable";
  }

function program6(depth0,data) {
  
  
  return "no_choose_btn";
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.programWithDepth(1, program1, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n";
  return buffer;
  });

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

if(App.isPaiApp)
{
    App.check_login(function(data)
    {
        if(!utility.int(data.pocoid))
        {
            App.openloginpage(function(data)
            {
                if(data.code == '0000')
                {
                    utility.refresh_page();
                }
            });

            return;
        }


    });

}

var _self = {};
var _coupon_map = {};

module.exports =
{
    render: function ($dom,data)
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择


        var self = this;

        $.each(data,function(i,obj)
        {

            if(obj.tab == 'available')
            {
                data[i] = $.extend(true,{},obj,{_class_for_available:true})
            }
            else if (obj.tab == 'used'){
                data[i] = $.extend(true,{},obj,{_class_for_used:true});
            }
            else if (obj.tab == 'expired') {
                data[i] = $.extend(true,{},obj,{_class_for_expired:true});
            }

            _coupon_map[obj.coupon_sn] = obj;
        });

        $dom.html('');

        $dom.html(item_tpl({data:data}));

        // 安装事件
        self.setup_event();

        return this;
    },
    refresh : function()
    {
        var self = this;

        var $loading=$.loading
        ({
            content:'加载中...'
        });

        utility.ajax_request
        ({
            url : window.$__config.ajax_url.get_user_coupon_list_by_check,
            type : 'GET',
            data :
            {
                module_type : self.module_type,
                request_id :self.request_id,
                quotes_id :self.quotes_id,
                order_total_amount  : self.order_total_amount ,
                order_pay_amount : self.order_pay_amount ,
                page : self.page
            },
            success : function(res)
            {
                var content = res.result_data;

                self.render(self.$container,content.list);

                $loading.loading("hide");
            },
            error : function()
            {
                $loading.loading("hide");

                $.tips
                ({
                    content : '网络异常',
                    stayTime:3000,
                    type:"warn"
                });
            }
        });


    },
    page_back : function()
    {

    },
    // 初始化
    init : function(options)
    {
        var self = this;

        self.$container = options.container || {};
        self.module_type = options.module_type || '';
        self.request_id = options.request_id || '';
		self.quotes_id = options.quotes_id || '';		
        self.order_total_amount  = options.order_total_amount  || '';
        self.order_pay_amount = options.order_pay_amount || '';
        self.page = options.page || 1;

        self.refresh();

        return self;
    },
    hide : function()
    {

    },
    // 安装事件
    setup_event : function()
    {
        var self = this;

        // 选择优惠券事件
        $('[data-coupon_sn]').on('click',function(ev)
        {
            var $cur_btn = $(ev.currentTarget);

            var coupon_sn = $cur_btn.attr('data-coupon_sn');

            var $check_box = $cur_btn.find('[type="checkbox"]');

            // 选中状态
            if($check_box.hasClass('no-selected') )
            {
                //先清空所有选择状态
                $('[data-coupon_sn]').find('[type="checkbox"]').attr('checked',"").addClass('no-selected');

                $check_box.removeAttr('checked').removeClass('no-selected');

                self.selected_coupon = _coupon_map[coupon_sn];
            }
            else
            {
                $check_box.attr('checked',"").addClass('no-selected');

                self.selected_coupon = '';
            }



        });
    }
};





 
});