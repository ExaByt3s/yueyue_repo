define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var scroll = require('../../common/scroll');
    var upload_pic = require('../../widget/upload_pic/view');
    var app = require('../../common/I_APP');
    var utility = require('../../common/utility');
    var global_config = require('../../common/global_config');
    var tip = require('../../ui/m_alert/view');
    var dialog = require('../../ui/dialog/index');
    var location_data = require('../../common/location_data');
    var m_select = require('../../ui/m_select/view');



    var main_tpl = require('./tpl/main.handlebars');

    var action_apply_view = view.extend({
        attrs:{
            template : main_tpl
        },
        events:{
            'tap [data-role=navigate]' : '_navigateToPage',
            'tap [data-role=avatar-img]' : '_upload_avater',
            'tap [data-role=save]' : '_save_info',
            'tap [data-role=page-back]' : function()
            {
                var self = this;
                if(!!self.has_change)
                {
                    var save_dialog = new dialog({
                        content: '<p>是否保存已修改内容？</p>',
                        buttons: [{
                            name: 'save',
                            text: '确定'
                        },{
                            name: 'close',
                            text: '取消'
                        }]
                    }).on('tap:button:save', function() {
                            self._save_info();
                            this.hide().destroy();
                        }).on('tap:button:close', function() {
                            this.hide().destroy();
                            page_control.back();
                        }).show();
                }
                else
                {
                    if(self.get('is_from_reg'))
                    {
                        page_control.navigate_to_page('mine');
                    }
                    else
                    {
                        page_control.back();
                    }

                }
            },
            'tap [data-role="location"]' : function()
            {
                var self = this;

                self.province.show();
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

            self.on('render',function(){
                if(!self.view_scroll_obj)
                {

                    self._setup_scroll();

                    self.view_scroll_obj.refresh();
                    return;
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
            var view_scroll_obj = scroll(self.$container,
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

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$uploadfile = self.$('[data-role=uploadfile]');

            self.$avatar = self.$('[data-role=avatar-img]');
            self.$saveBtn = self.$('[data-role=save]');
            self.$my_photo = self.$('[data-role=my-photo]');


            self.$nickname = self.$('[data-target=edit-nickname]');
            self.$phone = self.$('[data-target=edit-phone]');
            self.save_info = self.model.toJSON();

            self.has_change = false;

            /*// 安装事件
            self._setup_events();*/

            var pic_list = [];
            if(self.save_info && self.save_info.pic_arr){
                var _pic_list = self.save_info.pic_arr;
                if(_pic_list.length>0){
                    for(var i=0;i < _pic_list.length; i++){
                        pic_list.push(_pic_list[i].img);
                    }
                }
            }

            self._setup_upload_pic(pic_list);

            self._setup_select_location();

            // 安装事件
            self._setup_events();

            //self.view_scroll_obj.refresh();


        },

        _upload_avater : function(){
            var self = this;

            app.upload_img
            (   'header_icon',
                {
                    is_async_upload : 0,
                    max_selection : 1

                },function(data)
                {
                    console.log(data);

                    if(data.code == '1000'){
                        tip.show('上传失败', 'error', {
                            delay: 800
                        });
                    }else if(data.code == '1001'){
                        tip.show('上传被取消', 'error', {
                            delay: 800
                        });
                    }else if(data.imgs.length>0)
                    {
                        self.$avatar.css({backgroundImage: 'url()'});
                        var url = data.imgs[0].url + '?' + self.get_random();

                        self.$avatar.css({
                            backgroundImage: 'url(' + url + ')'
                        }).attr({
                            'data-image' : url
                        })


                        self.model.set({
                            'user_icon' : url
                        });
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
            var pic_w_h = Math.ceil(((utility.get_view_port_width() - 66) / 3));


            self.upload_pic_view = new upload_pic({
                templateModel :
                {
                    max_size : pic_w_h//设置方图的宽高（这里为设置添加按钮宽高）
                },
                parentNode:self.$my_photo
            }).set_w_h(pic_w_h).render();

            //有图片传入则添加
            if(pic_list){
                self.upload_pic_view.add_pic(pic_list);
            }

            self.upload_pic_view.on('tap:upload_pic',function(){

                /*var pic_list = [
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg'
                ]
                self.upload_pic_view.add_pic(pic_list);
                return;*/

                app.upload_img
                ('multi_img',{
                    is_async_upload : 0,
                    max_selection : 9 - self.upload_pic_view.get_amount(),
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
                        self.view_scroll_obj.resetLazyLoad();
                        self.view_scroll_obj.refresh();
                        self.view_scroll_obj.reset_top();
                    }
                });

            });


        },

        /**
         * 导航到目的页面
         * @param event
         * @private
         */
        _navigateToPage: function(event) {
            var self = this;

            var $target = $(event.currentTarget);
            var targetNav = $target.attr('data-target');

            self._editData || (self._editData = {});

            switch (targetNav) {
                case 'location':
                    self._locationSelected = {};
                    page_control.navigate_to_page('location', self._locationSelected);
                    break;
                case 'modify-password':
                    page_control.navigate_to_page('mine/password');
                    break;
                case 'edit-nickname':
                    self._editData.type = targetNav;
                    page_control.navigate_to_page('mine/edit/nickname', self._editData);
                    break;
                case 'edit-phone':
                    self._editData.type = targetNav;
                    page_control.navigate_to_page('mine/edit/phone', self._editData);
                    break;
                case 'edit-signature':
                    self._editData.type = targetNav;
                    page_control.navigate_to_page('mine/edit/signature', self._editData);
                    break;
            }
        },

        /**
         * 获得一个随机数
         */
        get_random : function(){
        return Math.random().toString().replace('.', '');
        },

        /**
         * 判断资料是否有改变
         * @private
         */
        _has_change_info : function(){
            var self = this;
            self.has_change = true;
            //self.$saveBtn.prop('disabled', false);

        },

        /**
         * 用户信息更新处理集合
         */
        _update_info_before: function() {
            tip.show('提交中', 'loading', {
                delay: -1
            });
        },

        _update_info_success: function() {

            var self = this;

            tip.show('保存成功', 'right', {
                delay: 800
            });
            this.model.set({
                'nickname' : this.save_info.nickname,
                'pic_arr' : this.save_info.pic_arr
            })
            self.has_change = false;

            // 新注册用户修改成功后直接跳转到首页 modify hudw 2014.9.27
            if(utility.int(self.get('is_from_reg')) == 1)
            {
                page_control.navigate_to_page('mine');
            }
            else
            {
                page_control.back();
            }

        },
        _setup_select_location : function()
        {
            var self = this;

            var province = location_data.two_lv_data.province;
            var city = location_data.two_lv_data.city;



            var province_arr = province;
            var city_arr = city;
            var selected_address = '请选择城市';
            var selected_city_str = '';
            var selected_province_str = '';


            /*province.unshift({"value":"","text":"请选择省",selected : true});
            city_arr.unshift({"value":"","text":"",selected : true});*/

            if(self.save_info && self.save_info.location_id)
            {
                var city_id = self.save_info.location_id;

            }
            else
            {
                var city_id = utility.get('location') && utility.get('location').location_id;
            }

            var province_id = self.to_number(city_id && city_id.slice(0,6));

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

            if(self.save_info && self.save_info.location_id)
            {
                selected_address = selected_province_str+'-'+selected_city_str;
            }

            self.$('[data-role="location"]').attr('data-city-id',self.save_info.location_id).html(selected_address);

            var city_arr = city[province_id];

            // 级联查询必须有“不限”的数据组
            self.province = new m_select
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


            self.province.on('change:options',function(arr,cur_scroll_obj)
            {

                if(cur_scroll_obj.index == 0)
                {
                    var key = arr[0].id;

                    var options = city[key];

                    console.log(options)

                    if(options)
                    {
                        options[0].selected = true;

                        self.province.set_options([options],1);
                    }


                }

            });

            // 地址确认
            self.province.on('confirm:success',function(arr)
            {
                if(utility.is_empty(arr[0].value))
                {
                    m_alert.show('请选择省份','right');

                    return;
                }

                self.$('[data-role="location"]').attr('data-city-id',arr[1].id).html(arr[0].value+"-"+arr[1].value);

            });
        },
        _update_info_error: function() {
            tip.show('保存失败', 'error', {
                delay: 800
            });
        },


        _save_info: function() {
            var self = this;


            var nickname = $.trim(self.$nickname.html());
            var pic_arr = self.upload_pic_view.get_value();


            //给接口的数据
            var saveInfo = {
                nickname : nickname,
                //phone : $.trim(self.$phone.html()),
                pic_arr : pic_arr,
                location_id : utility.int(self.$('[data-role="location"]').attr('data-city-id'))
            };

            //给模型的数据
            self.save_info.nickname = nickname;

            var model_pic = [];
            for(var i=0;i < pic_arr.length; i++){
                model_pic.push({
                     'img' : pic_arr[i],
                     'user_icon' : pic_arr[i],
                     'big_user_icon' : utility.matching_img_size(pic_arr[i])
                });
            }

            self.save_info.pic_arr = model_pic;

            self.model.update_info(saveInfo);
        },

        render : function()
        {
            var self = this;

            self._visible = true;

            view.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },

        /**
         * 格式化数字
         * @returns Float
         */
        to_number : function(s) {
            return parseFloat(s, 10) || 0;
        }

    })

    module.exports = action_apply_view;
});