define('common/widget/location/location_main', function(require, exports, module){ /**
 *  ��Բ
 *  2015-7-13
 *  �������
 */
 /**
  * @require modules/common/widget/location/location.scss
 **/

var $ = require('components/zepto/zepto.js');

// �������л�������
var hot_city_base = require('common/widget/location/hot_city_base');
var utility = require('common/utility/index');

var cookie = require('common/cookie/index');



// ȫ������
// var all_city = require('./all_city');

var all_city ;

// ���庯������
function location_fn(options) 
{
    var options = options || {} ;
    this.ele_tpl = options.ele ;
    this.hot_city = options.hot_city || {} ;
    this.city_history_num = options.city_history_num || 12 ;  //��ʾ����������м�¼
    this.is_search = options.is_search || false ;
    this.hot_city.data = this.hot_city.data || [];
    this.callback = options.callback;

    // ���ų��кϲ�
    this.all_hot_city_base = hot_city_base.data.concat(this.hot_city.data);
    this.all_hot_city = 
    [
        {
            title : hot_city_base.title,
            data :  this.all_hot_city_base ,
            id : hot_city_base.id
        }
    ]

    //  ��ʼ��
    this.init();
}

location_fn.prototype = 
{
    init : function() 
    {
        var self = this;
        // ��װ�¼�
        self.setup_event();

        if (self.is_search) 
        {
            // ��������
            self.search_city();
        }

        // ��Ⱦģ��
        self.render(self.ele_tpl);

        // �Ҳ�ƴ����ĸ����
        self.navigation();
    },

    setup_event : function() 
    {
        var self = this;

        // ����ȥ����չ
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
  


  return "<div class=\"location-mod\" data-role=\"location\">\n\n    <div data-role=\"local_storage_city\" class=\"local-city city-style\">\n        \n    </div>\n\n    <div data-role=\"hot_city\" class=\"city-style\">\n        \n    </div>\n\n    <div data-role=\"all_city\" >\n        \n    </div>\n\n\n    <div class=\"flight-ctltsfixed\"  style=\"position: fixed;top: 0;\">\n        <div class=\"flight-ctltsfixed-pd\" >\n            <ul class=\"flight-ctlts\" data-role=\"navigation\">\n                <li nav-id=\"history\">��ʷ</li>\n                <li nav-id=\"hot\">����</li>\n                <li nav-id=\"A\">A</li>\n                <li nav-id=\"B\">B</li>\n                <li nav-id=\"C\">C</li>\n                <li nav-id=\"D\">D</li>\n                <li nav-id=\"E\">E</li>\n                <li nav-id=\"F\">F</li>\n                <li nav-id=\"G\">G</li>\n                <li nav-id=\"H\">H</li>\n                <li nav-id=\"I\">I</li>\n                <li nav-id=\"J\">J</li>\n                <li nav-id=\"K\">K</li>\n                <li nav-id=\"L\">L</li>\n                <li nav-id=\"M\">M</li>\n                <li nav-id=\"N\">N</li>\n                <li nav-id=\"O\">O</li>\n                <li nav-id=\"P\">P</li>\n                <li nav-id=\"Q\">Q</li>\n                <li nav-id=\"R\">R</li>\n                <li nav-id=\"S\">S</li>\n                <li nav-id=\"T\">T</li>\n                <li nav-id=\"U\">U</li>\n                <li nav-id=\"V\">V</li>\n                <li nav-id=\"W\">W</li>\n                <li nav-id=\"X\">X</li>\n                <li nav-id=\"Y\">Y</li>\n                <li nav-id=\"Z\">Z</li>\n            </ul>\n        </div>\n    </div>\n\n\n</div>";
  });  
        self.current_view = ele.html(template({}));

        // ȫ��������Ⱦ
        self.all_city_ele = self.current_view.find("[data-role=all_city]");

        self.get_all_city();

        $(self).on('success:get_all_city',function(e,ret)
        {
            self.render_all_city(ret);

            // ��߶��ڵ�
            self.top_main_ele = self.current_view.find("[data-role=location]");

            // ���������ʷ��¼
            self.on_local_storage_city();

        });



        // �Ҳ���ĸ�����ڵ�
        self.navigation_ele = self.current_view.find('[data-role="navigation"]');

        // ���ų��нڵ�
        self.hot_city_ele = self.current_view.find("[data-role=hot_city]");

        // �Ƿ���Ⱦ���ų���
        if (self.hot_city.is_show) 
        {
            self.render_hot_city();
        }
        else
        {
            // �������Ⱦ���ų��У����Ҳർ������
            self.navigation_ele.find('[nav-id="hot"]').addClass('fn-hide');
        }

        // ��ʷ��¼ �ڵ�
        self.local_storage_city_ele = self.current_view.find('[data-role="local_storage_city"]');

        
        // ��Ⱦ��ʷ��¼html
        self.render_local_storage_city();

    },


    // �첽��ȡȫ������
    get_all_city : function() 
    {
        var self = this;
        // �첽��ȡȫ������
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
                    content:'������...'
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
                //     content:'�����쳣',
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

    // ���ų�����Ⱦ
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

    // a-z������Ⱦ
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

    // ���������ʷ��¼
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

            // ���Ҳ��Ӧ�ĵ�����ʾ
            self.navigation_ele.find('[nav-id=history]').removeClass('fn-hide');
        })
    },

    // ���������ʷ��¼
    set_local_storage_city: function(data) 
    {
        var self = this;
        var arr_city_data = [];

        // �ص�
        self.callback && self.callback.call(this,data);
        arr_city_data.push(data)
        var location_map_location =  utility.storage.get('location_map');
        //  ����ѵ���������Ѿ�������ĺϲ�
        if (location_map_location) 
        {
            arr_city_data = arr_city_data.concat(location_map_location);
        }
        // ����ȥ�ظ�����
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
        //  ���ñ��ش���
        utility.storage.set('location_map',end_city_data);
    },

    // ��Ⱦ�����ʷģ��
    render_local_storage_city : function() 
    {
        var self = this;
        // �ȶ�ȡ���س�������
        var location_map_location =  utility.storage.get('location_map');
        // ����������ʷ���У�����Ⱦģ��
        if (location_map_location) 
        {
            // ������ʾ��ʷ��¼����
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
                    title : "��ʷ",
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
            // ���򣬰��Ҳ��Ӧ�ĵ�������
            self.navigation_ele.find('[nav-id=history]').addClass('fn-hide');
        }
    },
    // ��������ʷ
    clear_storage_city : function() 
    {
        var self = this;
        //  ���ñ��ش���
        utility.storage.remove('location_map');
    },
    // ��������Ӧ�ڵ�
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
                // ��������ǰ����
                goto_cur_city() ;
                location_name = "��ʷ" ;
            } 
            else if( location_name == "hot" )
            {
                // ��������ǰ����
                goto_cur_city() ;
                location_name = "����" ;
            }

            else
            {
                // ��������ǰ����
                goto_cur_city() ;
            }
            var html_str = '<div class="flight-ctlts-selected">'+location_name+'</div>';
            $panel.append(html_str);

            // ����������ǰ����
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

    // ��������
    search_city : function() 
    {
        var self = this;
        console.log('����');
    }

}

return location_fn;
 
});