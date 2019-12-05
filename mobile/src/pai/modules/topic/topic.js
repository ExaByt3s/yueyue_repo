"use strict";

var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{


	var _self = {};

	// Ĭ�ϲ���
	var defaults=
	{
		title : 'ר��'
	};
		
	// ��ʼ��dom
	_self.$page_container = $('[data-role="page-container"]');		
	_self.$info_container = $('[data-role="info-container"]');
				
	// ���캯��
	var topic_class = function()
	{
		var self = this;

		 _self.topic_id = $('#topic_id').val();

		self.init();
						
	};
   
	// ��ӷ���������
	topic_class.prototype = 
	{
		refresh : function()
		{
			var self = this;

			var $loading=$.loading
			({
				content:'������...',
			})			

			utility.ajax_request
			({
				url : window.$__config.ajax_url.topic,
				data : {
					id : _self.topic_id
				},						
				success : function(res)
				{
					var data = res.result_data.data;	

					_self.$info_container.html('');
					
					_self.$info_container.html(data.content);

					// ��װ�¼�
					self.setup_event();

					$loading.loading("hide");

					//document.title = data.title;
				},
				error : function()
				{
					$loading.loading("hide");

					$.tips
					({
						content : '�����쳣',
						stayTime:3000,
				        type:"warn"
					});
				}
			});
		},
		page_back : function()
		{

		},
		// ��ʼ��					
		init : function()
		{
			var self = this;
		},
		hide : function()
		{

		},		
		// ��װ�¼�		
		setup_event : function()
		{
			var self = this;

			
		}
		
	};

	// ʵ����tt֧����			
	var topic_obj = new topic_class();		
	
	topic_obj.refresh();

})($,window);
