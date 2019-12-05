/**
 * Created by admin on 14-12-29.
 */
/**
 * 关注按钮
 * hdw 2014.8.18
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var global_config = require('../../common/global_config');
    var follow_tpl = require('./main.handlebars');

    module.exports = View.extend
    ({
        attrs :
        {
            template : follow_tpl
        },
        events :
        {

        },
        setup : function()
        {
            var self = this;

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
        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            self._render_item({key : self.get('follow_status')});

            return self;
        },
        _render_item : function(data)
        {
            var self = this;

            var key = data.key || '';

            var tpl = '';

            // 清空旧状态
            self.$el.html('');

            // 0 代表未关注
            // 1 代表已经关注
            // 2 代表相互关注

            switch (utility.int(key))
            {
                case 2:
                    tpl = '<div class="ui-follow-mod ui-eachotherfollow-mod" data-role="focus" data-focus-type="each"><i class="icon icon-ui-eachotherfollow-m"></i> <span class="txt">相互关注</span></div>'
                    break;
                case 1:
                    tpl = '<div class="ui-follow-mod ui-hasfollow-mod" data-role="focus" data-focus-type="focused"><i class="icon icon-ui-hasfollow-m"></i> <span class="txt">已关注</span></div>'
                    break;
                case 0:
                    tpl = '<div class="ui-follow-mod" data-role="focus" data-focus-type="to_focuse"><i class="icon icon-ui-follow-m"></i> <span class="txt">关注</span></div>'
                    break;
            }

            self.$el.html(tpl);

            return self;

        },
        follow_action : function(data,opt)
        {
            var self = this;

            utility.ajax_request
            ({
                url : global_config.ajax_url.follow_user,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch_follow',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:fetch_follow',collection,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:fetch_follow',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch_follow',xhr,status);
                }
            })
        }
    });
});