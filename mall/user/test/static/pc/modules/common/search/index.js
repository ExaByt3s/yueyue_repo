define('common/search/index', function(require, exports, module){ /**
 * @author hudw
 * 2015.10.15 
 * 搜索组件
 */

/**
 * @require modules/common/search/search.scss
 */

var search_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, self=this, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  
  return " \n			    onchange=\"submit_by_select()\"\n			    ";
  }

function program3(depth0,data) {
  
  
  return "selected";
  }

function program5(depth0,data,depth1) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                <div class=\"search-text fldi \" style=\"";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.show), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" data-role=\"";
  if (helper = helpers.search_type) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.search_type); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "-input-container\">\n                    <input ";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.show), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " value=\"";
  if (helper = helpers.text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-placeholder=\"";
  if (helper = helpers.place_holder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.place_holder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"search_text\" class=\"text font_wryh\" name=\"keywords\" autocomplete=\"off\" maxlength=\"255\" accesskey=\"s\" id=\"search-text\"\n                    ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.program(12, program12, data),fn:self.program(10, program10, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.search_type), "goods", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.search_type), "goods", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " />\n                    <input ";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.show), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " value=\"";
  if (helper = helpers.default_text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.default_text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" name=\"default_text\" type=\"hidden\" data-role=\"default-search-text\">\n                    <input ";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.show), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " value=\"";
  if (helper = helpers.default_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.default_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" name=\"default_url\" type=\"hidden\" data-role=\"default-url\">\n                    <button class=\"icon-search\" style=\"background-image:url('"
    + escapeExpression(((stack1 = (depth1 && depth1.icon_search)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "')\" data-role=\"search-go\" type=\"submit\"></button>\n                    <input ";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.show), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " type=\"hidden\" name=\"type_id\" value=\"";
  if (helper = helpers.type_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.type_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n                    <label style=\"";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.text), {hash:{},inverse:self.noop,fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"  data-role=\"label-place-holder\" class=\"label-place-holder\">";
  if (helper = helpers.place_holder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.place_holder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</label>\n\n                </div>\n            ";
  return buffer;
  }
function program6(depth0,data) {
  
  
  return "display:none;";
  }

function program8(depth0,data) {
  
  
  return "disabled";
  }

function program10(depth0,data) {
  
  
  return "title=\"请输入关键字\"";
  }

function program12(depth0,data) {
  
  
  return "title=\"请输入商家ID/商家名称\"";
  }

function program14(depth0,data) {
  
  
  return "visibility: hidden;display: none;";
  }

function program16(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n	<div class=\"search-hot-tag fldi\">\n		\n		<span>热门搜索：</span>\n		";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.hot_data), {hash:{},inverse:self.noop,fn:self.program(17, program17, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "		\n	</div>\n	";
  return buffer;
  }
function program17(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n		<span class=\"pr5 pl5\"><a href=\"";
  if (helper = helpers.link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" title=\"";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.str) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.str); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</a></span>\n		";
  return buffer;
  }

  buffer += "<div class=\"search-mod clearfix \">\n	<div class=\"search-form-container fldi \" >\n		<form method=\"GET\" action=\"";
  if (helper = helpers.search_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.search_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" id=\"search-form\">\n		    <div class=\"search-left fldi\">\n			    <select data-role=\"search_service_type\" name=\"search_type\" \n			    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.select_use_change), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n			    >\n			    	<option value=\"goods\" ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.search_type), "goods", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.search_type), "goods", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">服务</option>\n			    	<option value=\"seller\" ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.search_type), "seller", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.search_type), "seller", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">商家</option>\n			    </select>			    \n		    </div>\n		    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.programWithDepth(5, program5, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            <input type=\"hidden\" value=\"1\" name=\"p\">\n	    </form>\n	</div>\n	";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hot_data), {hash:{},inverse:self.noop,fn:self.program(16, program16, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n</div>\n<script>\n	// 下拉提交\n	function submit_by_select()\n	{\n		var doc = document;\n		doc.getElementById('search-text').value = '';\n		doc.getElementById('search-form').submit();\n	}\n\n\n</script>";
  return buffer;
  });
var filter_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n	            <li class=\"fldi\"><a href=\"";
  if (helper = helpers.link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "#dw\" ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.selected), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</a></li>\n	        ";
  return buffer;
  }
function program2(depth0,data) {
  
  
  return "class=\"a-selected\" ";
  }

function program4(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n	<div class=\"search-filter-body \" data-role=\"search-filter-body\">\n		";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.filter_data), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "	\n	</div>\n	";
  return buffer;
  }
function program5(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n		<div data-role-key-name=\"";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"clearfix search-filter-item\" data-role=\"search-filter-list\">\n		    <div class=\"search-filter-body-name fldi\" >";
  if (helper = helpers.text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "：</div>		   \n		    <ul class=\"search-filter-body-con fldi clearfix\" >\n		        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n		    </ul>\n		    <a href=\"javascript:;\" class=\"more fn-hide\"><span><i class=\"arrow bottom\"><s>◇</s></i><em>展开</em></span></a>\n		</div>\n		";
  return buffer;
  }
function program6(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n		            <li  data-role=\"search-filter-list-items\" class=\"fldi ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.rel_key), "self", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.rel_key), "self", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\">\n		            	<a class=\"";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.selected), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.rel_key), "self", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.rel_key), "self", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" href=\"";
  if (helper = helpers.link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "#dw\" >";
  if (helper = helpers.val) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.val); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</a>\n		            	";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(13, program13, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.rel_key), "self", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.rel_key), "self", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "		            	\n		            </li>\n		        ";
  return buffer;
  }
function program7(depth0,data) {
  
  
  return "clearfix";
  }

function program9(depth0,data) {
  
  
  return "a-selected";
  }

function program11(depth0,data) {
  
  
  return "fldi last";
  }

function program13(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n	            		<div class=\"ui-date-input-mod fldi \"><span>开始</span><input id=\"start-date\" type=\"text\" value=\"\" class=\"icon input pr10\" readonly=\"1\" name=\"huo_add_time_s\" /></div>\n	            		<div class=\"ui-date-input-mod fldi \"><span> - 结束</span><input id=\"end-date\" type=\"text\" value=\"\" class=\"icon input pr10\" readonly=\"1\" name=\"huo_add_time_e\"  /></div>\n	            		<button id=\"search-by-self\" class=\"fldi confirm\" data-link=\"";
  if (helper = helpers.link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">提交</button>\n		            	";
  return buffer;
  }

  buffer += "<div>\n	<div class=\"search-filter-header clearfix\">\n	    <div class=\"search-filter-header-name fldi\">品类：</div>	    \n	    <ul class=\"search-filter-header-con fldi clearfix\">\n	        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.type_data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n	    </ul>\n	</div>\n	";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.filter_data), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n</div>\n";
  return buffer;
  });
var sort_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n    <p data-res=\"\" class=\"data_res\"><i class=\"icon-tips mr10\" style=\"background-image:url('";
  if (helper = helpers.icon_tips) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.icon_tips); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "');\"></i>";
  if (helper = helpers.no_data_text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.no_data_text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n    ";
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.keywords), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n    ";
  return buffer;
  }
