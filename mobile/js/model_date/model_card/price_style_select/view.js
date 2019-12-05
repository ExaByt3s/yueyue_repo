define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var Scroll = require('../../../common/scroll');
    var choosen_group_view = require('../../../widget/choosen_group/view');
    var Utility = require('../../../common/utility');
    var m_alert = require('../../../ui/m_alert/view');



    var mainTpl = require('./tpl/main.handlebars');


    var action_apply_view = View.extend({
        attrs:{
            template:mainTpl
        },
        events:{
            'tap [data-role=web-page-back]' : function (ev)
            {
                delete window._model_style_id;//不做任何操作返回时将此变量删除，避免返回时对相应风格类型的设置
                page_control.back();
            },
            'tap [data-role=save]' : function (ev)
            {
                var self  = this;
                var select_style = self.style_choosen_group_view.get_value();
                var style_text = '';


                if(self.type == 'main' && select_style.length >3 ){
                    m_alert.show('主风格不能超过3个哦！', 'error', {
                        delay: 800
                    });
                    return
                }

                if(select_style.length <= 0 ){
                    m_alert.show('至少选择1个风格哦！', 'error', {
                        delay: 800
                    });
                    return
                }

                //第一次会有无window._model_style的情况，跳过
                if(window._model_style){
                    //先去掉此种价格的风格（初始化）
                    if(self.style_info && self.style_info.length>0)
                    {
                        //当前选择的类型
                        for(var n=0; n<self.style_info.length; n++)
                        {
                            var this_style = self.style_info[n];
                            console.log('this_style:'+this_style);

                            //把没此风格的从类型数组中删除
                            for(var i=0; i<window._model_style.length; i++)
                            {
                                if(right_trim(this_style) == right_trim(window._model_style[i].style))
                                {
                                    window._model_style.splice(i,1);
                                }
                            }
                        }
                    }
                }


                //重新设置风格选中
                if(select_style && select_style.length>0)
                {
                    //当前选择的类型
                    for(var n=0; n<select_style.length; n++)
                    {
                        var this_select_style = select_style[n];
                        var this_style = this_select_style.style = this_select_style.text;
                        style_text += this_style + ' ';


                        window._model_style.push(this_select_style);
                    }
                }

                // modify hudw 2014.12.6
                // 在返回之前将所有的选择、不可选状态都重置为false状态，目的是确保每次下次进入该页面的状态都是一样的
                var data_model_style = self.model_style;

                //console.table(data_model_style);

                for(var i =0;i<data_model_style.length;i++)
                {
                    data_model_style[i].selected = false;
                    data_model_style[i].disabled = false;
                }

                //console.table(data_model_style);
                //用全局变量传给编辑页数据
                window._model_style_text = style_text;
                page_control.back();

            }
        },

        _setup_events:function(){
            var self = this;

            // 模型事件
            // --------------------
            self.model.on('before:update_info:save', self._update_info_before, self)
                .on('success:update_info:save', self._update_info_success, self)
                .on('error:update_info:save', self._update_info_error, self)
                .on('complete:update_info:save', self._update_info_complete, self);


            if(!self.view_scroll_obj)
            {

                self._setup_scroll();

                self.view_scroll_obj.refresh();
                return;
            }

            self.view_scroll_obj.refresh();
        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;

        },



        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$style = self.$('[data-role=style]');

            // 安装事件
            self._setup_events();



        },






        /**
         * 安装选择组模块
         * @private
         */
        _setup_choosen_group : function()
        {
            var self = this;

            //设置风格选中
            var utility_model_style = self.style_info;
            var data_model_style = self.model_style;

            console.log(self.style_info);
            //console.table(data_model_style);

            if(utility_model_style && utility_model_style.length>0)
            {
                for(var n=0; n<utility_model_style.length; n++)
                {
                    var this_style = utility_model_style[n];

                    for(var i=0; i<data_model_style.length; i++)
                    {
                        var this_model_style = data_model_style[i];

                        if(this_style == this_model_style.text)
                        {
                           this_model_style.selected = true;
                           this_model_style.disabled = false;

                           console.log(this_style)

                        }


                    }
                }
            }

            //风格选择组件
            self.style_choosen_group_view = new choosen_group_view
            ({
                templateModel :
                {
                    list : self.model_style
                },
                btn_per_line : 3, //每行按钮个数
                line_margin : '0px', //行间距
                btn_width : '65px', //按钮宽度
                parentNode: self.$style,
                is_multiply : true
            }).render();

        },

        /**
         * 判断资料是否有改变
         * @private
         */
        _isChageInfo:function(){
            var self = this;
            self._isChange = true;
            self.$saveBtn.prop('disabled', false);

        },

        /**
         * 设置风格信息
         * @param options
         * @return {*}
         * @private
         */
        _set_style_info : function(options){
            var self = this;
            self.style_info = options.style_info;
            self.model_style = options.model_style;
            self.type = options.type;
            return self;
        },


        /**
         * 渲染主模版
         * @return {*}
         */
        render: function() {
            var self = this;

            View.prototype.render.apply(self);
            // 安装选择模块
            self._setup_choosen_group()

            // 选择模块事件绑定
            self.style_choosen_group_view.on('success:selected',function(obj){
                if(obj.selected){

                }else{

                }
                self.view_scroll_obj.refresh();
            })



            return self;
        }

    });

    function right_trim(str)
    {
        return str.replace(/\s*$/, '');
    }

    module.exports = action_apply_view;
});