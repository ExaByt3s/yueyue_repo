define(function(require, exports)
{
	var $ = require('zepto')
	var ua = require('ua')
	
	
	var loading_handle = $('<div></div>')
	loading_handle.html('<img src="./images/loading_2.gif"><span class="global_loading_text"></span>')
	
	loading_handle.css({
		'visibility' : 'hidden',
		'position' : 'absolute',
		'zIndex' : 999999
	})
	
	$(document.body).append(loading_handle)
	

	exports.show_loading = function(options)
	{
		var options = options || {}
		var loading_text = options.loading_text || ""
		
		loading_handle.find('.global_loading_text').html(loading_text)

		var handle_width = loading_handle.width()
		var handle_height = loading_handle.height()
		

		loading_handle.css({
			left : (ua.window_width - handle_width)/2,
			top : (ua.window_height - handle_height)/2
		}).css('visibility','visible')
	}
	

	exports.hide_loading = function()
	{
		loading_handle.css('visibility','hidden');
	}
});