/**
  *	 view滚动控制
  *	 针对支持overflow:scroll和低版本系统分别处理
  *	 @author Manson
  *  @version 2013.2.16
  */
define(function(require, exports)
{
	var $ = require('zepto')
	var ua = require('ua')
	var page_control = require('page_control')

	var options = {
		route : { "last/:query": "last" },
		transition_type : 'slide',
		dom_not_cache : true
	};

	options.initialize = function()
	{
		//loading.show_loading();

		this.render();
	}

	options.render = function()
	{
		console.log(222222222222222)
	}
	
	options.events = {
		
		//加载更多
		'tap .back_btn' : function(ev)
		{
			page_control.back(true)

			console.log('history_back')

			ev.stopPropagation();
		}
	};
	
	//页面显示时
	options.page_show = function(page_view)
	{
		
	}
	
	var view_scroll_obj

	//页面初始化时
	options.page_init = function(page_view,params_arr)
	{
		var img_src = decodeURIComponent(params_arr[0])


		var init_html = '<div style="height:50px;background: #511F56;color:white;line-height:50px;padding-left:30px;" class="back_btn">返回</div><section class="main_wraper" style="height:100%;background: #F5F5F5"><div style="padding:20px"><div ><img src="'+img_src+'"></div></section>';

		$(page_view.el).append($(init_html))


		var main_container = $(page_view.el).find('.main_wraper')

		var view_scroll = require('scroll')
		view_scroll_obj = view_scroll.new_scroll(main_container,{
			'view_height' : ua.window_height - 150
		})

		
		
		
	}


	var page = require('page').new_page(options);
	
	return page;
});