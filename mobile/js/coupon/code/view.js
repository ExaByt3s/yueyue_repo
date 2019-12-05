/**
 * Created by nolestLam on 2015/3/5.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var page_control = require('../../frame/page_control');
    var coupon_tpl = require('./tpl/main.handlebars');
    var m_alert = require('../../ui/m_alert/view');
    var Scroll = require('../../common/scroll');
    var global_config = require('../../common/global_config');


    var coupon_code_view = View.extend
    ({
        attrs:
        {
            template: coupon_tpl
        },
        events :
        {
            'tap [data-role="page-back"]' : function()
            {
                page_control.back();
            },
            'tap [data-role="code_btn"]' : function()
            {
                var self = this;

                if(!self.fetching)self._submit_request();

            }
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$scroll_container,{
                lazyLoad : false,
                _startY : 45,
                prevent_tag : 'slider',
                is_hide_dropdown : true,
                down_direction : 'down',
                down_direction_distance :50
            });

            self.view_scroll_obj = view_scroll_obj;
        },
        _submit_request : function()
        {
            var self = this;

            var code = self.$('[data-role="code_input"]').val()

            if(code.trim() == "")
            {
                m_alert.show('请输入消费码','error');
            }
            else
            {
                self.model.give_coupon(code);
            }

        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                self._setup_scroll();
            });
            self.model
                .on('before:give_coupon:fetch',function(response,options)
                {
                    self.fetching = true;
                    m_alert.show('兑换中...','loading');
                })
                .on('success:give_coupon:fetch',function(response,options)
                {
                    console.log(response);

                    m_alert.show(response.result_data.list.message,'right',{delay:1500});

                    setTimeout(function()
                    {
                        // modify by hudw 2015.3.17
                        // 兑换成功后跳转到优惠劵详情页
                        if(response.result_data && response.result_data.code == 1)
                        {
                            if(response.result_data.list)
                            {
                                var coupon_sn = response.result_data.list.coupon_sn;

                                page_control.navigate_to_page('coupon/details/'+coupon_sn);
                            }
                        }
                    },1500);



                })
                .on('error:give_coupon:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:give_coupon:fetch',function(response,options)
                {
                    self.fetching = false;
                })
        },
        _render_coupon : function(data)
        {

        },
        setup : function()
        {
            var self = this;

            self.$scroll_container = self.$('[data-role="content-body"]');

            self.$container = self.$('[data-role="list-container"]');

            self._setup_events();

            self.fetching = false;

        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = coupon_code_view;
});