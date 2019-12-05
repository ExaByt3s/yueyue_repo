/**
 * 首页 - 城市选择
 * 汤圆 2014.1.8
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var View = require('../common/view');
    var location = require('../location/tpl/main.handlebars');
    var utility = require('../common/utility');
    var templateHelpers = require('../common/template-helpers');
    var Scroll = require('../common/scroll');
    var footer = require('../widget/footer/index');
    var location_data = require('../common/location_data');
    var city_tpl = require('./tpl/city.handlebars');
    var m_alert = require('../ui/m_alert/view');
    var I_App = require('../common/I_APP');
    var cookie = require('cookie');
    var WeiXinSDK = require('../common/WX_JSSDK');
    var global_config = require('../common/global_config');

   var gps_now_city = require('./tpl/gps_now_city.handlebars');

    var location_view = View.extend
    ({

        attrs:
        {
            template: location
        },
        events :
        {
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role=date-city]' : function (ev)
            {
                var self = this;
                var $current_target = $(ev.currentTarget);

                var city_data =
                {
                    'location_id' : $current_target.attr('data-location-id'),
                    'location_name' : $current_target.attr('data-location-name')
                };

                utility.storage.set('location',city_data);

                cookie.set("yue_location_id",city_data.location_id);

                self.location_page_back();

            },
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;
                // self.location_page_back();
                page_control.back()

            },
            'tap [data-role="now-city"]' : function(ev)
            {
                var self = this; 
                self.is_open_city($(ev.currentTarget));
            }
        },

        //判断当前城市是否在全部开通城市中
        is_open_city : function(ev)
        {

            var self = this;
            var $current_target = ev;
            var now_city_txt = $current_target.attr('data-location-name');
            // console.log(now_city_txt);
            // var now_city_txt = '茂名';
            var all_open_city_list = [] ;
            $.each(self.all_open_city, function(i, val) {
                all_open_city_list.push(val.city);
            });

            var is_arr = $.inArray(now_city_txt, all_open_city_list);
            if (is_arr == -1) 
            {
                m_alert.show('该城市暂未开通服务，敬请期待！','error') ;
                return ;
            }

            var city_data =
            {
                'location_id' : $current_target.attr('data-location-id'),
                'location_name' : $current_target.attr('data-location-name')
            };

            utility.storage.set('location',city_data);

            cookie.set("yue_location_id",city_data.location_id);

            self.location_page_back();

        },
        location_page_back : function()
        {
            var self = this;

            if(self.get("from") == "from_hot")
            {
                page_control.navigate_to_page("hot")
            }
            else if(self.get("from") == "from_find")
            {
                page_control.navigate_to_page("find")
            }
            else
            {
                console.log('from_no_where')
                //会白屏
                page_control.back()
            }

            // modify by hudw 2015.2.5
            // 不是最好的解决方案，强制刷新
            m_alert.show('正在切换地区','loading',{delay:-1});

            setTimeout(function()
            {
                window.location.reload();
            },2000);

        },
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;
            self.collection
            .on('reset',self._reset,self)
            .on('add', self._add_one, self)
            .on('before:fetch',function(response,xhr)
            {
                m_alert.show('加载中...','loading');
            })
            .on('success:fetch',function(response,xhr)
            {
                m_alert.hide();

                self._render_city(response,xhr);
            })
            .on('error:fetch',function(response,xhr)
            {
                m_alert.show('网络异常','error',{delay:1000});

                self._render_city(response,xhr);
            })
            .on('complete:fetch',function(xhr,status)
            {

            });

            self.on('update_list',function(response,xhr)
            {

                // 区分当前对象
                var _self = this;

                self._setup_scroll();
                self.view_scroll_obj.refresh();

                // 重置渲染队列
                self._render_queue = [];
            })
            .once('render',self._render_after,self);

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
        /**
         * 安装底部导航
         * @private
         */
        _setup_footer : function()
        {
            var self = this;
            var footer_obj = new footer
            ({
                // 元素插入位置
                parentNode: self.$el,
                // 模板参数对象
                templateModel :
                {
                    // 高亮设置参数
                    is_model_pai : true
                }
            }).render();
        },
        _reset : function()
        {
            var self = this;
            self.collection.length && self.collection.each(self._add_one,self);
        },
        _add_one : function(model)
        {
            var self = this;
            self._render_queue.push(model.toJSON());
            return self;
        },
        
        /**
         * 渲染城市
         * @param response
         * @param options
         * @private
         */
         _render_city : function(response,xhr)
        {
            var self = this;
            var render_queue = self._render_queue[0];

            //全部开通城市数据
            self.all_open_city = render_queue.data ;
            var html_str = city_tpl
            ({
                list : render_queue

            });
            self.$hot_city_container.html(html_str);
            self.trigger('update_list',response,xhr);
        },

        _render_after :function()
        {
            var self = this ;
            // self.$('[data-role="now-city-text"]').html(utility.storage.get('location').location_name)
            self.collection.get_list();

        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {
            var self = this;

            // 渲染队列
            self._render_queue = [];
            // 滚动容器
            self.$container = self.$('[data-role=container]');
            // 热门城市容器
            self.$hot_city_container = self.$('[data-role=hot-city-container]');
            self.$gps_txt = self.$('[data-role=gps_txt]');
            self.$now_city_ele = self.$('[data-role=now-city-ele]');

            // 安装事件
            self._setup_events();
            // 安装底部导航
            //self._setup_footer();

            //微信版本判断
            if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")//(global_config.WeiXin_Version.indexOf('6.0.2') != -1|| global_config.WeiXin_Version.indexOf('6.1') != -1)
            {
                WeiXinSDK.ready(function()
                {
                    //获取地区
                    WeiXinSDK.getLocation(
                        {
                            success : function(res)
                            {
                                //var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                                //var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                                //var speed = res.speed; // 速度，以米/每秒计
                                //var accuracy = res.accuracy; // 位置精度
                                utility.ajax_request
                                ({
                                    url:global_config.ajax_url.get_location_by_gps,
                                    data:{long:res.longitude,lat:res.latitude},
                                    cache : false,
                                    success : function(response, xhr, options)
                                    {
                                        var location_id = response.result_data.data.location_id;
                                        var city = response.result_data.data.city;
                                        var address = response.result_data.data.address;
                                        var province = response.result_data.data.province;

                                        cookie.set("yue_location_id",location_id);

                                        var location_obj =
                                        {
                                            location_id : location_id,
                                            location_name : city
                                        };

                                        utility.storage.set('location',location_obj);

                                        // alert("OK" + JSON.stringify(response));

                                        self.$gps_txt.html('GPS当前定位城市') ;

                                        var gps_now_city_html = gps_now_city(location_obj);
                                        self.$now_city_ele.html(gps_now_city_html);
                                    },
                                    error : function(collection, response, options)
                                    {
                                        self.$gps_txt.html('GPS城市定位失败，默认显示') ;

                                        self.can_not_gps();
                                    },
                                    complete : function(xhr,status)
                                    {

                                    }
                                });
                            },
                            cancel : function()
                            {
                                alert('您不允许GPS定位当前城市，默认显示');
                                self.$gps_txt.html('显示默认城市') ;
                                self.can_not_gps();
                            },
                            fail : function(xhr,status)
                            {
                                self.$gps_txt.html('GPS城市定位失败，默认显示') ;

                                self.can_not_gps();

                            }
                        });


                });
            }
            else
            {
                self.can_not_gps();
            }

        },
        // 如果微信版本小于6.1，与用户不允许gps，默认显示广州;
        can_not_gps : function() 
        {
            var self = this;
            var gps_now_city_html = gps_now_city({
                location_id: "101029001", 
                location_name: "广州"
            });
            console.log(utility.storage.get('location'));
            self.$now_city_ele.html(gps_now_city_html);
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

    module.exports = location_view;
});