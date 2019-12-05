define('common/widget/footer/footer', function(require, exports, module){ /**
 * Created by ��Բ on 2015/9/22
 */
'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/widget/footer/footer.scss
 **/
 var $ = require('components/zepto/zepto.js');


function footer(options)
{
    var self = this;
    self.options = options || {};
    self.$render_ele = options.ele || {};
    self.content = options.content || {};
    self.init();
}


footer.prototype =
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


  buffer += "<footer class=\"footer-v2\">\n    <ul class=\"list clearfix f14 \">\n        <a href=\"";
  if (helper = helpers.index_link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.index_link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><li ><i class=\"icon icon-index\"></i>��ҳ</li></a>\n        <a href=\"";
  if (helper = helpers.my_link) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.my_link); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><li ><i class=\"icon icon-my\"></i>�ҵ�</li></a>\n    </ul>        \n</footer>";
  return buffer;
  });
        self.view = self.$render_ele.html(template(self.content));

        self.view.find('li').eq(self.content.current).addClass('cur');

      

    },


    setup_event : function() 
    {
        var self = this;

    }


};

return footer;



 
});