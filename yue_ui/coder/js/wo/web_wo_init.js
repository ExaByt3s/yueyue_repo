/**
  *	 应用逻辑控制层
  *  
  *  原则：
  *  1.在本页控制每个页面显隐时候应该调用的模块接口
  *  2.在各自的页面模块进行交互、数据等操作，然后暴露接口方便调用
  *  
  */
define('m_poco_wo/web_wo_init',['base_package','page_control','page','scroll','ua','index'],function(require, exports){
    
	var $ = require('zepto')
	var Backbone = require('backbone')   
    var wo_config = require('wo_config') 
	var ua = require('ua')
	

	var page_container_obj = $('.page_container');
	

	var page_control = require('page_control');
	page_control.init(page_container_obj,{
		default_index_route : "index"
	})


	var index_page_controler = require('index')
	
	page_control.add_page(index_page_controler)
	
	page_control.route_start()
})