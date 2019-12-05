
/**
 * nolestLam 2014/8/19.
 */
/**
 * 下拉列表组件
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var select_drop_tpl = require('./tpl/select_drop.handlebars');
    var page_control = require('../../frame/page_control');
    var App = require('../../common/I_APP');

    module.exports = View.extend
    ({
        attrs :
        {
            template : select_drop_tpl
        },
        events :
        {
            'tap [data-role="sys"]' : function()
            {
                var self = this;

                if(self.is_drop())
                {
                    console.log("扫一扫");
                    if(App.isPaiApp)
                    {
                        App.qrcodescan();

                        self.pull_up();
                    }
                }

            },
            'tap [data-role="go-search"]' : function()
            {
                var self = this;

                if(self.is_drop()) {
                    self.pull_up();

                    setTimeout(function () {
                        page_control.navigate_to_page('search');
                    }, 100)
                }
            },
            'tap [data-role="module-card"]' : function()
            {
                page_control.navigate_to_page("model_date/model_card/" + utility.user.get('user_id'));
            },
            'tap [data-role="go-act"]' : function()
            {
                //发活动
                page_control.navigate_to_page("act/pub_info",utility.user);
            },
            'touch [data-role="more-select-drop-cover"]' : function()
            {
                var self = this;

                self.pull_up();
            },
            'webkitAnimationEnd' : function()
            {
                var self = this;

                self._is_animating = false;

                //防止返回后再次出现上拉动画
                self.$select_drop.removeClass("pull-up");

                if(!self._is_dropped)
                {
                    self.$select_drop.addClass('fn-hide').removeClass('pin');

                }
                else
                {
                    self.$select_drop.addClass('pin').removeClass('fn-hide');

                }
            }
        },
        _setup_event : function()
        {
            var self = this;

        },
        setup : function()
        {
            var self = this;

            self.cover = self.$('[data-role="more-select-drop-cover"]');

            self._is_dropped = false;

            self._is_animating = false;

            self.$select_drop = self.$('[data-role="more-select-drop"]');

            self._setup_event();

        },
        is_drop : function()
        {
            //是否已下拉
            var self = this;

            return self._is_dropped;
        },
        drop_down : function()
        {
            //下拉
            var self = this;

            if(self._is_animating)
            {
                // 防止重复显示动画
                return;
            }

            self.$select_drop.removeClass("pull-up drop-down-stay fn-hide").addClass("drop-down");

            self._is_animating = true;

            self._is_dropped = true;

            self.cover.removeClass('fn-hide');
        },
        pull_up : function()
        {
            //上拉
            var self = this;

            if(self._is_animating)
            {
                // 防止重复显示动画
                return;
            }

            self.$select_drop.removeClass("drop-down-stay pin fn-hide").removeClass("drop-down").addClass("pull-up");

            self._is_animating = true;

            self._is_dropped = false;

            self.cover.addClass('fn-hide');
        },
        /* 在调用页面的index page_hide 内调用此方法
         * 用于固定下拉菜单
         */
        stay : function()
        {
            var self = this;

            if(self._is_dropped)
            {
                self.$select_drop.removeClass("drop-down").addClass('drop-down-stay');
            }
        }
    });
});