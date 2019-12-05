define('comment_list/bill', function(require, exports, module){ /**
 * 账单页
 * Created by hudingwen on 15/6/1.
 */
"use strict";


var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var scroll = require('common/scroll/index');
var items_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <li class=\"ui-border-t\">\n        <div class=\"ui-list-info\">\n            ";
  if (helper = helpers.content1) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.content1); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div>\n        <div >";
  if (helper = helpers.content2) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.content2); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</div>\n    </li>\n    ";
  return buffer;
  }

  buffer += "<ul class=\"ui-list ui-list-text ui-border-b\" >\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n</ul>";
  return buffer;
  });
var abnormal = require('common/widget/abnormal/index');
var menu = require('menu/index');

if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{
    var _self = $({});

    if(App.isPaiApp)
    {
        App.check_login(function(data)
        {
            /**
             * 获取个人信息函数，专用于app
             */

            var params = window.__YUE_APP_USER_INFO__;

            var local_user_id = utility.login_id;
            var client_user_id = window.__YUE_APP_USER_INFO__.user_id || 0;

            var async = (local_user_id == client_user_id);

            console.log("=====local_user_id,client_user_id=====");
            console.log(local_user_id,client_user_id);

            utility.ajax_request
            ({
                url: window.$__config.ajax_url.auth_get_user_info,
                data : params,
                cache: false,
                async : async,
                beforeSend: function (xhr, options) {

                },
                success: function (response, options) {
                    console.log(response);
                },
                error: function (model, xhr, options) {

                },
                complete: function (xhr, status) {

                }
            });

            if(!utility.int(data.pocoid))
            {
                App.openloginpage(function(data)
                {
                    if(data.code == '0000')
                    {
                        utility.refresh_page();
                    }
                });

                return;
            }

        });

    }
    else
    {
        if(!utility.auth.is_login())
        {
            window.location.href = '../../account/login.php?redirect_url='+encodeURIComponent(window.location.href);
        }
    }

    /*********** 右上角菜单栏 ************/
    /*
     传入对象
     {index:索引值,根据索引值从小到大排列选项顺序}
     {content:文本内容}
     {click_event:点击事件}
     */
    var menu_data =
        [
            {
                index:0,
                content:'首页',
                click_event:function()
                {
                    App.isPaiApp && App.switchtopage({page:'hot'});
                }
            },
            {
                index:1,
                content:'提现',
                click_event:function()
                {
                    window.location.href = '../withdrawals/';
                }
            }
        ];

    /*
     render() 方法传入(父容器，对象)
     show()   下拉菜单
     hide()   上拉菜单
     */
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
    /*********** 右上角菜单栏 ************/

    var bill_class = function()
    {
        var self = this;

        self.init();
    };

    bill_class.prototype =
    {
        refresh : function()
        {
            var self = this;

            self.action(_self.type,_self.page);
        },
        load_more : function()
        {
            var self = this;

            if(utility.int(_self.has_next_page))
            {
                _self.page++;

                self.action(_self.type,_self.page);
            }
            else
            {
                $.tips
                ({
                    content:'已经到尽头啦',
                    stayTime:3000,
                    type:'warn'
                });

                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
            }


        },
        init : function()
        {
            var self = this;

            // 初始化容器
            _self.$tab_container = $('.ui-tab');
            _self.tab_obj = self.init_tab_container('.ui-tab');
            _self.$loading = {};
            _self.$tarde_container = $('[data-role="trade"]');
            _self.$recharge_container = $('[data-role="recharge"]');
            _self.$withdraw_container = $('[data-role="withdraw"]');
            _self.$no_data = $('[data-role="no-data"]');
            _self.$has_data = $('[data-role="has-data"]');
            _self.page = 1 ;
            _self.type = $('#type').val();

            _self.$scroll_wrapper = $('[data-role="wrapper"]');
            _self.scroll_view_obj = scroll(_self.$scroll_wrapper);

            _self.scroll_view_obj.on('success:drag_down_load',function(e,dragger)
            {
                self.refresh();
            });

            _self.scroll_view_obj.on('success:drag_up_load',function(e,dragger)
            {
                self.load_more();
            });

            // 安装事件
            self._setup_event();

            self.refresh();
        },
        init_tab_container : function($el,options)
        {
            return {};
        },
        action : function(type,page,load_more)
        {
            var self = this;

            self._sending = false;

            if(self._sending)
            {
                return;
            }

            _self.ajax_obj = utility.ajax_request
            ({
                url : window.$__config.ajax_url.bill,
                data :
                {
                    type : type,
                    page : page
                },
                beforeSend : function()
                {
                    self._sending = true;
                    _self.$loading = $.loading
                    ({
                        content:'加载中...'
                    });
                },
                success : function(response)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    var res = response.result_data;

                    _self.trigger('success:get_bill_list',res);


                },
                error : function(res)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('error:get_bill_list',res);

                    $.tips
                    ({
                        content:'网络异常',
                        stayTime:3000,
                        type:'warn'
                    });
                }

            });
        },
        /**
         * 安装事件
         * @private
         */
        _setup_event : function()
        {
            var self = this;

            _self.on('success:get_bill_list',function(e,res)
            {

               try{
                   _self.has_next_page = res.has_next;

                   _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();

                   _self.$has_data.removeClass('fn-hide');

                   var xhr_data = _self.ajax_obj.xhr_data;

                   if(!res.data.length && (xhr_data.page == 1))
                   {
                       _self.$has_data.addClass('fn-hide');
                       _self.$no_data.removeClass('fn-hide').html('');

                       abnormal.render(_self.$no_data[0],{});

                       return;
                   }

                   if(res.code>0)
                   {
                       var html_str = items_tpl(res);

                       var method = (xhr_data.page == 1) ? 'html' : 'append';

                       _self.$tarde_container[method](html_str);
                   }
                   else
                   {
                       $.tips
                       ({
                           content:res.msg,
                           stayTime:3000,
                           type:'warn'
                       });
                   }
               }
               catch(err){
                   console.log(err);
               }


            })
            .on('error:get_bill_list',function(e,res)
            {
                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
            });
        }
    };


    _self.bill_obj = new bill_class();



})($,window); 
});