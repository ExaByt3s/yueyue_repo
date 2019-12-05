define('common/go_top/qr_code_preview', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

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
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��



        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function";


  buffer += "\n<div data-role=\"qr_code_preview\"  class=\"qr_code_preview font_wryh\">     \n    <div class=\"phone \" id=\"phone\">\n        <div class=\"img\"></div>\n        <p class=\"p1 cor-409 pt10\">���Ԥ��</p>\n    </div>\n\n    <div class=\"qr fn-hide\" id=\"qr\">\n        <div class=\"img\"> <img src=\"";
  if (helper = helpers.url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" /></div>\n        <p class=\"p1  cor-333\">\n            ɨһɨ <br />\n            ���ֻ���Ԥ���༭Ч��\n        </p>\n    </div>\n</div>";
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
        // ����ƫ��λ��
        var content_left = ( $('.content').offset().left + $('.content').width() - 90 );
        self.$render_ele.css({
            left: content_left
        });
    }

};

return qr_code_preview;



 
});