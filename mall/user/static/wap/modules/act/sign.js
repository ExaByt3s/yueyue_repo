define('act/sign', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/17.
 */
"use strict";


var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var items_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <li class=\"game-part\">\n        <div class=\"item\" data-role=\"dropdown\">\n            <div class=\"title\">";
  if (helper = helpers.table_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.table_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n            <div class=\"title-text\">\n                <div class=\"text\">";
  if (helper = helpers.event_total_join) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.event_total_join); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                <i class=\"icon icon-allow-grey icon-allow-grey-bottom\"></i>\n            </div>\n        </div>\n        <div class=\"item-details fn-hide\">\n            ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.first), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.backup), {hash:{},inverse:self.noop,fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.onlook), {hash:{},inverse:self.noop,fn:self.program(17, program17, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div>\n    </li>\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                <div class=\"first list\">\n                    <div class=\"first-title title open\" data-role=\"item-dropdown\">\n                        <div class=\"title-left\">正选已支付</div>\n                        <div class=\"title-right\">\n                            "
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.first)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\n                            <i class=\"icon-allow-grey icon-allow-grey-top\"></i>\n                        </div>\n                    </div>\n                    <div class=\"first-details details\">\n                        ";
  stack1 = helpers.each.call(depth0, ((stack1 = ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.first)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_list), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    </div>\n                </div>\n            ";
  return buffer;
  }
function program3(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                            <div class=\"child\" >\n                                <img class=\"img-container\" src=\"";
  if (helper = helpers.user_icon_165) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_icon_165); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"navigate\" data-target=\"zone\" data-uid=\"";
  if (helper = helpers.user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-user-role=\"";
  if (helper = helpers.role) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.role); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"/>\n                                <div class=\"info-container\">\n                                    <div class=\"top\">\n                                        <div class=\"nickname\">";
  if (helper = helpers.nick_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.nick_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                                        ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.program(6, program6, data),fn:self.program(4, program4, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.enroll_num), "1", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.enroll_num), "1", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.mark_text), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n                                    </div>\n                                    <div class=\"btm\">\n                                        <div class=\"identity\">ID:";
  if (helper = helpers.user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.cellphone), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.cellphone), {hash:{},inverse:self.noop,fn:self.program(12, program12, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                    </div>\n                                </div>\n                            </div>\n                        ";
  return buffer;
  }
function program4(depth0,data) {
  
  
  return "\n                                        ";
  }

function program6(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                                            <div class=\"num\">(共";
  if (helper = helpers.enroll_num) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.enroll_num); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "人)</div>\n                                        ";
  return buffer;
  }

function program8(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                                            <div class=\"sign-tip\">";
  if (helper = helpers.mark_text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.mark_text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                                        ";
  return buffer;
  }

function program10(depth0,data) {
  
  
  return "\n                                            <div>手机：</div>\n                                        ";
  }

function program12(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                                            <div class=\"phone\" data-role=\"call-phone\" data-phone=\"";
  if (helper = helpers.cellphone) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.cellphone); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.cellphone) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.cellphone); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                                        ";
  return buffer;
  }

