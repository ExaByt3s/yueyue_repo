/**
  *	 美图页
  *	 @author Manson
  *  @version 2013.5.2
  */
define("wo/index",["base_package","page_control","page","hammer"],function(require, exports)
{
	var $ = require('zepto')
	var page_control = require('page_control')
	var new_photowall_item_view = require('photowall_item_view')
	var common_function = require('commom_function')
	var wo_config = require('wo_config')
	var notice = require('notice')
    
    var user_authorize = require('user_authorize')
	
	var page_count = 20

	exports.route = { "index": "index"  }

	exports.new_page_entity = function()
	{
		var options = {
			
			transition_type : 'none'
		}

		options.initialize = function()
		{
			this.render()
		}

		var footer_view_obj
		options.render = function()
		{  
			
			
			var init_html = '<div><img class="img1" src="http://image142-c.poco.cn/mypoco/qing/20130725/12/8611224108093515116_480x640_320_600.jpg"></div><br><br><br><div><img src="http://image142-c.poco.cn/mypoco/qing/20130725/12/8611224108093515116_480x640_320_600.jpg" class="img2"></div><br><br><br><div><img src="http://image142-c.poco.cn/mypoco/qing/20130725/12/8611224108093515116_480x640_320_600.jpg" class="img3"></div><br><br><br><div><img src="http://image142-c.poco.cn/mypoco/qing/20130725/12/8611224108093515116_480x640_320_600.jpg" class="img4"></div><br><br><br>';  
			
			this.$el.append($(init_html))
		   
		}
		
		
		options.events = {
			
			'click .img1' : function()
			{
				alert('click')
			},
			'tap .img2' : function()
			{
				alert('tap')
			}
		}
		
		

		//页面初始化时
		options.page_init = function(page_view)
		{		
			//add by manson 2013.5.19底层事件绑定调整
			require('hammer')($)
			$(document.body).hammer()
		}


		var page = require('page').new_page(options);
		
		return page;
	}	
})