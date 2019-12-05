/**
 * 基础页面框架
 * 汤圆
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    //基础框架
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    // var Scroll = require('../common/new_iscroll');
    var Scroll = require('../../common/scroll');
    var footer = require('../../widget/footer/index');
    var m_alert = require('../../ui/m_alert/view');
    var App = require('../../common/I_APP');

    //当前页引用
    var current_main_template = require('./tpl/main.handlebars');
    var item_template = require('./tpl/item.handlebars');

    var current_view = View.extend(
    {
        attrs:
        {
            template: current_main_template
        },
        events:
        {
            'swiperight': function()
            {
                page_control.back();
            },
            'tap [data-role=page-back]': function(ev)
            {
                var self = this;
                self.$pop_each_other_demand_detail.addClass('fn-hide');
                page_control.back();


            },
            'tap [data-role=btn-submit]': function(ev)
            {
                var self = this;

                if(window.confirm('请确认报名哦？'))
                {
                    self.model.send_data(self.order_id_data);
                }

            },
            'tap [data-role=btn-go-camera]': function(ev)
            {
                var self = this;

                if(App.isPaiApp)
                {
                    App.nav_to_app_page
                    ({
                        page_type : 'cameraman_card',
                        user_id : self.user_id
                    });
                }
                else
                {
                    page_control.navigate_to_page('zone/'+self.user_id+'/cameraman');
                }


            }

            
        },
        /**
         * 安装事件
         * @private
         */
        setup_events: function()
        {
            var self = this;

            // console.log( self.model.toJSON() );
            // console.log( self.model.get("param_a") );



            // 监听渲染模板
            self.model
                .on('before:detail:fetch', function(response, xhr)
                {
                    m_alert.show('加载中...', 'loading',{delay: -1});
                })
                .on('success:detail:fetch', function(response, xhr)
                {
                    m_alert.hide();
                    self.render_html(response, xhr);
                })
                .on('error:detail:fetch', function(response, xhr)
                {
                    m_alert.hide();
                    m_alert.show('网络异常', 'error',
                    {
                        delay: 1000
                    });
                })
                .on('complete:detail:fetch', function(xhr, status) {
                    m_alert.hide();
                });


            //  监听表单跳转操作
            self.model
                .on('before:send_data:fetch', function(response, xhr)
                {
                    m_alert.show('加载中...', 'loading');
                })
                .on('success:send_data:fetch', function(response, xhr)
                {

                    m_alert.hide();
                    var code = response.result_data.code;
                    var msg = response.result_data.msg ;
                    if (code == 1) 
                    {
                        m_alert.show('报名成功', 'right',{delay:1000});
                        page_control.navigate_to_page('camera_demand/success')
                    }
                    else
                    {
                        m_alert.show(msg, 'error',{delay:2000});
                    }

                })
                .on('error:send_data:fetch', function(response, xhr)
                {
                    m_alert.hide();
                    m_alert.show('网络异常', 'error',
                    {
                        delay: 1000
                    });
                })
                .on('complete:send_data:fetch', function(xhr, status) {

                });




            //当前view 操作
            self.on('update_list', function(response, xhr)
                {
                    // 区分当前对象
                    if (!self.view_scroll_obj)
                    {
                        self._setup_scroll();
                    }
                    self.view_scroll_obj.refresh();

                    // 重置渲染队列
                    self._render_queue = [];

                })
                // .once('render', self.render_after, self);
        },

        /**
         * 视图初始化入口
         */
        setup: function()
        {
            var self = this;

            // 渲染队列
            self._render_queue = [];

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$contenter_ele = self.$('[data-role=contenter-ele]'); 
            self.$pop_each_other_demand_detail = self.$('[data-role=pop-each-other-demand-detail]'); 

            // 安装事件
            self.setup_events();

            // 安装滚动条
            self._setup_scroll();

            // 安装底部导航
            //self._setup_footer();

            self.order_id = self.get("order_id");

            self.order_id_data = {
                order_id : self.order_id
            }
            self.model.get_detail(self.order_id_data);
        },

        /**
         * 渲染模板
         * @param response
         * @param options
         * @private
         */
        render_html: function(response, xhr)
        {
        
            var self = this;
            var data = response.result_data.list;
            console.log(data);

            var is_sign = data.is_sign ;

            var ele_html = item_template(data,
                {
                    helpers : {if_equal:templateHelpers.if_equal}
                })

            self.$contenter_ele.html(ele_html);

            if (is_sign) 
            {
                self.$pop_each_other_demand_detail.removeClass('fn-hide');
            }

            // 获取摄影师id
            self.user_id = data.user_id;
            self.trigger('update_list', response, xhr);
        },
        
        render_after: function()
        {
            var self = this;
            // self.model.get_list();
        },

        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll: function()
        {

            var self = this;


            var view_scroll_obj = Scroll(self.$container,
            {
               
            });

            self.view_scroll_obj = view_scroll_obj;

            // 上拉刷新
            self.view_scroll_obj.on('dropload',function(e)
            {
                // self.refresh();
                self._drop_reset();
                console.log("刷新");

            });

        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },   
        /**
         * 安装底部导航
         * @private
         */
        _setup_footer: function()
        {
            var self = this;
            var footer_obj = new footer(
            {
                // 元素插入位置
                parentNode: self.$el,
                // 模板参数对象
                templateModel:
                {
                    // 高亮设置参数
                    is_model_pai: true
                }
            }).render();
        },
        _reset: function()
        {
            var self = this;
            // self.collection.length && self.collection.each(self._add_one, self);
        },
        _add_one: function(model)
        {
            var self = this;
            // self._render_queue.push(model.toJSON());
            return self;
        },
        render: function()
        {
            var self = this;
            // 调用渲染函数
            View.prototype.render.apply(self);
            self.trigger('render');
            self.trigger('update_list');
            return self;
        }
    });
    module.exports = current_view;
});