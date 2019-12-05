define('act/order_detail', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/21.
 */

var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var scroll = require('common/scroll/index');
var abnormal = require('common/widget/abnormal/index');
var items_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n    <div class=\"title\" data-role=\"title\" data-event_id=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\n        <a class=\"db w-100\" href=\"./detail.php?event_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\n            <div class=\"title-in\">\n                <div class=\"title-pic-container\">\n                    <div class=\"title-pic\" style=\"background:url('"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.cover_image)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "');background-size:cover;background-position:50%\"></div>\n                </div>\n                <div class=\"title-describe\">\n                    <p class=\"describe_title\" style=\"display: block;\">\n                        ";
  stack1 = ((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    </p>\n                    <p class=\"describe_price\">\n                        <span>��</span>\n                    <span data-role=\"price\">\n                        "
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\n                    </span>\n                        <span class=\"bla\">/��</span>\n                    </p>\n                </div>\n                <div class=\"an-arrow\">\n                    <i class=\"icon icon-allow-grey\"></i>\n                </div>\n            </div>\n        </a>\n    </div>\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.organizers), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.joiner), {hash:{},inverse:self.noop,fn:self.program(13, program13, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n\n\n\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.fk_n), {hash:{},inverse:self.noop,fn:self.program(32, program32, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.sm_n), {hash:{},inverse:self.noop,fn:self.program(34, program34, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.qd), {hash:{},inverse:self.noop,fn:self.program(36, program36, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pj_n), {hash:{},inverse:self.noop,fn:self.program(38, program38, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pj), {hash:{},inverse:self.noop,fn:self.program(40, program40, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.qx), {hash:{},inverse:self.noop,fn:self.program(42, program42, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pub_qd_n), {hash:{},inverse:self.noop,fn:self.program(44, program44, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pub_qd), {hash:{},inverse:self.noop,fn:self.program(44, program44, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pub_wc), {hash:{},inverse:self.noop,fn:self.program(46, program46, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pub_qx), {hash:{},inverse:self.noop,fn:self.program(48, program48, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_scan_button), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <div class=\"block\">\n            ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_detail), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            <div class=\"describe-info\">\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.add_time), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.complete_time), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.cancel_time), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            </div>\n        </div>\n    ";
  return buffer;
  }
function program3(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <div class=\"button\" data-role=\"act-go-scan\">\n                <a href=\"./sign.php?event_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" class=\"db w-100\">\n                    <button class=\"ui-button ui-button-block ui-button-size-x ui-button-100per ui-button-bd-555\">\n                        <span class=\"ui-button-content\"><i class=\"icon icon-btn-icon-sm\"></i> ɨ��ǩ��</span>\n                    </button>\n                </a>\n            </div>\n        ";
  return buffer;
  }

function program5(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                <div class=\"describe\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_detail)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n            ";
  return buffer;
  }

function program7(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.add_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program9(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line\"><span class=\"notice\">���ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.complete_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program11(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line\"><span class=\"notice\">ȡ��ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.cancel_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program13(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_pay_button), {hash:{},inverse:self.noop,fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_code_button), {hash:{},inverse:self.noop,fn:self.program(16, program16, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_comment_button), {hash:{},inverse:self.noop,fn:self.program(18, program18, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <div class=\"block\">\n            ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.bar_text), {hash:{},inverse:self.noop,fn:self.program(20, program20, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">���ţ�</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.order_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_time), {hash:{},inverse:self.noop,fn:self.program(22, program22, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_time), {hash:{},inverse:self.noop,fn:self.program(24, program24, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone), {hash:{},inverse:self.noop,fn:self.program(26, program26, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num), {hash:{},inverse:self.noop,fn:self.program(28, program28, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget), {hash:{},inverse:self.noop,fn:self.program(30, program30, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n\n\n\n\n            </div>\n        </div>\n    ";
  return buffer;
  }
function program14(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <div class=\"button\" data-role=\"act-go-pay\">\n                <a href=\"./pay.php?event_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "&enroll_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" class=\"db w-100\">\n                    <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-100per ui-button-bd-ff6\">\n                        <span class=\"ui-button-content\"><i class=\"icon icon-btn-icon-fk\"></i> ȥ֧��</span>\n                    </button>\n                </a>\n            </div>\n        ";
  return buffer;
  }

function program16(depth0,data) {
  
  
  return "\n            <div class=\"button\" data-role=\"act-show-qr\">\n                <button class=\"ui-button ui-button-block ui-button-size-x ui-button-100per ui-button-bd-555\">\n                    <span class=\"ui-button-content\"><i class=\"icon icon-er-wei-ma-w\"></i> ��ʾ�ȯ��ά��</span>\n                </button>\n            </div>\n        ";
  }

function program18(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <div class=\"button\" data-role=\"act-go-commit\">\n                <a href=\"./pay.php?event_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.event_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "&enroll_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "&table_id="
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.table_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" class=\"db w-100\">\n                    <button class=\"ui-button ui-button-block ui-button-size-x ui-button-100per ui-button-bd-555\">\n                        <span class=\"ui-button-content\"><i class=\"icon icon-btn-icon-pj\"></i> ���ۻ</span>\n                    </button>\n                </a>\n\n            </div>\n        ";
  return buffer;
  }

function program20(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                <div class=\"describe\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.bar_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n            ";
  return buffer;
  }

function program22(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program24(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line\"><span class=\"notice\">ǩ��ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program26(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line tel\"><span class=\"notice\">�ֻ����룺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program28(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line\"><span class=\"notice\">������</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program30(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <div class=\"describe-info-line\"><span class=\"notice\">�ܼۣ�</span><span class=\"notice-inside\">��"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                ";
  return buffer;
  }

function program32(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"button fn-hide\">\n            <button class=\"ui-button  ui-button-block  ui-button-size-x ui-button-100per ui-button-bd-ff6\">\n                <span class=\"ui-button-content\"><i class=\"icon icon-btn-icon-fk\"></i> ȥ֧��</span>\n            </button>\n        </div>\n        <div class=\"block\">\n            <div class=\"describe\">������</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">���ţ�</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line tel\"><span class=\"notice\">�ֻ����룺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">������</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">�ܼۣ�</span><span class=\"notice-inside\">��"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program34(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"button fn-hide\">\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-555 ui-button-100per\">\n                <span class=\"ui-button-content\"><i class=\"icon icon-er-wei-ma-w\"></i> ��ʾ�ȯ��ά��</span>\n            </button>\n        </div>\n        <div class=\"block\">\n            <div class=\"describe\">�Ѹ��׼��ǩ��</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">���ţ�</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line tel\"><span class=\"notice\">�ֻ����룺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">������</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">�ܼۣ�</span><span class=\"notice-inside\">��"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program36(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"block\">\n            <div class=\"describe\">���ֳ�ǩ��</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">���ţ�</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">ǩ��ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line tel\"><span class=\"notice\">�ֻ����룺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">������</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">�ܼۣ�</span><span class=\"notice-inside\">��"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program38(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"button fn-hide\">\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-555 ui-button-100per\">\n                <span class=\"ui-button-content\"><i class=\"icon icon-btn-icon-pj\"></i> ���ۻ</span>\n            </button>\n        </div>\n        <div class=\"block\">\n            <div class=\"describe\">������</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">���ţ�</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">ǩ��ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line tel\"><span class=\"notice\">�ֻ����룺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">������</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">�ܼۣ�</span><span class=\"notice-inside\">��"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program40(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"block\">\n            <div class=\"describe\">������</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">���ţ�</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.pay_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">ǩ��ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line tel\"><span class=\"notice\">�ֻ����룺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">������</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">�ܼۣ�</span><span class=\"notice-inside\">��"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program42(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"block\">\n            <div class=\"describe\">��ȡ��</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">���ţ�</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_id)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line tel\"><span class=\"notice\">�ֻ����룺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.phone)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">������</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">�ܼۣ�</span><span class=\"notice-inside\">��"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.total_budget)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program44(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"button fn-hide\">\n            <button class=\"ui-button ui-button-block ui-button-size-x ui-button-bd-555 ui-button-100per\">\n                <span class=\"ui-button-content\"><i class=\"icon icon-btn-icon-sm\"></i> ɨ��ǩ��</span>\n            </button>\n        </div>\n        <div class=\"block\">\n            <div class=\"describe\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_detail)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.add_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program46(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"block\">\n            <div class=\"describe\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_detail)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.add_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">���ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.complete_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

function program48(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"block\">\n            <div class=\"describe\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.scan_detail)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</div>\n            <div class=\"describe-info\">\n                <div class=\"describe-info-line\"><span class=\"notice\">����ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.add_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n                <div class=\"describe-info-line\"><span class=\"notice\">ȡ��ʱ�䣺</span><span class=\"notice-inside\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.cancel_time)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span></div>\n            </div>\n        </div>\n    ";
  return buffer;
  }

  stack1 = helpers['if'].call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
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
                                var qrcode = require('qrcode');
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
});