define('menu/index', function(require, exports, module){ /**
 * 菜单
 nolest 2015.5.22
 **/

/**
 * @require modules/menu/index.scss
 */

"use strict";

var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var App =  require('common/I_APP/I_APP');
var menu_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n        <div class=\"child\" data-index=\"";
  if (helper = helpers.index) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.index); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><p>";
  if (helper = helpers.content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p></div>\r\n    ";
  return buffer;
  }

  buffer += "<div class=\"app_drop_menu_lock fn-hide\" data-role=\"app_drop_menu_lock\">\r\n\r\n</div>\r\n<div class=\"app_drop_menu\" data-role=\"app_drop_menu\">\r\n    <div class=\"delta\"></div>\r\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.menu_data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n</div>\r\n";
  return buffer;
  });

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}


module.exports =
{
    render: function ($dom,data)
    {

        var self = this;

        self.data = self._sort(data);

        $dom.append(menu_tpl({menu_data:self.data}));

        self.init();
        // 安装事件
        self.setup_event();

        return this;
    },
    // 初始化
    init : function()
    {
        var self = this;

        self.menu_lock = $('[data-role="app_drop_menu_lock"]');

        self.menu = $('[data-role="app_drop_menu"]');

    },
    hide : function()
    {
        var self = this;

        setTimeout(function()
        {
            self.menu.removeClass('drop');
            self.menu.addClass('up');
        },0)
        self.menu_lock.css('visibility','hidden').addClass('fn-hide');
    },
    show : function()
    {
        var self = this;

        setTimeout(function()
        {
            self.menu.removeClass('up');
            self.menu.addClass('drop');
        },0)
        self.menu_lock.removeClass('fn-hide').css('visibility','visible');

    },
    // 安装事件
    setup_event : function()
    {
        var self = this;

        $.each($('[data-index]'),function(i,obj)
        {
            self.data[i].click_event && $(obj).on('click',self.data[i].click_event)
        });


        $('[data-role="app_drop_menu_lock"]').on('click',function()
        {
            self.hide();

        })
        /*
        $('[data-role="app_drop_menu_lock"]')[0].addEventListener('touchstart',function()
        {
            self.hide();

            window.__showTopBarMenuCount++;
        })
        */
    },
    _sort : function(events_data)
    {   //排序，返回序列化数组
        if(events_data.length < 2)
        {
            return
        }

        for(var k = 1;k <events_data.length;k++)
        {
            var obj_key = events_data[k];

            var j = k-1;

            while( j >= 0 && obj_key.index < events_data[j].index)
            {
                events_data[j+1] = events_data[j];
                j = j -1
            }
            events_data[j+1] = obj_key
        }
        return events_data
    }
};





 
});