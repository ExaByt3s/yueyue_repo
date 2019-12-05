/**
 * 我的 视图
 * hudw 2014.8.30
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var ua = require('../frame/ua');
    var View = require('../common/view');
    var main_tpl = require('../mine/tpl/main.handlebars');
    var utility = require('../common/utility');
    var App = require('../common/I_APP');
    var footer = require('../widget/footer/index');
    var pull_down = require('../widget/pull_down/view');
    var Scroll = require('../common/scroll');
    var m_alert = require('../ui/m_alert/view');


    var mine_view = View.extend
    ({
        attrs :
        {
            template : main_tpl
        },
        events :
        {
            // 调试模式，后门开启
            'tap [data-role="debug-header"]' : function()
            {



            },

            'tap [data-role=page-back]' : function (ev)
            {
                //page_control.back();
            },
            'tap [data-role="nav-to-login"]' : function()
            {
                page_control.navigate_to_page('account/login');
            },
            'tap [data-role="nav-to-zone"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                if($(ev.srcElement).hasClass('head-icon'))
                {
                    //return;
                }

                var role = self.model.get('role');

                if(role == 'cameraman')
                {
                    console.log(self.model)
                    page_control.navigate_to_page('zone/'+self.model.get('user_id')+'/cameraman');
                }
                else
                {
                    //page_control.navigate_to_page('zone/'+self.model.get('user_id')+'/model');
                    //page_control.navigate_to_page('model_date/model_card/edit_all');
                    page_control.navigate_to_page('model_date/model_card/edit_all');
                }


            },
            'tap [data-role="act"]' : function()
            {
                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }
                page_control.navigate_to_page('mine/status');
            },
            /**
             * 约拍列表
             */
            'tap [data-role="date"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }

                page_control.navigate_to_page('mine/consider');
            },
            /**
             * 活动劵
             */
            'tap [data-role="nav-to-security"]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }

                page_control.navigate_to_page('act/security');
            },
            /**
             * 提现
             */
            'tap [data-role="get-money-btn"]' : function()
            {
                var self = this;
                //没有绑定手机前不能提现
                if(self.phone && self.phone != '')
                {
                    page_control.navigate_to_page('mine/money/withdrawal');
                }
                else if (self.phone == '')
                {
                    m_alert.show('请先绑定手机','error');

                    setTimeout(function()
                    {
                        page_control.navigate_to_page('account/register/bind_phone');
                    },1000);
                }

            },
            /**
             * 充值
             */
            'tap [data-role="add-value-btn"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('mine/money/recharge/0');
            },
            'tap [data-role="setup"]' : function()
            {
                page_control.navigate_to_page("account/setup");
            },
            'tap [data-role="identity_money"]' : function()
            {
                page_control.navigate_to_page('mine/credit',{available_balance : utility.user.get('available_balance')});

            },
            'tap [data-role="show-chat-list"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return
                }

                if(!App.isPaiApp)
                {
                    console.warn('no App');

                    return;
                }

                App.show_chat_list();
            },
            'tap [data-role="nav-to-login"]' : function()
            {
                page_control.navigate_to_page('account/login');
            },
            'tap [data-role="to-be-model-btn"]' : function()
            {
                var self = this;

                if(confirm('请再次确认，身份暂不支持修改哦。'))
                {
                    window.role = 'model';

                    page_control.navigate_to_page('account/login/model');
                }
            },
            'tap [data-role="to-bill"]' : function()
            {
                page_control.navigate_to_page('mine/money/bill');
            },
            'tap [data-role="credit-level"]' : function()
            {
                page_control.navigate_to_page('mine/authentication_list');
            }


        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;

            self.model.on('before:get_info:fetch',function()
            {
                self.$avaliable_balance.html('金额读取中...');

            }).on('success:get_info:fetch',function(response)
            {
                if(self.pull_down_obj)
                {
                    self.pull_down_obj.set_pull_down_style('loaded')
                }

                self.phone = response.result_data.data.phone;

                self.render_info(response);

                self.render_role_items();

                if(!self.refresh_from_before )
                {
                    // 重置下拉
                    self._drop_reset();
                }



            }).on('error:get_info:fetch',function()
            {
                //m_alert.show('网络异常','error',{delay:1000});
                self.$avaliable_balance.html('数据加载失败');

            });


            self.once('render',function()
            {

                self.is_rendered = true;

                if(!self.footer_obj)
                {
                    self._setup_footer();
                }



            });

        },
        render_info : function(response)
        {
            var self = this;

            utility.user.update_user(response.result_data.data);

            self.$avaliable_balance.html('￥'+utility.user.get('available_balance'));

            //解决头像不闪 2014-12-4 nolest
            var img_obj = new Image();

            img_obj.src = utility.user.get('user_icon');

            img_obj.onload = function()
            {
                self.$login_info.find('[data-role="head-icon"]').find('i').css('background-image','url('+utility.user.get('user_icon')+')');
            };

            self.$login_info.find('[data-role="user-name"]').html(utility.user.get('nickname'));

            self.$login_info.find('[data-role="location-name"]').html(utility.user.get('city_name'));

            if(utility.user.get('ticket_num')>0)
            {
                self.$('[data-role="notice_security_container"]').removeClass('fn-hide');

                self.$('[data-role="notice_security_num"]').html(utility.user.get('ticket_num'));
            }
            else
            {
                self.$('[data-role="notice_security_container"]').addClass('fn-hide');

                self.$('[data-role="notice_security_num"]').html(0);
            }



            /*var time = 200;

            self._reset_scroll_to(time);*/

            if(!self.view_scroll_obj)
            {
                //主要滚动条
                self._setup_scroll(utility.auth.is_login()?false:true);

                self.view_scroll_obj.refresh();

                return;
            }

            self.view_scroll_obj.refresh();




        },
        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function(is_hide_dropdown)
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    _startY : 45,
                    is_hide_dropdown : is_hide_dropdown
                });

            self.view_scroll_obj = view_scroll_obj;

            // 下拉完成
            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

            // 拉动结束时
            /*self.view_scroll_obj.on('touchEnd', function()
            {
                if(!utility.login_id)
                {
                    return;
                }

                if (!self._refresh_bar_state) {
                    return;
                }

                var _scroll_obj = this;

                //self._pull_refresh();

                _scroll_obj.minScrollY = 0;
                self._refresh_bar_state = null;

            })
            // 拖拉中
            .on('scrollMove', function()
            {
                var _scroll_obj = this;

                if(!utility.login_id)
                {
                    return;
                }

                if (_scroll_obj.y > 60)
                {

                    if (!self._refresh_bar_state)
                    {
                        self.pull_down_obj.set_pull_down_style('release');
                        self._refresh_bar_state = 'ready';
                    }

                    return;
                }



                if (!self._refresh_bar_state)
                {
                    return;
                }

                self.pull_down_obj.set_pull_down_style('loaded');
                self._refresh_bar_state = null;
            });*/
        },

        /**
         * 安装底部导航
         * @private
         */
        _setup_footer : function()
        {
            var self = this;

            self.footer_obj = new footer
            ({
                // 元素插入位置
                parentNode: self.$el,
                // 模板参数对象
                templateModel :
                {
                    // 高亮设置参数
                    is_my_pai : true
                }
            }).render();
        },
        /**
         *
         * @private
         */
        _setup_pull_down : function()
        {
            var self = this

            self.pull_down_obj = new pull_down
            ({
                parentNode:self.$refresh_top_bar,
                templateModel:
                {
                    top : -65
                }
            }).render();
        },

        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 安装事件
            self._setup_events();

            self.recent_icon_url = '';
            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器

            self.mid_container =self.$('[data-role=scoll-wrapper]');

            self.$avaliable_balance = self.$('[data-role="availiable-balance"]');

            self.$login_info = self.$('[data-role="login-info"]');

            self.$no_login_info = self.$('[data-role="no-login-info"]');

            self.$setup_btn = self.$('[data-role="setup"]');

            self.$change_role_btn = self.$('[data-role="change-btn"]');

            self.$refresh_top_bar = self.$('[data-role=refresh-bar-container]');

            self.$appver_update_point = self.$('[data-appver-update-point]');

            //self._setup_pull_down();

            self.render_role_items();

            // 检查是否有红点更新
            App.isPaiApp && App.app_info(function(data)
            {
                if(utility.int(data.appupdate) == 1)
                {
                    self.$appver_update_point.removeClass('fn-hide');
                }
                else
                {
                    self.$appver_update_point.addClass('fn-hide');
                }
            });




        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        refresh : function(tag)
        {
            var self = this;

            if(utility.auth.is_login())
            {
                self.model.get_info();

                self.refresh_from_before = tag;

                self.$login_info.removeClass('fn-hide');
                self.$no_login_info.addClass('fn-hide');
                //self.$setup_btn.css('visibility','visible');
            }
            else
            {
                self.$no_login_info.removeClass('fn-hide');
                self.$login_info.addClass('fn-hide');
                //self.$setup_btn.css('visibility','hidden');
            }

        },
        render_role_items : function()
        {
            var self = this;

            var role = utility.user.get('role');



            if(utility.user.get('role') == 'cameraman')
            {
                self.$('[data-role-name="'+role+'"]').removeClass('fn-hide');
                self.$('[data-role-name="model"]').addClass('fn-hide');
                self.$('[data-role="credit-level"]').removeClass('fn-hide');
                

            }
            else if(utility.user.get('role') == 'model')
            {
                self.$('[data-role-name="'+role+'"]').removeClass('fn-hide');
                self.$('[data-role-name="cameraman"]').addClass('fn-hide');

            }
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 50;
        },
        _pull_refresh: function()
        {

            var self = this;

            self.pull_down_obj.set_pull_down_style('loading');

            self.view_scroll_obj.minScrollY = 60;
            self.view_scroll_obj.scrollTo(0, 60);
            self.refresh();
        },
        _reset_scroll_to: function(time)
        {
            var self = this;
            self.view_scroll_obj.minScrollY = 0;
            self.view_scroll_obj.scrollTo(0, 0, time);
        }

    });

    module.exports = mine_view;
});