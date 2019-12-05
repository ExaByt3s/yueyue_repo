define('topic', function(require, exports, module){ 
var $ = require('components/zepto/zepto.js');
var LZ = require('common/lazyload/lazyload');
var utility = require('common/utility/index');
var App =  require('common/I_APP/I_APP');
var menu = require('menu/index');
var WeiXinSDK =  require('common/I_WX_SDK/I_WX_SDK');

var demo_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var stack1, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "img_tpl", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "img_tpl", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "text_tpl", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "text_tpl", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "goods_tpl1", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "goods_tpl1", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(16, program16, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "goods_tpl2", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "goods_tpl2", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(23, program23, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "goods_tpl3", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "goods_tpl3", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(26, program26, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "list_tpl1", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "list_tpl1", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(28, program28, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "list_tpl2", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "list_tpl2", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n    ";
  stack1 = (helper = helpers.compare || (depth0 && depth0.compare),options={hash:{},inverse:self.noop,fn:self.program(30, program30, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.tpl_type), "==", "attr_goods_tpl", options) : helperMissing.call(depth0, "compare", (depth0 && depth0.tpl_type), "==", "attr_goods_tpl", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n        <!--0-->\r\n        <div class=\"tpl-goods-items\">\r\n            <img src=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.img_url)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-ori-src=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.img_url)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" class=\"items-img\">\r\n        </div>\r\n    ";
  return buffer;
  }

function program4(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n        <!--1-->\r\n        <div class=\"category_notice\">\r\n            <p class=\"title\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n            <p class=\"notice\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n        </div>\r\n    ";
  return buffer;
  }

function program6(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n            <!--2-->\r\n            <div>\r\n            ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.list), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n            </div>\r\n    ";
  return buffer;
  }
function program7(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\r\n                <div class=\"tpl-goods-demo1-child\">\r\n                    <div class=\"top\">\r\n                        <img src=\""
    + escapeExpression((helper = helpers.change_img_size || (depth0 && depth0.change_img_size),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.img_url), "260", options) : helperMissing.call(depth0, "change_img_size", (depth0 && depth0.img_url), "260", options)))
    + "\" class=\"goods-child-img\"> \r\n                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.goods_tag), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                        <p class=\"title\"></p>\r\n                    </div>   \r\n                    <div class=\"sideBar\">\r\n                        <p class=\"msg-title row2-hidden f16\">";
  if (helper = helpers.goods_text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n                        <!-- <p class=\"msg topic-tpl-nowrep f12\">";
  if (helper = helpers.goods_text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p> -->\r\n<!--                         <p class=\"msg f12\">动即享美食盛宴手指动</p> -->\r\n                        <div class=\"mt10\">\r\n<!--                             ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.noprice), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " -->\r\n                            <span class=\"money\">";
  if (helper = helpers.price) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.price); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span> \r\n                            <!-- <span class=\"text-del f12\">";
  if (helper = helpers.noprice) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.noprice); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>   -->\r\n                        </div>\r\n                        <a  href=\"";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.goods_url), {hash:{},inverse:self.program(14, program14, data),fn:self.program(12, program12, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" >\r\n                            <button class=\"ui-button ui-button-block ui-button-100per ui-button-size-x ui-button-bg-ff6 tpl-btn\"><span class=\"ui-button-content\">点击购买</span></button>\r\n                        </a>\r\n                    </div>                   \r\n                </div>\r\n\r\n            ";
  return buffer;
  }
function program8(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                        <p style=\"background: #FF3366\" class=\"tag\">";
  if (helper = helpers.goods_tag) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_tag); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n                        ";
  return buffer;
  }

function program10(depth0,data) {
  
  
  return " \r\n                            <span class=\"f10 money-tag\">优惠价</span>\r\n                            <span class=\"f16\">/</span>\r\n                            ";
  }

function program12(depth0,data) {
  
  var stack1, helper;
  if (helper = helpers.goods_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  return escapeExpression(stack1);
  }

function program14(depth0,data) {
  
  
  return "javascript:;";
  }

function program16(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n        <!--3-->\r\n        <div class=\"tpl-goods-demo1-child\" style=\"width:100%;\">\r\n            <div class=\"top\">\r\n                <img src=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.img_url)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" class=\"goods-child-img\"> \r\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_tag), {hash:{},inverse:self.noop,fn:self.program(17, program17, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                <p class=\"title\"></p>\r\n            </div>\r\n            <div class=\"sideBar\">\r\n                <p class=\"msg-title row2-hidden f16\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                <!-- <p class=\"msg topic-tpl-nowrep f12\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p> -->\r\n<!--                 <p class=\"msg f12\">动即享美食盛宴手指动</p>\r\n -->                <div class=\"mt10\"> \r\n<!--                     ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.noprice), {hash:{},inverse:self.noop,fn:self.program(19, program19, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " -->\r\n                    <span class=\"money\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.price)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span> \r\n<!--                     <span class=\"text-del f12\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.noprice)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span> -->\r\n                </div>\r\n                <a href=\"";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_url), {hash:{},inverse:self.program(14, program14, data),fn:self.program(21, program21, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" >\r\n                    <button class=\"ui-button ui-button-block ui-button-100per ui-button-size-x ui-button-bg-ff6 tpl-btn\"><span class=\"ui-button-content\">点击购买</span></button>\r\n                </a>\r\n            </div>                   \r\n        </div>\r\n    ";
  return buffer;
  }
function program17(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n                <p style=\"background: #FF3366\" class=\"tag\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_tag)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                ";
  return buffer;
  }

function program19(depth0,data) {
  
  
  return "\r\n                    <span class=\"f10 money-tag\">优惠价</span>\r\n                    <span class=\"f16\">/</span>\r\n                    ";
  }

function program21(depth0,data) {
  
  var stack1;
  return escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_url)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1));
  }

function program23(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\r\n        <!--4-->\r\n      <div class=\"tpl-goods-demo3-child\">\r\n            <div class=\"sideBar\">\r\n                <p class=\"msg-title row2-hidden f16\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                <!-- <p class=\"msg topic-tpl-nowrep f12\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_text)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p> -->\r\n<!--                 <p class=\"msg f12\">动即享美食盛宴手指动</p>\r\n -->                <div class=\"mt10\">\r\n<!--                     ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.noprice), {hash:{},inverse:self.noop,fn:self.program(19, program19, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " -->\r\n                    <span class=\"money\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.price)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span> \r\n<!--                     <span class=\"text-del f12\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.noprice)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span> -->\r\n                </div>\r\n                <a href=\"";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_url), {hash:{},inverse:self.program(14, program14, data),fn:self.program(21, program21, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" >\r\n                    <button class=\"ui-button ui-button-block ui-button-100per ui-button-size-x ui-button-bg-ff6 tpl-btn\"><span class=\"ui-button-content\">点击购买</span></button>\r\n                </a>\r\n            </div>  \r\n            <div class=\"top\">\r\n                <img src=\""
    + escapeExpression((helper = helpers.change_img_size || (depth0 && depth0.change_img_size),options={hash:{},data:data},helper ? helper.call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.img_url), "260", options) : helperMissing.call(depth0, "change_img_size", ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.img_url), "260", options)))
    + "\" class=\"goods-child-img\">\r\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.goods_tag), {hash:{},inverse:self.noop,fn:self.program(24, program24, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                <p class=\"title\"></p>\r\n            </div>\r\n        </div>\r\n        \r\n    ";
  return buffer;
  }
function program24(depth0,data) {
  
  
  return "\r\n                <p style=\"background: #FF3366\" class=\"tag\"></p>\r\n                ";
  }

function program26(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n        <!--5-->\r\n    <div class=\"tpl-demo-header\">\r\n        <a  class=\"db\" href=\"";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.goods_url), {hash:{},inverse:self.program(14, program14, data),fn:self.program(21, program21, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\">\r\n            <div class=\"header-img\" style=\"background-image: url("
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.img_url)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + ")\">\r\n                <div class=\"title-con\">\r\n                    <div class=\"text-description\">\r\n                        <p class=\"header-title topic-tpl-nowrep\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                        <p class=\"header-notice\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.subtitle)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </a>\r\n    </div>\r\n    ";
  return buffer;
  }

function program28(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n        <!--6-->\r\n        <div class=\"tpl-demo-header\">\r\n                <div class=\"header-img\" style=\"background-image: url("
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.img_url)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + ")\">\r\n                    <div class=\"title-con\">\r\n                        <div class=\"text-description\">\r\n                            <p class=\"header-title\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                            <p class=\"header-notice\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.subtitle)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n            <div class=\"category-notice\">\r\n                <p class=\"category-title\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</p>\r\n                <a href=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.custom_data)),stack1 == null || stack1 === false ? stack1 : stack1.button)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\r\n                    <button class=\"ui-button ui-button-block ui-button-100per ui-button-size-x ui-button-bg-ff6\" style=\"width:200px;margin:0 auto;margin-top: 1em;border: solid 1px;\"><span class=\"ui-button-content\">进入</span></button>\r\n                </a>\r\n            </div>\r\n        </div>\r\n    ";
  return buffer;
  }

