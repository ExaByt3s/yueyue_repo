/**
 * Created by huanggc on 15/11/12.
 * 

 */

/**依赖文件，要在注释上使用**/

/**
 * @require ./tap-header.scss
 **/
var tap_header_tpl = __inline("./tap-header.tmpl");

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