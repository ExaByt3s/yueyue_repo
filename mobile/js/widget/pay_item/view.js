/**
 * 支付选项表单
 * hdw 2014.8.26
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var item_tpl = require('./tpl/items.handlebars');
    var pay_item_model = require('./model');


    module.exports = View.extend
    ({
        attrs :
        {
            template : item_tpl

        },
        events :
        {
            // 选择信用金
            'tap [data-role="select-cameraman-require-row"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

                if($yes_tag.hasClass('current'))
                {
                    $yes_tag.removeClass('current');

                    self._is_select_balance_require = 0;
                }
                else
                {
                    $yes_tag.addClass('current');

                    self._is_select_balance_require = 1;
                }

                self.model.set('can_use_balance',self._is_select_balance_require);

                self.trigger('selected:available_balance');

            },
            // 选择支付类型
            'tap [data-role="choosen-pay-type"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

                var pay_type = $cur_btn.attr('data-type');

                self.model.set('pay_type',pay_type);

                self._select_pay_type(pay_type);
            },
            // 选择优惠劵
            'tap [data-role="select-coupon-require-row"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

                self.trigger('selected:coupon');

            }
        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);
        },
        _setup_events : function()
        {
            var self= this;

            self.listenTo(self.model,'change',function(obj)
            {
                self.trigger('selected:change',obj);

                self._render_pay_items();
            });

        },
        set_value : function(data)
        {
            var self = this;

            self.model.set(data);

        },
        get_value : function()
        {
            var self = this;

            return self.model;

        },
        _select_pay_time : function(type)
        {
            var self = this;


        },
        _select_pay_type : function(type)
        {
            var self = this;

            self.$pay_type_group.find('[data-role="yes-tag"]').removeClass('current');

            self.$('[data-type="'+type+'"]').find('[data-role="yes-tag"]').addClass('current');

            self._pay_type = type;

            self.model.set('pay_type', self._pay_type);

            self.trigger('selected:pay_type');
        },
        _render_pay_items : function()
        {
            var self = this;

            self.$total_price_container.html(self.model.get('total_price'));
            self.$pay_price_container.html(self.model.get('need_price'));
            self.$available_balance_container.html(self.model.get('available_balance'));

            // 设置优惠劵可用
            if(self.model.get('use_coupon'))
            {
                self.$coupon_container.find('[data-role="coupon-money"]').html('-￥'+self.model.get('coupon_info').face_value);
                self.$coupon_container.find('[data-role="coupon-text"]').html('('+self.model.get('coupon_info').batch_name+')');
                self.$coupon_container.find('[data-role="r"]').removeClass('fn-hide');
                self.$coupon_container.find('[data-role="yes-tag"]').addClass('fn-hide');
            }
            else
            {
                self.$coupon_container.find('[data-role="coupon-money"]').html('');
                self.$coupon_container.find('[data-role="coupon-text"]').html('');
                self.$coupon_container.find('[data-role="r"]').addClass('fn-hide');
                self.$coupon_container.find('[data-role="yes-tag"]').removeClass('fn-hide');
            }

            // 设置显示剩余的钱
            if(self.model.get('less_money'))
            {
                self.$less_money.removeClass('fn-hide');

                self.$less_money.html('-￥'+self.model.get('less_money'));
            }
            else if(self.model.get('less_money') == 0)
            {
                self.$less_money.addClass('fn-hide');
            }

            self.$use_balance_tag.removeClass('current');

            if(self.model.get('can_use_balance'))
            {
                self.$use_balance_tag.addClass('current');
            }

        },
        setup : function()
        {
            var self = this;

            self.$pay_price_container = self.$('[data-role=pay-price]');
            self.$pay_type_group = self.$('[data-role="choosen-pay-type"]');
            self.$other_pay_items = self.$('[data-role="other-pay-items"]');
            self.$total_price_container = self.$('[data-role=pay-details-price-info-line-price]');
            self.$use_balance_tag = self.$('[data-role="select-cameraman-require-row"]').find('[data-role=yes-tag]');
            self.$available_balance_container = self.$('[data-role="available-balance-str"]');
            self.$coupon_container = self.$('[data-role="select-coupon-require-row"]');
            self.$less_money = self.$('[data-role="less-money"]');

            self.model = new pay_item_model
            ({
                can_use_balance : self.get('templateModel').can_use_balance,
                total_price :self.get('templateModel').total_price ,
                available_balance : self.get('templateModel').available_balance,
                less_money : self.get('templateModel').less_money,
                is_support_outtime : self.get('templateModel').is_support_outtime,
                is_support_coupon : (self.get('templateModel').is_support_coupon == null) ? true : false ,
                is_support_now_out : self.get('templateModel').is_support_now_out
            });


            // 安装事件
            self._setup_events();

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
        show_other_pay_items : function()
        {
            var self = this;

            self.$other_pay_items.removeClass('fn-hide');
        },
        hide_other_pay_items : function()
        {
            var self = this;

            self.$other_pay_items.addClass('fn-hide');
        },
        /**
         * 根据指定类型隐藏支付选项
         * @param type
         */
        hide_pay_items_by_type : function(type)
        {
            var self = this;

            self.$el.find('[data-type="'+type+'"]').addClass('fn-hide');
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
});
