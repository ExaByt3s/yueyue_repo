/**
  *	 view滚动控制
  *	 针对支持overflow:scroll和低版本系统分别处理
  *	 @author Manson
  *  @version 2013.2.16
  */
define(function(require, exports)
{
	var $ = require('zepto')

	var options = {
		route : { "my": "my" },
		id : 'index',
		transition_type : 'none'
	};

	options.initialize = function()
	{
		//loading.show_loading();

		this.render();
	}

	options.render = function()
	{
		var init_html = '<div style="height:100%;background:#99cccc">我的页</div>';

		//var init_html = require('/photowall/m/template/index.tpl.html');

		this.$el.append($(init_html))
	}
	
	options.events = {
		
		//加载更多
		'tap .load_more_btn' : function()
		{
			photowall_item_list_collection.get_more_item()
		},
		'tap .refresh_btn' : function()
		{
			photowall_item_list_collection.refresh()
		},
		'tap .sign_up_btn' : function(ev)
		{
			page_control.navigate_to_page("last");		//直奔首页

			ev.stopPropagation();
		}
	};
	
	//页面显示时
	options.page_show = function(page_view)
	{
		
	}
	
	var view_scroll_obj

	//页面初始化时
	options.page_init = function(page_view)
	{
		
	}


	var page = require('page').new_page(options);
	
	return page;
});