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
    var items_tpl = require('./tpl/items.handlebars');
    var scroll = require('../../common/scroll');
    var iscroll_slide = require('../../widget/iscroll_slide/view');


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
                    console.log("style_grid_view.refresh");

                    self.style_grid_view.refresh();
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
            'tap [data-role="img-tap"]' : function(ev)
            {
                var $current_target = $(ev.currentTarget);

                var link_type = $current_target.attr('data-link-type');
                var url = $current_target.attr('data-url');

                switch (link_type)
                {
                    case 'inner_web':
                        page_control.navigate_to_page(url);
                        break;
                    case 'outside':
                        window.location.href = url;
                        break;
                    case 'other':
                        break;
                }
            },
            'tap [data-role="more_tap"]' : function(ev)
            {
                var $current_target = $(ev.currentTarget);

                var hash = $current_target.attr('data-more');
                page_control.navigate_to_page(hash);
            },
            'tap [data-role="topic_details"]' : function(ev)
            {
                var $current_target = $(ev.currentTarget);

                var hash = $current_target.attr('data-details-hash');
                page_control.navigate_to_page(hash);
            },
            'tap [data-inner-tap]' : function(ev)
            {
                var $current_target = $(ev.currentTarget);

                var search = $current_target.attr('data-search');
                var url = $current_target.attr('data-url');

                var search_info = {};

                search_info.tag = search;
                page_control.navigate_to_page("search_result/" + encodeURIComponent(JSON.stringify(search_info)))
                //search_result
            },
            'tap [data-role="more_more"]' : function()
            {
                page_control.navigate_to_page("topic_list")
            },
            'tap [data-role="select-loc"]' : function()
            {
                var self = this;
                //跳转地区
                page_control.navigate_to_page("location/from_find") ;
                // 切换城市
                // self.setup_city_change_dialog();
            }
        },
        _setup_iscroll_list : function(contents)
        {
            if(!(contents.length > 0)){
                return;
            }
console.log("123")
            var self = this;

            var data = [];

            //当只有一张图时不显示小圆点
            var no_single;
            (contents.length > 8) ? ( no_single = true ) : ( no_single = false );
            //设置选中第一个
            contents[0].class = "swiper-visible-switch";

            self.$ad_pic_out = self.$('[data-role=ad-pic-out]');

            var page_strs = [];//分页数组

            //每个对象加入html_str
            $.each(contents,function(i,obj)
            {
                contents[i] = $.extend(true,{},obj,
                    {
                        html_str : '<div class="find_roll_inner_block" data-url="' + obj.url + '" data-inner-tap data-search="' + obj.tag + '"' + '"  style="margin-left:' + Math.ceil((document.body.clientWidth - 57*4)/5) + 'px">' + obj.tag + '</div>'
                    });

                page_strs.push(contents[i].html_str);
            });

            //生成分页
            var page = 0;

            for(var i = 0 ; i < Math.ceil(page_strs.length/8); i++ )
            {
                var inner_html = '';

                ++page;

                for(var k = 0 ; k < 8; k++)
                {
                    var num = (page-1)*8 + k;

                    if(num < page_strs.length)
                    {
                        inner_html = inner_html + page_strs[num];
                    }
                }

                var html_page_str = '<div class="find_roll_pages">' + inner_html + '</div>';

                //分页插入插件data
                data.push(html_page_str)
            }

            self.iscroll_slide_view = new iscroll_slide({
                templateModel :
                {
                    find_page_bar_contents : data,
                    no_single : no_single,
                    height : 162
                },
                auto_play : 5000,
                height : 162,
                parentNode:self.$ad_pic_out
            }).render();


        },
        _render_items : function(data)
        {
            var self = this;



            var four_grid_one = data.ad_list.list[0];
            var four_grid_two = data.ad_list.list[1];
            var four_grid_thr = data.ad_list.list[2];
            var four_grid_fur = data.ad_list.list[3];

            data = $.extend(true,{},data,
                {four_grid_one:four_grid_one},
                {four_grid_two:four_grid_two},
                {four_grid_thr:four_grid_thr},
                {four_grid_fur:four_grid_fur});

            var html_str = items_tpl
            ({
                data : data
            });

            self.item_container.html(html_str);

            self._setup_iscroll_list(data.tag.list);

            //"更多"为空时，隐藏箭头
            if(data.topic_more_text == '')
            {
                self.$('[data-role="more_arr"]').addClass("fn-hide")
            }
            self.view_scroll_obj.refresh();
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = scroll(self.$container,{
                lazyLoad : true,
                _startY : 45,
                prevent_tag : 'slider',
                is_hide_dropdown : true
            });

            self.view_scroll_obj = view_scroll_obj;
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self._setup_scroll()
            });

            self.$style_collection
                .on('success:get_find_data',function(response, options)
                {
                    self._render_items(response.result_data.list);
                })

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
        _scroll_check : function()
        {

        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="content-body"]');

            self.$container_style = self.$('[data-role="container_style"]');

            self.$container_group = self.$('[data-grid]');

            self.location = utility.storage.get('location');

            self.item_container = self.$('[data-role="items"]');

            self.$style_collection = new find_collection
            ({
                type : "style"
            }).get_find_data();

            self._setup_events();

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

            self.$('[data-role="select-city"]').text(utility.storage.get('location').location_name)
        }
    });

    module.exports = find_view;
});