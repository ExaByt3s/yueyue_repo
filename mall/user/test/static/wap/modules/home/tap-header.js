define('home/tap-header', function(require, exports, module){ /**
 * Created by huanggc on 15/11/12.
 * 

 */

/**依赖文件，要在注释上使用**/

/**
 * @require modules/home/tap-header.scss
 **/
var tap_header_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                <div class=\"submenu ui-border-b\" data-role=\"btn-col\" data-id=\"";
  if (helper = helpers.id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n            ";
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                <div class=\"submenu ui-border-b\" data-role=\"btn-sort\" data-type=\"";
  if (helper = helpers.sort_by) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sort_by); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n            ";
  return buffer;
  }

  buffer += "<div class=\"ui-category-popup\">\r\n    <!--弹出框-->\r\n    <div class=\"menu top006\" data-role=\"category-m\">\r\n        <div class=\"subtag\">\r\n            ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.result_data)),stack1 == null || stack1 === false ? stack1 : stack1.type_data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n        </div>\r\n        <div class=\"bar\"></div>\r\n    </div>\r\n    <div class=\"menu top006\" data-role=\"sort\" style=\"height:138px;\">\r\n        <div class=\"subtag\">\r\n            ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.result_data)),stack1 == null || stack1 === false ? stack1 : stack1.sort_data), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n        </div>\r\n        <div class=\"bar\"></div>\r\n    </div>\r\n</div>";
  return buffer;
  });

var tap_header_class = function(options)
{
	var self = this;
	self.options = options || {};
	self.init(options);
}

tap_header_class.prototype = 
{
	init :function(options)
	{
		var self = this;
		self.options = options || {};
		self.$el = options.ele || {};
		self.data = options.data || "";

		console.log(self.data);
		self.render(self.data);
		self.setup_event();
	},
	render : function (data)
	{
		var self = this;

		var html_str = tap_header_tpl(data);
		self.$el.html(html_str);
	},
	setup_event : function()
	{
		var self = this;

		//品类选项弹出选项框
	    var triangle = $('[data-role="tag"]');
	    var $category = $('[data-role="category"]');
	    var mune = $('[data-role="category-m"]');
	    var bg_opacity = $('[data-role="bg"]');
	    $category.on('click',function(ev)
	    {
	        ev.stopPropagation();
	        if(mune.hasClass("top006")){
	            mune.removeClass("top006");
	            mune.addClass("top46");
	            triangle.addClass("tag-trfm");
	            $(this).addClass("ui-tips-success");
	            sort.removeClass("top46");
	            sort.addClass("top006");
	            triangle_sort.removeClass("tag-trfm");
	            $sort_btn.removeClass("ui-tips-success");
	        }else
	        {
	            mune.removeClass("top46");
	            mune.addClass("top006");
	            triangle.removeClass("tag-trfm");
	            $(this).removeClass("ui-tips-success");
	        }
	    });

	    //排序选项点击弹出选项框
	    var $sort_btn = $('[data-role="sort-btn"]');
	    var sort = $('[data-role="sort"]');
	    var triangle_sort = $('[data-role="sort-tag"]');
	    var collection_list = $('[data-role="collection-list"]');
	    $sort_btn.on('click',function(ev)
	    {
	        ev.stopPropagation();
	        if(sort.hasClass("top006")){
	            sort.removeClass("top006");
	            sort.addClass("top46");
	            triangle_sort.addClass("tag-trfm");
	            $(this).addClass("ui-tips-success");
	            mune.removeClass("top46");
	            mune.addClass("top006");
	            triangle.removeClass("tag-trfm");
	            $category.removeClass("ui-tips-success");
	        }else
	        {
	            sort.removeClass("top46");
	            sort.addClass("top006");
	            triangle_sort.removeClass("tag-trfm");
	            $(this).removeClass("ui-tips-success");
	        }
	    });
	    //点击页面收起选项框
	    $(document).on('click',function()
	    {
	        mune.removeClass("top46");
	        mune.addClass("top006");
	        triangle.removeClass("tag-trfm");
	        $category.removeClass("ui-tips-success");
	        sort.removeClass("top46");
	        sort.addClass("top006");
	        triangle_sort.removeClass("tag-trfm");
	        $sort_btn.removeClass("ui-tips-success");
	    });

	}
}

exports.init = function(options)
{
	return new tap_header_class(options);
} 
});