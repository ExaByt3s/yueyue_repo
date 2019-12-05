/**
 * 首页 - 发现视图
 * nolestLam 2014.8.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var App = require('../../common/I_APP');
    var page_control = require('../../frame/page_control');
    var find_tpl = require('../find/tpl/main.handlebars');
    var footer = require('../../widget/footer/index');
    var pull_down = require('../../widget/pull_down/view');
    var load_more = require('../../widget/load_more/view');
    var select_drop = require('../../ui/select_drop/view');
    var m_alert = require('../../ui/m_alert/view');
    var grid_view = require('./grid_view');
    var find_collection = require('../find/collection');
    var topic_collection = require('../../topic/collection');
    var topic_view = require('../../topic/list/view');


    var find_view = View.extend
    ({
        attrs:
        {
            template: find_tpl
        },
        events :
        {
            'tap [data-role="select-hot"]' : function()
            {
                page_control.navigate_to_page("hot");
            },
            'tap [data-role="select-find"]' : function()
            {
                var self = this;


                if(self.style_grid_view)
                {
                    self.style_grid_view.refresh();
                }
               /* if(self.height_grid_view)
                {
                    self.height_grid_view.refresh();
                }
                if(self.breast_grid_view)
                {
                    self.breast_grid_view.refresh();
                }*/
            },/*
            'touch [data-role="content-body"]' : function()
            {
                var self = this;

                if(!self.select_drop_obj.is_drop())
                {
                    return;
                }

                self.select_drop_obj.pull_up();

            },*/
            'tap [data-role="select-loc"]' : function()
            {
                page_control.navigate_to_page("location/from_find")
            },
            'tap [data-role="style-style"]' : function(ev)
            {
                var self = this;

                self.$container_group.hide();

                self.$container_style.show();

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                self.style_grid_view.view_scroll_obj.refresh();

            },
            'tap [data-role="style-breast"]' : function(ev)
            {
                var self = this;

                self.$container_group.hide();

                self.$container_breast.show();

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                /*if(self.breast_grid_view)
                {
                    self.breast_grid_view.view_scroll_obj.refresh();

                    return
                }*/

                /*self.breast_grid_view = new grid_view
                ({
                    collection : self.$breast_collection,
                    parentNode : self.$container_breast
                }).render();*/

            },
            'tap [data-role="style-tall"]' : function(ev)
            {
                var self = this;

                self.$container_group.hide();

                self.$container_height.show();

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                /*if(self.height_grid_view)
                {
                    self.height_grid_view.view_scroll_obj.refresh();

                    return
                }

                self.height_grid_view = new grid_view
                ({
                    collection : self.$height_collection,
                    parentNode : self.$container_height
                }).render();*/

            },
            /**
             * 打开主题列表
             * @param ev
             */
            'tap [data-role="topic"]' : function(ev)
            {
                var self = this;

                self.$container_group.hide();

                self.$container_topic.show();

                $(ev.currentTarget).addClass("cur").siblings().removeClass("cur");

                self.topic_list_obj.refresh();



                /*if(self.height_grid_view)
                 {
                 self.height_grid_view.view_scroll_obj.refresh();

                 return
                 }

                 self.height_grid_view = new grid_view
                 ({
                 collection : self.$height_collection,
                 parentNode : self.$container_height
                 }).render();*/

            },
            'tap [data-role="select-more"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }

                /*
                if(App.isPaiApp)
                {
                    App.qrcodescan();
                }

                return;
                 */


                if(self.select_drop_obj.is_drop())
                {
                    self.select_drop_obj.pull_up();
                }
                else
                {
                    self.select_drop_obj.drop_down();
                }
            },
            'tap [data-role=grid-pic-container]' : function(ev)
            {
                var $current_target = $(ev.currentTarget);

                var user_id = utility.int($current_target.attr('data-user-id'));

                page_control.navigate_to_page('model_card/'+user_id);
            },
            'tap [data-role="search"]' : function()
            {
                page_control.navigate_to_page('search');
            },
            'tap [data-role="nav-to-info"]' : function(ev)
            {
                var $current_target = $(ev.currentTarget);

                var topic_id = $current_target.attr('data-topic-id');

                page_control.navigate_to_page('topic/'+topic_id);
            }
        },
        _setup_select_drop : function()
        {
            var self = this;

            var role = utility.user.get('role');

            self.select_drop_obj = new select_drop
            ({
                parentNode: self.$el,
                templateModel:
                {
                    is_model : (role == 'model') ? true : false
                }

            }).render();

        },
        _setup_load_more : function()
        {

        },
        _setup_events : function()
        {
            var self = this;

            self.topic_collection_obj.on('before:list:fetch',function(response)
            {
                m_alert.show('加载中...','loading');
            }).on('success:list:fetch',function(response)
            {
                m_alert.hide({delay:-1});
            }).on('error:list:fetch',function(response)
            {
                m_alert.show('网络异常','error');
            });
        },
        _setup_footer : function()
        {
            var self = this;

            var footer_obj = new footer
            ({
                // 元素插入位置
                parentNode: self.$el,
                // 模板参数对象
                templateModel :
                {
                    // 高亮设置参数
                    is_model_pai : true
                }
            }).render();
        },
        /**
         * 渲染主题列表
         * @private
         */
        _render_topic_list : function(data)
        {
            var self = this;

            var list = data.result_data.list;

            var html_str = topic_list_tpl
            ({
                list : list
            });

            self.$container_topic.html(html_str);
        },
        _scroll_check : function()
        {

        },
        setup : function()
        {
            var self = this;

            self.style_grid_view = null;

            self.breast_grid_view = null;

            self.height_grid_view = null;

            self.$select_drop = self.$('[data-role="more-select-drop"]');

            self.$container_style = self.$('[data-role="container_style"]');

            self.$container_breast = self.$('[data-role="container_breast"]');

            self.$container_height = self.$('[data-role="container_height"]');

            self.$container_topic = self.$('[data-role="container_topic"]');

            self.$container_group = self.$('[data-grid]');

            self.location = utility.storage.get('location');

            self.$style_collection = new find_collection
            ({
                type : "style"
            });

            self.topic_collection_obj = new topic_collection();

            self.topic_list_obj = new topic_view
            ({
                collection : self.topic_collection_obj,
                parentNode : self.$container_topic
            }).render();

            //self.$container_topic.append(self.topic_list_obj.$el);

            /*self.$breast_collection = new find_collection
            ({
                type : "chest"
            });

            self.$height_collection = new find_collection
            ({
                type : "height"
            });*/

            self.style_grid_view = new grid_view
            ({
                collection : self.$style_collection,
                parentNode : self.$container_style

            }).render();

            self._setup_events();

            self._setup_select_drop();

            self._setup_footer();

        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        set_header_location : function()
        {
            var self = this;

            self.$('[data-loc]').text(utility.storage.get('location').location_name)
        }
    });

    module.exports = find_view;
});