define('search/search_popup', function(require, exports, module){ 
/**
 * Created by hgc on 15/11/6.
 */


/**依赖文件，要在注释上使用**/

/**
 * @require modules/search/search_popup.scss
 **/
var $ = require('components/zepto/zepto.js');
var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, self=this, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n<div class=\"popup popup-hide\" data-role=\"popup\">\r\n    <header class=\"popup-header\">\r\n        <div class=\"wbox clearfix\">\r\n            <div id=\"option-back\" class=\"back header-btn left0\" data-role=\"option-back\">\r\n                <i class=\"icon-back\">取消</i>\r\n            </div>\r\n            <h3 class=\"title\">筛选</h3>\r\n            <div id=\"confirm\" class=\"confirm header-btn right0\" data-role=\"page-confirm\">\r\n                <i class=\"icon-confirm\">确定</i>\r\n            </div>\r\n        </div>\r\n    </header>\r\n    <div class=\"wrapper\">\r\n        <div class=\"content native-scroll\">\r\n            ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.filter_data), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n            \r\n        </div>\r\n    </div>\r\n</div>\r\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n            <ul class=\"f14 filter-a\" data-role=\"filter-a\">\r\n                <li>\r\n                    <div class=\"name ui-border-b\" >\r\n                        <span class=\"layout_box boxflex\">";
  if (helper = helpers.text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\r\n                        <div>\r\n                            <div class=\"colorff6a6e filter-ab-name \" ><span class=\"text\" data-role=\"select-content\" data-selected-name=\"";
  if (helper = helpers.selected_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.selected_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-selected-key=\"";
  if (helper = helpers.selected_key) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.selected_key); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-third-selected-name=\"";
  if (helper = helpers.selected_third_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.selected_third_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-third-selected-key=\"";
  if (helper = helpers.selected_third_key) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.selected_third_key); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.selected_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.selected_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span></div>\r\n                        </div>\r\n                        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                    </div>\r\n                    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                    \r\n\r\n                </li>\r\n                \r\n            </ul>\r\n            ";
  return buffer;
  }
function program3(depth0,data) {
  
  
  return "\r\n                        <i class=\"icon-allow-grey icon-allow-grey-bottom ml15 icon-filter\" data-role=\"filter-icon\"></i>\r\n                        ";
  }

function program5(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n                    <ul class=\"filter-b fn-hide\" data-role='filter-b'>\r\n                        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.programWithDepth(6, program6, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                    </ul>\r\n                    ";
  return buffer;
  }
function program6(depth0,data,depth1) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\r\n                        <li class=\"item ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.selected), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.rel_key), "self", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.rel_key), "self", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" data-role=\"item-a\" data-val=\"";
  if (helper = helpers.val) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.val); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-key=\"";
  if (helper = helpers.rel_key) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.rel_key); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-name=\""
    + escapeExpression(((stack1 = (depth1 && depth1.name)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\r\n                            <div class=\"ui-border-b pb15 pt15 item-name pr15\" >\r\n                                <span data-role=\"a-btn-a\" class=\"layout_box \">";
  if (helper = helpers.val) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.val); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\r\n                                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.child_data)),stack1 == null || stack1 === false ? stack1 : stack1.data), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                            </div>\r\n                            ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.child_data)),stack1 == null || stack1 === false ? stack1 : stack1.data), {hash:{},inverse:self.noop,fn:self.program(13, program13, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                        </li>\r\n                        ";
  return buffer;
  }
function program7(depth0,data) {
  
  
  return "colorff6a6e";
  }

function program9(depth0,data) {
  
  
  return "fn-hide";
  }

function program11(depth0,data) {
  
  
  return "\r\n                                <i class=\"icon-allow-grey icon-allow-grey-bottom ml15\" data-role=\"item-icon\"></i>\r\n                                ";
  }

function program13(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n                            <div class=\"nav-content ui-border-b ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.child_data)),stack1 == null || stack1 === false ? stack1 : stack1.is_show_child_data), {hash:{},inverse:self.program(9, program9, data),fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" data-role=\"item-b\" data-name=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.child_data)),stack1 == null || stack1 === false ? stack1 : stack1.name)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">\r\n\r\n                                ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.child_data)),stack1 == null || stack1 === false ? stack1 : stack1.data), {hash:{},inverse:self.noop,fn:self.programWithDepth(16, program16, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                            </div> \r\n                            ";
  return buffer;
  }
function program14(depth0,data) {
  
  var buffer = "";
  return buffer;
  }

function program16(depth0,data,depth1) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                                <button data-role=\"search-type-btn\" data-type=\"\" class=\"ui-button ui-button-size-x ui-button-bg-fff search-type-btn ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.selected), {hash:{},inverse:self.noop,fn:self.program(17, program17, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" data-key=\"";
  if (helper = helpers.key) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.key); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-val=\"";
  if (helper = helpers.val) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.val); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-parent-val=\""
    + escapeExpression(((stack1 = (depth1 && depth1.val)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" >\r\n                                    <span data-role=\"a-btn-b\">";
  if (helper = helpers.val) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.val); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\r\n                                </button>\r\n                                ";
  return buffer;
  }
function program17(depth0,data) {
  
  
  return "on-btn";
  }

function program19(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n<div class=\"popup popup-hide\" data-role=\"popup\">\r\n    <header class=\"popup-header\">\r\n        <div class=\"wbox clearfix\">\r\n            <div id=\"option-back\" class=\"back header-btn left0\" data-role=\"option-back\">\r\n                <i class=\"icon-back\">取消</i>\r\n            </div>\r\n            <h3 class=\"title\">排序</h3>\r\n            <div id=\"confirm\" class=\"confirm header-btn right0 fn-hide\" data-role=\"page-confirm\">\r\n                <i class=\"icon-confirm\">确定</i>\r\n            </div>\r\n        </div>\r\n    </header>\r\n    <div class=\"wrapper\">\r\n        <div class=\"content native-scroll\">\r\n            ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.sort_data), {hash:{},inverse:self.noop,fn:self.program(20, program20, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n        </div>\r\n    </div>\r\n</div>\r\n";
  return buffer;
  }
function program20(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n            <ul class=\"f14 filter-a\" data-role=\"filter-a\">\r\n                <li data-role=\"orderby\" data-val=\"";
  if (helper = helpers.orderby) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.orderby); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.selected), {hash:{},inverse:self.noop,fn:self.program(21, program21, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" >\r\n                    <div class=\"name ui-border-b \"  >\r\n                        <span class=\"layout_box boxflex\">";
  if (helper = helpers.text) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.text); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\r\n                    </div>\r\n\r\n                </li>\r\n                \r\n            </ul>\r\n            ";
  return buffer;
  }
function program21(depth0,data) {
  
  
  return "colorff6a6e selected";
  }

  buffer += "<!--筛选列表-->\r\n";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.popup_type), "filter", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.popup_type), "filter", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n<!--排序列表-->\r\n";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(19, program19, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.popup_type), "sort", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.popup_type), "sort", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });
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
 
});