/**
 * Created by nolest on 2014/9/3.
 *
 *
 *
 *
 *
 *  介绍页'添加场次'模块
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var tpl = require('./tpl/main.handlebars');
    var select_view = require('../select/view');
    var number_btn_view = require('../number_btn/view');
    var m_select = require('../../ui/m_select/view');

    module.exports = View.extend
    ({
        attrs :
        {
            template : tpl
        },
        events :
        {

            'tap [data-role="begin-time"]': function()
            {
                var self = this;

                self['begin_time'].show();
            },
            'tap [data-role="end-time"]' : function()
            {
                var self = this;

                if(!self['begin_time'].is_selected)
                {
                    alert('请先选择开始时间');

                    return;
                }

                self['end_time'].show();
            }
        },
        /**
         * 设置下拉选择
         * @private
         */
        _setup_select : function()
        {
            var self = this;

            var date_arr = utility.select_time.mix_date().date_arr;
            var hour_arr = utility.select_time.mix_date().hour_arr;
            var min_arr = utility.select_time.mix_date().min_arr;

            self['begin_time'] = new m_select
            ({
                templateModel :
                {
                    options : [date_arr,hour_arr,min_arr]

                },
                parentNode: self.parentNode
            }).render();

            self['end_time'] = new m_select
            ({
                templateModel :
                {
                    options : [date_arr,hour_arr,min_arr]
                },
                parentNode: self.parentNode
            }).render();


            // 开始时间确认
            self['begin_time'].on('confirm:success',function(arr)
            {
                self.$('[data-role="select_container_for_hour_begin"]').html(arr[0].value+" "+arr[1].value+":"+arr[2].value);

                self['begin_time'].is_selected = true;
            });

            // 结束时间确认
            self['end_time'].on('confirm:success',function(arr)
            {

                var t1_arr = self['begin_time'].get_value();
                var t1 = t1_arr[0].value+" "+t1_arr[1].value+":"+t1_arr[2].value;

                var t2 = arr[0].value+" "+arr[1].value+":"+arr[2].value;

                if(utility.is_bigger_time(t1,t2))
                {
                    alert('开始时间不能大于结束时间');

                    return;
                }
                else if(t1 == t2)
                {
                    alert('开始时间不能和结束时间一样');

                    return;
                }


                self.$('[data-role="select_container_for_hour"]').html(t2);

                self['end_time'].is_selected = true;
            });



        },
        get_selected_obj : function()
        {
            var self = this;

            var member = self['member'].get_value();

            var start_time_arr = self['begin_time'].get_value();

            var end_time_arr = self['end_time'].get_value();

            var start_time = start_time_arr[0].value+" "+start_time_arr[1].value+":"+start_time_arr[2].value;

            var end_time = end_time_arr[0].value+" "+end_time_arr[1].value+":"+end_time_arr[2].value;


            var obj =
            {
                num : member,
                begin_time : self['begin_time'].is_selected ?start_time : 0,
                end_time : self['end_time'].is_selected? end_time :0
            };

            return obj
        },
        _setup_number_btn : function()
        {
            var self = this;

            self['member'] = new number_btn_view
            ({
                templateModel :
                {
                    type : 'tel'
                },
                min_value : 0,
                step : 1,
                parentNode: self.$number_menber,
                value : 1,
                is_floor : true
            }).render();

        },
        _setup_events : function()
        {

        },
        _set_arrange_panel_count : function()
        {
            var self = this;

            self.$('[data-role="pick-close-panel"]').attr("arrange_panel_count",self.arrange_panel_count);

            self.set("arrange_panel_count",self.arrange_panel_count);

        },
        setup : function()
        {
            var self = this;

            // 安装事件
            self._setup_events();

            self.$select_container_for_hour = self.$('[data-role="select_container_for_hour"]');

            self.$select_container_for_hour_begin = self.$('[data-role="select_container_for_hour_begin"]');

            self.$number_menber = self.$('[data-role="number_container_for_menber"]');

            self.arrange_panel_count = self.get("arrange_panel_count");

            self._set_arrange_panel_count();

            self._setup_select();

            self._setup_number_btn();

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

            return self;
        }
    });
});
