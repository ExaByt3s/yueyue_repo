define(function(require, exports)
{
	var $ = require('zepto')
	var page_control = require('page_control');
	var Mustache = require('mustache')
	var ua = require('ua')


	var options = {
		route : { "last": "last" },
		id : 'last',
		transition_type : 'slide'

	};

	options.initialize = function()
	{
		this.render();
	}

	options.render = function()
	{
		var init_html = '<div style="margin-bottom:5px;width:100%;height:85px; position:relative; overflow: hidden; background: -webkit-gradient(linear,0 0,0 100%,from(#FDFDFD),to(#EAEAEA)); box-shadow:0 2px 2px rgba(155,155,155,0.5)" class="clearfix"><div class="back_btn" style="float:left;"><img src="./images/back.png" style="height:48px;"></div></div><article class="main_wraper"><div style="background:red; height:1000px;"></div></article>';

		

		this.$el.append($(init_html))
	}

	options.events = {
		'tap .back_btn' : function()
		{
			page_control.back(true);
		}
		
	};


	//页面显示时
	options.page_show = function(page_view)
	{
		
	}

	//页面初始化时
	options.page_init = function(page_view)
	{
		var main_container = $(page_view.el).find('article');
		main_container.css({
			height : ua.window_height - 90
		})
	}

	var page = require('page').new_page(options);
	
	return page;
});