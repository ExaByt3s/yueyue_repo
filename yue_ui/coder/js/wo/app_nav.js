define(function(require, exports){

	var $ = require('zepto')
	var Backbone = require('backbone')
	var page_control = require('page_control')

	
	var page_view_class = Backbone.View.extend({
	
		el : $('.wo_nav'),
		initialize : function()
		{
			require('hammer')($)
			

			//导航点击事件绑定
			var hammer_handle = this.$el.hammer()
			hammer_handle.on("tap", "[data-nav]" ,function()
			{
				var nav_to_page = $(this).attr('data-nav')

				page_control.navigate_to_page(nav_to_page)
			});

		}
	})

	new page_view_class
})