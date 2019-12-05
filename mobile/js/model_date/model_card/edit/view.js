define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var Scroll = require('../../../common/scroll');
    var Dialog = require('../../../ui/dialog/index');
    var choosen_group_view = require('../../../widget/choosen_group/view');
    var select_view = require('../../../widget/select/view');
    var location_data = require('../../../common/location_data');
    var upload_pic = require('../../../widget/upload_pic/view');
    var App = require('../../../common/I_APP');
    var Utility = require('../../../common/utility');
    var Tip = require('../../../ui/m_alert/view');
    var m_select = require('../../../ui/m_select/view');


    var works_html_s = '个人作品（'
    var works_html_e = '/15）'



    var main_tpl = require('./tpl/main.handlebars');
    var popup_item_tpl = require('./tpl/popup_item.handlebars');

    var model_caed_edit_view = View.extend({
        attrs:{
            template:main_tpl
        },
        events:{
            'tap [data-role=navigate]' : '_navigate_to_page',
            'tap [data-role=popup]' : '_popup',
            'tap [data-role=change-avatar]' : '_changeAvatarFromAPP',
            'tap [data-role=avatar-img]' : '_upload_avater',

            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                if(App.isPaiApp)
                {
                    App.switchtopage({page:'mine'});
                }
                else
                {
                    if(Utility.int(self.get('is_can_back')))
                    {
                        page_control.navigate_to_page('mine');
                    }
                    else
                    {
                        page_control.back();
                    }
                }




            },
            'tap [data-role="select-sex"]' : function()
            {
                var self = this;

                self.select_sex_view.show();
            },
            'tap [data-role="select-city"]' : function()
            {
                var self = this;

                self.select_province_view.show();
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
                 .listenTo(self.model, 'complete:update_avater:save',self._complete_update_avater);


            //安装下拉选择框
            self._setup_select();
            /*var pic_list = [
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg',
                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg'
            ]*/



            //安装封面图上传组件
            var cover_arr = [Utility.user.get('cover_img')];
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



            self.on('render',function(){
                if(!self.view_scroll_obj)
                {

                    self._setup_scroll();

                }

                self.view_scroll_obj.refresh();

            });

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

            //self.view_scroll_obj.refresh();
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

            self.$avatar = self.$('[data-role=avatar-img]');
            self.$saveBtn = self.$('[data-role=save]');
            self.$province = self.$('[data-role=province]');
            self.$city = self.$('[data-role=city]');
            self.$works = self.$('[data-role=works]');


            self.$shoot_style = self.$('[data-role=shoot-style]');
            self.$upload_pic = self.$('[data-role=upload-pic]');
            //self.$upload_cover = self.$('[data-role=upload-cover]');
            self.$nickname = self.$('[data-role=nickname]');

            self.$height = self.$('[data-target=height]');//身高
            self.$weight = self.$('[data-target=weight]');//体重
            self.$cup = self.$('[data-role=cup]');//罩杯
            self.$cwh = self.$('[data-target=cwh]');//三围
            self.$select_sex = self.$('[data-role=select-sex]');//性别
            self.$remarks = self.$('[data-role=remarks]');//备注
            //self.$remarks.html('期待与摄影大师出大片。左手臂上有纹身详情看我的作品图');
            self.$upload_cover = self.$('[data-role=upload-cover]');



            // 安装事件
            self._setup_events();
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
                        self.$avatar.css({
                            backgroundImage: 'url(' + url + ')'
                        }).attr({
                            'data-image' : url
                        })
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
                        console.log(data.imgs);
                        if(data.imgs.length>0)
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

            // modify by hudw 2015.3.2
            // 增加上传图片的上限数
            var upload_pic_limit = 15;

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
                        console.log(data.imgs);
                        if(data.imgs.length>0)
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
                                self.view_scroll_obj.change_scroll_position();
                                //self.view_scroll_obj.reset_top();
                            }, 50);


                            self.$works.html(works_html_s + self.upload_pic_view.get_amount() + works_html_e);
                        }
                    });
                }else{

                    var pic_list = [
                     'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg'
                     ]
                     self.upload_pic_view.add_pic(pic_list);
                     self.$works.html(works_html_s + self.upload_pic_view.get_amount() + works_html_e);
                     self.view_scroll_obj.refresh();
                     self.view_scroll_obj.resetlazyLoad();
                     self.view_scroll_obj.change_scroll_position();
                     //self.view_scroll_obj.reset_top();
                     return;

                }

                self.$works.html(works_html_s + self.upload_pic_view.get_amount() + works_html_e);

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

            //罩杯下拉参数
            if(cup_arr.length>0){
                for(var i=0;i<cup_arr.length;i++){
                    (cup_arr[i].value == cup.replace(/\d*/,"")) && (cup_arr[i].selected = true)
                }
            }

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
                (sex_arr[i].value == Utility.user.get('sex') || '女') && (sex_arr
                    [i].selected = true)
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



        },

        /**
         * 安装选择组模块
         * @private
         */
