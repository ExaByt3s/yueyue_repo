define('common/personal_made/personal_made', function(require, exports, module){ /**
 * Created by ��Բ on 2015/9/22
 */
'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/personal_made/personal_made.scss
 **/
 var $ = require('components/zepto/zepto.js');


function personal_made(options)
{
    var self = this;
    self.options = options || {};
    self.$render_ele = options.ele || {};
    self.content = options.content || {};
    self.init();

}


personal_made.prototype =
{
    init : function()
    {
        var self = this;


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
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<a href=\"";
  if (helper = helpers.link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <div class=\"personal-made-mod color-333\">\n        <div class=\"title f18 tc\">";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        <div class=\"wbox clearfix\" >\n            <div class=\"icon-dz fldi\" style=\"background-image: url(";
  if (helper = helpers.ico) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ico); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + ") ;\"></div>\n            <div class=\"rbox fldi\">";
  if (helper = helpers.desc) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.desc); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        </div>\n    </div>\n</a>";
  return buffer;
  });
        self.view = self.$render_ele.html(template(self.content));
    },


    setup_event : function() 
    {
        var self = this;

    }


};

return personal_made;



 
});