define('common/widget/header/main', function(require, exports, module){ 'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/widget/header/header.scss
**/

module.exports = 
{
    render: function (dom) {
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��		

		//console.log(css);		

		console.log(Handlebars)						

		var data = 
		{
			'size' : '30px;'
		};
		
		var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<header class=\"header\" style=\"font-size:";
  if (helper = helpers.size) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.size); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <h1>����ͷ��</h1>\n</header>";
  return buffer;
  });	

        dom.innerHTML = template(data);
    }
}; 
});