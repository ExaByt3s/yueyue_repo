/**
 * 针对移动端的下拉选择框
 * hdw 2014.10.8
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var scroll = require('../../common/new_iscroll');
    var select_tpl = require('./tpl/m_select.handlebars');
    var options_tpl = require('./tpl/m_options.handlebars');

    module.exports = View.extend
    ({
        attrs :
        {
            template : select_tpl
        },
        events:
        {
            'webkitTransitionEnd': function(event)
            {

            },
            /**
             * 确认
             */
            'tap [data-role="confirm"]' : function()
            {
                var self = this;

                self.trigger('confirm:success',self.get_value());

                self.hide();
            },
            /**
             * 取消
             */
            'tap [data-role="cancel"]' : function()
            {
                var self = this;

                self.hide();
            },
            'tap [data-role="selected-area"]' : function()
            {
                var self = this;

                self.trigger('confirm:success',self.get_value());

                self.hide();
            }
        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);


        },
        set_value : function(data)
        {
            var self = this;

            self.selected_data = data
        },
        set_data_select : function(key)
        {
            var self = this;

            var li = self.$warpper.find('li');

            li.eq(key).attr('selected',"");
        },
        /**
         * 获取选中值
         */
        get_value : function()
        {
            var self = this;

            var $selected_obj_arr = self.$warpper.find('li[selected]');

            var val_arr = [];

            for(var n=0;n<$selected_obj_arr.length;n++)
            {
                var $selected_obj = $selected_obj_arr.eq(n);

                var value = $selected_obj.attr('data-value');
                var id = $selected_obj.attr('data-id');
                var text  = $selected_obj.html();

                val_arr[n] = {value :value,text:text,id:id};
            }

            return val_arr;


        },
        set_options : function(data,scroll_idx)
        {
            var self = this;

            var scroll_idx = scroll_idx || 0;

            var options_html_str = options_tpl
            ({
                options : data
            });

            // 重新清除
            self.$scroll_wrapper_groups.eq(scroll_idx) && self.$scroll_wrapper_groups.eq(scroll_idx).remove();

            self.$scroll_view_obj['scroll_'+scroll_idx] = null;

            self.$warpper.append(options_html_str);

            self.$scroll_wrapper_groups = self.$('[data-role="scroll_warpper"]');

            self._setup_scroll();


        },
        setup : function()
        {
            var self = this;

            self.$warpper = self.$('[data-role=select-wrapper]');

            self.$container = self.$('[data-role="content"]');

            self.$warpper.height(245);

            var template_model = self.get('templateModel');

            self.$scroll_view_obj = {};

            // 渲染下拉选择列表

            var options_html_str = options_tpl
            ({
                options : template_model.options
            });

            self.$warpper.html(options_html_str);

            self.$scroll_wrapper_groups = self.$('[data-role="scroll_warpper"]');

            self._setup_scroll();

        },
        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll : function()
        {
            var self = this;

            self.$scroll_wrappers = [];

            self.is_init = true;

            self.$scroll_wrapper_groups.each(function(i,obj)
            {
                self.$scroll_wrappers[i] = $(obj);

                if(!self.$scroll_view_obj['scroll_'+i])
                {
                    self.$scroll_wrappers[i].height(36);

                    // 初始化滚动条

                    self.$scroll_view_obj['scroll_'+i] = scroll(self.$scroll_wrappers[i],
                        {
                            bounce: false,
                            snap:"li",
                            momentum: false,
                            hScroll : false,
                            vScroll : true,
                            hScrollbar: false,
                            vScrollbar: false,
                            checkDOMChanges: true
                        });

                    self.$scroll_wrappers[i].css('overflow','');

                    self.$scroll_view_obj['scroll_'+i].index = i;


                    self.$scroll_view_obj['scroll_'+i].on('scrollMove',function()
                    {
                        self.is_init = false;

                    });

                    // 选中触发
                    self.$scroll_view_obj['scroll_'+i].on('scrollEnd',function()
                    {
                        if(self.is_init)
                        {
                            return;
                        }

                        var _self = this;

                        // 设置选中
                        var $li = $(_self.wrapper).find('li');

                        $li.removeAttr('selected');

                        $li.eq(this.currPageY).attr('selected','true');

                        setTimeout(function()
                        {
                            self.$warpper.find('ul').css('background-color','#fff');
                        },10);


                        // 多个选项选的时候
                        if(self.$scroll_wrappers.length>1)
                        {
                            var val_arr = [];

                            //self.$warpper.find('ul').css('background-color','#f90');

                            var $selected_obj_arr = self.$warpper.find('li[selected]');

                            for(var n=0;n<$selected_obj_arr.length;n++)
                            {
                                var $selected_obj = $selected_obj_arr.eq(n);

                                var value = $selected_obj.attr('data-value');
                                var text  = $selected_obj.html();
                                var id = $selected_obj.attr('data-id');

                                val_arr[n] = {value :value,text:text,id:id};
                            }

                            if(self.is_show)
                            {
                                self.trigger('change:options',val_arr,_self);
                            }

                        }
                        else
                        {
                            var value = $li.eq(this.currPageY).attr('data-value');
                            var text  = $li.eq(this.currPageY).html();
                            var id = $li.eq(this.currPageY).attr('data-id');

                            self.trigger('change:options',[{value :value,text:text,id:id}],_self);
                        }




                    });
                }

            });
        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');

            for(var i =0;i<self.$scroll_wrappers.length;i++)
            {

                self.$scroll_view_obj['scroll_'+i].refresh();


            }

            // 此处目的是为了延时触发change:options事件
            setTimeout(function()
            {

                self.is_show = true;

                // 第二次循环滚动条组，为了实现滚动到指定位置，但不是最好做法
                for(var i =0;i<self.$scroll_wrappers.length;i++)
                {
                    var idx_obj = self.$scroll_wrappers[i].find('li[selected="true"]');

                    self.$scroll_view_obj['scroll_'+i].scrollToElement(idx_obj[0],0);

                }

            },0);

            $('body').css('pointer-events','none');

            self.$el.css('pointer-events','auto');
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');

            self.is_show = false;

            setTimeout(function()
            {
                $('body').css('pointer-events','auto');

            },500)


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