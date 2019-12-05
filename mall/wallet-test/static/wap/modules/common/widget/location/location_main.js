define('common/widget/location/location_main', function(require, exports, module){ /**
 *  汤圆
 *  2015-7-13
 *  地区组件
 */
 /**
  * @require modules/common/widget/location/location.scss
 **/

var $ = require('components/zepto/zepto.js');

// 基本城市基本数据
var hot_city_base = require('common/widget/location/hot_city_base');
var utility = require('common/utility/index');

var cookie = require('common/cookie/index');



// 全部城市
// var all_city = require('./all_city');

var all_city ;

// 定义函数对象
function location_fn(options) 
{
    var options = options || {} ;
    this.ele_tpl = options.ele ;
    this.hot_city = options.hot_city || {} ;
    this.city_history_num = options.city_history_num || 12 ;  //显示多少浏览城市记录
    this.is_search = options.is_search || false ;
    this.hot_city.data = this.hot_city.data || [];
    this.callback = options.callback;

    // 热门城市合并
    this.all_hot_city_base = hot_city_base.data.concat(this.hot_city.data);
    this.all_hot_city = 
    [
        {
            title : hot_city_base.title,
            data :  this.all_hot_city_base ,
            id : hot_city_base.id
        }
    ]

    //  初始化
    this.init();
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
    },


    render : function(ele) 
    {
        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<div class=\"location-mod\" data-role=\"location\">\n\n    <div data-role=\"local_storage_city\" class=\"local-city city-style\">\n        \n    </div>\n\n    <div data-role=\"hot_city\" class=\"city-style\">\n        \n    </div>\n\n    <div data-role=\"all_city\" >\n        \n    </div>\n\n\n    <div class=\"flight-ctltsfixed\"  style=\"position: fixed;top: 0;\">\n        <div class=\"flight-ctltsfixed-pd\" >\n            <ul class=\"flight-ctlts\" data-role=\"navigation\">\n                <li nav-id=\"history\">历史</li>\n                <li nav-id=\"hot\">热门</li>\n                <li nav-id=\"A\">A</li>\n                <li nav-id=\"B\">B</li>\n                <li nav-id=\"C\">C</li>\n                <li nav-id=\"D\">D</li>\n                <li nav-id=\"E\">E</li>\n                <li nav-id=\"F\">F</li>\n                <li nav-id=\"G\">G</li>\n                <li nav-id=\"H\">H</li>\n                <li nav-id=\"I\">I</li>\n                <li nav-id=\"J\">J</li>\n                <li nav-id=\"K\">K</li>\n                <li nav-id=\"L\">L</li>\n                <li nav-id=\"M\">M</li>\n                <li nav-id=\"N\">N</li>\n                <li nav-id=\"O\">O</li>\n                <li nav-id=\"P\">P</li>\n                <li nav-id=\"Q\">Q</li>\n                <li nav-id=\"R\">R</li>\n                <li nav-id=\"S\">S</li>\n                <li nav-id=\"T\">T</li>\n                <li nav-id=\"U\">U</li>\n                <li nav-id=\"V\">V</li>\n                <li nav-id=\"W\">W</li>\n                <li nav-id=\"X\">X</li>\n                <li nav-id=\"Y\">Y</li>\n                <li nav-id=\"Z\">Z</li>\n            </ul>\n        </div>\n    </div>\n\n\n</div>";
  });  
        self.current_view = ele.html(template({}));

        // 全部城市渲染
        self.all_city_ele = self.current_view.find("[data-role=all_city]");

        self.get_all_city();

        $(self).on('success:get_all_city',function(e,ret)
        {
            self.render_all_city(ret);

            // 最高顶节点
            self.top_main_ele = self.current_view.find("[data-role=location]");

            // 监听浏览历史记录
            self.on_local_storage_city();

        });



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
        // console.log(user_id);
        $.ajax({
            url: 'http://yp.yueus.com/mobile/test/location.php?callback=?',
            data: {
                user_id : user_id,
                wap : '1'
            },
            dataType: 'JSONP',
            type: 'GET',
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
                $(self).trigger('success:get_all_city',ret.data);

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
    },

    // 热门城市渲染
    render_hot_city : function()
    {
        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n    <div class=\"title\" data-id=\"";
  if (helper = helpers.id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n    <div class=\"city-wrap clearfix\">\n        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n            <div class=\"item\" data-role=\"item\" location_id=\"";
  if (helper = helpers.location_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.location_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.city) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.city); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        ";
  return buffer;
  }

  buffer += "\n";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data_main), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n    \n\n\n    \n";
  return buffer;
  });  
        self.hot_city_ele.html(template({
            data_main : self.all_hot_city
        }));

    },

    // a-z城市渲染
    render_all_city : function(all_city)
    {
        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n    <div class=\"title\" data-id=\"";
  if (helper = helpers.id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n    <div class=\"city-wrap clearfix\">\n        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n            <div class=\"item\" data-role=\"item\" location_id=\"";
  if (helper = helpers.location_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.location_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.city) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.city); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        ";
  return buffer;
  }

  buffer += "\n";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data_main), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n    \n\n\n    \n";
  return buffer;
  });  
        var view = self.all_city_ele.html(template({
            data_main :  all_city
        }));

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
            var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n    <div class=\"title\" data-id=\"";
  if (helper = helpers.id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n    <div class=\"city-wrap clearfix\">\n        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n            <div class=\"item\" data-role=\"item\" location_id=\"";
  if (helper = helpers.location_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.location_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.city) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.city); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        ";
  return buffer;
  }

  buffer += "\n";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data_main), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n    \n\n\n    \n";
  return buffer;
  });  
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

return location_fn;
 
});