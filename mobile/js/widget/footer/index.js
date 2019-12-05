define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var system = require('../../common/system');
    var App = require('../../common/I_APP');
    var templateHelpers = require('../../common/template-helpers');
    var footer_tpl = require('./tpl/footer.handlebars');
    var n =0 ;

    /**
     * 传入模板参数设置高亮
     * @ is_out_pai   外拍活动
     * @ is_model_pai 模特约拍
     * @ is_my_pai    我的
     */

    var footer = View.extend
    ({
        attrs :
        {
            template : footer_tpl
        },
        events :
        {
            'tap [data-role="nav"]' : function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                var nav_url = $cur_btn.attr('data-nav-url');

                if(nav_url == 'message')
                {
                    if(!utility.login_id)
                    {
                        page_control.navigate_to_page('account/login');

                        return;
                    }

                    App.isPaiApp && App.show_chat_list();

                }

                page_control.navigate_to_page(nav_url);
            }
        },
        /**
         * 构建元素
         * @private
         */
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);
        },
        /**
         * 事件安装
         * @private
         */
        _setup_events : function()
        {
            var self = this;

            //self.update_count({num:5});

            $_AppCallJSObj.on('show_notice_num',function(event,data)
            {
                self.update_count(data);
            });

            self.on('render',function()
            {

               /* n++;

                self.update_count({num : 0});*/

                App.isPaiApp && App.get_chat_info(function(data)
                {
                    var num = data.unread_msgcount;

                    self.update_count({num : num});
                });
            });

        },
        _navigate_page : function()
        {

        },
        setup : function()
        {
            var self = this;

            if(App.isPaiApp)
            {
                App.save_device(true,function(data)
                {
                    if(data.appver>'1.0.0')
                    {
                        self.$el.addClass('fn-hide');
                    }

                })

            }


            footer.all_bars.push(self);

            self._setup_events();


        },
        update_count : function(data)
        {
            var self = this;




            var sys_msg = utility.int(data.num);
            var act_msg = utility.int(utility.user.get('ticket_num')) || 0;
            var total = 0;

            total = sys_msg;

            var $footer_num = self.$('[data-flag="footer-msg"]').find('[data-role="num"]');


            if($footer_num.length>0)
            {
                if(total>0)
                {

                    $footer_num.removeClass('fn-hide').html(total);
                }
                else
                {

                    $footer_num.addClass('fn-hide').html(0);
                }

            }



            // 更新个人中心消息数
            system.update_mine_msg
            ({
                total : total,
                sys_msg : sys_msg,
                act_msg : act_msg
            });

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

    module.exports = footer;

    footer.all_bars = [];

    function each_tabbars(fn)
    {
        var cache_bars = footer.all_bars;
        for (var i = 0, len = cache_bars.length; i < len; i++)
        {
            fn.call(cache_bars, cache_bars[i], i);
        }
    }



});