define('common/widget/header/main', function(require, exports, module){ 'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require modules/common/widget/header/header.scss
**/

module.exports = 
{
    render: function (dom) {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择		

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
    + "\">\n    <h1>这是头部</h1>\n</header>";
  return buffer;
  });	

        dom.innerHTML = template(data);
    }
}; 
});