define('pay_tt/pay_tt_success', function(require, exports, module){ /**
 *                   
   hudw 2015.4.15
**/
"use strict";

var back_btn = require('common/widget/back_btn/main');

var $ = require('components/zepto/zepto.js');
var App =  require('common/I_APP/I_APP');
var fastclick = require('components/fastclick/fastclick.js');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{
	var $header = $('#nav-header');	
	var $confirm_btn = $('#confirm-btn');

	var $back_btn_html = back_btn.render();
	$header.prepend($back_btn_html);

	$confirm_btn.on('click',function()
	{
		if(App.isPaiApp)
		{
            App.openttpayfinish();
			App.close_webview();
		}
		
	});

	$('[data-role="page-back"]').on('click',function()
	{
		if(App.isPaiApp)
		{
            App.openttpayfinish();
			App.app_back();
		} 
	});

})($,window);



 
});