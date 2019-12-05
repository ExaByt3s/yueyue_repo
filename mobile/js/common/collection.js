/**
 *  封装collection ，预留以后进行扩展
 *  hdw 2014.8.14
 */
define(function(require, exports, module)
{
	var $ = require('$');
	var Backbone = require('backbone');

	var Collection = Backbone.Collection.extend({});

	module.exports = Collection;
});