define(function(require, exports){

	var $ = require('zepto')
	require('hammer')($)
	

	var el = $('.app_total')
	var hammer_handle = el.hammer()
	hammer_handle.on("tap", "[data-nav]" ,function()
	{
		console.log(84365467546574654)
	});
})