function program30(depth0,data) {
  
  
  return "\r\n        <!--7-->\r\n        <div>attr_goods_tpl商品属性模版</div>\r\n    ";
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.demo), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { return stack1; }
  else { return ''; }
  });
var mySwiper = require('common/widget/swiper/1.0.0/swiper3.07.min');


$(function()
    {
        // 加载轮播图
        var mySwiper = new Swiper ('.swiper-container', {
            direction: 'horizontal',
            loop: false,
            autoplay : 3000,
            speed:300,
            autoplayDisableOnInteraction : false,
            // 如果需要分页器
            pagination: '.swiper-pagination'
        });

        // ===== 加载自定义模板 =====
        var html_str = demo_tpl
        ({
            demo : tpl_json
        });
        $('.tpl-demo').html(html_str);
        // ===== 加载自定义模板 =====

        var $topic_container = $('.topic-info-container').eq(0);        

        $topic_container.find('img').each(function(i,obj)
        {
            var ori_img_url = $(obj).attr('src');
            $(obj).attr({'src':'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkJDQzA1MTVGNkE2MjExRTRBRjEzODVCM0Q0NEVFMjFBIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkJDQzA1MTYwNkE2MjExRTRBRjEzODVCM0Q0NEVFMjFBIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QkNDMDUxNUQ2QTYyMTFFNEFGMTM4NUIzRDQ0RUUyMUEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QkNDMDUxNUU2QTYyMTFFNEFGMTM4NUIzRDQ0RUUyMUEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6p+a6fAAAAD0lEQVR42mJ89/Y1QIABAAWXAsgVS/hWAAAAAElFTkSuQmCC','height':200,'data-lazyload-url':ori_img_url,'data-ori-src':ori_img_url});
        });

        $topic_container.removeClass('fn-hide');

        //new 对象 新建内置对象
        var lazyloading = new LZ($('body'),{

        });

        App.isPaiApp && App.showtopmenu(true);

        /**** 调用微信分享 ****/
        if(WeiXinSDK.isWeiXin())
        {
            var share = share_text.result_data;

            // 朋友圈
            var WeiXin_data_Timeline =
                {
                    title: share.title, // 分享标题
                    link: share.url, // 分享链接
                    imgUrl: share.img, // 分享图标
                    success: function ()
                    {
                        // 用户确认分享后执行的回调函数
                        new Image().src = 'http://yp.yueus.com/action/wx_share_callback.php?platform=timeline&url='+encodeURIComponent(window.location.href);
                    },
                    cancel: function ()
                    {
                        // 用户取消分享后执行的回调函数
                    }
                };

            // 好友、QQ
            var WeiXin_data =
                {
                    title: share.title, // 分享标题
                    desc: share.content, // 分享描述
                    link: share.url, // 分享链接
                    imgUrl: share.img, // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function ()
                    {
                        // 用户确认分享后执行的回调函数
                        new Image().src = 'http://yp.yueus.com/action/wx_share_callback.php?platform=friends&url='+encodeURIComponent(window.location.href);
                    },
                    cancel: function ()
                    {
                        // 用户取消分享后执行的回调函数
                    }
                };

            WeiXinSDK.ready(function()
            {
                WeiXinSDK.ready(function()
                {
                    WeiXinSDK.ShareToFriend(WeiXin_data);

                    WeiXinSDK.ShareTimeLine(WeiXin_data_Timeline);

                    WeiXinSDK.ShareQQ(WeiXin_data);
                });

            });
        }
        /**** 调用微信分享 ****/
        

        $('.topic-info-container').find('img').on('click',function(ev){
            var self = this;

            var $cur_btn = $(ev.currentTarget);

            var $total_alumn_img = $('.topic-info-container').find('img');

            if($cur_btn.parents('a').length > 0 )
            {
                var cur_a_link = $cur_btn.parents('a').attr('href');

                window.location.href = cur_a_link;

                return false;
            }

            var total_alumn_img_arr = [];

            // 当前图片索引
            var index = $total_alumn_img.index($cur_btn);

            var data =
                {
                    img_arr : total_alumn_img_arr,
                    index : index
                };
            ev.preventDefault();
            var cur_img = $(this).attr('data-ori-src');

            if(App.isPaiApp)
            {
                $total_alumn_img.each(function(i,obj)
                {
                    total_alumn_img_arr.push
                    ({
                        url : $(obj).attr('data-ori-src'),
                        text : ''
                    });
                });
                App.show_alumn_imgs(data);
            }
            else if(WeiXinSDK.isWeiXin())
            {
                // 微信显示大图操作
                $total_alumn_img.each(function(index, item)
                {
                    total_alumn_img_arr.push($(item).attr('data-ori-src'))
                });
                //todo 以后微信或wap版本调用
                WeiXinSDK.imagePreview(cur_img,total_alumn_img_arr);
            }
        });

        //右上角菜单弹出层
        var menu_data =
                    [
                        {
                            index:0,
                            content:'分享',
                            click_event:function()
                            {
                                var self = this;
                                App.share_card(share_text.result_data,
                                        function(data)
                                        {

                                        }
                                )
                            }
                        },
                        {
                            index:1,
                            content:'首页',
                            click_event:function()
                            {
                                App.isPaiApp && App.switchtopage({page:'hot'});
                            }
                        },
                        {
                            index:2,
                            content:'刷新',
                            click_event:function()
                            {
                                window.location.href = window.location.href;
                            }
                        }

                    ];
        menu.render($('body'),menu_data);
        var __showTopBarMenuCount = 0;

        utility.$_AppCallJSObj.on('__showTopBarMenu',function(event,data)
        {

            __showTopBarMenuCount++;

            if(__showTopBarMenuCount%2!=0)
            {
                menu.show()
            }
            else
            {
                menu.hide()
            }
        });
    }); 
});