/**
 * 主题详情视图
 *
 * hdw 2014.10.27
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var info_tpl = require('../tpl/info.handlebars');
    var main_info_tpl = require('../tpl/main_info.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var App = require('../../common/I_APP');
    var global_config = require('../../common/global_config');

    var topic_list_view = View.extend(
    {
        attrs:
        {
            template: main_info_tpl
        },
        events:
        {
            'swiperight': function()
            {
                page_control.back();
            },
            'tap [data-role=page-back]': function(ev)
            {
                page_control.back();
            },

            //导航跳转
            'tap [data-role="nav-page"]': function(ev)
            {
                var self = this;
                var $cur_btn = $(ev.currentTarget);
                var url_style =  $cur_btn.attr('data-url-style');
                var data_url =  $cur_btn.attr('data-url');
                var no_jump = $cur_btn.attr('data-no-jump');

                if(no_jump)
                {
                   if(!utility.auth.is_login())
					{
						if(App.isPaiApp)
						{
							App.openloginpage(function(data)
							{
								if(data.code == '0000')
								{
									utility.refresh_page();
								}
							});
						}
						else
						{
							page_control.navigate_to_page('account/login');
						}



						return;
					}
                }

                switch (url_style)
                {
                    case 'inside':

                        // 跳转原生app模特卡页面
                        if(/model_card/.test(data_url))
                        {
                            var user_id = data_url.split('/')[1];

                            App.nav_to_app_page
                            ({
                                page_type : 'model_card',
                                user_id : user_id
                            });
                            return;
                        }
                        // 跳转原生app模特卡页面
                        if(/zone/.test(data_url))
                        {
                            var user_id = data_url.split('/')[1];

                            App.nav_to_app_page
                            ({
                                page_type : 'cameraman_card',
                                user_id : user_id
                            });
                            return;
                        }

                        page_control.navigate_to_page(data_url);
                        break;

                    case 'outside': 
                        window.location.href = data_url ;
                        break;

                    case 'other':
                        //
                        break;
                }     
            }
            ,
            'tap [data-role="share"]' : function()
            {
                // 弹出层设置

                var self = this;

                if(App.isPaiApp)
                {
                    App.share_card(self.share_data,
                        function(data)
                        {
                            console.log(data);

                        }
                    )
                }
            }
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events: function()
        {
            var self = this;

            self.model
            .on('before:info:fetch', function(response) {

            })
            .on('success:info:fetch', function(response)
            {
                self._render_topic_info(response);
            })
            .on('error:info:fetch', function(response) {

            });

            self.on('update_list', function()
            {
                if (!self.$scroll_view_obj)
                {
                    self._setup_scroll();
                    self.$scroll_view_obj.scrollTo(0, 0);
                    self.$scroll_view_obj.refresh();
                    return;
                }
                self.$scroll_view_obj.refresh();
            });
        },
        _render_topic_info: function(response)
        {
            var self = this;
            var data = self.model.toJSON();
            self.$title.html(data.title);
            var html_str = info_tpl(data);

            // 根据reset判断是否分页
            self.$info['html'](html_str);
            self.trigger('update_list');

            if(response.result_data.data.share_text)
            {

                var share = response.result_data.data.share_text;

                console.log(share);

                self.share_data =
                {
                    url : share.url,
                    pic : share.img,
                    userid : share.user_id,
                    title: share.title,
                    content: share.content,
                    sinacontent : share.sina_content,
                    qrcodeurl: share.qrcodeurl,
                    jscbfunc : 'onShare',
                    sourceid : global_config.analysis_page['topic_info'].pid

                };

                console.log(self.share_data)
            }
        },
        _setup_scroll: function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {
                is_hide_dropdown: true
            });
            self.$scroll_view_obj = view_scroll_obj;
        },
        /**
         * 视图初始化入口
         */
        setup: function()
        {
            var self = this;
            //滚动容器
            self.$container = self.$('[data-role="container"]');
            self.$info = self.$('[data-role="info"]');
            self.$title = self.$('[data-role="title"]');
            //安装事件
            self._setup_events();
            self.refresh();
        },
        render: function()
        {
            var self = this;
            // 调用渲染函数
            View.prototype.render.apply(self);
            self.trigger('render');
            return self;
        },
        refresh: function()
        {
            var self = this;
            self.model.get_info(self.get('topic_id'));
        }
    });
    module.exports = topic_list_view;
});