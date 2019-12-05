define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var Scroll = require('../../../common/scroll');
    var Dialog = require('../../../ui/dialog/index');
    var choosen_group_view = require('../../../widget/choosen_group/view');
    var select_view = require('../../../widget/select/view');
    //var footer = require('../../../widget/footer_to_edit_all/index');
    var location_data = require('../../../common/location_data');
    var upload_pic = require('../../../widget/upload_pic/view');
    var model_style_card = require('../../../widget/model_style_card/view');
    var App = require('../../../common/I_APP');
    var Utility = require('../../../common/utility');
    var Tip = require('../../../ui/m_alert/view');
    var m_select = require('../../../ui/m_select/view');

    //=========================================================================
    var number_btn_view = require('../../../widget/number_btn/view');

    // modify by hudw 2015.3.2
    // 增加上传图片的上限数
    var upload_pic_limit = 15;
    var works_html_s = '个人作品（'
    var works_html_e = '/'+upload_pic_limit+'）'



    var main_tpl = require('./tpl/main_all.handlebars');
    var popup_item_tpl = require('./tpl/popup_item.handlebars');
    var number_btn_tpl = require('./tpl/number_btn_item.handlebars');
    var style_price_tpl = require('./tpl/style_price_item.handlebars');

    var model_caed_edit_view = View.extend({
        attrs:{
            template:main_tpl
        },
        events:{
            'tap [data-role=nav]' : 'navigate_to_page',
            'tap [data-role=popup]' : '_popup',
            'tap [data-role=change-avatar]' : '_changeAvatarFromAPP',
            'tap [data-role=avatar-img]' : '_upload_avater',
            'tap [data-role=save]' : '_save_info',
            'tap [data-role=add-other-style]' : '_add_other_style',
            'tap [data-role=minus-other-style]' : '_minus_other_style',
            'tap [data-role=set-hour]' : 'set_model_style_hour',
            'tap [data-role=to-select-style]' : 'to_select_model_style',
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;
                var $target = $(ev.currentTarget);


                if(Utility.int(self.get('is_can_back')))
                {
                    if(App.isPaiApp)
                    {
                        App.switchtopage({page : 'mine'})
                    }
                    else
                    {
                        page_control.navigate_to_page('mine');
                    }

                }
                else
                {
                    page_control.back();
                }


            },
            'tap [data-role="select-city"]' : function()
            {
                var self = this;

                self.select_province_view.show();
            },
            'tap [data-role="select-sex"]' : function()
            {
                var self = this;

                self.select_sex_view.show();
            },
            'tap [data-role="cup"]' : function()
            {
                var self = this;

                self.select_cup_view.show();
            },
            'tap [data-role="edit-remarks"]' : function()
            {
                var self = this;
                page_control.navigate_to_page('edit_page/textarea',
                    {
                        title:'修改备注',
                        text : $.trim(self.$remarks.html()),
                        edit_obj:self.$remarks,
                        is_empty : true,
                        des: '备注可以让你更好地个性化表达自己，另外还可以在这里写上你一般空档的时间。',
                        word_limit : 140
                    }
                )
            },
            'tap [data-role="nickname"]' : function()
            {
                var self = this;
                page_control.navigate_to_page('edit_page/text',
                    {
                        title:'修改昵称',
                        text : $.trim(self.$nickname.html()),
                        edit_obj:self.$nickname,
                        is_empty : false,
                        des: '使用真实名字让人更快的找到和认识你。',
                        word_limit : 15
                    }
                )
            }
        },

        _setup_events:function(){
            var self = this;

            // 模型事件
            // --------------------

            self.listenTo(self.model, 'before:get_base_info:fetch', self._before_get_base_info)
                 .listenTo(self.model, 'success:get_base_info:fetch',self._success_get_base_info)
                 .listenTo(self.model, 'error:get_base_info:fetch',self._error_get_base_info)
                 .listenTo(self.model, 'complete:get_base_info:fetch',self._complete_get_base_info)
                 .listenTo(self.model, 'before:update_avater:save',self._before_update_avater)
                 .listenTo(self.model, 'success:update_avater:save',self._success_update_avater)
                 .listenTo(self.model, 'error:update_avater:save',self._error_update_avater)
                 .listenTo(self.model, 'complete:update_avater:save',self._complete_update_avater)
                 .listenTo(self.model, 'before:update_info:save', self._update_info_before)
                 .listenTo(self.model, 'success:update_info:save', self._update_info_success)
                 .listenTo(self.model, 'error:update_info:save', self._update_info_error)
                 .listenTo(self.model, 'complete:update_info:save', self._update_info_complete);


            //安装下拉选择框
            self._setup_select();
            /*var pic_list = [
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg'
            ]*/


            //安装风格价格
            self._setup_model_style_card(Utility.user.get('model_style_combo').main,'price-style-list-main');

            if(Utility.user.get('model_style_combo').other)
            {
                self._setup_model_style_card(Utility.user.get('model_style_combo').other,'price-style-list-other');
            }



            //安装封面图上传组件
            var cover_arr = [Utility.user.get('cover_img')];
            //var cover_arr = ['undefined'];
            self._setup_upload_cover(cover_arr);


            //处理图片数据
            var _pic_arr = Utility.user.get('pic_arr');
            var pic_arr=[];
            if(_pic_arr && _pic_arr.length>0){
                for(var i=0;i<_pic_arr.length;i++){
                    pic_arr.push(_pic_arr[i].img);
                }
            }


            //安装图片上传组件
            self._setup_upload_pic(pic_arr);
            //设置个人作品html
            if(_pic_arr && _pic_arr.length>0){
                self.$works.html(works_html_s + _pic_arr.length + works_html_e);
            }

            //===============================================================================================

            // 安装每次拍摄人数组件
            var options = {};
            options.min_value = 1;
            options.max_value = 10;
            options.step = 1;
            options.parentNode = self.$max_person;
            options.value =  Utility.user.get('limit_num');
            options.id = 'max_person';
            self._setup_number_btn(options);


            self.on('render',function(){
                if(!self.view_scroll_obj)
                {

                    self._setup_scroll();

                    self.view_scroll_obj.refresh();
                    return;
                }

                self.view_scroll_obj.refresh();
            });
            /*if(!self.view_scroll_obj)
            {

                self._setup_scroll();

                self.view_scroll_obj.refresh();
                return;
            }

            self.view_scroll_obj.refresh();*/
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
                    is_hide_dropdown : true,
                    lazyLoad : true
                });

            self.view_scroll_obj = view_scroll_obj;

        },



        setup : function()
        {
            var self = this;

            self.model_info={
                'nickname' : '',
                'location_id' : '',
                'height' : '',
                'weight' : '',
                'cup' : '',
                'chest' : '',
                'waist' : '',
                'hip' : '',
                model_type : [],
                balance_require : [],
                model_style :[]
            };

            //获得模特卡基础数据
            self.model.get_base_info();

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$uploadfile = self.$('[data-role=uploadfile]');
            self.$title = self.$('[data-role=title]');//标题
            self.$model_info = self.$('[data-role=model_info]');//修改个人资料
            self.$model_pic = self.$('[data-role=model_pic]');
            self.$model_price = self.$('[data-role=model_price]');//修改价格
            self.$back = self.$('[data-role=page-back]')//返回上一页
            self.$footer = self.$('[data-role=footer]');
            self.$save = self.$('[data-role=save]');
            self.$select_city = self.$('[data-role=select-city]')
            self.$price_style_list_other = self.$('[data-role=price-style-list-other]')//其他风格容器




            self.$avatar = self.$('[data-role=avatar-img]');
            self.$saveBtn = self.$('[data-role=save]');
            self.$province = self.$('[data-role=province]');
            self.$city = self.$('[data-role=city]');
            self.$works = self.$('[data-role=works]');
            self.$select_sex = self.$('[data-role=select-sex]');
            self.$select_sex.html(Utility.user.get('sex')|| '女');

            self.$shoot_style = self.$('[data-role=shoot-style]');
            self.$upload_pic = self.$('[data-role=upload-pic]');
            self.$upload_cover = self.$('[data-role=upload-cover]');
            self.$nickname = self.$('[data-role=nickname]');


            self.$height = self.$('[data-target=height]');//身高
            self.$weight = self.$('[data-target=weight]');//体重
            self.$cup = self.$('[data-role=cup]');//罩杯
            self.$cwh = self.$('[data-target=cwh]');//三围
            self.$remarks = self.$('[data-role=remarks]');//备注
            self.$remarks.html(Utility.user.get('intro'));
            //self.$remarks.html(Utility.user.get('intro')||'期待与摄影大师出大片。左手臂上有纹身详情看我的作品图');


//=======================================================================
            self.$complete_btn = self.$('[data-role=complete]');

            self.$style = self.$('[data-role=style]');
            self.$money = self.$('[data-role=money]');
            //self.$price = self.$('[data-role=price]');
            self.$price_out = self.$('[data-role=price-out]');
            self.$max_person = self.$('[data-role=max-person]');
            self.$remark = self.$('[data-role=remark]');


            //所有已选风格
            window._model_style = Utility.user.get('model_style_v2');
            //风格卡id数组
            self.model_style_card_id_arr = [];

            self.select_style_hour_view = {};

            // 安装事件
            self._setup_events();

            // 安装底部导航
            //self._setup_footer();

            //self.view_scroll_obj.refresh();




        },

        /**
         * 上传头像
         * @private
         */
        _upload_avater : function(){
            var self = this;

            App.upload_img
            (   'header_icon',
                {
                    is_async_upload : 0,
                    max_selection : 1

                },function(data)
                {
                    console.log(data);

                    if(data.code == '1000'){
//                        Tip.show('上传失败', 'error', {
//                            delay: 800
//                        });
                    }else if(data.code == '1001'){
//                        Tip.show('上传被取消', 'error', {
//                            delay: 800
//                        });
                    }else if(data.imgs.length>0)
                    {
                        var url = data.imgs[0].url + '?' + self.get_random();
                        self.model_info.user_icon = url;
                        console.log(self.model_info.user_icon);
                        self.$avatar.css({
                            backgroundImage: 'url(' + url + ')'
                        }).attr({
                            'data-image' : url
                        })
                        /*self.$avatar.find('i').css({
                            backgroundImage: 'url(' + url + ')'
                        }).attr({
                            'data-image' : url
                        })*/
                    }
                });
        },

        /**
         * 安装上传封面图组件
         * @param pic_list
         * @private
         */
        _setup_upload_cover : function(pic_list){

            var self = this;
            var pic_w_h = Math.ceil(((Utility.get_view_port_width() - 50) / 3));
            var upload_cover_txt_w = Utility.get_view_port_width() - pic_w_h - 20 - 15

            self.upload_cover_view = new upload_pic({
                templateModel :
                {
                    max_size : pic_w_h//设置方图的宽高（这里为设置添加按钮宽高）
                },
                max_pic : 1,
                cover_max_size_h : Math.ceil((pic_w_h / 3)) * 4,
                parentNode : self.$upload_cover

            }).set_w_h(pic_w_h).render();


            self.$upload_cover.append('<div class="upload-cover-txt" style="width: ' + upload_cover_txt_w + 'px">封面照片用于首页及专题页等展示位置的呈现，上传上半身近照、像素高一点的照片会更好看，更容易被摄影师约拍哦。</div>')

            //有图片传入则添加
            if(pic_list[0] || pic_list[0] != ''){
                self.upload_cover_view.add_pic(pic_list);
            }

            self.upload_cover_view.on('tap:del_upload_pic', function(){
                self.view_scroll_obj.refresh();
            });


            self.upload_cover_view.on('tap:upload_pic',function(){
                if(App.isPaiApp){
                    App.upload_img
                    ('modify_cardcover',{
                        is_async_upload : 0,
                        max_selection : 1,
                        is_zip : 1

                    },function(data)
                    {
                        var pic_list=[];

                        if(data.imgs && data.imgs.length>0)
                        {
                            for(var i = 0;i<data.imgs.length;i++)
                            {
                                var img = Utility.matching_img_size(data.imgs[i].url,165);

                                pic_list.push(img);
                            }
                            console.log(pic_list);
                            self.upload_cover_view.add_pic(pic_list);


                            setTimeout(function() {
                                self.view_scroll_obj.refresh();
                                self.view_scroll_obj.change_scroll_position();
                            }, 50);
                        }
                    });
                }else{

                    var pic_list = [
                        'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg'
                    ]
                    self.upload_cover_view.add_pic(pic_list);
                    self.view_scroll_obj.refresh();
                    self.view_scroll_obj.change_scroll_position();
                    return;

                }


            });


        },

        /**
         * 安装上传图片组件
         * @param pic_list
         * @private
         */
        _setup_upload_pic : function(pic_list){

            var self = this;
            var pic_w_h = Math.ceil(((Utility.get_view_port_width() - 50) / 3));



            self.upload_pic_view = new upload_pic({
                templateModel :
                {
                    max_size : pic_w_h//设置方图的宽高（这里为设置添加按钮宽高）
                },
                max_pic : upload_pic_limit,
                parentNode:self.$upload_pic
            }).set_w_h(pic_w_h).render();

            //有图片传入则添加
            if(pic_list[0]){
                self.upload_pic_view.add_pic(pic_list);
            }

            self.upload_pic_view.on('tap:del_upload_pic', function(){
                self.$works.html(works_html_s + self.upload_pic_view.get_amount() + works_html_e);
                self.view_scroll_obj.refresh();
            });


            self.upload_pic_view.on('tap:upload_pic',function(){
                if(App.isPaiApp){
                    App.upload_img
                    ('multi_img',{
                        is_async_upload : 0,
                        max_selection : upload_pic_limit - self.upload_pic_view.get_amount(),
                        is_zip : 1

                    },function(data)
                    {
                        var pic_list=[];

                        if(data.imgs && data.imgs.length>0)
                        {
                            for(var i = 0;i<data.imgs.length;i++)
                            {
                                pic_list.push(data.imgs[i].url);
                            }
                            console.log(pic_list);
                            self.upload_pic_view.add_pic(pic_list);


                            setTimeout(function() {
                                self.view_scroll_obj.refresh();
                                self.view_scroll_obj.resetlazyLoad();
                                self.view_scroll_obj.reset_top();
                            }, 50);


                            self.$works.html(works_html_s + self.upload_pic_view.get_amount() + works_html_e);
                        }
                    });
                }else{

                var pic_list = [
                    /*'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',*/
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg'
                ]
                self.upload_pic_view.add_pic(pic_list);
                self.$works.html(works_html_s + self.upload_pic_view.get_amount() + works_html_e);
                self.view_scroll_obj.refresh();
                self.view_scroll_obj.change_scroll_position();
                return;

            }

                self.$works.html(works_html_s + self.upload_pic_view.get_amount() + works_html_e);
               //self.view_scroll_obj.refresh();

            });


        },


        /**
         * 安装下拉选择框
         * @private
         */
        _setup_select : function()
        {
            var self = this;
            var province = location_data.two_lv_data.province;
            var city = location_data.two_lv_data.city;
            var city_id = self.to_number(Utility.user.get('location_id'));
            var province_id = self.to_number(Utility.user.get('location_id') && Utility.user.get('location_id').slice(0,6)) || 101001;
            var cup = Utility.user.get('cup');
            var chest_inch = Utility.user.get('chest_inch');
            //设置罩杯
            self.$cup.html(Utility.user.get('cup_v2'));
            self.chest_inch = chest_inch;


            var province_arr = province;
            var city_arr = city;
            var selected_address = '请选择城市';
            var selected_city_str = '';
            var selected_province_str = '';

            if(province_id == 101001)
            {
                var defalut_key = '北京';
            }


            //省下拉参数
            $(province_arr).each(function(i,obj)
            {
                if(obj.id == province_id)
                {
                    obj.selected = true;

                    selected_province_str = obj.text;

                    return  false;
                }
            });


            //市下拉参数
            $(city_arr[province_id]).each(function(i,obj)
            {

                //0为默认这时直接选中第一个
                if(city_id == 0 || obj.id == city_id)
                {
                    obj.selected = true;

                    selected_city_str = obj.text;

                    return  false;
                }
            });

            if(Utility.user.get('location_id'))
            {
                selected_address = selected_province_str+'-'+selected_city_str;
            }

            // 设置默认市
            self.$('[data-role="select-city"]').attr('data-city-id',Utility.user.get('location_id')).html(selected_address);

            var city_arr = city[province_id];

            // 级联查询必须有“不限”的数据组
            self.select_province_view = new m_select
            ({
                templateModel :
                {
                    options :
                        [
                            province,
                            city_arr
                        ]
                },
                parentNode: self.$el
            }).render();


            self.select_province_view.on('change:options',function(arr,cur_scroll_obj)
            {

                if(cur_scroll_obj.index == 0)
                {
                    var key = arr[0].id;

                    var options = city[key];

                    options[0].selected = true;

                    self.select_province_view.set_options([options],1);
                }

            });

            // 地址确认
            self.select_province_view.on('confirm:success',function(arr)
            {
                self.$('[data-role="select-city"]').attr('data-city-id',arr[1].id).html(arr[0].value+"-"+arr[1].value);
            });


            //罩杯数据
            var cup_arr = [
                { 'text' : 'A' , 'value' : 'A'},
                { 'text' : 'B' , 'value' : 'B'},
                { 'text' : 'C' , 'value' : 'C'},
                { 'text' : 'D' , 'value' : 'D'},
                { 'text' : 'E+' , 'value' : 'E+'}
            ];

            //罩杯尺寸
            var chest_inch_arr = [
                { 'text' : '30' , 'value' : '30'},
                { 'text' : '32' , 'value' : '32'},
                { 'text' : '34' , 'value' : '34'},
                { 'text' : '36' , 'value' : '36'},
                { 'text' : '38' , 'value' : '38'}
            ];


            for(var i=0;i<chest_inch_arr.length;i++)
            {
                if(chest_inch_arr[i].value == chest_inch)
                {
                    chest_inch_arr[i].selected = true;

                    break;
                }
            }

            //罩杯下拉参数
            if(cup_arr.length>0){
                for(var i=0;i<cup_arr.length;i++){
                    (cup_arr[i].value == cup.replace(/\d*/,"")) && (cup_arr[i].selected = true)
                }
            }

            //罩杯下拉框
            self.select_cup_view = new m_select
            ({
                templateModel :
                {
                    options :
                        [
                            chest_inch_arr,
                            cup_arr
                        ]
                },
                parentNode: self.$el
            }).render();

            // 罩杯确认
            self.select_cup_view.on('confirm:success',function(arr,cur_scroll_obj)
            {
                self.$cup.html(arr[0].value + arr[1].value);

                self.chest_inch = arr[0].value;
            });


            //性别
            var sex_arr = [
                { 'text' : '男' , 'value' : '男'},
                { 'text' : '女' , 'value' : '女'}
            ]

            for(var i=0;i<sex_arr.length;i++){
                (sex_arr[i].value == Utility.user.get('sex') || '女') && (sex_arr[i].selected = true)
            }


            //性别下拉框
            self.select_sex_view = new m_select
            ({
                templateModel :
                {
                    options :
                        [
                            sex_arr
                        ]
                },
                parentNode: self.$el
            }).render();

            // 性别确认
            self.select_sex_view.on('confirm:success',function(arr,cur_scroll_obj)
            {
                self.$select_sex.html(arr[0].value);

                self.model_info.sex = arr[0].value;

            });


            //风格选择小时数据
            // modify hudw
            // 将style_hour_arr 变量设置为self 私有的
            self.style_hour_arr = [
                { 'text' : '元/4小时' , 'value' : '4'},
                { 'text' : '元/2小时' , 'value' : '2'}
            ];

            //风格选择小时下拉参数
            /*if(cup_arr.length>0){
                for(var i=0;i<cup_arr.length;i++){
                    (cup_arr[i].value == cup.replace(/\d*//*,"")) && (cup_arr[i].selected = true)
                }
            }*/







        },
        /**
        *渲染（input）弹窗模板
         */
        _render_popup_item: function(items) {
            var self = this;
            var htmlStr = popup_item_tpl({
                items: items
            });

            return htmlStr;
        },

        /**
         * 信息填写弹框
         * @param event
         * @private
         */
        _popup : function(event) {
            var self = this;

            var $target = $(event.currentTarget);
            var targetNav = $target.attr('data-target');
            var items=[];

            switch (targetNav) {
                case 'height':
                    items=[
                        {
                        'unit': 'cm',
                        'name' : '身高',
                        'input-type': 'tel',
                        'input-role': 'height',
                        'input-data-role': 'height'
                        }
                    ]
                    break;
                case 'weight':
                    items=[
                        {
                            'unit': 'kg',
                            'name' : '体重',
                            'input-type': 'tel',
                            'input-role': 'weight',
                            'input-data-role': 'weight'
                        }
                    ]
                    break;
                case 'cup':
                    items=[
                        {
                            'unit': '',
                            'name' : '罩杯',
                            'input-role': 'cup',
                            'input-type': 'tel',
                            'input-data-role': 'cup'
                        }
                    ]
                    break;
                case 'cwh':
                    var cwh = self.$cwh.html().split('-')
                    items=[
                        {
                            'unit': 'cm',
                            'name' : '胸围',
                            'input-role': 'cwh',
                            'input-type': 'tel',
                            'value': cwh[0],
                            'input-data-role': 'chest'
                        },
                        {
                            'unit': 'cm',
                            'name' : '腰围',
                            'input-role': 'cwh',
                            'input-type': 'tel',
                            'value': cwh[1],
                            'input-data-role': 'waist'
                        },
                        {
                            'unit' : 'cm',
                            'name' : '臀围',
                            'input-role': 'cwh',
                            'input-type': 'tel',
                            'value': cwh[2],
                            'input-data-role': 'hip'
                        }
                    ]

                    break;
            }

            var saveDialog = new Dialog({
                content: self._render_popup_item(items),
                buttons: [{
                    name: 'save',
                    text: '确定'
                },{
                    name: 'close',
                    text: '取消'
                }]
            }).on('tap:button:save', function() {
                    var text;//确定完成后的text
                    var set_html_target;
                    var input_data_role;
                    var texts=[];
                    var this_text;//下面循环中当前text;

                    for(var i=0;i<this.$('input').length;i++){
                        this_text = self.to_number(this.$('input')[i].value);
                        input_data_role = $(this.$('input')[i]).attr('input-data-role');
                        if(!(/^\d*$/.test(this_text))){
                            Tip.show('请输入大于0的数字！', 'error', {
                                delay: 1000
                            });
                            return false;
                        }
                        if(!this_text){
                            Tip.show('不能为0或空哦！', 'error', {
                                delay: 800
                            });
                            return false;
                        }
                        //各项的最大值控制
                        switch(input_data_role){
                            case 'height':
                                if(this_text > 300){
                                    Tip.show('身高不能超过300cm哦！', 'error', {
                                        delay: 800
                                    });
                                    return false;
                                }

                                break;
                            case 'weight':
                                if(this_text > 150){
                                    Tip.show('体重不能超过150kg哦！', 'error', {
                                        delay: 800
                                    });
                                    return false;
                                }
                                break;
                            case 'chest':
                            case 'waist':
                            case 'hip':
                                if(this_text > 200){
                                    Tip.show('三围不能有超过200cm的哦！', 'error', {
                                        delay: 800
                                    });
                                    return false;
                                }
                                break;
                        }
                        texts.push(this_text);
                        if(i==0){
                            set_html_target = $(this.$('input')[0]).attr('input-role');
                        }

                        //设置三围于attr中
                        if(set_html_target == 'cwh'){
                            self.$cwh.attr('data-' + input_data_role , this_text);
                            if(input_data_role == "chest"){
                                //设置罩杯
                                var old_cup = self.$cup.html().replace(/\d*/,"");//去掉数字
                                //self.$cup.html(this_text + old_cup);
                            }
                        }
                        self.model_info[input_data_role] = this_text;//修改信息对象

                    }

                    text = texts.join('-');
                    self.$('[data-target='+ set_html_target + ']').html(text);
                    this.hide().destroy();
                }).on('tap:button:close', function() {
                    this.hide().destroy();
                }).show();
        },

        /**
         * 导航到目的页面
         * @param event
         * @private
         */
        navigate_to_page: function(ev,type) {
            var self = this;
            var target_nav;
            var $target

            if(type){
                target_nav = type;
                $target = ev;
            }else{
                $target = $(ev.currentTarget);
                target_nav = $target.attr('data-target');
            }


            if(target_nav != 'model_preview')
            {
                $target.addClass('cur').siblings().removeClass('cur');
            }

            switch (target_nav) {
                case 'model_pic':
                    if(!self.$model_pic.hasClass('fn-hide'))
                    {
                        return;
                    }
                    self.$model_pic.removeClass('fn-hide');
                    self.$model_info.addClass('fn-hide')
                    self.$model_price.addClass('fn-hide')
                    //self.$model_pic.addClass('fn-hide');
                    //self.$footer.addClass('fn-hide');
                    self.$title.html('编辑模特卡');
                    /*self.$back.attr({
                        'hide-page':target_nav
                    })*/
                    self.view_scroll_obj.refresh();
                    break;
                case 'model_info':
                    if(!self.$model_info.hasClass('fn-hide'))
                    {
                        return
                    }
                    self.$model_info.removeClass('fn-hide');
                    self.$model_pic.addClass('fn-hide');
                    self.$model_price.addClass('fn-hide')
                    //self.$footer.addClass('fn-hide');
                    self.$title.html('修改个人资料');
                    /*self.$back.attr({
                        'hide-page':target_nav
                    })*/
                    self.view_scroll_obj.refresh();
                    break;
                case 'model_price':
                    if(!self.$model_price.hasClass('fn-hide'))
                    {
                        return
                    }
                    self.$model_price.removeClass('fn-hide');
                    self.$model_pic.addClass('fn-hide');
                    self.$model_info.addClass('fn-hide');
                    //self.$footer.addClass('fn-hide');
                    self.$title.html('修改价格');
                    /*self.$back.attr({
                        'hide-page':target_nav
                    })*/
                    self.view_scroll_obj.refresh();
                    break;
                case 'model_preview':
                    self._get_all_value();
                    page_control.navigate_to_page('model_card/'+ Utility.login_id, {data:self.model_info});

                    break;
            }
        },

        /**
         * 获得模特卡数据
         * @param response
         * @param options
         * @private
         */
        _success_get_base_info: function(response,options) {
            var self = this;
            var data = response.result_data.data;

            self.model_info.balance_require = data.balance_require;
            self.model_info.model_style = data.model_style;
            self.model_style_count = data.model_style.length;//模特风格总数

            // 安装选择模块
            self._setup_choosen_group();

            self.money_choosen_group_view.on('success:selected',function(obj){
                //self.model_info.cameraman_require = obj.value;//保存选择的信用金
                self.model_info.level_require = obj.id;
                self.$remark.html(obj.remark);
            })
        },
        _error_get_base_info : function()
        {
            var self = this;

            Tip.show('网络异常', 'error', {
                delay: 2000
            });

            setTimeout(function()
            {
                //获得模特卡基础数据
                self.model.get_base_info();
            },2000);


        },

        /**
         * 头像上传开始
         * @private
         */
        _before_update_avater: function() {
            Tip.show('提交中', 'loading', {
                delay: -1
            });
        },

        /**
         * 头像上传成功
         * @private
         */
        _success_update_avater: function() {
            Tip.show('更新成功', 'loading', {
                delay: 800
            });
        },

        /**
         * 头像上传失败
         * @private
         */
        _error_update_avater: function() {
            Tip.show('更新失败', 'error', {
                delay: 800
            });
        },

        //=======================================================================


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

            //self.view_scroll_obj.refresh();

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
            if(self.get_all_select_model_style().length >= self.model_style_count){
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
         * 安装选择组模块
         * @private
         */
        _setup_choosen_group : function()
        {
            var self = this;

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


            self.view_scroll_obj&&self.view_scroll_obj.refresh();


        },

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

            //删除全局风格卡id变量
            delete window._model_style_id
            //成功后保存本地模型
            console.log(Utility.user.toJSON());
            Utility.user.set(self.model_info);
            console.log(Utility.user.toJSON());




            //page_control.navigate_to_page('hot');

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

        _get_all_value : function(){
            var self = this;

            //获取风格价格数据
/*            var model_price_arr = [];
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
                        style : style,
                        text : style
                    });
                }
            }

            self.model_info.model_style = model_style;
            self.model_info.model_style_arr = model_style_arr;
            self.model_info.model_price_arr = model_price_arr;*/
            self.model_info.limit_num = self.number_btn_view_max_person.$('[data-role=input-num-btn]').val();


            //获取风格价格数据(新)
            //==========================================================================================================
            var new_model_style_arr = {};
            var preview_model_style_arr =[] //给预览的数据
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

                if(!(/^\d*$/.test(card_value.price)) || !(/^\d*$/.test(card_value.continue_price)) || card_value.price < 10 || card_value.price > 10000 || card_value.continue_price < 10 || card_value.continue_price > 10000 || card_value.style == ''){
                    self.price_error = true;
                }

                //给预览的数据
                //===============================================
                this_model_style.text = card_value.style;
                //为空时下面的split也会分割生成一个[0]为空的数组所以要做此判断
                if(this_model_style.text){
                    this_model_style.combo_text = card_value.price + '元(' + card_value.hour + '小时)';
                    //this_model_style.continue_text = '加钟续拍每小时' + card_value.continue_price + '元';

                    //分割一个价多个风格的情况
                    var split_style_arr = right_trim(card_value.style).split(' ');
                    if(split_style_arr.length > 1){
                        for(var n=0;n<split_style_arr.length;n++){
                            var split_model_style = {};
                            split_model_style.text = split_style_arr[n];
                            split_model_style.combo_text = this_model_style.combo_text;
                            preview_model_style_arr.push(split_model_style);
                        }
                    }else{
                        preview_model_style_arr.push(this_model_style);
                    }
                }
                //===============================================


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



            //获取数据
            //self.model_info.nickname = $.trim(self.$nickname.val());
            self.model_info.nickname = $.trim(self.$nickname.html());
            //self.model_info.location_id = self.select_city_view.get_value().id;
            self.model_info.location_id = self.$('[data-role="select-city"]').attr('data-city-id');

            self.model_info.pic_arr = self.upload_pic_view.get_value();//图片数组
            self.model_info.cover_img = self.upload_cover_view.get_value()[0];//封面图

            //self.model_info.pic_arr = pic_arr;
            self.model_info.height = self.$height.html();
            self.model_info.weight = self.$weight.html();
            //self.model_info.model_type = model_type_arr;
            var cup = self.$cup.html();

            //modify hudw
            self.model_info.cup = cup.replace(/\d*/,"");//去掉数字
            self.model_info.chest_inch = self.chest_inch;
            self.model_info.cup_v2 = self.model_info.chest_inch + self.model_info.cup;


            //self.model_info.cup = self.select_cup_view.get_value().value;
            self.model_info.chest = self.$cwh.attr('data-chest');
            self.model_info.waist = self.$cwh.attr('data-waist');
            self.model_info.hip = self.$cwh.attr('data-hip');

            self.model_info.intro = self.$remarks.html()//备注
            self.model_info.sex = self.$select_sex.html()//性别

            //给预览的参数（配合模特卡）
            //=================================================================================
            self.model_info.model_style_v2 = preview_model_style_arr;
            self.model_info.model_pic =[];
            self.model_info.new_model_pic =[];
            //合并封面图和图片

            /*self.model_info._pic_arr = [];
            self.model_info._pic_arr =self.model_info.pic_arr.concat([self.model_info.cover_img])


            for(var k=0;k < self.model_info._pic_arr.length; k++){*/
            for(var k=0;k < self.model_info.pic_arr.length; k++){
                var img = {};
                if(k == 0)
                {
                    img.type = 'double'
                }else{
                    img.type = 'one'
                }
                img.user_icon = self.model_info.pic_arr[k];
                img.big_user_icon = Utility.matching_img_size(img.user_icon);
                self.model_info.model_pic.push(img)
            }

            self.model_info.take_photo_times = Utility.user.get('take_photo_times');
            self.model_info.score = Utility.user.get('score');
            self.model_info.be_follow_count = Utility.user.get('be_follow_count');
            self.model_info.level = Utility.user.get('level');
            self.model_info.jifen = Utility.user.get('jifen');
            self.model_info.comment_stars_list = Utility.user.get('comment_stars_list');

            //构造轮播图数据
            var model_pic_len = self.model_info.model_pic.length;
            if(model_pic_len > 0)
            {
                // modify by hudw
                // hudw 2015.3.10
                self.model_info.new_model_pic[0] =self.model_info.model_pic.slice(0,3);

                if(model_pic_len>3)
                {
                    self.model_info.new_model_pic[1] =self.model_info.model_pic.slice(3,9);
                }

                if(model_pic_len>9)
                {
                    self.model_info.new_model_pic[2] =self.model_info.model_pic.slice(9,15);
                }


                /*self.model_info.new_model_pic[0] =self.model_info.model_pic.slice(0,3);

                if(model_pic_len > 3)
                {
                    self.model_info.new_model_pic[1] =self.model_info.model_pic.slice(3,upload_pic_limit);
                }*/
            }

            self.model_info.user_name = self.model_info.nickname;
            self.model_info.bust = cup;
            //self.model_info.user_icon = self.$('[data-role=data-image]').attr('data-image');
            self.model_info.user_icon = self.$('[data-role=avatar-img]').attr('data-image');
            self.model_info.BWH = self.$cwh.html();
            self.model_info.city_name = self.$select_city.html();
            //=================================================================================

        },

        /**
         * 设置模特信息
         * @param model_info
         * @return {*}
         * @private
         */
        _set_model_info : function(model_info){
            var self = this;
            self.model_info = model_info;
            return self;
        },

        _save_info: function() {
            var self = this;

            self._get_all_value();

            //错误提示
            var error_txt = null;
            //!self.model_info.intro && (error_txt = '请填写备注');
            !self.model_info.sex && (error_txt = '请填选择性别');
            !self.model_info.nickname && (error_txt = '请填写昵称');
            !self.model_info.location_id && (error_txt = '请填写城市');
            !self.model_info.height && (error_txt = '请填写身高');
            !self.model_info.weight && (error_txt = '请填写体重');
            !self.model_info.height && (error_txt = '请填写身高');
            !self.model_info.cup && (error_txt = '请选择罩杯');
            (!self.model_info.cover_img || self.model_info.cover_img =='undefined') && (error_txt = '请上传一张封面图');
            !self.$cwh && (error_txt = '请填写三围');
            self.price_error && (error_txt = '请填写风格及价格的相关信息');
            //self.model_info.model_type.length < 1 && (error_txt = '至少选择一个拍摄类型');
            self.model_info.pic_arr.length < 3 && (error_txt = '至少上传3张个人作品');
            (self.model_info.limit_num < 1 || self.model_info.limit_num > 10 || !(/^\d*$/.test(self.model_info.limit_num))) && (error_txt = '每次拍摄人数设置有误');
            //self.model_info.model_style_arr < 1 && (error_txt = '至少选择一个风格');
            //!self.model_info.cameraman_require && (error_txt = '请选择一个信用金');
            !self.model_info.level_require && (error_txt = '请选择一个信用金');
            self.model_style_card_has_empty_value  && (error_txt = '风格设置中还有未填选项！');



            if(error_txt){
                Tip.show(error_txt,'error');
                self.price_error && (self.price_error = false);
                return;
            }
            //删除不需要的字段
            delete self.model_info.balance_require;
            //delete self.model_info.model_style;

            var data = self.model_info;




            //上传到服务器
            self.model.save_model_card_info(self.model_info);
        },

        render : function()
        {
            var self = this;

            self._visible = true;

            View.prototype.render.apply(self);

            //高度不够时增加高度出现滚动条实现图片后加载
            if(self.$model_pic && self.$model_pic.height() < Utility.get_view_port_height()){
                self.$model_pic.css({
                    'min-height':Utility.get_view_port_height()
                })
            }



            self.trigger('render');

            return self;
        },

        /**
         * 格式化数字
         * @returns Float
         */
        to_number : function(s) {
        return parseFloat(s, 10) || 0;
        },

        /**
         * 获得一个随机数
         */
        get_random : function(){
            return Math.random().toString().replace('.', '');
        }
        /**
         * 安装底部导航
         * @private
         */
//        _setup_footer : function()
//        {
//            var self = this;
//
//            var footer_obj = new footer
//            ({
//                // 元素插入位置
//                parentNode: self.$el,
//                // 模板参数对象
//                templateModel :
//                {
//                    // 高亮设置参数
//                    is_model_pai : true
//                }
//            }).render();
//        }

    });

    function right_trim(str)
    {
        return str.replace(/\s*$/, '');
    }

    module.exports = model_caed_edit_view;
});