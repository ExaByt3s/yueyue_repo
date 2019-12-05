/**
 * 首页 - 城市选择
 * 汤圆 2014.18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var View = require('../common/view');
    var location = require('../location/tpl/main.handlebars');
    var utility = require('../common/utility');
    var templateHelpers = require('../common/template-helpers');
    var Scroll = require('../common/scroll');
    var footer = require('../widget/footer/index');
    var location_data = require('../common/location_data');
    var city_tpl = require('./tpl/city.handlebars');
    var m_alert = require('../ui/m_alert/view');
    var I_App = require('../common/I_APP');
   
    var location_view = View.extend
    ({

        attrs:
        {
            template: location
        },
        events :
        {
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role=date-city]' : function (ev)
            {
                var self = this;

                var $current_target = $(ev.currentTarget);

                if($current_target.attr('data-location-name') != '广州')
                {
                    m_alert.show('该城市暂未开通服务，敬请期待！','error')

                    return
                }

                var city_data =
                {
                    'location_id' : $current_target.attr('data-location-id'),
                    'location_name' : $current_target.attr('data-location-name')
                };

                utility.storage.set('location',city_data);

                self.location_page_back();

            },
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                self.location_page_back();

            },
            'tap [data-role="now-city"]' : function(ev)
            {
                var self = this;

                var $current_target = $(ev.currentTarget);

                if($current_target.attr('data-location-name') != '广州')
                {
                    m_alert.show('该城市暂未开通服务，敬请期待！','error')

                    return
                }

                var city_data =
                {
                    'location_id' : $current_target.attr('data-location-id'),
                    'location_name' : $current_target.attr('data-location-name')
                };

                utility.storage.set('location',city_data);

                page_control.back();

            }
        },
        location_page_back : function()
        {
            var self = this;

            if(self.get("from") == "from_hot")
            {
                page_control.navigate_to_page("hot")
            }
            else if(self.get("from") == "from_find")
            {
                page_control.navigate_to_page("find")
            }
            else
            {
                console.log('from_no_where')
                //会白屏
                page_control.back()
            }

        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;
            self.collection
            .on('reset',self._reset,self)
            .on('add', self._add_one, self)
            .on('before:fetch',function(response,xhr)
            {
                m_alert.show('加载中...','loading');
            })
            .on('success:fetch',function(response,xhr)
            {
                m_alert.hide();

                self._render_city(response,xhr);
            })
            .on('error:fetch',function(response,xhr)
            {
                m_alert.show('网络异常','error',{delay:1000});

                self._render_city(response,xhr);
            })
            .on('complete:fetch',function(xhr,status)
            {

            })

            ;


            
            self.on('update_list',function(response,xhr)
            {

                // 区分当前对象
                var _self = this;

                self._setup_scroll();
                self.view_scroll_obj.refresh();

                // 重置渲染队列
                self._render_queue = [];
            })
            .once('render',self._render_after,self);

        },

        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {
                is_hide_dropdown : true
            });
            self.view_scroll_obj = view_scroll_obj;
        },
        /**
         * 安装底部导航
         * @private
         */
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
        _reset : function()
        {
            var self = this;
            self.collection.length && self.collection.each(self._add_one,self);
        },
        _add_one : function(model)
        {
            var self = this;
            self._render_queue.push(model.toJSON());
            return self;
        },
        
        /**
         * 渲染城市
         * @param response
         * @param options
         * @private
         */
         _render_city : function(response,xhr)
        {
            var self = this;
            var render_queue = self._render_queue;
            var all_city_data = render_queue.concat(location_data.data.list);

            var html_str = city_tpl
            ({
                list : self._render_queue//all_city_data

            });

            // var more_html_str = more_city_tpl
            // ({
            //     more_list : render_queue
            // });
            self.$hot_city_container.html(html_str);
 
            self.trigger('update_list',response,xhr);
        },

        _render_after :function()
        {
            var self = this ;

            self.$('[data-role="now-city-text"]').html(utility.storage.get('location').location_name)

            self.collection.get_list();
        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 渲染队列
            self._render_queue = [];

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$hot_city_container = self.$('[data-role=hot-city-container]'); // 热门城市容器


            // 安装事件
            self._setup_events();

            // 安装底部导航
            //self._setup_footer();


        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }

    });

    module.exports = location_view;
});