define(function(require, exports)
{
	var header_height = 100					//ͷ���߶�
	var status_bar_height = 120				//iphone safari ״̬���߶�

	var ua = require('ua')
	
	var wo_config = {}
	//wo_config.container_height = ua.window_height - header_height + status_bar_height

	wo_config.container_height = ua.window_height - header_height
	
	return wo_config;
})