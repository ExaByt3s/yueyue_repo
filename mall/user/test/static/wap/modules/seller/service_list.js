define('seller/service_list', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/25.
 */

// ========= 模块引入 ========= 
var $ = require('components/zepto/zepto.js');
var header = require('common/widget/header/main');
var utility = require('common/utility/index');
var yue_ui = require('yue_ui/frozen');
var abnormal = require('common/widget/abnormal/index');
var LZ = require('common/lazyload/lazyload');
var _self = $({});



// 渲染头部
_self.header_obj = header.init
({
    ele : $("#global-header"), //头部渲染的节点
    title:"服务列表",
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
        show :true,  //是否显示文字
        content:"编辑"  //显示文字内容
    }
});
var SELLER_AJAX_URL = window.$__ajax_domain+'get_sell_services_list.php';
var page_params = window.__page_params;
var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += " \r\n<a href=\"";
  if (helper = helpers.link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"items\">\r\n    <div class=\"item\">\r\n        <div class=\"lbox \">\r\n            <i data-lazyload-url=\""
    + escapeExpression((helper = helpers.change_img_size || (depth0 && depth0.change_img_size),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.images), "260", options) : helperMissing.call(depth0, "change_img_size", (depth0 && depth0.images), "260", options)))
    + "\" class=\"img image-img min-height\"></i>\r\n        </div>\r\n\r\n        <div class=\"rbox \">\r\n            <h3 class=\"title color-000 f14\">";
  if (helper = helpers.titles) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.titles); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</h3>\r\n            <div>\r\n                <p class=\"price color-ff6\">";
  if (helper = helpers.prices) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.prices); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\r\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.abate), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                </p>\r\n                <div class=\"num color-999 f12\">\r\n                    <p>";
  if (helper = helpers.buy_num) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.buy_num); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n                    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.notice), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </div>\r\n</a>\r\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                <span class=\"price-tips\">";
  if (helper = helpers.abate) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.abate); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\r\n                ";
  return buffer;
  }

function program4(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                    <div class=\"ml15\">\r\n                    <span class=\"tags\">促</span><span class=\"msg\">";
  if (helper = helpers.notice) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.notice); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\r\n                    </div>\r\n                    ";
  return buffer;
  }

  buffer += "<div class=\"hp\">\r\n";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.list), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " \r\n</div>";
  return buffer;
  });
var list_item_class = require('list'); 

var list_obj = new list_item_class(
    {
        //渲染目标
        ele : $('#render_ele'),
        //请求地址
        url : SELLER_AJAX_URL,
        //传递参数
        params : page_params,
        //模板
        template : template,
        //lz是否开启参数
        is_open_lz_opts : false  
    });

 
});