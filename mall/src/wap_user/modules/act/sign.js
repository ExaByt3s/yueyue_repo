/**
 * Created by hudingwen on 15/7/17.
 */
"use strict";


var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var items_tpl = __inline('./sign.tmpl');
var menu = require('../menu/index');
var header = require('../common/widget/header/main');

if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

exports.init = function(page_data)
{
    var _self = $({});

    var sign_class = function()
    {
        var self = this;

        self.init();
    };

    sign_class.prototype =
    {
        init : function()
        {
            var self = this;

            _self.$list = $('[data-role="action-signin-list"]');
            _self.$join_btn = $('[data-role="join-btn"]');

            App.isPaiApp && App.showtopmenu(true);

            //右上角菜单弹出层
            var menu_data =
                    [
                        {
                            index:0,
                            content:'扫描二维码',
                            click_event:function()
                            {
                                var self = this;
                                App.qrcodescan();
                            }
                        },
                        {
                            index:1,
                            content:'首页',
                            click_event:function()
                            {
                                App.isPaiApp && App.switchtopage({page:'hot'});
                            }
                        },
                        {
                            index:2,
                            content:'刷新',
                            click_event:function()
                            {
                                window.location.href = window.location.href;
                            }
                        }

                    ];
            menu.render($('body'),menu_data);

            var __showTopBarMenuCount = 0;

            utility.$_AppCallJSObj.on('__showTopBarMenu',function(event,data)
            {

                __showTopBarMenuCount++;

                if(__showTopBarMenuCount%2!=0)
                {
                    menu.show()
                }
                else
                {
                    menu.hide()
                }
            });

            // 渲染头部
            header.init({
                ele : $("#global-header"), //头部渲染的节点
                title:"查看名单",
                header_show : true , //是否显示头部
                right_icon_show : false, //是否显示右边的按钮

                share_icon :
                {
                    show :false,  //是否显示分享按钮icon
                    content:""
                },
                omit_icon :
                {
                    show :false,  //是否显示三个圆点icon
                    content:""
                },
                show_txt :
                {
                    show :false,  //是否显示文字
                    content:"编辑"  //显示文字内容
                }
            });

        },
        setup_event : function()
        {
            var self = this;

            _self.$item_dropdown = $('[data-role="item-dropdown"]');
            _self.$dropdown = $('[data-role="dropdown"]');

            $('[data-role="scan-btn"]').on('click',function()
            {
                if(App.isPaiApp)
                {
                    App.qrcodescan();
                }

            });

            $('[data-user-role]').on('click',function(ev)
            {
                var can_use_chat = window.__can_use_chat;   

                if(App.isPaiApp && can_use_chat == 1)
                {
                    var $cur_btn = $(ev.currentTarget);    

                    var receive = 
                    {
                        receiverid : $cur_btn.attr('data-uid'),
                        receivername : $cur_btn.attr('data-nickname'),
                        receivericon : $cur_btn.attr('src')
                    };

                    var sender = window.__chat_json.result_data;

                    var chat_json = $.extend(true,sender,receive);

                    App.chat(chat_json);
                }
            });


            // 大列表容器
            _self.$dropdown.on('click',function(ev)
            {
                var $target = $(ev.currentTarget);
                $target.toggleClass('open').next().toggleClass('fn-hide');

                var icon_obj = $target.toggleClass('open').find('.icon-allow-grey');

                if(icon_obj.hasClass("icon-allow-grey-bottom"))
                {
                    icon_obj.removeClass("icon-allow-grey-bottom");
                    icon_obj.addClass("icon-allow-grey-top");

                }
                else
                {
                    icon_obj.removeClass("icon-allow-grey-top");
                    icon_obj.addClass("icon-allow-grey-bottom");
                }
            });

            // 小列表容器
            _self.$item_dropdown.on('click',function(ev)
            {

                var $target = $(ev.currentTarget);
                $target.toggleClass('open').next().toggleClass('fn-hide');

                var icon_obj = $target.toggleClass('open').find('.icon-allow-grey');

                if(icon_obj.hasClass("icon-allow-grey-bottom"))
                {
                    icon_obj.removeClass("icon-allow-grey-bottom");
                    icon_obj.addClass("icon-allow-grey-top");

                }
                else
                {
                    icon_obj.removeClass("icon-allow-grey-top");
                    icon_obj.addClass("icon-allow-grey-bottom");
                }
            });

            _self.$join_btn.on('click',function()
            {
                var event_id = $('#event_id').val();

                window.location.href = './apply.php?event_id='+event_id;
            });

        },
        refresh : function()
        {
            var self = this;

            var html_str = items_tpl({data:page_data.result_data});

            _self.$list.html(html_str);

            self.setup_event();


        }
    };

    var sign_obj = new sign_class();

    sign_obj.refresh();

};