define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    
    var Scroll = require('../../../common/scroll');
    var choosen_group_view = require('../../../widget/choosen_group/view');
    var model_style_card = require('../../../widget/model_style_card/view');
    var number_btn_view = require('../../../widget/number_btn/view');
    var upload_pic = require('../../../widget/upload_pic/view');
    var Utility = require('../../../common/utility');
    var Tip = require('../../../ui/m_alert/view');
    var m_select = require('../../../ui/m_select/view');
    var App = require('../../../common/I_APP');


    var mainTpl = require('./tpl/main.handlebars');
    var numberBtnTpl = require('./tpl/number_btn_item.handlebars');


    var action_apply_view = View.extend({
        attrs:{
            template:mainTpl
        },
        events:{
            'tap [data-role=navigate]' : '_navigateToPage',
            'tap [data-role=complete]' : '_save_info',
            'tap [data-role=add-other-style]' : '_add_other_style',
            'tap [data-role=minus-other-style]' : '_minus_other_style',
            'tap [data-role=set-hour]' : 'set_model_style_hour',
            'tap [data-role=to-select-style]' : 'to_select_model_style',
            'tap [data-role=web-page-back]' : function (ev)
            {
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


            //安装风格价格
            self._setup_model_style_card(Utility.user.get('model_style_combo').main,'price-style-list-main');

            if(Utility.user.get('model_style_combo').other)
            {
                self._setup_model_style_card(Utility.user.get('model_style_combo').other,'price-style-list-other');
            }







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


            self.$complete_btn = self.$('[data-role=complete]');

            self.$style = self.$('[data-role=style]');
            self.$price_style_list_other = self.$('[data-role=price-style-list-other]')//其他风格容器
            self.$money = self.$('[data-role=money]');
            //self.$price = self.$('[data-role=price]');
            self.$price_out = self.$('[data-role=price-out]');
            self.$max_person = self.$('[data-role=max-person]');
            self.$remark = self.$('[data-role=remark]');


            //所有已选风格
            if(!Utility.user.get('model_style_v2')){
                window._model_style = [];
            }else{
                //复制一个model_style_v2且让以后的操作不改变model_style_v2，修复后退再进来时之前的操作对model_style_v2的影响
                window._model_style = [];
                window._model_style = Utility.user.get('model_style_v2').slice(0);
                //window._model_style = Utility.user.get('model_style_v2');
            }

            //风格卡id数组
            self.model_style_card_id_arr = [];

            self.select_style_hour_view = {};

            //风格选择小时数据
            // modify hudw
            // 将style_hour_arr 变量设置为self 私有的
            self.style_hour_arr = [
                { 'text' : '元/4小时' , 'value' : '4'},
                { 'text' : '元/2小时' , 'value' : '2'}
            ];

            // 安装事件
            self._setup_events();

            //self.view_scroll_obj.refresh();


        },

        /**
         * 渲染数字按钮
         * @param style
         * @param id
         * @returns {*}
         * @private
         */
        _render_number_btn: function(style,id) {
            var self = this;
            var htmlStr = numberBtnTpl({
                style:style,
                id:id
            });

            var  $htmlStr = $(htmlStr)
            //生成并插入主模板
            self.$price_out.append($htmlStr);

            var $price = $htmlStr.find('[data-role=price]')

            //插入按钮组件
            var options = {};
            options.min_value = 10;
            options.max_value = 10000;
            options.step = 100;
            options.parentNode = $price;
            options.value = 100;
            options.id = id;
            self._setup_number_btn(options);

            self.view_scroll_obj.refresh();

            return htmlStr;
        },


        /**
         * 安装数字按钮
         * @private
         */
        _setup_number_btn : function(options)
        {
            var self = this;

            //self.number_btn_view = new number_btn_view
            self['number_btn_view' + '_' + options.id] = new number_btn_view
            ({
                templateModel :
                {
                    type : 'tel'
                },
                min_value : options.min_value,
                max_value : options.max_value,
                step : options.step,
                parentNode: options.parentNode,
                value : options.value
            }).render();

            self.view_scroll_obj.refresh();

        },

        /**
         * 安装选择组模块
         * @private
         */
        _setup_choosen_group : function()
        {
            var self = this;

            //设置信用金选中

//            var cameraman_require = Utility.user.get('cameraman_require');
//            self.model_info.cameraman_require = cameraman_require;
            //var data_model_type = data.model_type;



//            if(cameraman_require){
//                var this_balance_require;
//                if(self.model_info.balance_require.length>0){
//                    for(var i=0; i<self.model_info.balance_require.length; i++){
//                        this_balance_require = self.model_info.balance_require[i];
//                        if(cameraman_require == this_balance_require.text){
//                            this_balance_require.selected = true;
//                        }
//                    }
//                }
//            }

            var level_require = Utility.user.get('level_require');
            self.model_info.level_require = level_require;


            if(level_require){
                var this_balance_require;
                if(self.model_info.balance_require.length>0){
                    for(var i=0; i<self.model_info.balance_require.length; i++){
                        this_balance_require = self.model_info.balance_require[i];
                        if(level_require == this_balance_require.id){
                            this_balance_require.selected = true;
                        }
                    }
                }
            }

            //设置风格选中
/*            var utility_model_style = Utility.user.get('model_style');
            var data_model_style = self.model_info.model_style;

            if(utility_model_style && utility_model_style.length>0){
                for(var n=0; n<utility_model_style.length; n++){
                    var this_style = utility_model_style[n].text
                    var this_price = utility_model_style[n].price

                    for(var i=0; i<data_model_style.length; i++){
                        var this_model_style = data_model_style[i];
                        if(this_style == this_model_style.text){
                            this_model_style.selected = true;
                            self._render_number_btn(this_model_style.text,this_model_style.id);
                            self['number_btn_view' + '_' + this_model_style.id].set_vaule(this_price)
                        }
                    }
                }
            }


            //风格选择组件
            self.style_choosen_group_view = new choosen_group_view
            ({
                templateModel :
                {
                    list : self.model_info.model_style
                },

                parentNode: self.$style,
                is_multiply : true
            }).render();*/

            //摄影师信用金要求选择组件
            self.money_choosen_group_view = new choosen_group_view
            ({
                templateModel :
                {
                    list : self.model_info.balance_require
                },
                btn_per_line : 3, //每行按钮个数
                line_margin : '0px', //行间距
                btn_width : '70px', //按钮宽度
                parentNode: self.$money,
                is_multiply : false
            }).render();


        },

        //新风格相关
        //==========================================================================================================

        /**
         * 设置风格小时
         * @param ev
         */
        set_model_style_hour : function(ev){
            var self = this;
            var $target = $(ev.currentTarget);
            var id = $target.attr('data-id');

            self.current_model_style_id = id;

            // modify by hudw 2014.12.6
            // 修改成点击时间的时候才初始化一个下拉选择框，目的为了每新建一个风格卡，就新建一个时间选择
            //风格选择小时下拉框
            if(!self.select_style_hour_view['hour_view' + '_' + self.current_model_style_id ])
            {
                var model_style_hour =  $target.find('[data-role="model-style-hour"]').html();

                for(var i=0;i<self.style_hour_arr.length;i++)
                {
                    if(model_style_hour == self.style_hour_arr[i].value )
                    {
                        self.style_hour_arr[i].selected = true;
                        break;
                    }
                }

                self.select_style_hour_view['hour_view' + '_' + self.current_model_style_id ] = new m_select
                ({
                    templateModel :
                    {
                        options :
                            [
                                self.style_hour_arr
                            ]
                    },
                    parentNode: self.$el
                }).render();

                // 风格选择小时确认
                self.select_style_hour_view['hour_view' + '_' + self.current_model_style_id ].on('confirm:success',function(arr)
                {
                    self['model_style_card' + '_' + self.current_model_style_id ].$model_style_hour.html(arr[0].value);
                });
            }

            self.select_style_hour_view['hour_view' + '_' + self.current_model_style_id ].show();

        },

        /**
         * 选择风格
         * @param ev
         */
        to_select_model_style : function(ev){
            var self = this;
            var $target = $(ev.currentTarget);

            // modify hudw 2014.12.6
            var input_model_style = right_trim($target.find('[data-role="model-style"]').html());

            var this_model_style_arr = input_model_style.split(' ');


            var type = $target.attr('data-type');
            window._model_style_id = $target.attr('data-id');




            self.init_model_style_info();

            console.log(self.model_info.model_style);
            console.log(self.model_style_card_id_arr);

            page_control.navigate_to_page('model_date/model_card/price_style_select',{
                style_info : this_model_style_arr,
                model_style : self.model_info.model_style,
                type : type
            });


        },
        /**
         * 添加一张其他风卡
         * @private
         * hudw 2014.12.4
         */
        _add_other_style : function(){

            var self = this;
            if(self.get_all_select_model_style().length >= self.model_info.model_style_count){
                Tip.show('已无风格可选！', 'error', {
                    delay: 800
                });
                return;
            }

            self._setup_model_style_card(
                [{
                    continue_price: "",
                    hour: self.style_hour_arr[1].value,
                    price: "",
                    style: ""
                }],'price-style-list-other');

        },


        /**
         * 减去一张其他风格卡
         * @param ev
         * @private
         */
        _minus_other_style : function(ev){
            var self = this;
            var $target = $(ev.currentTarget);
            var id = $target.attr('data-id');
            var card_value = self.get_one_model_style_card_value(id);
            self['model_style_card' + '_' + id ].destroy();

            var style_arr = card_value.style.split(' ');

            //console.table(window._model_style);
            //console.table(self.model_info.model_style);


            //删除相应风格
            if(style_arr && style_arr.length>0)
            {
                //当前选择的类型
                for(var n=0; n<style_arr.length; n++)
                {
                    var this_style = style_arr[n];
                    console.log('this_style:'+this_style);

                    //把没此风格的从类型数组中删除
                    for(var i=0; i<window._model_style.length; i++)
                    {
                        if(right_trim(this_style) == right_trim(window._model_style[i].style))
                        {
                            var current_style = window._model_style.splice(i,1);
                            //清楚删除的风格的状态
                            for(var i=0; i<self.model_info.model_style.length; i++)
                            {
                                if(current_style.style == self.model_info.model_style[i].style)
                                {
                                    self.model_info.model_style[i].selected = false;
                                    self.model_info.model_style[i].disabled = false;
                                }
                            }
                        }
                    }
                }
            }


            //console.table(window._model_style);
            //console.table(self.model_info.model_style);

        },

        /**
         * 生成一张风格卡
         * @param options
         * @param type
         * @private
         */
        _setup_model_style_card : function(options,type){
            var self = this;

            /*var id =self.get_random();
             self.model_style_card_id_arr.push(id);*/
            var is_other = false;
            if(type == "price-style-list-other"){
                is_other = true;
            }

            if(options)
            {
                for(var i=0;i<options.length;i++){
                    var id =self.get_random();
                    self.model_style_card_id_arr.push(id);

                    self['model_style_card' + '_' + id ] = new model_style_card
                    ({
                        templateModel :
                        {
                            model_style : options,
                            is_other : is_other,
                            id : id,
                            price : options[i].price,
                            continue_price : options[i].continue_price,
                            style : options[i].style,
                            hour : options[i].hour
                        },
                        parentNode: self.$('[data-role=' + type + ']')
                    }).render('prepend');
                }


            }
        },

        /**
         * 初始化已选择的风格类型对象
         */
        init_model_style_info : function(){
            var self = this;

            var data_model_style = self.model_info.model_style;

            //初始化self.model_info.model_style
            for(var i=0;i<self.model_info.model_style.length;i++){
                self.model_info.model_style[i].disabled = false;
                self.model_info.model_style[i].selected = false;
            }

            //获得所有已选风格数据
            var all_select_model_style = self.get_all_select_model_style();



            if(all_select_model_style.length>0){
                for(var n=0; n<all_select_model_style.length; n++){
                    var this_style = all_select_model_style[n];

                    for(var i=0; i<data_model_style.length; i++){
                        var this_model_style = data_model_style[i];
                        if(this_style == this_model_style.text){
                            this_model_style.disabled = true;
                            this_model_style.selected = false;
                        }
                    }
                }
            }

            //console.table(data_model_style);
        },

        /**
         * 构造所有已选风格数组
         * @returns {Array}
         */
        get_all_select_model_style : function(){
            var all_select_model_style = [];

            if(window._model_style && window._model_style.length>0) {
                for (var n = 0; n < window._model_style.length; n++) {
                    var this_style = window._model_style[n].style;
                    all_select_model_style = all_select_model_style.concat(right_trim(this_style).split(' '));
                }
            }

            return all_select_model_style;
        },

        /**
         * 返回一张风格卡数据
         * @param id
         * @returns {*}
         */
        get_one_model_style_card_value : function(id){
            var self = this;
            return self['model_style_card' + '_' + id ].get_value();
        },

        //==========================================================================================================

        /**
         * 用户信息更新处理集合
         */
        _update_info_before: function() {
            Tip.show('提交中', 'loading', {
                delay: -1
            });
        },

        _update_info_error: function() {
            Tip.show('网络异常', 'right', {
                delay: 800
            });
        },

        _update_info_success : function(res){
            var self = this;

            var msg = res.result_data && res.result_data.msg;

            Tip.show(msg, 'right', {
                delay: 800
            });
            //删除不需要的字段
            delete self.model_info.balance_require;
            delete self.model_info.model_style_arr;
            delete self.model_info.model_price_arr;
            //成功后保存本地模型
            console.log(Utility.user.toJSON());
            Utility.user.set(self.model_info);
            console.log(Utility.user.toJSON());

            if(App.isPaiApp)
            {
                App.switchtopage({page : 'mine'});
            }
            else
            {
                page_control.navigate_to_page('mine');
            }



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
         * 设置模特信息
         * @param model_info
         * @return {*}
         * @private
         */
        _set_model_info : function(model_info){
            var self = this;

            //self.model_info.model_style = $.extend([], model_info.model_style,self.model_info.model_style);

            //self.model_info.model_style = model_info.model_style
            self.model_info = model_info;



            return self;
        },

        _save_info: function() {
            var self = this;

            /*var _model_style = self.style_choosen_group_view.get_value();
            var model_price_arr = [];
            var model_style_arr = [];
            var model_style = [];
            if(_model_style.length>0){
                for(var i=0; i<_model_style.length; i++){
                    var price,style;
                    price = self['number_btn_view' + '_' + _model_style[i].id].$('[data-role=input-num-btn]').val();
                    style = _model_style[i].text;

                    if(price < 10 || price > 10000){
                        self.price_error = true;
//                        Tip.show('风格价格数据有误', 'error', {
//                            delay : 800
//                        });
//                        return;
                    }

                    model_price_arr.push(price);
                    model_style_arr.push(style);
                    model_style.push({
                        price : price,
                        style : style
                    });
                }
            }


            self.model_info.model_style_arr = model_style_arr;
            self.model_info.model_price_arr = model_price_arr;*/

            self.model_info.limit_num = self.number_btn_view_max_person.$('[data-role=input-num-btn]').val();



            //获取风格价格数据(新)
            //==========================================================================================================
            var new_model_style_arr = {};
            self.model_style_card_has_empty_value = false;
            //var this_model_style={};
            new_model_style_arr.main = [];
            new_model_style_arr.other = [];

            console.log(self.model_style_card_id_arr)

            for(var i=0;i<self.model_style_card_id_arr.length;i++){
                var this_model_style={};//放在外面定义所有的值都是最后一个（闭包的概念）
                if(!self['model_style_card' + '_' + self.model_style_card_id_arr[i] ].$el){
                    continue;
                }
                var card_value = self.get_one_model_style_card_value(self.model_style_card_id_arr[i]);
                //风格卡中是否有未填项
                self.model_style_card_has_empty_value = card_value.has_empty_value;
                var type = self['model_style_card' + '_' + self.model_style_card_id_arr[i] ].$model_style.attr('data-type')//获得是主风格还是其他
                this_model_style.continue_price = card_value.continue_price;
                this_model_style.hour = card_value.hour;
                this_model_style.price = card_value.price;
                this_model_style.style = card_value.style;


                if(card_value.price < 10 || card_value.price > 10000 || card_value.continue_price < 10 || card_value.continue_price > 10000 || card_value.style == ''){
                    self.price_error = true;
                }


                if(type =='main'){
                    new_model_style_arr.main.push(this_model_style);
                }else{
                    new_model_style_arr.other.push(this_model_style);
                }
            }
            //console.table(new_model_style_arr);
            console.log(new_model_style_arr);

            self.model_info.new_model_style_arr = new_model_style_arr;
            //==========================================================================================================


            var error_txt = null;
            //self.model_info.model_style_arr < 1 && (error_txt = '至少选择一个风格');
            self.price_error && (error_txt = '请填写风格及价格的相关信息');
            (self.model_info.limit_num < 1 || self.model_info.limit_num > 10) && (error_txt = '每次拍摄人数设置有误');
            //!self.model_info.cameraman_require && (error_txt = '请选择一个信用金');
            !self.model_info.level_require && (error_txt = '请选择一个信用金');


            if(error_txt){
                Tip.show(error_txt,'error');
                self.price_error && (self.price_error = false);
                return;
            }
            //删除不需要的字段
            delete self.model_info.balance_require;
            //delete self.model_info.model_style;

            var data = self.model_info




            //上传到服务器
            self.model.save_model_card_info(self.model_info);
        },

        /**
         * 渲染主模版
         * @return {*}
         */
        render: function() {
            var self = this;

            View.prototype.render.apply(self);
            // 安装选择模块
            self._setup_choosen_group();

            // 安装每次拍摄人数组件
            var options = {};
            options.min_value = 1;
            options.max_value = 10;
            options.step = 1;
            options.parentNode = self.$max_person;
            options.value =  Utility.user.get('limit_num');
            options.id = 'max_person';
            self._setup_number_btn(options);

            // 选择模块事件绑定
/*            self.style_choosen_group_view.on('success:selected',function(obj){
                if(obj.selected){
                    self._render_number_btn(obj.value,obj.id);
                }else{
                    self.$('[data-id="' + obj.id + '"]').remove();
                    self['number_btn_view' + '_' + obj.id].destroy();
                }
                self.view_scroll_obj.refresh();
            })*/

            self.money_choosen_group_view.on('success:selected',function(obj){
                //self.model_info.cameraman_require = obj.value;//保存选择的信用金
                self.model_info.level_require = obj.id;
                self.$remark.html(obj.remark);
                //不需要此提示2014-12-11 zy
                /*if(!Utility.storage.get('first-cameraman-require-'+Utility.login_id)){
                    Utility.storage.set('first-cameraman-require-'+Utility.login_id,1);
                    Tip.show('注意：信用金是摄影师约拍你的信用担保，也是和你私聊的条件，请根据个人的事迹情况合理选择','error',{delay:7000});
                }*/
            })

            return self;
        },

        /**
         * 获得一个随机数
         */
        get_random : function(){
            return Math.random().toString().replace('.', '');
        }

    });

    function right_trim(str)
    {
        return str.replace(/\s*$/, '');
    }

    module.exports = action_apply_view;
});