
/**
 * Created by hgc on 15/11/6.
 */


/**依赖文件，要在注释上使用**/

/**
 * @require ./search_popup.scss
 **/
var $ = require('zepto');
var template  = __inline('./search_popup.tmpl');
var idx = 0;
function search_popup(options)
{
	var self = this;
    var options = options || {} ;

    self.init(options);

}

search_popup.prototype = 
{
	init : function(options)
	{
		var self = this;

		self.options = options;

		self.popup_type = self.options.popup_type || 'filter';
		self.page_params = self.options.page_params;

		var data = self.options.data || [];

		data.popup_type = self.popup_type;

		self.render(data);

		self.setup_event();
	},
	render : function(data)
	{
		var self = this;
		var html = template(data);		

		$('body').append('<div data-role="ui-search-popup-wrapper-'+idx+'"></div>');

		self.$el = $('[data-role="ui-search-popup-wrapper-'+idx+'"]');

		self.$el.html(html);

		idx++;
	},
	setup_event : function()
	{
		var self = this;
		
	    //弹层容器
	    self.$popup = self.$el.find('[data-role="popup"]');
	    //遮罩容器 此处遮罩应该为全局
	    self.$fade = $('[data-role="fade"]');
	    //返回按钮
	    self.$option_back = self.$el.find('[data-role="option-back"]');
	    //确定按钮
	    self.$confirm = self.$el.find('[data-role="page-confirm"]');

	    //返回按钮点击事件
	    self.$option_back.on('click',function()
	    {
	    	self.toggle();
	    });
	    //确定按钮点击事件
	    self.$confirm.on('click',function()
	    {
	    	self.go_to_search();

	    });

	    //按钮选中状态切换
	    self.$type_btn = self.$el.find('[data-role="search-type-btn"]');
	    //第一级条件名 
	    self.$name_a = self.$el.find('[data-role="a-name-a"]');
	    //第二级条件名
	    self.$name_b = self.$el.find('[data-role="a-name-b"]');
	    //第一级条件按钮
	    self.$btn_a = self.$el.find('[data-role="a-btn-a"]');
	    //第二级条件按钮
	    self.$btn_b = self.$el.find('[data-role="a-btn-b"]');

	    //选项栏伸缩
	    self.$item_a = self.$el.find('[data-role="item-a"]');
	    //所有二级目录内容
	    self.$item_b = self.$el.find('[data-role="item-b"]');
	    self.$filter_a = self.$el.find('[data-role="filter-a"]');
	    //所有一级目录内容
	    self.$filter_b = self.$el.find('[data-role="filter-b"]');
	    //一级目录点击事件
	    self.$filter_a.on('click',function(ev)
	    {
	    	var $this = $(this);

	    	if(self.popup_type == 'sort')
	    	{
	    		self.go_to_search();
	    		return;
	    	}

	    	//当前二级目录内容
	    	var $cur_filter = $this.find('[data-role="filter-b"]');
	    	
	    	if($cur_filter.hasClass('fn-hide'))
	    	{
	    		//如果隐藏，则显示内容
	    		$cur_filter.removeClass('fn-hide');
	    		
	    	}else
	    	{
	    		//如果显示，则隐藏内容
	    		$cur_filter.addClass('fn-hide');
	    	}
	    	//点击自己
	    	self.$filter_b.not($cur_filter).addClass('fn-hide')
	    })
	    //二级目录点击事件
	    self.$item_a.on('click',function(ev)
	    {
	    	ev.stopPropagation();

	    	var $this = $(this);

	    	var $cur_item = $this.find('[data-role="item-b"]');
	    	var val = $this.attr('data-val');
	    	var key = $this.attr('data-key');
	    	var name = $this.attr('data-name');

	    	self.$item_a.removeClass('colorff6a6e');
	    	$this.addClass('colorff6a6e');	    	

	    	if($cur_item.hasClass('fn-hide'))
	    	{
	    		$cur_item.removeClass('fn-hide');
	    	}else
	    	{
	    		$cur_item.addClass('fn-hide');
	    	}

	    	self.$item_b.not($cur_item).addClass('fn-hide');

	    	var $obj = $this.parents('[data-role="filter-a"]').find('[data-role="select-content"]');

	    	// 如果没有三级目录就直接选中
	    	if($cur_item.length == 0)
	    	{
	    		$obj.html(val)
	    		.attr
	    		({
	    			'data-selected-name' : name,
	    			'data-selected-key' : key,
	    			'data-third-selected-name' : '',
	    			'data-third-selected-key' : '',
	    		}); 
	    	}
	    	else
	    	{
	    		$obj.attr
	    		({
	    			'data-selected-name' : name,
	    			'data-selected-key' : key,
	    			'data-third-selected-name' : '',
	    			'data-third-selected-key' : '',
	    		});
	    	}

	    	
	    	
	    });

	    // 第三级目录点击事件
	    self.$type_btn.on('click',function(ev)
    	{
    		ev.stopPropagation();
    		var $this = $(this);

            self.$type_btn.removeClass('on-btn');

            $this.addClass('on-btn');

            $this.find(self.$name_a).html(self.$btn_a.text());
            $this.find(self.$name_b).html($this.text());

            var key = $this.attr('data-key');
            var val = $this.attr('data-val');
            var parent_name = $this.parent().attr('data-name');
            var parent_val = $this.attr('data-parent-val');
            var $obj = $this.parents('[data-role="filter-a"]').find('[data-role="select-content"]');
            
            // 保存筛选条件
            var params = {};
	    	params[parent_name] = key;

    		// 保存筛选条件
    		self.save_params(params,true);
	    	$obj.html(parent_val + '/' + val).attr
	    	({
	    		'data-third-selected-name' : parent_name,
	    		'data-third-selected-key' : key,
	    	});

    	});

	    // 排序点击
    	self.$orderby = self.$el.find('[data-role="orderby"]');
    	self.$orderby.on('click',function()
    	{
    		var $cur_btn = $(this);
    		var orderby = $cur_btn.attr('data-val');

    		self.$orderby.removeClass('colorff6a6e');
    		$cur_btn.addClass('colorff6a6e');

    		for(var i = 0;i<self.options.data.sort_data.length;i++)
    		{
    			if(orderby == self.options.data.sort_data[i].orderby)
    			{
    				self.options.data.sort_data[i].selected = true;
    			}
    			else
    			{
    				self.options.data.sort_data[i].selected = false;
    			}
    		}
    	});
	},
	/**
	 * 保存筛选与排序参数
	 * @param  {[type]} data   [description]
	 * @param  {[type]} extend [description]
	 * @return {[type]}        [description]
	 */
	save_params : function(data,extend)
	{
		var self = this;

		return false;
	},
	get : function(key)
	{
		var self = this;

		return self.options[key] || false;
	},
	set : function(key,value,extend)
    {
        var self = this;

        extend = extend || false;

        if(extend)
        {
        	var obj = {};
        	obj[key] = value;
        	self.options = $.extend(self.options,obj);
        }
        else
        {

        	self.options[key] = value;
        }

        
    },
	toggle : function()
	{
		var self = this;

		if(self.$popup.hasClass('popup-hide'))
        {
        	self.show();    
        }
        else
        {
            self.hide();
        }
	},
	/**
	 * 显示弹层
	 * @return {[type]} [description]
	 */
	show : function()
	{
		var self = this;

		$('html,body').css('overflow','hidden');

		self.$popup.removeClass('popup-hide').addClass('popup-show');
        self.$fade.removeClass('fn-hide');
        self.$fade.removeClass('anim_fade');

        self.set('is_show',true);

	},
	/**
	 * 隐藏弹层
	 * @return {[type]} [description]
	 */
	hide : function()
	{
		var self = this;

		$('html,body').css('overflow','auto');

		self.$popup.addClass('popup-hide').removeClass('popup-show');
        self.$fade.addClass('fn-hide');
		self.$fade.addClass('anim_fade');

		self.set('is_show',false);
	},
	/**
	 * 跳转到搜索
	 * @return {[type]}
	 */
	go_to_search : function()
	{
		var self = this;

		self.toggle();

    	var $sc = $('body').find('[data-role="select-content"]');

    	// 筛选参数
    	var params = {};

    	$sc.each(function(i,obj)
    	{
    		var name = $(obj).attr('data-selected-name');
    		var key = $(obj).attr('data-selected-key');
    		var third_name = $(obj).attr('data-third-selected-name');
    		var third_key = $(obj).attr('data-third-selected-key');

    		if(name && key)
    		{
    			params[name] = key;
    		}
    		if(third_name && third_key)
    		{
    			params[third_name] = third_key;
    		} 
    		
    		
    	});

    	var search = parseQueryString(window.location.search);
    	var keywords = search['keywords'];
    	var search_type = search['search_type'];
    	var type_id = search['type_id'];

    	// 排序参数
    	var orderby = '-1';
    	for(var i = 0;i<self.options.data.sort_data.length;i++)
		{
			if(self.options.data.sort_data[i].selected)
			{
				orderby = self.options.data.sort_data[i].orderby;
				break;
			}
		} 

		params['orderby'] = orderby;

    	params = $.param(params);

    	params = decodeURIComponent(params);

    	
    	console.log(params);

    	window.location.search = '?keywords='+keywords+'&search_type='+search_type+'&type_id='+type_id+'&' + params;
	}
}

function parseQueryString(url)
{
	var obj={};
	var keyvalue=[];
	var key="",value=""; 
	var paraString=url.substring(url.indexOf("?")+1,url.length).split("&");
	for(var i in paraString)
	{
		keyvalue=paraString[i].split("=");
		key=keyvalue[0];
		value=keyvalue[1];
		obj[key]=value; 
	} 
	return obj;
}
exports.init = function(options)
{
	return new search_popup(options);
};
