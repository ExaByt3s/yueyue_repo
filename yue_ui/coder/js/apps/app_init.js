/**
  *	 应用逻辑控制层
  *  
  *  原则：
  *  1.在本页控制每个页面显隐时候应该调用的模块接口
  *  2.在各自的页面模块进行交互、数据等操作，然后暴露接口方便调用
  *  
  */
define(function(require, exports){

	var $ = require('zepto');
	var ua = require('ua');
	

	//内部内容高度
	var page_container_obj = $('.page_container');

	page_container_obj.css({
		height : ua.window_height - 100
	})
	
	var page_control = require('page_control');
	page_control.init(page_container_obj);
	
	var index_page_controler = require('apps/index');
	var last_page_controler = require('apps/last');

	page_control.add_page(index_page_controler);
	page_control.add_page(last_page_controler);
	

	page_control.route_start()
	

	if(window.location.hash=="" || window.location.hash=="#")
	{
		page_control.navigate_to_page("index");		//直奔首页

	}

});