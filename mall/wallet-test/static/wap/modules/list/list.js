define('list', function(require, exports, module){ /**
 * Created by huanggc on 15/10/9.
 * 
 * 模板方法
 *  //星星数
    handlebars模板使用星星接口数据用法 style="width:{{percent score "5"}}"
    percent : 
    score ：星星数
    "5" : 星星数最大值

    //图片尺寸
    data-lazyload-url="{{change_img_size images "260" }}
     change_img_size：方法名
     images ：图片数据
     "260" : 图片尺寸
 */

/**依赖文件，要在注释上使用**/

/**
 * @require modules/list/list.scss
 **/

// ========= 模块引入 ========= 
var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');
var yue_ui = require('yue_ui/frozen');
var abnormal = require('common/widget/abnormal/index');
var LZ = require('common/lazyload/lazyload');
var _self = $({});

/**
 * 构造函数
 * @param options
 * @param matter 
 * @param event
 */
 function list_item_class(options)
{ 
    var self = this;

    var options = options || {} ;
    //渲染目标
    self.$render_ele = options.ele || {} ;
    //发送请求地址
    self.send_url = options.url || "";
    //传递参数
    self.ajax_params = options.params || {};
	self.ajax_type = options.ajax_type || 'GET';

    //模板 
    self.template = options.template || {} ;
    //是否开启lz
    self.is_open_lazyload = options.is_open_lazyload == null ? true:false;
    //是否开启lz参数
    self.is_open_lz_opts = options.is_open_lz_opts || "";

    self.init(options);
}
list_item_class.prototype =
{
    /**
     * 刷新 可添加请求参数
     * @param  {[type]} params [description]
     * @return {[type]}        [description]
     */
    refresh   : function(params)
    {   
        var self = this;

        if(params)
        {
            //给整合数据添加页数
            self.ajax_params = $.extend(self.ajax_params,params);
        }

        self.page = 1;

        self.action(self.page);
    },
    /**
     * 加载更多
     * [load_more description]
     * @return {[type]} [description]
     */
    load_more : function()
    {
        var self = this;

        if(self._sending)
        {
            return;
        }

        if(self.has_next_page)
        {
            self.page++;

            self.action(self.page);
        }
        else
        {
            self.$load_more.html('到底部了');
        }
    },
    /**
     * 初始化
     * @param  {[type]} options [description]
     * @return {[type]}         [description]
     */
    init : function(options)
    {
        var self = this;

        _self.$loading = {};
        self.page = 1 ;
        //创建父容器
        self.$render_ele.append('<div data-role="list"></div>');
        //无数据处理渲染容器
        self.$render_ele.append('<div class="" data-role="abnormal"></div>');
        //添加一行加载tips  
        self.$render_ele.append('<div class="ui-list-load-more-btn fn-hide" data-role="load_more">正在加载...</div>');

        self.$list_container = self.$render_ele.find('[data-role="list"]');
        self.$abnormal = self.$render_ele.find('[data-role="abnormal"]');
        self.$load_more = self.$render_ele.find('[data-role="load_more"]');

        // 定义el元素
        self.$el = self.$render_ele;             

        self.refresh();
        self.window_scroll_more();
    },

    action : function (page)
    {
        var self = this ;

        if(self._sending)
        {
            return;
        }
        //给整合数据添加页数
        self.ajax_params = $.extend(self.ajax_params,{page:page});

        _self.ajax_obj = utility.ajax_request 
        ({
            url : self.send_url,
            data : self.ajax_params,
			type : self.ajax_type,
            beforeSend : function()
            {
                self._sending = true;
                _self.$loading = $.loading
                ({
                    content:'加载中...'
                });

            },
            success : function(data)
            {

                console.log(data);
                self._sending = false;
                //获取数据
                var list_data = data.result_data.list;

                self.$el.rq_status = 'success';

                //渲染前处理事件
                self.$el.trigger('list_render:before',[self.$list_container,data]);

                _self.$loading.loading("hide");

                //是否有分页
                self.has_next_page = data.result_data.has_next_page;

                // 无数据处理
                if(!list_data.length && page == 1)
                {
                    abnormal.render(self.$abnormal[0],{});
                    self.$abnormal.removeClass('fn-hide');
                    self.$list_container.addClass('fn-hide');
                    self.$load_more.addClass('fn-hide');

                    //渲染后处理事件
                    self.$el.trigger('list_render:after',[self.$list_container,data,$(html_str)]);

                    return;
                }
                else
                {
                    self.$abnormal.addClass('fn-hide');
                    self.$list_container.removeClass('fn-hide');
                    self.$load_more.removeClass('fn-hide');
                }

                //把数据放入模板
                var html_str = self.template
                ({
                    list : list_data
                });
                //插入渲染列表
                if(page==1)
                {
                    self.$list_container.html(html_str);
                }else
                {
                    self.$list_container.append(html_str);
                }
                //渲染后处理事件
                self.$el.trigger('list_render:after',[self.$list_container,data,$(html_str)]);

                self.setup_event();
            },
            error : function()
            {
                self._sending = false;
                _self.$loading.loading("hide");
                $.tips
                    ({
                        content:'网络异常',
                        stayTime:3000,
                        type:'warn'
                    });

                self.$el.trigger('request:error');

                self.$el.rq_status = 'error';
            }
        })

    },
    /**
     * 滚动操作事件
     * @return {[type]} [description]
     */
    window_scroll_more : function()
    {
        var self = this;
        $(window).scroll(function()
        {
            if(!self.$list_container.height())
            {
                return;
            }

            if($(window).scrollTop() + $(window).height() > $(document).height() - 50)
            {
                self.load_more();
            }
        });
    },
    /**
     * 安装事件
     * @return {[type]} [description]
     */
    setup_event : function()
    {
        var self = this;
        //new 对象 新建内置对象
        if(self.is_open_lazyload)
        {
            if(!self.lazyloading)
            {
                var lz_options_obj =
                    {
                        size : window.innerWidth
                    };

                if(!self.is_open_lz_opts)  
                {
                    lz_options_obj = {};
                }

                self.lazyloading = new LZ($('body'),lz_options_obj);
            }
            else
            {
                self.lazyloading.refresh();
            }
        }

        //判断列表小于屏幕高度
        if(self.$render_ele.height()<window.innerHeight)
        {
            self.$load_more.addClass('fn-hide');
        }

    }

}
return list_item_class; 
});