function program14(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                <div class=\"backup list\">\n                    <div class=\"backup-title title open\" data-role=\"item-dropdown\">\n                        <div class=\"title-left\">候补已支付</div>\n                        <div class=\"title-right\">\n                            "
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.backup)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\n                            <i class=\"icon-allow-grey icon-allow-grey-top\"></i>\n                        </div>\n                    </div>\n                    <div class=\"backup-details details\">\n                        ";
  stack1 = helpers.each.call(depth0, ((stack1 = ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.backup)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_list), {hash:{},inverse:self.noop,fn:self.program(15, program15, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    </div>\n                </div>\n            ";
  return buffer;
  }
function program15(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                            <div class=\"child\">\n                                <img class=\"img-container\" src=\"";
  if (helper = helpers.user_icon_165) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_icon_165); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"navigate\" data-target=\"zone\" data-uid=\"";
  if (helper = helpers.user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-user-role=\"";
  if (helper = helpers.role) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.role); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"/>\n                                <div class=\"info-container\">\n                                    <div class=\"top\">\n                                        <div class=\"nickname\">";
  if (helper = helpers.nick_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.nick_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                                        ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.program(6, program6, data),fn:self.program(4, program4, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.enroll_num), "1", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.enroll_num), "1", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.mark_text), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                    </div>\n                                    <div class=\"btm\">\n                                        <div class=\"identity\">ID:";
  if (helper = helpers.user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n                                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.cellphone), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.cellphone), {hash:{},inverse:self.noop,fn:self.program(12, program12, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                                    </div>\n                                </div>\n                            </div>\n                        ";
  return buffer;
  }

function program17(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                <div class=\"onlook list\">\n                    <div class=\"onlook-title title open\" data-role=\"item-dropdown\">\n                        <div class=\"title-left\">报名未支付</div>\n                        <div class=\"title-right\">\n                            "
    + escapeExpression(((stack1 = ((stack1 = ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.onlook)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\n                            <i class=\"icon-allow-grey icon-allow-grey-top\"></i>\n                        </div>\n                    </div>\n                    <div class=\"onlook-details details\">\n                        ";
  stack1 = helpers.each.call(depth0, ((stack1 = ((stack1 = (depth0 && depth0.enroll_arr)),stack1 == null || stack1 === false ? stack1 : stack1.onlook)),stack1 == null || stack1 === false ? stack1 : stack1.enroll_list), {hash:{},inverse:self.noop,fn:self.program(15, program15, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    </div>\n                </div>\n            ";
  return buffer;
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  return buffer;
  });
var menu = require('menu/index');
var header = require('common/widget/header/main');

if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

exports.init = function(page_data)
{
    var _self = $({});

    var sign_class = function()
    {
        var self = this;

        self.init();
    };

    sign_class.prototype =
    {
        init : function()
        {
            var self = this;

            _self.$list = $('[data-role="action-signin-list"]');
            _self.$join_btn = $('[data-role="join-btn"]');

            //右上角菜单弹出层
            var menu_data =
                    [
                        {
                            index:0,
                            content:'扫描二维码',
                            click_event:function()
                            {
                                var self = this;
                                App.qrcodescan();
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

            // 渲染头部
            header.init({
                ele : $("#global-header"), //头部渲染的节点
                title:"报名",
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
        setup_event : function()
        {
            var self = this;

            _self.$item_dropdown = $('[data-role="item-dropdown"]');
            _self.$dropdown = $('[data-role="dropdown"]');

            // 大列表容器
            _self.$dropdown.on('click',function(ev)
            {
                var $target = $(ev.currentTarget);
                $target.toggleClass('open').next().toggleClass('fn-hide');

                var icon_obj = $target.toggleClass('open').find('.icon-allow-grey');

                if(icon_obj.hasClass("icon-allow-grey-bottom"))
                {
                    icon_obj.removeClass("icon-allow-grey-bottom");
                    icon_obj.addClass("icon-allow-grey-top");

                }
                else
                {
                    icon_obj.removeClass("icon-allow-grey-top");
                    icon_obj.addClass("icon-allow-grey-bottom");
                }
            });

            // 小列表容器
            _self.$item_dropdown.on('click',function(ev)
            {

                var $target = $(ev.currentTarget);
                $target.toggleClass('open').next().toggleClass('fn-hide');

                var icon_obj = $target.toggleClass('open').find('.icon-allow-grey');

                if(icon_obj.hasClass("icon-allow-grey-bottom"))
                {
                    icon_obj.removeClass("icon-allow-grey-bottom");
                    icon_obj.addClass("icon-allow-grey-top");

                }
                else
                {
                    icon_obj.removeClass("icon-allow-grey-top");
                    icon_obj.addClass("icon-allow-grey-bottom");
                }
            });

            _self.$join_btn.on('click',function()
            {
                var event_id = $('#event_id').val();

                window.location.href = './apply.php?event_id='+event_id;
            });

        },
        refresh : function()
        {
            var self = this;

            var html_str = items_tpl({data:page_data.result_data});

            _self.$list.html(html_str);

            self.setup_event();


        }
    };

    var sign_obj = new sign_class();

    sign_obj.refresh();

}; 
});