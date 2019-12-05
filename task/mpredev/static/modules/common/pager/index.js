define('common/pager/index', function(require, exports, module){ /**
 * Created by hudw on 15-4-17.
 * 分页组件
 */
(function($)
{
    var url = window.location.href;

    // 解决origin兼容问题
    if (!window.location.origin)
    {
        window.location.origin = window.location.protocol + '//' + window.location.hostname + (window.location.port ? (':' + window.location.port) : '');
    }



    var _pager_tpl =  '<section class="ui-pager">'+
        '<div class="wrapper">'+
            '<div class="content">'+
                '<div class="nav-bar">'+
                    '<div class="first">'+
                        '<a>首页</a>'+
                    '</div>'+
                    '<div class="pre">'+
                        '<a>上一页</a>'+
                    '</div>'+
                    '<div class="nav-page-cur">'+
                        '<div class="nav-page-text">'+
                        '<span data-role="page-text"></span>'+
                        '<i></i>'+
                        '</div>'+
                        '<select class="sel-control" id="sel-control">'+
                        '</select>'+
                    '</div>'+
                    '<div class="next">'+
                        '<a>下一页</a>'+
                    '</div>'+
                    '<div class="end">'+
                        '<a>末页</a>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>'+
        '</section>';

    var pager_class = function(el,options)
    {
        var self = this;

        self.$el = $(el);

        self._pager_tpl = _pager_tpl;
        self.options = options;

        self.init();

    };

    // 添加分页类的属性和方法
    pager_class.prototype =
    {
        init : function()
        {
            var self = this;

            self.$el.html(self._pager_tpl);
            self.$sel_control = self.$el.find('#sel-control');
            self.$sel_control_page_text = self.$el.find('[data-role="page-text"]');

            // 初始化上下页 按钮状态
            if(self.options.cur_page == 1)
            {
                self.$el.find('.pre').addClass('btn-disable');
                self.$el.find('.first').addClass('btn-disable');
            }

            if(self.options.cur_page == self.options.total_page)
            {
                self.$el.find('.next').addClass('btn-disable');
                self.$el.find('.end').addClass('btn-disable');
            }


            // 分页参数
            var total_page = self.options.total_page;
            var sel_html_arr = [];

            sel_html_arr.push('<option value="0">--请选择页数--</option>');

            for(var i=0;i<total_page;i++)
            {
                sel_html_arr.push('<option value="'+(i+1)+'">第'+(i+1)+'页</option>');
            }

            self.$sel_control.html(sel_html_arr.join(''));
            self.$sel_control_page_text.html(self.options.cur_page+'/'+self.options.total_page);

            // 安装事件
            self.setup_event();

        },
        show : function()
        {

        },
        hide : function()
        {

        },
        setup_event : function()
        {
            var self = this;

            self.$el.find('.first').on('click',function()
            {
                if(self.options.cur_page == 1)
                {
                    return;
                }

                window.location.href = set_url_param(window.location.href,'p',1);
            });

            self.$el.find('.end').on('click',function()
            {
                if(self.options.cur_page == self.options.total_page)
                {
                    return;
                }

                window.location.href = set_url_param(window.location.href,'p',self.options.total_page);

            });

            self.$el.find('.pre').on('click',function()
            {
                if(self.options.cur_page == 1)
                {
                    return;
                }

                window.location.href = set_url_param(window.location.href,'p',self.options.pre);
            });

            self.$el.find('.next').on('click',function()
            {
                if(self.options.cur_page == self.options.total_page)
                {
                    return;
                }

                window.location.href = set_url_param(window.location.href,'p',self.options.next);
            });

            self.$sel_control.on('change',function()
            {
                var value = self.$sel_control[0].options[self.$sel_control[0].selectedIndex].value;

                if(!value)
                {
                    return;
                }

                self.$sel_control_page_text.html(value+'/'+self.options.total_page);

                window.location.href = set_url_param(window.location.href,'p',value);
            });
        },
        nav_to_page : function()
        {

        }
    };

    // 获取当前窗口url中param参数的值
    function get_param(param){
        var query = location.search.substring(1).split('&');
        for(var i=0;i<query.length;i++){
            var kv = query[i].split('=');
            if(kv[0] == param){
                return kv[1];
            }
        }
        return null;
    }

    // 设置指定url中param的值，返回处理后的url
    function set_url_param(destiny, par, par_value){
        var pattern = par+'=([^&]*)';
        var replaceText = par+'='+par_value;
        if (destiny.match(pattern))
        {
            var tmp = '/\\'+par+'=[^&]*/';
            tmp = destiny.replace(eval(tmp), replaceText);
            return (tmp);
        }
        else
        {
            if (destiny.match('[\?]'))
            {
                return destiny+'&'+ replaceText;
            }
            else
            {
                return destiny+'?'+replaceText;
            }
        }
        return destiny+'\n'+par+'\n'+par_value;
    }



    window._Pager = pager_class;

})(window.Zepto);
 
});