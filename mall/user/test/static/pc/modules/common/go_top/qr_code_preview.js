define('common/go_top/qr_code_preview', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require modules/common/go_top/qr_code_preview.scss
 **/
var $ = require('components/jquery/jquery.js');



function qr_code_preview(options)
{
    var self = this;
    var options = options || {};
    self.$render_ele = options.render_ele;
    self.url = options.url ;
}


qr_code_preview.prototype =
{
    init : function()
    {
        var self = this;


        self.ele_position()
        $(window).resize(function() {
            self.ele_position()
        });

        self.render();
        self.setup_event();
    },


    render: function () 
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择



        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function";


  buffer += "\n<div data-role=\"qr_code_preview\"  class=\"qr_code_preview font_wryh\">     \n    <div class=\"phone \" id=\"phone\">\n        <div class=\"img\"></div>\n        <p class=\"p1 cor-409 pt10\">点击预览</p>\n    </div>\n\n    <div class=\"qr fn-hide\" id=\"qr\">\n        <div class=\"img\"> <img src=\"";
  if (helper = helpers.url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" /></div>\n        <p class=\"p1  cor-333\">\n            扫一扫 <br />\n            在手机上预览编辑效果\n        </p>\n    </div>\n</div>";
  return buffer;
  });
        var view = self.$render_ele.html(template({
            url : self.url
        }));
        self.phone = $(view).find('#phone');
        self.qr = $(view).find('#qr');

    },

    setup_event : function() 
    {
        var self = this;
        self.$render_ele.hover(function() {
            self.phone.addClass('fn-hide');
            self.qr.removeClass('fn-hide');
        }, function() {
            self.phone.removeClass('fn-hide');
            self.qr.addClass('fn-hide');
        });


    },

    ele_position : function() 
    {
        var self = this;
        // 处理偏移位置
        var content_left = ( $('.content').offset().left + $('.content').width() - 90 );
        self.$render_ele.css({
            left: content_left
        });
    }

};

return qr_code_preview;



 
});