define('common/widget/back_btn/main', function(require, exports, module){ 'use strict';

/**依赖文件，要在注释上使用**/

/**
 * 
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

		var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<a class=\"left-button-wrap\"  data-role=\"page-back\">\n	<i class=\"icon icon-back\"></i>\n</a>\n";
  });	

        return template();
    }
}; 
});