define('comment_list_seller/comment_list', function(require, exports, module){ /**
 * 评价列表
 * 汤圆
 */
"use strict";

var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
// var App =  require('../common/I_APP/I_APP');
var scroll = require('common/scroll/index');
var items_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, self=this, functionType="function", escapeExpression=this.escapeExpression;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n\n\n<div class=\"item\">\n    <div class=\"wbox clearfix\">\n\n        <div class=\"lbox \">\n            <div class=\"ui-avatar-icon ui-avatar-icon-m\">\n                    <i style=\"background-image:url(";
  if (helper = helpers.user_icon) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_icon); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + ")\"></i>\n            </div>\n        </div>\n        <div class=\"rbox \">\n            <div class=\"box-a\">\n                <div class=\"name f14 color-333\">";
  if (helper = helpers.nickname) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.nickname); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n\n                <div class=\"ui-start-mod\">\n                ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.stars_list), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                </div>\n\n            </div>\n\n            <div class=\"box-b f10 color-aaa\">\n                ";
  if (helper = helpers.add_time) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.add_time); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n            </div>\n        </div>\n    </div>\n\n    <div class=\"des f14 color-333\">\n        ";
  if (helper = helpers.comment) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.comment); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n    </div>\n</div>\n\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += " \n                    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.is_red), {hash:{},inverse:self.program(5, program5, data),fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }
function program3(depth0,data) {
  
  
  return "\n                        <i class=\"icon-s-mod  icon-start-yellow\"></i>\n                    ";
  }

function program5(depth0,data) {
  
  
  return "\n                        <i class=\"icon-s-mod icon-start-grey\"></i>\n                    ";
  }

  buffer += "\n\n\n";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.list), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });
var abnormal = require('common/widget/abnormal/index');


if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

    var _self = $({});

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

            var params = $.extend(true,{},params,{page : page});
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
    
                   if(!res.list && (xhr_data.page == 1))
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



 
});