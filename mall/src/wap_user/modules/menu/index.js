/**
 * 菜单
 nolest 2015.5.22
 **/

/**
 * @require ./index.scss
 */

"use strict";

var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var App =  require('../common/I_APP/I_APP');
var menu_tpl = __inline('./menu.tmpl');

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





