/**
 * Created by nolestLam on 2014/8/26.
 */
/**
 * 按钮组
 * templateModel :
 * {
 *    list : list //对象arr [{...},{...},{...}]
 * }
 * [int]btn_per_line : 2, //每行按钮个数
 * [string]line_margin : '0px 0px 15px 0px', //每行margin
 * [string]btn_width : '143px', //按钮宽度
 * [obj]parentNode: self.$btn_container,
 * [Boolean]is_multiply : false
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var item_tpl = require('./tpl/choosen_group.handlebars');


    module.exports = View.extend
    ({
        attrs :
        {
            template : item_tpl,
            css  : '',
            is_multiply : false
        },
        events :
        {
            'tap [data-role=ui-choosen-btn]' : function(ev)
            {
                var self = this;


                var $cur_btn = $(ev.currentTarget);

                var selected_value = $cur_btn.attr('data-selected-value');
                var id = $cur_btn.attr('data-selected-id');
                var params = $cur_btn.attr('data-selected-params');
                var remark = $cur_btn.attr('data-selected-remark');
                var disabled = $cur_btn.attr('data-disabled');
                var readonly = $cur_btn.attr('data-readonly');
                var normal_status = $cur_btn.attr('data-normal_status');

                var is_multiply = self.is_multiply;

                if(disabled)
                {
                    return;
                }

                if(readonly)
                {
                    return;
                }

                if(!is_multiply)
                {
                    self._clear_btn_selected();
                }

                if(utility.int(normal_status) !=2 || utility.int(normal_status) !=3)
                {
                    self._set_btn_selected($cur_btn);
                }

                self.trigger('success:selected',
                    {
                        value : selected_value,
                        id:id,
                        selected:$cur_btn.hasClass('current'),
                        params : params,
                        remark : remark,
                        normal_status : normal_status
                    });
            }
        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            var divide_obj =
            {
                list:[],
                btn_width : self.get('btn_width'), //按钮宽度
                line_margin : self.get("line_margin") //行间距
            };

            var list_length = template_model.list.length;

            var btn_per_line = self.get('btn_per_line')?self.get('btn_per_line'):3;

            var line_num = Math.ceil(list_length/btn_per_line);

            for(var i = 0;i < line_num;i++)
            {
                divide_obj.list.push(template_model.list.slice(btn_per_line*i,btn_per_line*(i+1)))
            }

            self.set('templateModel', divide_obj);

            View.prototype._parseElement.apply(self);
        },
        _setup_events : function()
        {
            var self= this;

        },
        setup : function()
        {
            var self = this;

            // 是否多选
            self.is_multiply = self.get('is_multiply') || false;

            // 所有的选择模块
            self.$choosen_btn = self.$('[data-role=ui-choosen-btn]');

            self._setup_events();

            // 设置样式
            self._set_item_size();
        },
        get_obj_by_idx : function(idx)
        {
            var self = this;

            return self.$choosen_btn.eq(idx);
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
        _set_item_size : function()
        {
            var self = this;

            var css = self.get('css');

            if(css)
            {
                self.$choosen_btn.addClass(css);
            }

        },
        /**
         * 设置按钮选中
         * @param btn
         * @private
         */
        _set_btn_selected : function(btn)
        {
            var $btn = btn;

            $btn.toggleClass('current');

        },
        /**
         * 清除选中按钮样式
         * @private
         */
        _clear_btn_selected : function()
        {
            var self = this;

            self.$choosen_btn.removeClass('current');
        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;

        },
        /**
         * 获取选中的值
         * @returns {Array}
         */
        get_value : function(key)
        {
            var self = this;

            var value_arr = [];


            var $selected_btn_group = self.$el.find('.current[data-role=ui-choosen-btn]');

            $selected_btn_group.each(function(i,obj)
            {
                value_arr.push
                ({
                    text : $(obj).attr('data-selected-value'),
                    id :  $(obj).attr('data-selected-id'),
                    params : $(obj).attr('data-selected-params')
                })
            });

            return value_arr;
        },
        /**
         * 获取选中个数
         * @returns {Number}
         */
        get_selected_count : function()
        {
            var self = this;

            var value_arr = self.get_selected_value();

            return value_arr.length;
        }
    });
});
