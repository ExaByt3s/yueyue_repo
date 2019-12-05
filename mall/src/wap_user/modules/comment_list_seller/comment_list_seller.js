/**
 * 评价列表
 * 汤圆
 */
"use strict";

var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
// var App =  require('../common/I_APP/I_APP');
var scroll = require('../common/scroll/index');
var items_tpl = __inline('./items.tmpl');
var abnormal = require('../common/widget/abnormal/index');


if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

    var _self = $({});

    _self.page = 1;

    function comment_list(options) 
    {
        var self = this;
        self.options = options || {} ;

        self.init()
    }

    comment_list.prototype =
    {
        refresh : function()
        {
            var self = this;

            self.action(self.options,_self.page);
        },
        load_more : function()
        {
            var self = this;

            if(_self.has_next_page)
            {
                _self.page++;
                self.action(self.options,_self.page);
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
            _self.$loading = {};
            _self.$no_data = $('[data-role="no-data"]');
            _self.$has_data = $('[data-role="has-data"]');
            _self.page = 1 ;
            _self.$comment_container = $('[data-role="comment_ele"]');
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
        action : function(params,page,load_more)
        {
            var self = this;

            self._sending = false;

            if(self._sending)
            {
                return;
            }

            var params = $.extend(true,{},params,{page : page,type:'seller'});
            _self.ajax_obj = utility.ajax_request
            ({
                url :  window.$__ajax_domain+'get_comment_list.php',
                data : params,
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
                    _self.trigger('success:get_comment_list',res);


                },
                error : function(res)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('error:get_comment_list',res);

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

            _self.on('success:get_comment_list',function(e,res)
            {

               try{
                   _self.has_next_page = res.has_next_page;
                   _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
                   _self.$has_data.removeClass('fn-hide');
                   var xhr_data = _self.ajax_obj.xhr_data;
    
                   if(!res.list.length && (xhr_data.page == 1))
                   {
                        // _self.$has_data.addClass('fn-hide');
                        // _self.$no_data.removeClass('fn-hide').html('');
                        _self.$scroll_wrapper.addClass('fn-hide');
                        abnormal.render(_self.$no_data[0],{});
                        return;
                   }

                   if(res.list.length)
                   {
                       var html_str = items_tpl(res);
                       var method = (xhr_data.page == 1) ? 'html' : 'append';
                       _self.$comment_container[method](html_str);
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
            .on('error:get_comment_list',function(e,res)
            {
                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
            });
        }
    };


return  comment_list;



