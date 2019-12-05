/**
 * 提交约拍申请
 * nolestLam 2014.8.25
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var submit_application = require('../submit_application/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/scroll');
    var choosen_group_view = require('../../widget/choosen_group/view');
    var number_btn_view = require('../../widget/number_btn/view');
    var select_view = require('../../widget/select/view');
    //var location_data = require('../../common/location_data');
    var global_config = require('../../common/global_config');

    var m_alert = require('../../ui/m_alert/view');
    var m_select = require('../../ui/m_select/view');


    var submit_application_view = View.extend
    ({
        attrs:
        {
            template: submit_application
        },
        forms :
        {
            // 表单类
            number : {},
            select : {},
            choosen_btn : {}
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },

            'tap [data-role=btn-more-sec]' : function (ev)
            {
                var self = this;

                //self.$('[data-role=btn-more-sec]').find('.icon').toggleClass("icon-deg");

                self.$content_empty.toggleClass('fn-hide');

                self.$content_box.toggleClass('fn-hide');

                if(self.$content_box.hasClass('fn-hide'))
                {
                    self._is_show_detail = false;
                }
                else
                {
                    self._is_show_detail = true;
                }

                self.view_scroll_obj.refresh();


            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=submit-application-btn]' : function(ev)
            {
                var self = this;

                utility.page_pv_stat_action({tj_point:'submit_order'});
                
                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');
                    
                    return;
                }

                self._init_selected_value_to_model();

                if(self.cansend)
                {
                    console.log(self.model)
                    page_control.navigate_to_page('model_date/payment',self.model);

                    // debugger
                }

            },
            'tap [data-role="time"]' : function()
            {
                var self = this;

                if(self.model.get('model_selected_info') && self.model.get('model_selected_info').disable_time)
                {
                    return;
                }

                self.forms['select']['time'].show();
            },
            'tap [data-role="address"]' : function()
            {
                var self = this;

                if(self.model.get('model_selected_info') && self.model.get('model_selected_info').disable_address)
                {
                    return;
                }

                if(self.has_no_location_data)
                {
                    self._init_location_data();

                    return;
                }

                self.forms['select']['province'].show();
            },
            'tap [data-role="ui-choosen-btn"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var price = $cur_btn.attr('data-selected-price');

                self.forms['number']['price'].set_vaule(price);
            },
            'tap [data-role="add"]' : function()
            {
                var self = this;

                //self._set_price(self.forms['number']['time'].get_value());
            },
            'tap [data-role="minus"]' : function()
            {
                var self = this;

                //self._set_price(self.forms['number']['time'].get_value());
            },
            'tap [data-role="btn-test"]' : function()
            {
                var self = this;
                
                return;
                
                self.submit_yuepai();
            },
            'tap [data-role="detail-location"]' : function()
            {
                var self = this;

                if(self.model.get('model_selected_info') && self.model.get('model_selected_info').disable_address)
                {
                    return;
                }

                page_control.navigate_to_page('edit_page/textarea',
                    {
                        title:'详细地址',
                        text : self.$('[data-role="detail-location"]').val(),
                        edit_obj:self.$('[data-role="detail-location"]'
                )})
            }


        },

        /**
         * 安装事件
         * @private
         */

        _setup_events : function()
        {

            var self = this;

            self.on('render',function()
            {

                self.$('[data-role="total-price-v2"]').html(self.model.get("model_selected_info").combo_text);

                /*
                self.$('[data-role="input-num-btn"]')[0].oninput = function()
                {
                    var reg_num = new RegExp(/^[0-9]*$/);

                    if(!reg_num.test(self.forms['number']['time'].get_value()))
                    {
                        m_alert.show('请正确输入时长','error');

                        self.forms['number']['time'].set_vaule(utility.int(self.forms['number']['time'].get_value()))

                        return;

                    }
                    self._set_price(self.forms['number']['time'].get_value());
                }
                */
            });




            // 订单请求
            self.model
            .on('before:fetch', function()
            {

            }).on('success:fetch', function()
            {


            }).on('complete:fetch', function (xhr, status)
            {

            });

            self.on('update_info',function(response,xhr)
            {
                // 区分当前对象
                var _self = this;

                // 头部滚动条
                // self._setup_scroll_top();
                // self.view_scroll_obj_top.refresh();


                //主要滚动条
                //self._setup_scroll();
                //self.view_scroll_obj.refresh();

            });

            self._setup_scroll();
            self.view_scroll_obj.refresh();

            self.get('agreement_view_obj').on('receive',function()
            {
                self.cansend = true;

                self.is_receive = true;

                self._init_selected_value_to_model();

                page_control.navigate_to_page('model_date/payment',self.model);
            });
        },
        /**
         * 安装滚动条
         * @private
         */
            /*
        _set_price : function(hour)
        {
            var self = this;

            var style_price = self.model.get("model_selected_info").price;

            if(hour.trim() == '')
            {
                return
            }
            else
            {
                self.$('[data-role="total-price"]').html(utility.int(style_price*hour))

            }

        },
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
        /**
         * 安装选择组模块
         * @private
         */
        _setup_choosen_group : function()
        {
            var self = this;

            var key = self.model.get('model_selected_info').style;

            var model_style = self.model.get('model_style');

            //var model_type = self.model.get('model_type');

            var model_type = self.model.get('new_model_type_list');

            var style_list = [];

            $(model_style).each(function(i,obj)
            {
                if(obj.text == key)
                {
                    obj.selected = true;
                }
                else
                {
                    obj.selected = false;
                }

                style_list.push(obj);
            });

            console.log(style_list);
            self.forms['choosen_btn']['choosen_style_group'] = new choosen_group_view
            ({
                templateModel :
                {
                    list : style_list

                },
                parentNode: self.$choosen_style_group,
                is_multiply : false,
                css : "btn_width"
            }).render();

            self.forms['choosen_btn']['choosen_type_group'] = new choosen_group_view
            ({
                templateModel :
                {
                    list : model_type

                },
                parentNode: self.$choosen_type_group,
                is_multiply : false,
                css : "btn_wided_sec"
            }).render();

            self.forms['choosen_btn']['choosen_style_group'].on('success:selected',function(obj)
            {
                console.log(obj);
            });

        },
        /**
         * 安装数字按钮
         * @private
         */
        _setup_number_btn : function()
        {
            var self = this;

            /*
            self.forms['number']['price'] = new number_btn_view
            ({
                templateModel :
                {
                    type : 'tel'
                },
                min_value : 1,
                step : 50,
                parentNode: self.$number_for_price,
                value : self.model.get('model_selected_info').price,
                is_floor : true
            }).render();

            self.forms['number']['time'] = new number_btn_view
            ({
                templateModel :
                {
                    type : 'tel'
                },
                min_value : 1,
                step : 1,
                parentNode: self.$number_for_time,
                value : 1,
                is_floor : true
            }).render();
            */
            self.forms['number']['limit_num'] = new number_btn_view
            ({
                templateModel :
                {
                    type : 'tel',
                    disable : true
                },
                min_value : 1,
                max_value : self.get("templateModel").limit_num,
                step : 1,
                parentNode: self.$number_for_limit_num,
                value: self.model.get("model_selected_info").limit_num || 1,
                disable: self.get("is_from_custom_params") ? true : false,
                is_floor : true
            }).render();

        },

        /**
         * 安装下拉选择框
         * @private
         */
        _setup_select: function(location_data)
        {
            var self = this;

            self.has_no_location_data = false;

            var province = location_data.two_lv_data.province;
            var city = location_data.two_lv_data.city;

            var date_arr = utility.select_time.mix_date().date_arr;
            var hour_arr = utility.select_time.mix_date().hour_arr;
            var min_arr = utility.select_time.mix_date().min_arr;

            // 初始化时间
            // modify by hudw 2015.1.11
            if(self.model.get("model_selected_info") && self.model.get("model_selected_info").time)
            {
                var form_time = new Date(self.model.get("model_selected_info").time);
                var form_year = form_time.getFullYear();
                var form_month = form_time.getMonth()+1;
                var form_date = form_time.getDate();
                var form_hour = form_time.getHours();
                var form_min = form_time.getMinutes();

                for(var obj in date_arr)
                {
                    if(date_arr[obj].value == (form_year+'-'+form_month+'-'+form_date))
                    {
                        date_arr[obj].selected = true;
                    }
                    else
                    {
                        date_arr[obj].selected = false;
                    }
                }

                for(var obj in hour_arr)
                {
                    if(hour_arr[obj].value == form_hour)
                    {
                        hour_arr[obj].selected = true;
                    }
                    else
                    {
                        hour_arr[obj].selected = false;
                    }
                }

                for(var obj in min_arr)
                {
                    if(min_arr[obj].value == form_min)
                    {
                        min_arr[obj].selected = true;
                    }
                    else
                    {
                        min_arr[obj].selected = false;
                    }


                }

            }



            self.forms['select']['time'] = new m_select
            ({
                templateModel :
                {
                    options : [date_arr,hour_arr,min_arr]

                },
                parentNode: self.$el
            }).render();



            // 开始时间确认
            self.forms['select']['time'].on('confirm:success',function(arr)
            {

                self.$('[data-role="time_str"]').html(arr[0].value+" "+arr[1].value+":"+arr[2].value);
            });




            // 如果有自定义 location id 时进行初始化
            if(self.model.get("model_selected_info") && self.model.get("model_selected_info").location_id)
            {

                var location_id = self.model.get("model_selected_info").location_id;
                var city_id = location_id;
                var province_id = utility.int(city_id && city_id.toString().slice(0,6));

                // 设置默认市
                var city_arr = city[province_id];

                for(var i=0;i<province.length;i++)
                {
                    if(province[i].id == province_id)
                    {
                        province[i].selected = true;

                        break;
                    }
                }

                for(var i=0;i<city_arr.length;i++)
                {
                    if(city_arr[i].id == city_id)
                    {
                        city_arr[i].selected = true;

                        break;
                    }
                }

                var address_str = self.model.get('model_selected_info').address;

                self.$('[data-role="address_str"]')
                    .attr('data-city-id',city_id)
                    .html(address_str);

            }
            else
            {
                // 设置默认市
                var city_arr = city[province[0].id];

                // 设置省第一项默认选中
                province[0].selected = true;
                city_arr[0].selected = true;

                self.$('[data-role="address_str"]').attr('data-city-id',city_arr[0].id).html(province[0].text+"-"+city_arr[0].value);
            }

            // 级联查询必须有“不限”的数据组
            self.forms['select']['province'] = new m_select
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


            self.forms['select']['province'].on('change:options',function(arr,cur_scroll_obj)
            {

                if(cur_scroll_obj.index == 0)
                {
                    var key = arr[0].id;

                    var options = city[key];

                    console.log(options)

                    if(options)
                    {
                        options[0].selected = true;

                        self.forms['select']['province'].set_options([options],1);
                    }


                }

            });

            // 地址确认
            self.forms['select']['province'].on('confirm:success',function(arr)
            {
                if(utility.is_empty(arr[0].value))
                {
                    m_alert.show('请选择省份','right');

                    return;
                }

                self.$('[data-role="address_str"]').attr('data-city-id',arr[1].id).html(arr[0].value+"-"+arr[1].value);

            });
        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;
            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$container_img_list = self.$('[data-role=container-img-list]'); // 头部图片容器

            // 数字按钮
            //self.$number_for_price = self.$('[data-role="number-btn-container-for-price"]');
            //self.$number_for_time = self.$('[data-role="number-btn-container-for-time"]');
            self.$number_for_limit_num = self.$('[data-role="number-btn-container-for-limit_num"]');

            // 风格选择选项
            self.$choosen_style_group = self.$('[data-role="choosen_style_group"]');

            // 类型选择选项
            self.$choosen_type_group = self.$('[data-role="choosen_type_group"]');

            // 下拉选择容器
            self.$select_container_for_year = self.$('[data-role="select-container-for-year"]');
            self.$select_container_for_month = self.$('[data-role="select-container-for-month"]');
            self.$select_container_for_day = self.$('[data-role="select-container-for-day"]');
            self.$select_container_for_hour = self.$('[data-role="select-container-for-hour"]');
            self.$select_container_for_province = self.$('[data-role="select-container-for-province"]');
            self.$select_container_for_city = self.$('[data-role="select-container-for-city"]');
            //信息是否合法
            self.cansend = false;
            // 详细地址
            self.$detail_location = self.$('[data-role="detail-location"]');

            //空块
            self.$content_empty = self.$('[data-role="content-empty"]');
            // 填选项
            self.$content_box = self.$('[data-role="content-box"]');



            // 安装事件
            self._setup_events();

            // 安装按钮模块
            self._setup_number_btn();

            self._init_location_data();

            self.has_no_location_data = true;

            // 初始化订单
            self._init_forms();

            // 安装选择模块
            //self._setup_choosen_group();

            //self._setup_select(location_data);

            /*require.async('../../common/location_data',function(location_data)
            {

            });*/



        },
        /**
         * 初始化订单
         * @private
         */
        _init_forms : function()
        {
            var self = this;

            var model_selected_info = self.model.get('model_selected_info');

            // 初始化表单
            self.$('[data-role="time_str"]').html(model_selected_info.time || '');

            if(model_selected_info.is_from_custom_data)
            {
                var price = utility.int(model_selected_info.price);

                var hour = utility.int(model_selected_info.hour);

                self.model.get('model_selected_info').price = (utility.float(price * hour));

            }


        },
        submit_yuepai : function ()
        {
            var self = this;
            page_control.navigate_to_page('model_date/submit_success/12');
        },
        // 安装下拉模块
        _init_location_data : function()
        {

            var self = this;

            if(utility.storage.get('location_data_v2'))
            {
                self._setup_select(utility.storage.get('location_data_v2'));

                return;
            }

            utility.ajax_request
            ({
                url : global_config.ajax_url.location_data_v2,
                cache : true,
                beforeSend : function()
                {
                    m_alert.show('地区加载中...','loading',{delay:-1});
                },
                success : function(ret)
                {
                    m_alert.hide();

                    var data = ret.result_data;

                    var location_data_v2 =
                    {
                        two_lv_data :
                        {
                            province:JSON.parse(data.two_lv_data.province),
                            city : eval("(" + data.two_lv_data.city + ")")
                        }
                    };

                    utility.storage.set('location_data_v2',location_data_v2);

                    self._setup_select(location_data_v2);
                },
                error : function()
                {
                    self.has_no_location_data = true;

                    self.$('[data-role="address_str"]').html('点击此处 加载地区数据');

                    m_alert.show('网络异常','error');
                }
            })
        },
        /**
         * 初始化获取选中的值
         * @private
         */
        _init_selected_value_to_model : function()
        {
            var self = this;

            console.log(self.model);
            //var price = self.forms['number']['price'].get_value();
            //var dur_time  = self.forms['number']['time'].get_value();
            var limit_num = self.forms['number']['limit_num'].get_value();
            //var style = self.forms['choosen_btn']['choosen_style_group'].get_value();
            //var type  = self.forms['choosen_btn']['choosen_type_group'].get_value();
            var province_city = self.forms['select']['province'].get_value();


            /*var date = self.forms['select']['year'].get_value().value + "-" +
                       self.forms['select']['month'].get_value().value + "-" +
                       self.forms['select']['day'].get_value().value + "-" +
                       self.forms['select']['hour'].get_value().value;*/

            var date = self.$('[data-role="time_str"]').html();

            var detail_location =  self.$detail_location.val();

            var address = self.$('[data-role="address_str"]').html() + detail_location;

            var cameraman_require = utility.float(self.model.get('cameraman_require'));

            var available_balance = utility.user.get('available_balance');

            self.cansend = true;
            /*
            if(price == 0)
            {
                m_alert.show('出价不能为0','error');

                self.cansend = false;

                return
            }

            if(price < 10)
            {
                m_alert.show('至少10块','error');

                self.cansend = false;

                return
            }

            if(dur_time == 0)
            {
                m_alert.show('时长不能为0','error');

                self.cansend = false;

                return
            }

            var reg_num = new RegExp(/^[0-9]*$/);

            if(!reg_num.test(dur_time))
            {
                m_alert.show('请正确输入时长','error');

                //self._set_price(self.forms['number']['time'].get_value());

                //self.$('[data-role="total-price"]').html(self.model.get("model_selected_info").price);

                self.cansend = false;

                return
            }
            */
            /*
            if(price < 0 || isNaN(price))
            {
                m_alert.show('请正确输入价格','error');

                self.cansend = false;

                return
            }

            if(price > 10000)
            {
                m_alert.show('金额过高','error');

                self.cansend = false;

                return
            }

            if(dur_time < 0 || isNaN(dur_time))
            {
                m_alert.show('请正确输入时间','error');

                self.cansend = false;

                return
            }

            if(dur_time > 24)
            {
                m_alert.show('约拍时长不能超过24小时','error');

                self.cansend = false;

                return
            }
             */
            if(limit_num <= 0 || isNaN(limit_num))
            {
                m_alert.show('请正确输入人数','error');

                self.cansend = false;

                return
            }

            if(self.model.get('model_selected_info') && self.model.get('model_selected_info').limit_num)
            {
                if(utility.int(limit_num) > self.model.get('model_selected_info').limit_num)
                {
                    m_alert.show('人数过多','error');

                    self.cansend = false;

                    return;
                }
            }
            else
            {
                if(utility.int(limit_num) > utility.int(self.get("templateModel").limit_num))
                {
                    m_alert.show('人数过多','error');

                    self.cansend = false;

                    debugger;

                    return;
                }
            }

            if(date.trim() =='' || date.indexOf('data-role="notice-span"') > 0)
            {
                m_alert.show('请选择拍摄时间','error');

                self.cansend = false;

                return;
            }

            if(self.$detail_location.val().trim() == '')
            {
                m_alert.show('详细地址不能为空','error');

                self.cansend = false;

                return;
            }

            console.log(self.model.get('model_selected_info'))
            if(self.model.get('model_selected_info') && self.model.get('model_selected_info').text == '人体' && !self.is_receive)
            {

                self.cansend = false;

                self.get('agreement_view_obj').show();

                return;
            }

            console.log(self.model.get("model_selected_info"));
            var style = self.model.get("model_selected_info").text;
            /*if (self.get("is_from_custom_params")) {
                style = self.model.get("model_selected_info").style;

                self.model.set("user_name", self.model.get("model_selected_info").user_name);
            }*/

            self.model.set('model_selected_info',
                $.extend(self.model.get('model_selected_info'),{
                    style : style,// 风格
                    //price  : price, // 单价
                    //hour : dur_time,// 时长
                    limit_num : limit_num,//人数上限
                    total_price :  self.model.get('model_selected_info').price, // 总价
                    can_use_balance : available_balance>0?1:0,
                    //type  : type.length && type[0].text || '',// 类型
                    date  : date,// 日期
                    available_balance : available_balance, //自己的钱包
                    address : address// 地区,
                }));

            console.log(self.model.get('model_selected_info'))
        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            /*if (self.get("is_from_custom_params"))
            {
                self.$(".inside_arrow").addClass("fn-hide");
                self.$('[data-role="limit_num_str"]').addClass("fn-hide");
                var price = utility.int(self.model.get("model_selected_info").price);
                var hour = utility.int(self.model.get("model_selected_info").hour);

                self.model.get('model_selected_info').price = (utility.float(price * hour));

                self.$('[data-role="detail_price"]').html(utility.float(price * hour));
            }*/

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height() - 95;
        },
        reset_scroll_height : function()
        {
            console.log("22")
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.view_scroll_obj.refresh();
        }

    });

    module.exports = submit_application_view;
});