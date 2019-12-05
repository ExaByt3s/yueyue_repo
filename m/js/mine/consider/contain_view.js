/**
 * Created by nolest on 2014/9/24.
 */


define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var contain_view_tpl = require('./tpl/contain_view.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/new_iscroll');
    var m_alert = require('../../ui/m_alert/view');
    var invited_card_view = require('./tpl/invite.handlebars');
    var abnormal = require('../../widget/abnormal/view');
    var load_more = require('../../widget/load_more/view');


    var considerView = View.extend
    ({
        attrs:
        {
            template: contain_view_tpl
        },
        events :
        {

        },
        _setup_events : function()
        {
            var self = this;

            self.model
                .on('before:fetch',function()
                {
                    m_alert.show('加载中','loading',{delay:1000})
                })
                .on('success:fetch',function(response,options)
                {
                    //区分获取数据的view
                    if(options.data.status == self.get("templateModel").type_tag)
                    {
                        console.log(response)

                        self._render_invited_card(response.result_data);

                        self.fetching = false;
                    }
                })
                .on("error:fetch",function(response,options)
                {
                    m_alert.show('加载超时','loading',{delay:1000});

                    self.fetching = false;
                })
                .on("complete:fetch",function(response,options)
                {

                })

        },
        _setup_load_more : function()
        {
            var self = this;

            self.load_more_obj = new load_more
            ({
                templateModel:{text:'上拉加载更多'},
                parentNode:self.$load_more

            }).render();

        },
        setup : function()
        {
            var self = this;

            self.type_tag = self.get("templateModel").type_tag;

            self.$container = self.$('[data-role='+ self.type_tag + '-warp]');

            self.$content_inside = self.$('[data-role=' + self.type_tag + '-content-inside]');

            self.$load_more = self.$('[data-role='+self.type_tag + '-load_more]');

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.page_count = 1;

            self.fetching = false;

            self._setup_events();

            self._setup_load_more();

            self._setup_scroll();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') -40;
        },
        /* 渲染列表
         *
         */
        _render_invited_card : function(data)
        {
            var self = this;

            self.has_next_page = data.has_next_page;

            self.page_count++;

            self.data_cache = data.list;

            $.each(data.list,function(i,obj)
            {
                obj.is_cameraman = self.get("is_cameraman");

                console.log(obj);
                /*重写 2014.11.20 nolest
                //modify hudw 2014.11.8
                if(obj.pay_status == 0 && obj.is_cameraman)
                {
                    //待付款
                    obj.status_text = '待付款';
                }
                else if(obj.pay_status == 1)
                {
                    if(obj.date_status == 'wait')
                    {
                        obj.status_text = 'NEW'
                    }
                    else if (obj.date_status == 'confirm')
                    {
                        obj.status_text = ''
                    }
                    else if(obj.date_status == 'cancel')
                    {
                        obj.status_text = '已拒绝'
                    }
                }
                */
                var is_confirm = false;

                if(obj.date_status == 'confirm')
                {
                    is_confirm = true;
                }
                //红色标签
                var is_new = false;

                var is_empty = false;
                console.log(obj.is_new);
                if(obj.lable == 'NEW')
                {
                    is_new = true;
                }

                if(obj.lable == '')
                {
                    is_empty = true;
                }

                /*
                //已完成的活动的灰色标签
                if(obj.is_end == 1 && obj.is_comment == 1)
                {
                    obj.date_status = 'cancel';
                    obj.status_text = '已完成';
                }
                */

                var data = $.extend(true,{},obj,{is_cameraman : self.get("templateModel").is_cameraman,is_confirm : is_confirm,is_new : is_new,is_empty : is_empty});

                var invited = invited_card_view
                ({
                    data : data
                });
                self.$content_inside.append(invited);

            });
            //无内容时显示背景
            if(self.$content_inside.children().length == 0)
            {
                self.abnormal_view = new abnormal({
                    templateModel :
                    {
                        content_height : utility.get_view_port_height('all') - 75
                    },
                    parentNode:self.$content_inside
                }).render();
            }

            self.$load_more.find('[data-role="load-more"]').addClass("fn-hide");

            if(self.has_next_page)
            {
                self.$load_more.find('[data-role="load-more"]').removeClass("fn-hide");
            }

            self._drop_reset();
            self.view_scroll_obj.refresh();

        },
        /* 安装滚动条
         *
         */
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    //is_hide_dropdown : false,

                });




            view_scroll_obj.on('scrollMoveAfter',function()
            {
                var _self = this;

                if(_self.maxScrollY - _self.y > 40)
                {
                    //有下一页时才加载
                    if(self.has_next_page && !self.fetching)
                    {
                        self.fetching = true;

                        self.get_list();
                    }
                }
            });

            self.view_scroll_obj = view_scroll_obj;


            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

            self.view_scroll_obj.refresh();
        },
        /* 加载数据
         *
         */
        get_list : function()
        {
            var self = this;

            var data =
            {
                status: self.get("templateModel").type_tag,
                role: self.get("templateModel").role,
                page : self.page_count
            };

            self.model.get_more_list(data);
        },
        /*  刷新方法
         *
         */
        refresh : function()
        {
            var self = this;

            self.page_count = 1;

            self.$content_inside.html("");

            var refresh_data =
            {
                status: self.get("templateModel").type_tag,
                role: self.get("templateModel").role,
                page : self.page_count
            };

            self.model.get_more_list(refresh_data);

            self.view_scroll_obj.scrollTo(0,0);
        },
        _drop_reset : function()
        {
            var self = this;

            return;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        /*  返回点击数据 大view调用
         *
         */
        search_data : function(id)
        {
            var self = this;

            var data;

            if(self.data_cache)
            {
                $.each(self.data_cache,function(i,obj)
                {
                    if(obj.date_id == id)
                    {
                        data =  obj
                    }
                })
            }
            return data;
        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');
        }
    });

    module.exports = considerView;
});
