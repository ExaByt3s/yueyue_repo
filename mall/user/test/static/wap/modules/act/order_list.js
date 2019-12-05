define('act/order_list', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/21.
 */

var utility = require('common/utility/index');
var ua = require('common/ua/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var scroll = require('common/scroll/index');
var abnormal = require('common/widget/abnormal/index');
var items_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  
  return "\n            <a class=\"db\" href=\"javascript:;\">\n        ";
  }

function program3(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <a class=\"db\" href=\"./detail.php?event_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" >\n        ";
  return buffer;
  }

function program5(depth0,data) {
  
  
  return "\n                    <div class=\"tips-con-red\">\n                        <div class=\"tips\">官方</div>\n                        <div class=\"delta\"></div>\n                    </div>\n                ";
  }

function program7(depth0,data) {
  
  
  return "\n                    <div class=\"tips-con-green\">\n                        <div class=\"tips\">免费</div>\n                        <div class=\"delta\"></div>\n                    </div>\n                ";
  }

function program9(depth0,data) {
  
  
  return "\n                    <div class=\"tips-con-orange\">\n                        <div class=\"tips\">推荐</div>\n                        <div class=\"delta\"></div>\n                    </div>\n                ";
  }

function program11(depth0,data) {
  
  
  return "\n                    <div class=\"pic-fade\"></div>\n                    <div class=\"icon icon-pic-reject pic-logo-reject\"></div>\n                ";
  }

function program13(depth0,data) {
  
  
  return "\n                    <div class=\"pic-fade\" style=\"text-align:center;line-height:100px;color:#fff;\">活动审核中</div>\n                    <div class=\"icon icon-pic-no-reject pic-logo-no-reject\" ></div>\n                ";
  }

function program15(depth0,data) {
  
  
  return "\n                <a class=\"db\" href=\"javascript:;\">\n            ";
  }

function program17(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                <a class=\"db\" href=\"./order_detail.php?enroll_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "&event_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\n            ";
  return buffer;
  }

function program19(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_comment), {hash:{},inverse:self.noop,fn:self.program(20, program20, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }
function program20(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.stars_list), {hash:{},inverse:self.noop,fn:self.program(21, program21, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    ";
  return buffer;
  }
function program21(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                            <p class=\"detail\">\n                                <i class=\"icon icon-act-list-starts\"></i>\n                                ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.stars_list), {hash:{},inverse:self.noop,fn:self.program(22, program22, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                            </p>\n                        ";
  return buffer;
  }
function program22(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                                    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.is_red), {hash:{},inverse:self.program(25, program25, data),fn:self.program(23, program23, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                ";
  return buffer;
  }
function program23(depth0,data) {
  
  
  return "\n                                        <i class=\"icon icon-stat-s-y\"></i>\n                                    ";
  }

function program25(depth0,data) {
  
  
  return "\n                                        <i class=\"icon icon-stat-s-g\"></i>\n                                    ";
  }

function program27(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <!--p class=\"detail\" data-role=\"pub_notice\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pub_btn_notice_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p-->\n                ";
  return buffer;
  }

function program29(depth0,data) {
  
  
  return "style=\"border-top: none\"";
  }

function program31(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-bd-ff6\" data-prev-default=\"1\" data-role=\"btn_unpaid_pay\" data-event-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-enroll-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\n                <span class=\"ui-button-content\">支付</span>\n            </button>\n        ";
  return buffer;
  }

function program33(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-aaa\" data-prev-default=\"1\" data-role=\"btn_unpaid_delete\" data-event-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-enroll-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\n                <span class=\"ui-button-content\">取消</span>\n            </button>\n        ";
  return buffer;
  }

function program35(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-555\" data-event-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-enroll-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-role=\"btn_pub_show_ewm\">\n                <span class=\"ui-button-content\">出示活动券</span>\n            </button>\n        ";
  return buffer;
  }

function program37(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-555\" data-can-comment=\"1\" data-prev-default=\"1\" data-table-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.table_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"  data-to-date-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.to_date_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-event-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-role=\"btn_paid_commit\">\n                <span class=\"ui-button-content\">评价活动</span>\n            </button>\n        ";
  return buffer;
  }

function program39(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-555\" data-prev-default=\"1\" data-event-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-role=\"btn_pub_finnish\">\n                <span class=\"ui-button-content\">完成活动</span>\n            </button>\n        ";
  return buffer;
  }

function program41(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-555\" data-prev-default=\"1\" data-event-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-role=\"btn_pub_scan\">\n                <span class=\"ui-button-content\">扫码签到</span>\n            </button>\n        ";
  return buffer;
  }

function program43(depth0,data) {
  
  
  return "\n            <div class=\"btn_unpaid_container\">\n\n\n            </div>\n        ";
  }

function program45(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <div class=\"btn_paid_container\">\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_end), {hash:{},inverse:self.program(52, program52, data),fn:self.program(46, program46, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            </div>\n        ";
  return buffer;
  }
function program46(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.show_comment_btn), {hash:{},inverse:self.noop,fn:self.program(47, program47, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }
function program47(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_comment), {hash:{},inverse:self.program(50, program50, data),fn:self.program(48, program48, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    ";
  return buffer;
  }
function program48(depth0,data) {
  
  
  return "\n                        ";
  }

function program50(depth0,data) {
  
  
  return "\n\n                        ";
  }

function program52(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.show_scan_btn), {hash:{},inverse:self.noop,fn:self.program(53, program53, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }
function program53(depth0,data) {
  
  
  return "\n\n                    ";
  }

function program55(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <div class=\"btn_pub_container\">\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_finish_button), {hash:{},inverse:self.noop,fn:self.program(56, program56, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_scan_button), {hash:{},inverse:self.noop,fn:self.program(56, program56, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n            </div>\n        ";
  return buffer;
  }
function program56(depth0,data) {
  
  
  return "\n\n                ";
  }

  buffer += "\n<div class=\"child\" data-role=\"event-item\" data-enroll-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-event_id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-view-type=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.view_type)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-audit_status=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.audit_status)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\n    <div class=\"lists\" data-role=\"lists-to-info\" data-org-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.user_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-enroll-id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-event_id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-view-type=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.view_type)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-audit_status=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.audit_status)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\n        ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.program(3, program3, data),fn:self.program(1, program1, data),data:data},helper ? helper.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id), 0, options) : helperMissing.call(depth0, "if_equal", ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id), 0, options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "        \n            <i class=\"child-img \" style=\"background-image: url('"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.cover_image)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "')\">\n\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_authority), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_free), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_recommend), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pic_show_reject), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pic_show_no_reject), {hash:{},inverse:self.noop,fn:self.program(13, program13, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n\n            </i>\n        </a>\n\n        <div class=\"child-text\">\n            ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.program(17, program17, data),fn:self.program(15, program15, data),data:data},helper ? helper.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id), 0, options) : helperMissing.call(depth0, "if_equal", ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id), 0, options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            \n                <p class=\"title\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\n                <p class=\"detail\">\n                    <i class=\"icon icon-act-list-athor\"></i>\n                    "
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.nickname)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\n                <p class=\"detail\">\n                    <i class=\"icon icon-act-list-date\"></i>\n                    "
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.start_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\n                <p class=\"detail\">\n                    <i class=\"icon icon-act-list-price\"></i>\n                    ￥"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "/人</p>\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.is_end), {hash:{},inverse:self.noop,fn:self.program(19, program19, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                <p class=\"detail\"><i class=\"icon icon-act-list-join-num\"></i>"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_join)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "人</p>\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pub_btn_notice), {hash:{},inverse:self.noop,fn:self.program(27, program27, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            </a>\n        </div>\n\n    </div>\n\n    <div class=\"list-btns\" data-role=\"list-btns\" ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.hide_line), {hash:{},inverse:self.noop,fn:self.program(29, program29, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_pay_button), {hash:{},inverse:self.noop,fn:self.program(31, program31, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_cancel_button), {hash:{},inverse:self.noop,fn:self.program(33, program33, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_code_button), {hash:{},inverse:self.noop,fn:self.program(35, program35, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_comment_button), {hash:{},inverse:self.noop,fn:self.program(37, program37, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_finish_button), {hash:{},inverse:self.noop,fn:self.program(39, program39, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_scan_button), {hash:{},inverse:self.noop,fn:self.program(41, program41, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.btn_type_unpaid), {hash:{},inverse:self.noop,fn:self.program(43, program43, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.btn_type_paid), {hash:{},inverse:self.noop,fn:self.program(45, program45, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.btn_type_pub), {hash:{},inverse:self.noop,fn:self.program(55, program55, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n</div>\n\n\n\n";
  return buffer;
  });
var header = require('common/widget/header/main');

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

    var list_class = function()
    {
        var self = this;

        self.init();

        App.isPaiApp && App.showtopmenu(false);
    };

    list_class.prototype =
    {
        refresh : function()
        {
            var self = this;

			_self.page = 1;

            self.action(_self.type,_self.page);
        },
        load_more : function()
        {
            var self = this;

            if(_self.has_next_page)
            {
                _self.page++;

                self.action(_self.type,_self.page);
            }
            else
            {
                $.tips
                ({
                    content:'已经到尽头啦',
                    stayTime:3000,
                    type:'warn'
                });

                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
            }


        },
        init : function()
        {
            var self = this;

            _self._page_params = _page_params.result_data;

            // 初始化容器
            _self.$tab_container = $('.ui-tab');
            _self.tab_obj = self.init_tab_container('.ui-tab');
            _self.$loading = {};
            _self.$no_data = $('[data-role="no-data"]');
            _self.$has_data = $('[data-role="has-data"]');
            _self.page = 1 ;
            _self.type = _self._page_params.type;
            _self.$status_container = $('[data-role="status-container"]');

            _self.$scroll_wrapper = $('[data-role="wrapper"]');
            _self.scroll_view_obj = scroll(_self.$scroll_wrapper);

            var c_height = App.isPaiApp ? 95 : 136;

            _self.$scroll_wrapper.height(window.innerHeight - c_height);

            _self.scroll_view_obj.on('success:drag_down_load',function(e,dragger)
            {
                self.refresh();
            });

            _self.scroll_view_obj.on('success:drag_up_load',function(e,dragger)
            {
                self.load_more();
            });

            header.init({
                ele : $("#global-header"), //头部渲染的节点
                title:"活动订单",
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
                    content:'',  //显示文字内容
                    style : ''
                }
            });

            // 安装事件
            self._setup_event();

            self.refresh();
        },
        init_tab_container : function($el,options)
        {
            return {};
        },
        action : function(type,page,load_more)
        {
            var self = this;

            self._sending = false;

            if(self._sending)
            {
                return;
            }

            _self.ajax_obj = utility.ajax_request
            ({
                url : window.$__ajax_domain+'get_my_act_list.php',
                data :
                {
                    type : type,
                    page : page
                },
                beforeSend : function()
                {
                    self._sending = true;

                    _self.$loading = $.loading
                    ({
                        content:'加载中...'
                    });
                },
                success : function(response)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    var res = response.result_data;

                    _self.trigger('success:get_my_act_list',res);


                },
                error : function(res)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('error:get_my_act_list',res);

                    $.tips
                    ({
                        content:'网络异常',
                        stayTime:3000,
                        type:'warn'
                    });
                }

            });
        },
        /**
         * 安装事件
         * @private
         */
        _setup_event : function()
        {
            var self = this;

            _self.on('success:get_my_act_list',function(e,res)
            {

                _self.has_next_page = res.has_next_page;

                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();

                _self.$has_data.removeClass('fn-hide');

                var xhr_data = _self.ajax_obj.xhr_data;

                if(!res.list.length && (xhr_data.page == 1))
                {
                    _self.$has_data.addClass('fn-hide');
                    _self.$no_data.removeClass('fn-hide').html('');

                    abnormal.render(_self.$no_data[0],{});

                    return;
                }

                if(res.code>0)
                {

                    var html_str = self.render_card(res,_self.type).join('');

                    var method = (xhr_data.page == 1) ? 'html' : 'append';

                    _self.$status_container[method](html_str);

                    if(!App.isPaiApp)
                    {
                       $('[data-role="btn_pub_scan"]').addClass('fn-hide');     
                    }
                }

                self._setup_button_event();


            })
            .on('error:get_my_act_list',function(e,res)
            {
                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
            });
        },
        _setup_button_event : function()
        {
            var self = this;


            $('[data-role="btn_unpaid_pay"]').on('click',function(ev)
            {
                //去付款

                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');
                var enroll_id = $cur_btn.attr('data-enroll-id');

                window.location.href = './pay.php?event_id='+event_id+'&enroll_id='+enroll_id;

                //page_control.navigate_to_page('act/payment/'+event_id+'/'+enroll_id);
            });
            // 待付款 取消报名付款
            $('[data-role="btn_unpaid_delete"]').on('click',function(ev)
            {


                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');
                var enroll_id = $cur_btn.attr('data-enroll-id');

                if(confirm('是否取消报名'))
                {
                    self.set_act_status({enroll_id:enroll_id,type:'del_enroll'});
                }
            });

            $('[data-role="lists-to-info"]').on('click',function(ev)
            {


                var $cur_btn = $(ev.currentTarget);

                var enroll_id = $cur_btn.attr('data-enroll-id');

                var view_type = $cur_btn.attr('data-view-type');

                var event_id = $cur_btn.attr('data-event_id');

                var audit_status = $cur_btn.attr('data-audit_status');

                var data;

                if(audit_status == '0')
                {
                    alert('该活动在审核当中');

                    return;
                }

                if(audit_status == '2')
                {
                    alert('该活动未通过审核','error');

                    return;
                }
                console.log(enroll_id)
                //我发布的
                if(!enroll_id)
                {
                    //page_control.navigate_to_page("mine/info/event/" + event_id);
                }
                else
                {
                    /*switch(view_type)
                    {
                        case 'unpaid' : data = self.uppaid_view.search_data_by_id(enroll_id);break;
                        case 'paid' : data = self.paid_view.search_data_by_id(enroll_id);break;
                        case 'pub' : data = self.pub_view.search_data_by_id(enroll_id);break;
                    }*/

                    //page_control.navigate_to_page("mine/info/enroll/" + enroll_id,data);
                }



            });

            $('[data-role="btn_pub_show_ewm"]').on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');
                var enroll_id = $cur_btn.attr('data-enroll-id');

                //m_alert.show('加载二维码中...','loading');

                utility.ajax_request
                ({
                    url : window.$__ajax_domain+'get_act_ticket_detail.php',
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
                            alert('二维码不存在');

                            return;
                        }

                        //m_alert.hide({delay:-1});

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

                        if(App.isPaiApp)
                        {
                            App.qrcodeshow(qr_arr);
                        }
                        else
                        {
                            // 处理二维码
                            var qrcode = require('qrcode');
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

                    }
                });
            });
            //去评价
            $('[data-role="btn_paid_commit"]').on('click',function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);
                //事件id
                var event_id = $cur_btn.attr('data-event-id');
                //被评价id
                var to_date_id = $cur_btn.attr('data-to-date-id');

                var table_id = $cur_btn.attr('data-table-id');

                var can_comment = $cur_btn.attr('data-can-comment');

                var user_id = $cur_btn.attr('data-org-id');

                if(can_comment)
                {
                    window.location.href = '../comment/?event_id='+event_id+'&table_id='+table_id+'&type=event';
                    //page_control.navigate_to_page('comment/event/'+ event_id + '/'+user_id,{table_id : table_id});
                }
            });

            $('[data-role="btn_pub_finnish"]').on('click',function(ev)
            {
                //活动结束

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');

                if(confirm('是否结束活动'))
                {
                    self.set_act_status({event_id:event_id,type:'end'});

                }
            });

            // 取消活动
            $('[data-role="btn_pub_cencel"]').on('click',function(ev)
            {

                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');

                if(confirm('是否取消活动'))
                {
                    //m_alert.show('提交中','loading');
                    self.set_act_status({event_id:event_id,type:'cancel'});

                }
            });

            $('[data-role="btn_pub_scan"]').on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var event_id = $cur_btn.attr('data-event-id');

                window.location.href = './sign.php?event_id='+event_id;
            });
        },
        render_card : function(data,type)
        {
            var self = this;

            var arr = [];

            $.each(data.list,function(i,obj)
            {
                var hide_line = false;

                switch (type)
                {
                    case 'unpaid' :
                        hide_line = !(obj.event_detail.enroll_pay_button || obj.event_detail.enroll_cancel_button);
                        break;
                    case 'paid' :
                        hide_line = !(obj.event_detail.enroll_code_button || obj.event_detail.enroll_comment_button);
                        break;
                    case 'pub' :
                        hide_line = !(obj.event_detail.event_finish_button || obj.event_detail.event_scan_button);
                        break;
                }

                //在每个view中加入type 和隐藏1px border-top 和enroll_id nolset 2015-02-06
                obj.event_detail = $.extend(true,{},obj.event_detail,{view_type : type,hide_line:hide_line,enroll_id:obj.enroll_id});

                //待审核、审核未通过的图
                switch (obj.event_detail.audit_status)
                {
                    case '0':obj.event_detail = $.extend(true,obj.event_detail,{pic_show_reject:0,pic_show_no_reject:1});break;
                    case '1':obj.event_detail = $.extend(true,obj.event_detail,{pic_show_reject:0,pic_show_no_reject:0});break;
                    case '2':obj.event_detail = $.extend(true,obj.event_detail,{pic_show_reject:1,pic_show_no_reject:0});break;
                }

                var view = items_tpl
                ({
                    data : obj.event_detail
                });

                arr.push(view);
            });



            return arr;
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

                    _self.$loading = $.loading
                    ({
                        content:'加载中...'
                    });
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

    var list_obj = new list_class();


}; 
});