/**
 * @author hudw
 * 2015.10.15 
 * 搜索组件
 */

/**
 * @require ./search.scss
 */

var search_tpl = __inline('./search.tmpl');
var filter_tpl = __inline('./filter.tmpl');
var sort_tpl = __inline('./sort_btn_list.tmpl');

var $ = require('jquery');
var utility = require('../utility/index');
var selectbox = require('../../yue_ui/selectbox/selectbox');


var search_class = function (options) 
{
    var self = this;

    options = options || {};

    self.options = options;

    self.total_data = self.options.total_data || [];
    self.select_use_change = self.options.select_use_change || false;

    self.icon_search = self.options.icon_search;
    self.icon_tips = self.options.icon_tips;


    self.$input_container = self.options.input_container || {};
    self.$filt_data_container = self.options.filt_data_container || {};
    self.$sort_btn_list = self.options.sort_btn_list || {};

	self.filter_rendered = self.options.filter_rendered || function(){};

    self.$input_container.parent().addClass('ui-search-com');

    self.search_url =  window.$__project_domain+'/search/';

    // 初始化
    self.init();


};

/**
 * 搜索类
 * @type {[type]}
 */
search_class.prototype = 
{
    init : function()
    {
        var self = this;

        self.render_input_container(self.total_data.input_data);
        self.render_filter_container
        ({
            type_data:self.total_data.type_data,
            filter_data:self.total_data.filter_data,
            is_no_data : self.total_data.input_data.is_no_data
        });
        self.render_sort_container(self.total_data.sort_data);

        self.setup_event();
    },
    /**
     * 渲染搜索输入框和热门标记
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    render_input_container : function(data)
    {
        var self = this;

        if(!self.$input_container.length)
        {
            return;
        } 

        data = $.extend(data,
            {
                icon_search : self.icon_search,
                search_url : self.search_url,
                select_use_change : self.select_use_change
            });

        var html_str = search_tpl(data);

        self.$input_container.html(html_str);

        selectbox(self.$input_container.find('select')[0]);
    },
    /**
     * 渲染过滤数据
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    render_filter_container : function(data)
    {
        var self = this;

        if(!self.$filt_data_container.length)
        {
            return;
        }

        var html_str = filter_tpl(data);

        self.$filt_data_container.html(html_str);

		self.filter_rendered.call(this,self.$filt_data_container);
		
    },
    /**
     * 渲染排序数据
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    render_sort_container : function(data)
    {
        var self = this;

        if(!self.$sort_btn_list.length)
        {
            return;
        }

        data = $.extend(data,{icon_tips:self.icon_tips})

        var html_str = sort_tpl(data);

        self.$sort_btn_list.html(html_str);
    },
    /**
     * 安装事件
     * @return {[type]} [description]
     */
    setup_event : function()
    {
        var self = this;

        if(self.$input_container.length) 
        {
            self.setup_select();
            self.setup_submit();
        }

        if(self.$filt_data_container.length)
        {
            self.set_more_btn_show();
        }

        if(self.$sort_btn_list.length>0)
        {
            var $sort_btn = self.$sort_btn_list.find('[data-sort-arr]');

            $sort_btn.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var sort_arr = $cur_btn.attr('data-sort-arr').split(',');                            
                var orderby = $cur_btn.attr('data-orderby');                    

                // 反选排序
                if(!orderby)
                {
                    orderby = sort_arr[0];
                }
                else
                {
                    for(var i=0;i<sort_arr.length;i++)
                    {                    
                        if(orderby != sort_arr[i])
                        {
                            orderby = sort_arr[i];
                            break;
                        }

                        orderby = sort_arr[i];
                    }    
                }
                

                
                // 替换参数                    
                var cur_url = 'http://'+window.location.host+window.location.pathname+window.location.search;

                var hash = window.location.hash.substr(1);

                cur_url = change_url_params(cur_url,'orderby',orderby);
                cur_url = change_url_params(cur_url,'p',1);

                window.location.href = cur_url+'#px';

                return false;
            });
        }
    },
    /**
     * 处理展开收起
     */
    set_more_btn_show : function()
    {
        var self = this;
        var $list = self.$filt_data_container.find('[data-role="search-filter-list"]');
        
        $list.each(function()
        {
            var list_width = $(this).width();
            var $items = $(this).find('[data-role="search-filter-list-items"]');
            var all_items_width = 0;
            var $more = $(this).find('.more');

            $items.each(function()
            {
                all_items_width += $(this).outerWidth();                
            });



            if(list_width >= all_items_width)
            {
                // 内容不多，不用隐藏
                $more.addClass('fn-hide');                
            }
            else
            {
                // 内容太多了，要换行的
                $more.removeClass('fn-hide');                   
            }

        });

        // 收缩展开事件
        self.$filt_data_container.find('.more').on('click',function()
        {
            var $cur_btn = $(this);
            if(!$cur_btn.hasClass('fn-hide'))
            {
                var $list = $cur_btn.parent('[data-role="search-filter-list"]');
                if($list.hasClass('extend'))
                {
                    $list.removeClass('extend');
                    $list.find('ul').removeClass('auto');
                    $cur_btn.find('i').removeClass('top').addClass('bottom');
                    $cur_btn.removeClass('top').addClass('bottom').find('em').html('展开');
                }
                else
                {
                    $list.addClass('extend');  
                    $cur_btn.find('i').removeClass('bottom').addClass('top');                    
                    $cur_btn.removeClass('bottom').addClass('top').find('em').html('收起');                    
                    $list.find('ul').addClass('auto');
                } 
            }
        });
    },
    setup_select : function()
    {
        var self = this;

        var $select = self.$input_container.find('[data-role="search_service_type"]');
        var $text = self.$input_container.find('[data-role="search_text"]');

        $select.on('change',function()
        {
            var $cur_btn = $(this);
            var val = $cur_btn.val();

            if(val == 'seller')
            {
                self.$input_container.find('[data-role="seller-input-container"]').show();
                self.$input_container.find('[data-role="goods-input-container"]').hide();

                self.$input_container.find('[data-role="seller-input-container"]').find('input').removeAttr('disabled');
                self.$input_container.find('[data-role="goods-input-container"]').find('input').attr('disabled','disabled');
            }
            else if(val == 'goods')
            {
                self.$input_container.find('[data-role="seller-input-container"]').hide();
                self.$input_container.find('[data-role="goods-input-container"]').show();

                self.$input_container.find('[data-role="goods-input-container"]').find('input').removeAttr('disabled');
                self.$input_container.find('[data-role="seller-input-container"]').find('input').attr('disabled','disabled');
            }



        });

    },	
    setup_submit : function()
    {
        var self = this;

        var $label_place_holder = self.$input_container.find('[data-role="label-place-holder"]');
        var $text = self.$input_container.find('[data-role="search_text"]');
        var $default_url = self.$input_container.find('[data-role="default-url"]');
        var $form = self.$input_container.find('#search-form');
        var $default_text = self.$input_container.find('[data-role="default-search-text"]');

        // 先自动聚焦
        if(!$.trim($text.val()))
        {
            $text.focus();
        }


        // 监听表单提交
        $form.on('submit',function()
        {

            if($default_url.val() && !$.trim($text.val()))
            {
                window.location.href = $default_url.val();

                return false;
            }

            if(!$default_url.val())
            {
                $default_url.attr('disabled','disabled');
            }
            if(!$.trim($default_text.val()))
            {
                $default_text.attr('disabled','disabled');
            }

            if(self.$input_container.find('[data-role="seller-input-container"]').css('display') != 'none')
            {
                var $text = self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="search_text"]');


            }
            else
            {
                var $text = self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="search_text"]');
            }

            if(!$.trim($text.val()))
            {
                alert('请输入关键字');
                return false;
            }

            if(!$.trim($text.val()) && $.trim($default_text.val()))
            {
                $text.val($default_text.val());
                $form.submit();
            }
        });

        self.$input_container.find('.search-text').each(function(i,obj)
        {
            $(obj).on('click',function()
            {
                focus_place_holder();
            });

            $(obj).find('[data-role="search_text"]').on('input propertychange',function()
            {
                focus_place_holder();
            });

            $(obj).find('[data-role="search_text"]').on('blur',function()
            {
                if(self.$input_container.find('[data-role="seller-input-container"]').css('display') != 'none')
                {
                    var $text = self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="search_text"]');
                    if(!$text.val().length)
                    {
                        self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="label-place-holder"]')
                            .css('visibility','visible').show();
                    }

                }
                else
                {
                    var $text = self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="search_text"]');
                    if(!$text.val().length)
                    {
                        self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="label-place-holder"]')
                            .css('visibility','visible').show();
                    }

                }

            });
        });





        function focus_place_holder()
        {
            if(self.$input_container.find('[data-role="seller-input-container"]').css('display') != 'none')
            {
                self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="label-place-holder"]')
                    .css('visibility','hidden').hide();

                var $temp_text = self.$input_container.find('[data-role="seller-input-container"]').find('[data-role="search_text"]');

                var s = $temp_text.val();

                $temp_text.focus();


            }
            else
            {
                self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="label-place-holder"]')
                    .css('visibility','hidden').hide();

                var $temp_text = self.$input_container.find('[data-role="goods-input-container"]').find('[data-role="search_text"]')

                var s = $temp_text.val();

                $temp_text.focus();
            }
        }
    }

};

/* 
* url 目标url 
* arg 需要替换的参数名称 
* arg_val 替换后的参数的值 
* return url 参数替换后的url 
*/ 
function change_url_params(url,name,value)
{

    var url= url ;
    var newUrl="";
    
    var reg = new RegExp("(^|)"+ name +"=([^&]*)(|$)");
    var tmp = name + "=" + value;
    if(url.match(reg) != null)
    {
        newUrl= url.replace(eval(reg),tmp);
    }
    else
    {
        if(url.match("[\?]"))
        {
            newUrl= url + "&" + tmp;
        }
        else
        {
            newUrl= url + "?" + tmp;
        }
    }

       
    return newUrl;
}



exports.init = function(options)
{
    return new search_class(options);
}
