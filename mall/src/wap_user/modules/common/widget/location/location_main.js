/**
 *  汤圆
 *  2015-7-13
 *  地区组件
 */
 /**
  * @require ./location.scss
 **/

var $ = require('zepto');

// 基本城市基本数据
var hot_city_base = require('./hot_city_base');
var utility = require('../../utility/index');

var cookie = require('../../cookie/index');


// 全部城市
// var all_city = require('./all_city');

var all_city ;

// 定义函数对象
function location_fn(options) 
{
    var options = options || {} ;
    var self = this;
    self.ele_tpl = options.ele;
    self.$el = options.ele ;
    self.hot_city = options.hot_city || {} ;
    self.city_history_num = options.city_history_num || 12 ;  //显示多少浏览城市记录
    self.is_search = options.is_search || false ;
    self.hot_city.data = self.hot_city.data || [];
    self.callback = options.callback;
    self.verson = options.verson || 1 ;

    // 热门城市合并
    self.all_hot_city_base = hot_city_base.data.concat(self.hot_city.data);
    self.all_hot_city = 
    [
        {
            title : hot_city_base.title,
            data :  self.all_hot_city_base ,
            id : hot_city_base.id
        }
    ]

    //  初始化
    self.init();

    return self;
}

location_fn.prototype = 
{
    init : function() 
    {
        var self = this;
        // 安装事件
        self.setup_event();

        if (self.is_search) 
        {
            // 搜索功能
            self.search_city();
        }



        // 渲染模板
        self.render(self.ele_tpl);

        // 右侧拼音字母导航
        self.navigation();

    },

    setup_event : function() 
    {
        var self = this;

        // 数组去重扩展
        Array.prototype.uniqueFn = function () 
        {
            var n = [] ;
            for (var i = 0; i < this.length; i++) {
                
                if (n.indexOf(this[i]) == -1 )
                {
                    n.push(this[i])
                };
            };
            return  n ;
        }

        // self.go_top() ;
    },

    // 滚动条拖顶部
    go_top : function() 
    {
        // debugger;
        var self = this;
        window.scrollTo(0,  0);
    },

    render : function(ele) 
    {
        var self = this;
        var template  = __inline('./location.tmpl');  
        self.current_view = ele.html(template({}));



        // 全部城市渲染
        self.all_city_ele = self.current_view.find("[data-role=all_city]");





        self.$el.on('success:get_all_city',function(e,ret)
        {
            self.render_all_city(ret);

            // 最高顶节点
            self.top_main_ele = self.current_view.find("[data-role=location]");

            // 监听浏览历史记录
            self.on_local_storage_city();

        });


        self.get_all_city();



        // 右侧字母导航节点
        self.navigation_ele = self.current_view.find('[data-role="navigation"]');

        // 热门城市节点
        self.hot_city_ele = self.current_view.find("[data-role=hot_city]");

        // 是否渲染热门城市
        if (self.hot_city.is_show) 
        {
            self.render_hot_city();
        }
        else
        {
            // 如果不渲染热门城市，把右侧导航隐藏
            self.navigation_ele.find('[nav-id="hot"]').addClass('fn-hide');
        }

        // 历史记录 节点
        self.local_storage_city_ele = self.current_view.find('[data-role="local_storage_city"]');

        
        // 渲染历史记录html
        self.render_local_storage_city();

    },


    // 异步读取全部城市
    get_all_city : function() 
    {
        var self = this;
        // 异步读取全部城市
        var user_id = cookie.get('yue_fav_userid') ? cookie.get('yue_fav_userid') : 0;



        var city_verson = utility.storage.get('local_storage_city_verson') ;

        //  判断是否有缓存本地，如果有，读取本地，没有读取线上
        if(city_verson) 
        {
            // 匹配缓存版本号与传入版本号，哪个是最新版
            if (  self.verson !== city_verson ) 
            {
                get_city_ajax() ;
            }
            else
            {
                var ret_data = utility.storage.get('local_storage_city_data') ;
                self.$el.trigger('success:get_all_city', ret_data);
            }
        }
        else
        {
            get_city_ajax() ;
        }


        function get_city_ajax() 
        {
            // console.log(user_id);
            utility.ajax_request({
                url: 'http://yp.yueus.com/action/location_data.php?callback=?',
                data: {
                    user_id : user_id,
                    wap : '1'
                },
                dataType: 'JSONP',
                cache: true,
                beforeSend: function() 
                {
                    self.$loading = $.loading
                    ({
                        content:'加载中...'
                    });
                },
                success: function(ret) 
                {
                    self.$loading.loading("hide");
                    self.$el.trigger('success:get_all_city',ret.data);

                    // 设置缓存
                    utility.storage.set('local_storage_city_data',ret.data)

                    // 成功后设置版本号
                    utility.storage.set('local_storage_city_verson', self.verson)

                },    
                error: function() 
                {
                    self.$loading.loading("hide");
                    // $.tips
                    // ({
                    //     content:'网络异常',
                    //     stayTime:3000,
                    //     type:'warn'
                    // });
                },    
                complete: function() 
                {
                    self.$loading.loading("hide");
                   
                } 
            });
        }

    },

    // 热门城市渲染
    render_hot_city : function()
    {
        var self = this;
        var template  = __inline('./city-item.tmpl');  
        var view = self.hot_city_ele.html(template({
            data_main : self.all_hot_city ,
            is_hide : 0  //是否隐藏城市下拉
        }));


        view.find('[data-role="item"]').on('click', function(event) {
            var location_id_val = $(this).attr('location_id');
            var city_val = $(this).html();

            var city_obj = 
            {
                location_id :  location_id_val,
                city : city_val
            }
            self.set_local_storage_city(city_obj);
            self.render_local_storage_city();
        });

    },

    // a-z城市渲染
    render_all_city : function(all_city)
    {
        var self = this;
        var template  = __inline('./city-item.tmpl');  
        var view = self.all_city_ele.html(template({
            data_main :  all_city,
            is_hide : 1  //是否隐藏城市下拉
        }));

        view.find('[data-id]').on('click', function(event) {
            $(this).parent().find('.city-wrap').not($(this).next()).addClass('fn-hide');
            $(this).next().toggleClass('fn-hide');
            // window.scrollTo(0,  $('[data-id='+ $(this).attr('data-id') +']').offset().top - 45 );
        });

    },

    // 监听浏览历史记录
    on_local_storage_city : function()
    {
        var self = this;
        self.top_main_ele.find('[data-role="item"]').on('click', function(event) {
            var location_id_val = $(this).attr('location_id');
            var city_val = $(this).html();
            var city_obj = 
            {
                location_id :  location_id_val,
                city : city_val
            }
            self.set_local_storage_city(city_obj);
            self.render_local_storage_city();

            // 把右侧对应的导航显示
            self.navigation_ele.find('[nav-id=history]').removeClass('fn-hide');
        })
    },

    // 设置浏览历史记录
    set_local_storage_city: function(data) 
    {
        var self = this;
        var arr_city_data = [];

        // 回调
        self.callback && self.callback.call(this,data);
        arr_city_data.push(data)
        var location_map_location =  utility.storage.get('location_map');
        //  如果已点击过，把已经点击过的合并
        if (location_map_location) 
        {
            arr_city_data = arr_city_data.concat(location_map_location);
        }
        // 数组去重复操作
        var num_val = [];
        var city_arr = [];
        var end_city_data = [];

        for (var i = 0; i < arr_city_data.length; i++) {
            num_val.push(arr_city_data[i]['location_id']);
            city_arr[arr_city_data[i]['location_id']] = arr_city_data[i]['city']
        };

        var new_num_val =  num_val.uniqueFn() ;

        for (var i = 0; i < new_num_val.length; i++) {
            end_city_data.push({
                location_id : new_num_val[i] ,
                city: city_arr[new_num_val[i]]
            })
        };
        //  设置本地存蓄
        utility.storage.set('location_map',end_city_data);
    },

    // 渲染浏览历史模板
    render_local_storage_city : function() 
    {
        var self = this;
        // 先读取本地城市数据
        var location_map_location =  utility.storage.get('location_map');
        // 如果有浏览历史城市，即渲染模板
        if (location_map_location) 
        {
            // 控制显示历史记录个数
            var render_location_map_location = [];
            for (var i = 0; i < location_map_location.length; i++) {
                if ( i < self.city_history_num ) 
                {
                    render_location_map_location.push(location_map_location[i])
                }
            };
            var render_data = 
            [
                {
                    title : "历史",
                    id : "history",
                    data : render_location_map_location
                }
            ]
            var template  = __inline('./city-item.tmpl');  
            var view = self.local_storage_city_ele.html(template({
                data_main :  render_data
            }));

            view.find('[data-role="item"]').on('click', function(event) {
                var location_id_val = $(this).attr('location_id');
                var city_val = $(this).html();

                var city_obj = 
                {
                    location_id :  location_id_val,
                    city : city_val
                }
                self.set_local_storage_city(city_obj);
                self.render_local_storage_city();
            });
        }
        else
        {
            // 否则，把右侧对应的导航隐藏
            self.navigation_ele.find('[nav-id=history]').addClass('fn-hide');
        }
    },
    // 清空浏览历史
    clear_storage_city : function() 
    {
        var self = this;
        //  设置本地存蓄
        utility.storage.remove('location_map');
    },
    // 导航到对应节点
    navigation : function() 
    {
        var self = this;
        var nav_ele = self.navigation_ele;

        self.navigation_ele.on('touchstart', function(ev) {
            nav_ele.find(".flight-ctlts-selected").remove();
            var $panel = $(ev.target);
            var location_name = $panel.attr('nav-id') ;

            if( !location_name)
            {
                return ;
            }

            if (location_name == "history") 
            {
                // 滚动到当前城市
                goto_cur_city() ;
                location_name = "历史" ;
            } 
            else if( location_name == "hot" )
            {
                // 滚动到当前城市
                goto_cur_city() ;
                location_name = "热门" ;
            }

            else
            {
                // 滚动到当前城市
                goto_cur_city() ;
            }
            var html_str = '<div class="flight-ctlts-selected">'+location_name+'</div>';
            $panel.append(html_str);

            // 左侧滚动到当前城市
            function goto_cur_city() 
            {
        
                if ($('[data-id='+location_name+']').offset()) 
                {


                    window.scrollTo(0,  $('[data-id='+location_name+']').offset().top - 45 );
                    // $("html,body").animate({scrollTop:$("#XXX").offset().top},1000);
                } 

            }
        });

        self.navigation_ele.on("touchend", function(ev) {
            nav_ele.find(".flight-ctlts-selected").remove();
        })

    },

    // 搜索功能
    search_city : function() 
    {
        var self = this;
        console.log('搜索');
    }

}

module.exports = location_fn;