//        _setup_choosen_group : function(list)
//        {
//            var self = this;
//
//            self.choosen_group_view = new choosen_group_view
//            ({
//                templateModel :
//                {
//                    list : list
//                },
//
//                parentNode : self.$shoot_style,
//                is_multiply : true
//            }).render();
//
//        },

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
                            'input-type': 'tel',
                            'input-role': 'cup',
                            'input-data-role': 'cup'
                        }
                    ]
                    break;
                case 'cwh':
                    items=[
                        {
                            'unit': 'cm',
                            'name' : '胸围',
                            'input-type': 'tel',
                            'input-role': 'cwh',
                            'input-data-role': 'chest'
                        },
                        {
                            'unit': 'cm',
                            'name' : '腰围',
                            'input-type': 'tel',
                            'input-role': 'cwh',
                            'input-data-role': 'waist'
                        },
                        {
                            'unit' : 'cm',
                            'name' : '臀围',
                            'input-type': 'tel',
                            'input-role': 'cwh',
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
                                if(this_text > 200){
                                    Tip.show('三围不能有超过200cm的哦！', 'error', {
                                        delay: 800
                                    });
                                    return false;
                                }
                                break;
                            case 'waist':
                                if(this_text > 200){
                                    Tip.show('三围不能有超过200cm的哦！', 'error', {
                                        delay: 800
                                    });
                                    return false;
                                }
                                break;
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
        _navigate_to_page: function(event) {
            var self = this;

            var $target = $(event.currentTarget);
            var targetNav = $target.attr('data-target');

            self._editData || (self._editData = {});

            switch (targetNav) {
                case 'next':
                    self._editData.type = targetNav;
                    var model_type_arr = [];
                    //构造model_types数组
//                    var _model_type_arr = self.choosen_group_view.get_value();
//                    if(_model_type_arr.length>0){
//                        for(var i=0; i<_model_type_arr.length; i++){
//                            model_type_arr.push(_model_type_arr[i].text);
//                        }
//                    }
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
                    self.model_info.intro = self.$remarks.html()//备注
                    self.model_info.sex = self.$select_sex.html()//性别
                    self.model_info.model_type = model_type_arr;

                    // modify by hudw 2015.1.8
                    //self.model_info.cup = self.$cup.html().replace(/\d*/,"");//去掉数字;
                    var cup = self.$cup.html();
                    self.model_info.cup = cup.replace(/\d*/,"");//去掉数字
                    self.model_info.chest_inch = self.chest_inch;
                    self.model_info.cup_v2 = self.model_info.chest_inch + self.model_info.cup;


                    self.model_info.chest = self.$cwh.attr('data-chest');
                    self.model_info.waist = self.$cwh.attr('data-waist');
                    self.model_info.hip = self.$cwh.attr('data-hip');

                    //错误提示
                    var error_txt = null;
                    //!self.model_info.intro && (error_txt = '请填写备注');
                    !self.model_info.sex && (error_txt = '请填选择性别');
                    !self.model_info.nickname && (error_txt = '请填写昵称');
                    !self.model_info.location_id && (error_txt = '请填写城市');
                    !self.model_info.height && (error_txt = '请填写身高');
                    !self.model_info.weight && (error_txt = '请填写体重');
                    (!self.model_info.cover_img || self.model_info.cover_img =='undefined') && (error_txt = '请上传一张封面图');
                    !self.model_info.height && (error_txt = '请填写身高');
                    !self.model_info.cup && (error_txt = '请选择罩杯');
                    !self.$cwh && (error_txt = '请填写三围');
                    //self.model_info.model_type.length < 1 && (error_txt = '至少选择一个拍摄类型');
                    self.model_info.pic_arr.length < 3 && (error_txt = '至少上传3张个人作品');


                    if(error_txt){
                        Tip.show(error_txt,'error');
                        return;
                    }

                    page_control.navigate_to_page('model_date/model_card/edit_condition', {model_info:self.model_info});
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

//            var model_type = Utility.user.get('model_type');
//            console.log(Utility.user.toJSON())
//            var data_model_type = data.model_type;
//
//
//            //设置拍摄类型选中
//
//            if(model_type && model_type.length>0){
//                for(var n=0;n<model_type.length; n++){
//                    var this_model_type = model_type[n];
//                    for(var i=0; i<data_model_type.length; i++){
//                        if(Utility.obj_Equal(data_model_type[i],this_model_type)){
//                            data_model_type[i].selected = true;
//                        }
//                    }
//                }
//            }
//
//
//
//            // 安装选择模块
//            self._setup_choosen_group(data_model_type);
//
//            self.choosen_group_view.on('success:selected',function(obj){
//                //self.model_info.model_type[0] = obj.value;
//            })

            self.model_info.balance_require = data.balance_require;
            self.model_info.model_style = data.model_style;
            self.model_info.model_style_count = data.model_style.length;//模特风格总数

            //self.view_scroll_obj.refresh();
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

        render : function()
        {
            var self = this;

            self._visible = true;

            View.prototype.render.apply(self);

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

    })

    module.exports = model_caed_edit_view;
});