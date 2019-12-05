/**
 * Created by nolest on 2014/8/30.
 * 活动列表
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var select_view = require('../../../widget/select/view');
    var location_data = require('../../../common/location_data');
    var m_alert = require('../../../ui/m_alert/view');
    var m_select = require('../../../ui/m_select/view');


    var pub_info_view = View.extend
    ({

        attrs:
        {
            template: tpl
        },
        forms :
        {
            select : {}
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role="page-back"]':function()
            {
                page_control.back();
            },
            'tap [data-role="next"]' : function()
            {
                var self = this;

                var obj = self._init_selected_value();

                if(obj.title.trim() == "")
                {
                    m_alert.show('标题不能为空','error');

                    return
                }
                if(obj.title.length >= 25)
                {
                    m_alert.show('标题过长','error');

                    return
                }
                if(obj.address == "请选择地址 ")
                {
                    m_alert.show('请选择地址','error');

                    return
                }
                if(obj.detail_location.trim() == "")
                {
                    m_alert.show('详细地址不能为空','error');

                    return
                }
                /* 2014-10-30 禅道20947
                if(obj.club_name == "" || obj.club_name == '请选择俱乐部')
                {
                    m_alert.show('请选择俱乐部','error');

                    return
                }
                */
                if(obj.budget == "" || obj.budget <= 0 || isNaN(obj.budget))
                {
                    m_alert.show('请正确填写费用','error');

                    return
                }
                if(obj.budget < 10)
                {
                    m_alert.show('至少10块','error');

                    return
                }

                if(obj.budget > 10000)
                {
                    m_alert.show('至多10000块','error');

                    return
                }




                page_control.navigate_to_page('act/pub_intro',obj)
            },
            /**
             * 开始时间
             */
            'tap [data-role="begin-time"]' : function()
            {
                var self = this;

                self.forms['select']['begin_time'].show();

            },
            /**
             * 结束时间
             */
            'tap [data-role="end-time"]' : function()
            {
                var self = this;

                self.forms['select']['end_time'].show();
            },
            /**
             * 选择地区
             */
            'tap [data-role="address"]' : function()
            {
                var self = this;

                self.forms['select']['province'].show();


            },
            /**
             * 选择俱乐部
             */
            'tap [data-role="club"]' : function()
            {
                var self = this;

                self.forms['select']['club'].show();
            }
        },
        _init_selected_value : function()
        {
            var self = this;

            var title = self.$title_input.val();

            /*var date_begin = self.forms['select']['year_begin'].get_value().value + "-" +
                self.forms['select']['month_begin'].get_value().value + "-" +
                self.forms['select']['day_begin'].get_value().value + "-" +
                self.forms['select']['hour_begin'].get_value().value;

            var date = self.forms['select']['year'].get_value().value + "-" +
                self.forms['select']['month'].get_value().value + "-" +
                self.forms['select']['day'].get_value().value + "-" +
                self.forms['select']['hour'].get_value().value;*/

            var start_time = self.$('[data-role="begin-time-str"]').html();
            var end_time = self.$('[data-role="end-time-str"]').html();

            var detail_location =  self.$detail_location.val();

            var address = self.$('[data-role="address_str"]').html()+" "+detail_location;

            var location_id = self.$('[data-role="address_str"]').attr('data-city-id');

            var club_id = self.$('[data-role="club_str"]').attr('data-club-id') || '';

            var club_name = self.$('[data-role="club_str"]').attr('data-club-name') || '';

            var fee = utility.int(self.$fee.val());

            var obj =
            {
                title : title,
                /*start_time : start_time,
                end_time : end_time,*/
                location_id : location_id,
                detail_location : detail_location,
                address : address,
                club : club_id,
                club_name : club_name,
                budget : fee,
                pay_type : 1
            };

            console.log(obj);

            return obj

        },
        _setup_events : function()
        {
            var self = this;

            var club_list = utility.storage.get("club");

            if( !club_list || club_list.length == 0 )
            {
                self.model
                    .on('success:fetch',function(response,options)
                    {
                        var data =response.result_data.list;

                        utility.storage.set("club",data);

                        self._setup_club_select(data);

                        // 俱乐部的更新依靠新旧数据的对比长度，这是暂时的处理办法，最好的办法应该是加上版本号
                        if(data.length != utility.storage.get("club").length)
                        {
                            utility.storage.remove("club");
                        }
                    })
            }
            else
            {
                var data = utility.storage.get("club");

                self._setup_club_select(data);
            }


        },
        _setup_club_select : function(data)
        {
            var self = this;

            var last_club_list = utility.storage.get("club");

            last_club_list.unshift({"value":"","text":"请选择俱乐部",selected : true})

            //last_club_list[0].selected = true;

            // 选择俱乐部
            self.forms['select']['club'] = new m_select
            ({
                templateModel :
                {
                    options : [last_club_list]

                },
                parentNode: self.$el
            }).render();

            // 确认俱乐部
            self.forms['select']['club'].on('confirm:success',function(arr)
            {
                self.$('[data-role="club_str"]').attr({'data-club-id':arr[0].value,'data-club-name':arr[0].text}).html(arr[0].text);
            });



        },
        _setup_select : function()
        {

            var self = this;

            var province = location_data.two_lv_data.province;
            var city = location_data.two_lv_data.city;

            var date_arr = utility.select_time.mix_date().date_arr;
            var hour_arr = utility.select_time.mix_date().hour_arr;
            var min_arr = utility.select_time.mix_date().min_arr;

            self.forms['select']['begin_time'] = new m_select
            ({
                templateModel :
                {
                    options : [date_arr,hour_arr,min_arr]

                },
                parentNode: self.$el
            }).render();

            self.forms['select']['end_time'] = new m_select
            ({
                templateModel :
                {
                    options : [date_arr,hour_arr,min_arr]
                },
                parentNode: self.$el
            }).render();

            // 开始时间确认
            self.forms['select']['begin_time'].on('confirm:success',function(arr)
            {
                var cur_year = utility.select_time.today_is().year;

                self.$('[data-role="begin-time-str"]').html(arr[0].value+" "+arr[1].value+":"+arr[2].value);
            });

            // 结束时间确认
            self.forms['select']['end_time'].on('confirm:success',function(arr)
            {
                var cur_year = utility.select_time.today_is().year;

                self.$('[data-role="end-time-str"]').html(arr[0].value+" "+arr[1].value+":"+arr[2].value);
            });

            // 设置默认市
            var city_arr = city[province[0].id];

            // 设置省第一项默认选中
            province[0].selected = true;
            city_arr[0].selected = true;

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

                    options[0].selected = true;

                    self.forms['select']['province'].set_options([options],1);
                }

            });

            // 地址确认
            self.forms['select']['province'].on('confirm:success',function(arr)
            {
                self.$('[data-role="address_str"]').attr('data-city-id',arr[1].id).html(arr[0].value+"-"+arr[1].value);

            });



            /*

            self.forms['select']['province'] = new select_view
            ({
                templateModel :
                {
                    options : province,
                    default_key : '北京'

                },
                parentNode: self.$select_container_for_province
            }).render();

            self.forms['select']['city'] = new select_view
            ({
                templateModel :
                {
                    options : city[self.forms['select']['province'].get_default_value().id]

                },
                parentNode: self.$select_container_for_city
            }).render();*/

            //self.forms['select']['province'].get_default_value()

            //下拉change事件获取选中的值
            /*self.forms['select']['province'].on('change:options',function(obj)
                {

                    var _self = this;

                    var city_obj = self.forms['select']['city'];

                    var id = obj.id;

                    city_obj.show();

                    city_obj.set_options({options:city[id]});
                });*/

        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {

                    is_hide_dropdown : true
                });

            view_scroll_obj.on('scrollMoveAfter',function()
            {
                var _self = this;

                console.log(_self);

                if(_self.maxScrollY - _self.y > 160)
                {

                }
            });

            self.view_scroll_obj = view_scroll_obj;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 50;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="action-info-content"]'); // 滚动容器

            self.$title = self.$('[data-role="title"]');

            self.$select_container_for_year = self.$('[data-role="select_container_for_year"]');

            self.$select_container_for_month = self.$('[data-role="select_container_for_month"]');

            self.$select_container_for_day = self.$('[data-role="select_container_for_day"]');

            self.$select_container_for_hour = self.$('[data-role="select_container_for_hour"]');

            self.$select_container_for_year_begin = self.$('[data-role="select_container_for_year_begin"]');

            self.$select_container_for_month_begin = self.$('[data-role="select_container_for_month_begin"]');

            self.$select_container_for_day_begin = self.$('[data-role="select_container_for_day_begin"]');

            self.$select_container_for_hour_begin = self.$('[data-role="select_container_for_hour_begin"]');

            self.$detail_location = self.$('[data-role="address-input"]');

            self.$fee = self.$('[data-role="fee-input"]');

            self.$title_input = self.$('[data-role="title"]');

            self.$select_container_for_province = self.$('[data-role="select_container_for_province"]');

            self.$select_container_for_city = self.$('[data-role="select_container_for_city"]');

            self.$select_container_for_club = self.$('[data-role="bar"]');

            self._setup_events();

            if(utility.storage.get("club"))
            {
                //使用缓存俱乐部数据
                self._setup_club_select(utility.storage.get("club"))
            }
            else
            {
                //首次未缓存时获取俱乐部数据
                self.model.get_list();
            }

            self._setup_scroll();

            self._setup_select();

            self.view_scroll_obj.refresh();

        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_scroll_height : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.view_scroll_obj.refresh();
        }

    });

    module.exports = pub_info_view;
});