function program4(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n        <p data-res=\"\" class=\"data_res\">搜索到 ";
  if (helper = helpers.keywords) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.keywords); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + " ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.is_seller), {hash:{},inverse:self.program(7, program7, data),fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</p>\r\n        ";
  return buffer;
  }
function program5(depth0,data) {
  
  
  return "商家";
  }

function program7(depth0,data) {
  
  
  return "服务";
  }

function program9(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n<div class=\"w1000\" data-role=\"achor-sort-btn\" >\r\n    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.is_seller), {hash:{},inverse:self.program(14, program14, data),fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n</div>\r\n";
  return buffer;
  }
function program10(depth0,data) {
  
  var buffer = "", stack1;
  buffer += " \r\n    <ul class=\"search-order clearfix\">        \r\n        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.sort_btn), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n        \r\n    </ul>\r\n    ";
  return buffer;
  }
function program11(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n        <li class=\"fldi ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.selected), {hash:{},inverse:self.noop,fn:self.program(12, program12, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\">\r\n        <a class=\"db\" href=\"javacript:;\" data-sort-arr=\"";
  if (helper = helpers.sort) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sort); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-orderby=\"";
  if (helper = helpers.orderby) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.orderby); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "<i class=\"";
  if (helper = helpers.arrow) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.arrow); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + " ml5\"></a></i></li>            \r\n        ";
  return buffer;
  }
function program12(depth0,data) {
  
  
  return "cur";
  }

function program14(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    <ul class=\"search-order clearfix\">        \r\n        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.sort_btn), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n    </ul>\r\n    ";
  return buffer;
  }

  buffer += "<div class=\"w1000\">\r\n    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.is_no_data), {hash:{},inverse:self.program(3, program3, data),fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n    \r\n</div>\r\n\r\n";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.is_no_data), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });

var $ = require('components/jquery/jquery.js');
var utility = require('common/utility/index');
var selectbox = require('yue_ui/selectbox/selectbox');


