define('topic/list', function(require, exports, module){ var tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n        <li data-role=\"nav-to-info\" data-role-id=\"";
  if (helper = helpers.id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\r\n            <div class=\"topic-front\">\r\n                <i class=\"image-img\" data-lazyload-url=\"";
  if (helper = helpers.cover_image) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.cover_image); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"></i>\r\n            </div>\r\n        </li>\r\n    ";
  return buffer;
  }

  buffer += "\r\n\r\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.arr), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n\r\n\r\n\r\n";
  return buffer;
  });


var $ = require('components/zepto/zepto.js');
var scroll = require('common/scroll/index');
var utility = require('common/utility/index');
var yue_ui = require('yue_ui/frozen');
var menu = require('menu/index');
var abnormal = require('common/widget/abnormal/index');
var LZ = require('common/lazyload/lazyload');
$(function()
{

    var _self = $({});



    /*********** 滑动刷新加载列表内容 ************/

    var list_class = function()
    {
        var self = this;

        self.init();

    };

    list_class.prototype =
    {
        /*********** 刷新 ************/
        refresh : function()
        {
            var self = this;

            _self.page = 1;
            self.action(_self.page);

        },
        /*********** 加载 ************/
        load_more : function()
        {
            var self = this;

            console.log(_self.has_next_page);

            if(_self.has_next_page)
            {
                _self.page++;
                self.action(_self.page);
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
        /*********** 初始化 ************/
        init : function()
        {
            var self = this;

            // 初始化容器

            _self.$loading = {};
            _self.page = 1 ;
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

        action : function(page,load_more)
        {

            var self = this;

            self._sending = false;

            if(self._sending)
            {
                return;
            }
            _self.ajax_obj = utility.ajax_request
            ({
                url       : window.$__config.ajax_url.get_topic_list,
                data      :
                {
                    page  : page
                },
                cache     : true,
                beforeSend: function ()
                {
                    self._sending = true;

                    _self.$loading = $.loading
                    ({
                        content:'加载中...'
                    });
                },
                success   : function (data)
                {

                    /* 加载后*/
                    self._sending = false;

                    _self.$loading.loading("hide");

                    var res = data.result_data;

                    var list = res.list;

                    _self.has_next_page = res.has_next_page;

                    var html_str = tpl
                    ({
                        arr: list
                    });

                    var insert = $('[data-role="insert"]');

                    if(_self.page == 1)
                    {
                        insert.html(html_str);
                    }else
                    {
                        insert.append(html_str);
                    }

                    _self.trigger('success:get_list',res);

                    $('[data-role="nav-to-info"]').on('click',function()
                    {

                        var _id = $(this).attr('data-role-id');

                        window.location.href= "./index.php?topic_id="+_id;

                    });


                },
                error : function(res)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('error:get_list',res);

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

            _self.on('success:get_list',function(res)
            {

                //new 对象 新建内置对象
                if(!self.lazyloading)
                {
                    self.lazyloading = new LZ(_self.$scroll_wrapper,
                        {
                            size : window.innerWidth -20
                        });
                }
                else
                {
                    self.lazyloading.refresh();
                }


                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();


            })
                .on('error:get_list',function(e,res)
                {
                    _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
                });
        }
    };
    _self.list_obj = new list_class();
}); 
});