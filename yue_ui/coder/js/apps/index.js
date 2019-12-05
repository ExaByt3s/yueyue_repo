/**
  *	 view滚动控制
  *	 针对支持overflow:scroll和低版本系统分别处理
  *	 @author Manson
  *  @version 2013.2.16
  */
define(function(require, exports)
{
	var $ = require('zepto')
	var page_control = require('page_control')
	var loading = require('loading')
	var Backbone = require('backbone')
	var Mustache = require('mustache')
	var ua = require('ua')
	
	
	var page_view_obj = {};

	//数据model
	var photowall_item_model = Backbone.Model.extend({
		defaults:
		{
			
			big_img_url : "",
			small_img_url : "",
			des : "",
			add_time : ""
		}
	})

	//数据集合
	var photowall_item_list_class = Backbone.Collection.extend({
		index_page : 1,
		onload : false,
		model : photowall_item_model,
		url : "http://www.mansonchor.com/photowall/m/action/get_photowall_item_list.php",
		refresh : function()
		{
			loading.show_loading();
			
			this.index_page = 1;

			this.fetch
			({
				type: "GET",  
				data: {page : 1 , t : parseInt(new Date().getTime()) },
				success : function(model,response)
				{
					console.log(response);  
				},  
				error:function(err)
				{  
					console.log("err");  
				}  
			});
		},
		get_more_item : function()
		{
			var that = this

			if(that.onload)
			{
				return false;
			}

			that.onload = true

			get_more_state()

			/*$.ajax({
				type: 'GET',
				url: this.url ,
				data: {page : this.index_page + 1 , t : parseInt(new Date().getTime()) },
				dataType: 'json',
				success: function(data)
				{
					that.onload = false
					finish_more_state()

					photowall_item_list_collection.add(data)

					that.index_page++
				},
				error: function(xhr, type)
				{
					that.onload = false
					finish_more_state()
				}
			})*/

			this.fetch
			({
				update: true,
				type: "GET",  
				data: {page : that.index_page + 1 , t : parseInt(new Date().getTime()) },
				success : function(model,response)
				{
					that.onload = false
					finish_more_state()

					that.index_page++
				},  
				error:function(err)
				{  
					that.onload = false
					finish_more_state()
				}  
			});
		}
	});
	
	var photowall_item_list_collection = new photowall_item_list_class

	//列表项view
	var item_view_class = Backbone.View.extend({

		tagName :  "div",
		className : "item",
		render : function() 
		{
			//var template = require('/photowall/m/template/index_item_list.tpl.html');

			var template = '<div style="border-bottom : 1px solid #cccccc;margin-bottom:10px;padding-bottom:15px;"><div class="clearfix" style="margin: 20px 0;"><div style="float:left; border-radius: 10px;width: 100px;height: 100px; background:url(http://www.mansonchor.com/images/manson.jpg);background-size:100% 100%;border:1px solid #cccccc;"></div><div style="font-size:26px;padding-left:115px;">{{{des}}}</div></div><div class="clearfix"><div style="float:left;padding:10px 10px 8px 10px;background:white;border:1px solid #cccccc;box-shadow: 1px 1px 3px #666;"><img src="{{big_img_url}}"></div></div></div>'

			var html = Mustache.to_html(template, this.model.toJSON() );
			
			$(this.el).html(html);
			
			return this;
		}
	});


	var options = {
		route : { "index": "index" },
		id : 'index',
		transition_type : 'none'
	};

	options.initialize = function()
	{
		//loading.show_loading();

		this.render();
	}

	options.render = function()
	{
		require('../../template/index.css')

		var init_html = '<div style="margin-bottom:5px;width:100%;height:85px; position:relative; overflow: hidden; background: -webkit-gradient(linear,0 0,0 100%,from(#FDFDFD),to(#EAEAEA)); box-shadow:0 2px 2px rgba(155,155,155,0.5)" class="clearfix"><div class="sign_up_btn" style="float:left;"><img src="./images/sign_up.png" style="height:48px;"></div><div  class="refresh_btn" style="float:right;"><img src="./images/refresh.png" style="height:48px;"></div></div><article class="main_wraper"><div><section class="main_container"></section><footer class="load_more_btn" style="display:none;"><span>加载更多</span><img src="./images/big_loading.gif" style="vertical-align:-5px;visibility:hidden;"></footer></div></article>';

		//var init_html = require('/photowall/m/template/index.tpl.html');

		this.$el.append($(init_html))
	}
	
	options.events = {
		
		//加载更多
		'tap .load_more_btn' : function()
		{
			photowall_item_list_collection.get_more_item()
		},
		'tap .refresh_btn' : function()
		{
			photowall_item_list_collection.refresh()
		},
		'tap .sign_up_btn' : function(ev)
		{
			page_control.navigate_to_page("last");		//直奔首页

			ev.stopPropagation();
		}
	};
	
	//页面显示时
	options.page_show = function(page_view)
	{
		
	}
	
	var view_scroll_obj

	//页面初始化时
	options.page_init = function(page_view)
	{
		/*var main_container = $(page_view.el).find('.main_wraper');
		main_container.css({
			height : ua.window_height - 90
		})*/
		
		var main_container = $(page_view.el).find('.main_wraper')

		var view_scroll = require('scroll')
		view_scroll_obj = view_scroll.new_scroll(main_container,{
			'view_height' : ua.window_height - 190
		})


		page_view_obj = page_view

		photowall_item_list_collection.bind('add', add_render_list , page_view)

		photowall_item_list_collection.bind('reset', re_render_list , page_view)
		
		setTimeout(function(){
			photowall_item_list_collection.refresh()
		},300)
		
	}

	function add_render_list(item_model)
	{
		var item_view = new item_view_class({ model : item_model});
		var that = this;
		var main_container = $(that.el).find('.main_container');

		main_container.append(item_view.render().el);
	}
	
	//刷新列表操作
	function re_render_list()
	{
		loading.hide_loading();

		var that = this;
		
		//加载更多按钮
		var load_more_btn = $(that.el).find('.load_more_btn')
		load_more_btn.show();
		
		//重新滚回顶部
		view_scroll_obj.scroll_to(0)
		

		var main_container = $(that.el).find('.main_container')
		
		main_container.html('');

		photowall_item_list_collection.each(function(item_model)
		{
			var item_view = new item_view_class({ model : item_model});
			
			main_container.append(item_view.render().el);
		})
	}

	function get_more_state()
	{
		page_view_obj.$el.find('.load_more_btn img').css('visibility','visible');
		page_view_obj.$el.find('.load_more_btn span').html('加载中');
	}

	function finish_more_state()
	{
		page_view_obj.$el.find('.load_more_btn img').css('visibility','hidden');
		page_view_obj.$el.find('.load_more_btn span').html('加载更多');
	}


	var page = require('page').new_page(options);
	
	return page;
});