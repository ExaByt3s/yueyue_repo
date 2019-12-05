
/**
 * Created by hgc on 15/11/6.
 */


/**�����ļ���Ҫ��ע����ʹ��**/

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
		
	    //��������
	    self.$popup = self.$el.find('[data-role="popup"]');
	    //�������� �˴�����Ӧ��Ϊȫ��
	    self.$fade = $('[data-role="fade"]');
	    //���ذ�ť
	    self.$option_back = self.$el.find('[data-role="option-back"]');
	    //ȷ����ť
	    self.$confirm = self.$el.find('[data-role="page-confirm"]');

	    //���ذ�ť����¼�
	    self.$option_back.on('click',function()
	    {
	    	self.toggle();
	    });
	    //ȷ����ť����¼�
	    self.$confirm.on('click',function()
	    {
	    	self.go_to_search();

	    });

	    //��ťѡ��״̬�л�
	    self.$type_btn = self.$el.find('[data-role="search-type-btn"]');
	    //��һ�������� 
	    self.$name_a = self.$el.find('[data-role="a-name-a"]');
	    //�ڶ���������
	    self.$name_b = self.$el.find('[data-role="a-name-b"]');
	    //��һ��������ť
	    self.$btn_a = self.$el.find('[data-role="a-btn-a"]');
	    //�ڶ���������ť
	    self.$btn_b = self.$el.find('[data-role="a-btn-b"]');

	    //ѡ��������
	    self.$item_a = self.$el.find('[data-role="item-a"]');
	    //���ж���Ŀ¼����
	    self.$item_b = self.$el.find('[data-role="item-b"]');
	    self.$filter_a = self.$el.find('[data-role="filter-a"]');
	    //����һ��Ŀ¼����
	    self.$filter_b = self.$el.find('[data-role="filter-b"]');
	    //һ��Ŀ¼����¼�
	    self.$filter_a.on('click',function(ev)
	    {
	    	var $this = $(this);

	    	if(self.popup_type == 'sort')
	    	{
	    		self.go_to_search();
	    		return;
	    	}

	    	//��ǰ����Ŀ¼����
	    	var $cur_filter = $this.find('[data-role="filter-b"]');
	    	
	    	if($cur_filter.hasClass('fn-hide'))
	    	{
	    		//������أ�����ʾ����
	    		$cur_filter.removeClass('fn-hide');
	    		
	    	}else
	    	{
	    		//�����ʾ������������
	    		$cur_filter.addClass('fn-hide');
	    	}
	    	//����Լ�
	    	self.$filter_b.not($cur_filter).addClass('fn-hide')
	    })
	    //����Ŀ¼����¼�
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

	    	// ���û������Ŀ¼��ֱ��ѡ��
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

	    // ������Ŀ¼����¼�
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
            
            // ����ɸѡ����
            var params = {};
	    	params[parent_name] = key;

    		// ����ɸѡ����
    		self.save_params(params,true);
	    	$obj.html(parent_val + '/' + val).attr
	    	({
	    		'data-third-selected-name' : parent_name,
	    		'data-third-selected-key' : key,
	    	});

    	});

	    // ������
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
	 * ����ɸѡ���������
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
	 * ��ʾ����
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
	 * ���ص���
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
	 * ��ת������
	 * @return {[type]}
	 */
	go_to_search : function()
	{
		var self = this;

		self.toggle();

    	var $sc = $('body').find('[data-role="select-content"]');

    	// ɸѡ����
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

    	// �������
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