var search_class = function (options) 
{
    var self = this;

    options = options || {};

    self.options = options;

    self.total_data = self.options.total_data || [];
    self.select_use_change = self.options.select_use_change || false;

    self.icon_search = self.options.icon_search;
    self.icon_tips = self.options.icon_tips;


    self.$input_container = self.options.input_container || {};
    self.$filt_data_container = self.options.filt_data_container || {};
    self.$sort_btn_list = self.options.sort_btn_list || {};

	self.filter_rendered = self.options.filter_rendered || function(){};

    self.$input_container.parent().addClass('ui-search-com');

    self.search_url =  window.$__project_domain+'/search/';

    // 初始化
    self.init();


};

/**
 * 搜索类
 * @type {[type]}
 */
search_class.prototype = 
{
    init : function()
    {
        var self = this;

        self.render_input_container(self.total_data.input_data);
        self.render_filter_container
        ({
            type_data:self.total_data.type_data,
            filter_data:self.total_data.filter_data,
            is_no_data : self.total_data.input_data.is_no_data
        });
        self.render_sort_container(self.total_data.sort_data);

        self.setup_event();
    },
    /**
     * 渲染搜索输入框和热门标记
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    render_input_container : function(data)
    {
        var self = this;

        if(!self.$input_container.length)
        {
            return;
        } 

        data = $.extend(data,
            {
                icon_search : self.icon_search,
                search_url : self.search_url,
                select_use_change : self.select_use_change
            });

        var html_str = search_tpl(data);

        self.$input_container.html(html_str);

        selectbox(self.$input_container.find('select')[0]);
    },
    /**
     * 渲染过滤数据
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    render_filter_container : function(data)
    {
        var self = this;

        if(!self.$filt_data_container.length)
        {
            return;
        }

        var html_str = filter_tpl(data);

        self.$filt_data_container.html(html_str);

		self.filter_rendered.call(this,self.$filt_data_container);
		
    },
    /**
     * 渲染排序数据
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    render_sort_container : function(data)
    {
        var self = this;

        if(!self.$sort_btn_list.length)
        {
            return;
        }

        data = $.extend(data,{icon_tips:self.icon_tips})

        var html_str = sort_tpl(data);

        self.$sort_btn_list.html(html_str);
    },
    /**
     * 安装事件
     * @return {[type]} [description]
     */
    setup_event : function()
    {
        var self = this;

        if(self.$input_container.length) 
        {
            self.setup_select();
            self.setup_submit();
        }

        if(self.$filt_data_container.length)
        {
            self.set_more_btn_show();
        }

        if(self.$sort_btn_list.length>0)
        {
            var $sort_btn = self.$sort_btn_list.find('[data-sort-arr]');

            $sort_btn.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var sort_arr = $cur_btn.attr('data-sort-arr').split(',');                            
                var orderby = $cur_btn.attr('data-orderby');                    

                // 反选排序
                if(!orderby)
                {
                    orderby = sort_arr[0];
                }
                else
                {
                    for(var i=0;i<sort_arr.length;i++)
                    {                    
                        if(orderby != sort_arr[i])
                        {
                            orderby = sort_arr[i];
                            break;
                        }

                        orderby = sort_arr[i];
                    }    
                }
                

                
                // 替换参数                    
                var cur_url = 'http://'+window.location.host+window.location.pathname+window.location.search;

                var hash = window.location.hash.substr(1);

                cur_url = change_url_params(cur_url,'orderby',orderby);
                cur_url = change_url_params(cur_url,'p',1);

                window.location.href = cur_url+'#px';

                return false;
            });
        }
    },
    /**
     * 处理展开收起
     */
    set_more_btn_show : function()
    {
        var self = this;
        var $list = self.$filt_data_container.find('[data-role="search-filter-list"]');
        
        $list.each(function()
        {
            var list_width = $(this).width();
            var $items = $(this).find('[data-role="search-filter-list-items"]');
            var all_items_width = 0;
            var $more = $(this).find('.more');

            $items.each(function()
            {
                all_items_width += $(this).outerWidth();                
            });



            if(list_width >= all_items_width)
            {
                // 内容不多，不用隐藏
                $more.addClass('fn-hide');                
            }
            else
            {
                // 内容太多了，要换行的
                $more.removeClass('fn-hide');                   
            }

        });

        // 收缩展开事件
        self.$filt_data_container.find('.more').on('click',function()
        {
            var $cur_btn = $(this);
            if(!$cur_btn.hasClass('fn-hide'))
            {
                var $list = $cur_btn.parent('[data-role="search-filter-list"]');
                if($list.hasClass('extend'))
                {
                    $list.removeClass('extend');
                    $list.find('ul').removeClass('auto');
                    $cur_btn.find('i').removeClass('top').addClass('bottom');
                    $cur_btn.removeClass('top').addClass('bottom').find('em').html('展开');
                }
                else
                {
                    $list.addClass('extend');  
                    $cur_btn.find('i').removeClass('bottom').addClass('top');                    
                    $cur_btn.removeClass('bottom').addClass('top').find('em').html('收起');                    
                    $list.find('ul').addClass('auto');
                } 
            }
        });
    },
    setup_select : function()
    {
        var self = this;

        var $select = self.$input_container.find('[data-role="search_service_type"]');
        var $text = self.$input_container.find('[data-role="search_text"]');

        $select.on('change',function()
        {
            var $cur_btn = $(this);
            var val = $cur_btn.val();

            if(val == 'seller')
            {
                self.$input_container.find('[data-role="seller-input-container"]').show();
                self.$input_container.find('[data-role="goods-input-container"]').hide();

                self.$input_container.find('[data-role="seller-input-container"]').find('input').removeAttr('disabled');
                self.$input_container.find('[data-role="goods-input-container"]').find('input').attr('disabled','disabled');
            }
            else if(val == 'goods')
            {
                self.$input_container.find('[data-role="seller-input-container"]').hide();
                self.$input_container.find('[data-role="goods-input-container"]').show();

                self.$input_container.find('[data-role="goods-input-container"]').find('input').removeAttr('disabled');
                self.$input_container.find('[data-role="seller-input-container"]').find('input').attr('disabled','disabled');
            }



        });

    },	
    setup_submit : function()
    {
        var self = this;

        var $label_place_holder = self.$input_container.find('[data-role="label-place-holder"]');
        var $text = self.$input_container.find('[data-role="search_text"]');
        var $default_url = self.$input_container.find('[data-role="default-url"]');
        var $form = self.$input_container.find('#search-form');
        var $default_text = self.$input_container.find('[data-role="default-search-text"]');

        // 先自动聚焦
        if(!$.trim($text.val()))
        {
            $text.focus();
        }


        // 监听表单提交
        $form.on('submit',function()
        {

            if($default_url.val() && !$.trim($text.val()))
            {
                window.location.href = $default_url.val();

                return false;
            }

            if(!$default_url.val())
            {
                $default_url.attr('disabled','disabled');
            }
            if(!$.trim($default_text.val()))
            {
                $default_text.attr('disabled','disabled');
            }

            if(self.$input_container.find('[data-role="seller-input-container"]').css('display') != 'none')
            {
                var $text = self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="search_text"]');


            }
            else
            {
                var $text = self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="search_text"]');
            }

            if(!$.trim($text.val()))
            {
                alert('请输入关键字');
                return false;
            }

            if(!$.trim($text.val()) && $.trim($default_text.val()))
            {
                $text.val($default_text.val());
                $form.submit();
            }
        });

        self.$input_container.find('.search-text').each(function(i,obj)
        {
            $(obj).on('click',function()
            {
                focus_place_holder();
            });

            $(obj).find('[data-role="search_text"]').on('input propertychange',function()
            {
                focus_place_holder();
            });

            $(obj).find('[data-role="search_text"]').on('blur',function()
            {
                if(self.$input_container.find('[data-role="seller-input-container"]').css('display') != 'none')
                {
                    var $text = self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="search_text"]');
                    if(!$text.val().length)
                    {
                        self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="label-place-holder"]')
                            .css('visibility','visible').show();
                    }

                }
                else
                {
                    var $text = self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="search_text"]');
                    if(!$text.val().length)
                    {
                        self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="label-place-holder"]')
                            .css('visibility','visible').show();
                    }

                }

            });
        });





        function focus_place_holder()
        {
            if(self.$input_container.find('[data-role="seller-input-container"]').css('display') != 'none')
            {
                self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="label-place-holder"]')
                    .css('visibility','hidden').hide();

                var $temp_text = self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="search_text"]');

                var s = $temp_text.val();

                $temp_text.focus();


            }
            else
            {
                self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="label-place-holder"]')
                    .css('visibility','hidden').hide();

                var $temp_text = self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="search_text"]')

                var s = $temp_text.val();

                $temp_text.focus();
            }
        }
    }

};

/* 
* url 目标url 
* arg 需要替换的参数名称 
* arg_val 替换后的参数的值 
* return url 参数替换后的url 
*/ 
function change_url_params(url,name,value)
{

    var url= url ;
    var newUrl="";
    
    var reg = new RegExp("(^|)"+ name +"=([^&]*)(|$)");
    var tmp = name + "=" + value;
    if(url.match(reg) != null)
    {
        newUrl= url.replace(eval(reg),tmp);
    }
    else
    {
        if(url.match("[\?]"))
        {
            newUrl= url + "&" + tmp;
        }
        else
        {
            newUrl= url + "?" + tmp;
        }
    }

       
    return newUrl;
}



exports.init = function(options)
{
    return new search_class(options);
}
 
});