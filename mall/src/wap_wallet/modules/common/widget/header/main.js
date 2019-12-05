/**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require ./header.scss
 **/
var $ = require('zepto');
var ua = require('../../ua/index');

function header_class(options)
{
    var self = this;

    self.init(options);
}

header_class.prototype =
{
    init : function(options)
    {
        var self = this;
        self.options =  options;
        self.render_ele = options.ele;
        self.left_side_html =  $.trim(options.left_side_html);
        if (self.options.left_icon_show == null) 
        {
            self.options.left_icon_show = true ;
        }

        self.config = true ;


        //如果头部隐藏，要把当前页节点margin-top改为0
        if (!options.header_show)
        {
            self.set_bar_css()
        }

        // 如果标题为空，标题读取文档标题
        if (!options.title.trim())
        {
            var document_title = document.title;
            self.options = $.extend(true,{},self.options,{title : document_title, left_side_html:self.left_side_html});
        }

        var cur_pathname = window.location.pathname;

        if(/test/.test(cur_pathname))
        {
            options.title = '测试-'+options.title;
        }

        self.render(self.rend_ele);

        if(ua.is_yue_app || ua.is_yue_seller)
        {
            self.$el.addClass('fn-hide');
            self.set_bar_css()
        }

        return self;

    },
    render: function (dom) {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择

        var self = this;
        var data = self.options;
        var template  = __inline('./header.tmpl');
        self.render_ele.html(template(data));

        self.$el = self.render_ele;

        self.setup_event(self.$el);
    },
    setup_event : function($el)
    {
        var self = this;

        var use_page_back = self.options.use_page_back == null ? true : false;

        self.$el.find('[data-role="page-back"]').on('click',function()
        {


            if(use_page_back)
            {
                self.page_back();
            }
            else
            {
                self.$el.trigger('click:left_btn');
            }


        });

        self.$el.find('[data-role="right-btn"]').on('click',function()
        {
            self.$el.trigger('click:right_btn');
        });

    },

    
    page_back : function()
    {
        var self = this;

        if(document.referrer)
        {
            window.history.back();
            return false;
        }
        else
        {
            window.location.href = __index_url_link ;
        }



    },
    set_bar_css : function()
    {
        var self = this;
        $("[role=main]").css({
            paddingTop: '0'
        });
    }
};

exports.init = function(options)
{
    return new header_class(options);
};

