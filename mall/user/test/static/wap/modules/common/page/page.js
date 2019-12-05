define('common/page/page', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/25.
 * 页面基类
 */

// ========= 模块引入 ========= 
var YB = require('base/index');
var $ = require('components/zepto/zepto.js');

var yue_page = function(options)
{
	var self = this;

	options = options || {};

	self.options = options;

	self.$el = self.options.$el || $('body');

	self.init();
};

yue_page.prototype = 
{
	init : function()
	{
		var self = this;
	},
	render : function()
	{
		var self = this;
	},
	events : 
	{

	},
	show : function()
	{
		var self = this;
	},
	hide : function()
	{
		var self = this;
	}


};

module.exports = yue_page; 
});