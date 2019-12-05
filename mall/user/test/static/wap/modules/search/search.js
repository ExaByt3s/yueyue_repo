define('search', function(require, exports, module){ var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');
var search_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <button data-role=\"btn\" class=\"ui-button ui-button-size-x ui-button-bg-fff hot-btn\" data-code-name=\"";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n        <span>";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\n    </button>\n";
  return buffer;
  }

  buffer += "\n";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.btn_list), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });
$(document).ready(function()
{
    //返回按钮
    $('[data-role="page-back"]').on('click',function()
    {
        if(document.referrer)
        {
            window.history.back();
        }
        else
        {
            window.location.href = "../index.php" ;
        }
    });
    document.body.addEventListener('touchstart',function(){});


    //搜索事件
    var _self = $({});
    _self.$search_merchant = $('[data-role="merchant"]');
    _self.$search_service = $('[data-role="service"]');
    _self.$hot_service_content = $('[data-role="hot-service-content"]');
    _self.$hot_seller_content = $('[data-role="hot-seller-content"]');
    _self.$history = $('[data-role="history"]');
    _self.$history_service_content = $('[data-role="history-service-content"]');
    _self.$history_seller_content = $('[data-role="history-seller-content"]');
    _self.$empty = $('[data-role="empty"]');
    _self.$type_btn = $('[data-role="type-btn"]');
    _self.$search_btn = $('[data-role="right-btn"]');
    _self.$search_text = $('[data-role="search-text"]');
    _self.$search_type_txt = $('.search-type-btn');
    _self.$type_select = $('#type-select');
    _self.$hot = $('[data-role="hot"]');
    _self.$search_type_init = $('#search-type-init');


    // 服务的热门标签数组
    _self.service_tags =  window.__service_tags || [];
    _self.service_btn_list = [];
    // 商家的热门标签数组
    _self.seller_tags =  window.__seller_tags || [];
    _self.seller_btn_list = [];
    //type_id参数
    _self.type_id = window.__type_id;


    //======== 循环重新整合标签格式 ========
    if(_self.service_tags.length>0)
    {
        for(var i= 0 ;i<_self.service_tags.length;i++)
        {
            var list_obj = {};
            list_obj.name = _self.service_tags[i];
            _self.service_btn_list.push(list_obj);
        }
    }

    if(_self.seller_tags.length>0)
    {
        for(var i= 0 ;i<_self.seller_tags.length;i++)
        {
            var list_obj = {};
            list_obj.name = _self.seller_tags[i];
            _self.seller_btn_list.push(list_obj);
        }
    }
    //======== 循环重新整合标签格式 ========

    //搜索服务类型切换

    _self.type = _self.$search_type_init.val() || "goods";

    init_logic_data();

    // hudw hack android
    if(_self.$search_type_txt.val() == 'goods')
    {
        _self.$search_type_txt.val('服务');
    }

    _self.$type_btn.on('click',function()
    {
        var type_str  = '服务';

        _self.type = $(this).attr('data-type');

        _self.$type_select.addClass('fn-hide');

        if(_self.type == 'seller')
        {
            type_str = '商家';

            var lst_key = "seller-history";

            // 显示商家的热门、历史标签
            _self.$hot_seller_content.removeClass('fn-hide');
            _self.$history_seller_content.removeClass('fn-hide');
            _self.$hot_service_content.addClass('fn-hide');
            _self.$history_service_content.addClass('fn-hide');

        }
        else if(_self.type == 'goods')
        {
            type_str = '服务';

            var lst_key = "service-history";

            // 显示服务的热门、历史标签
            _self.$hot_service_content.removeClass('fn-hide');
            _self.$history_service_content.removeClass('fn-hide');
            _self.$hot_seller_content.addClass('fn-hide');
            _self.$history_seller_content.addClass('fn-hide');

            var history_btn = utility.storage.get(_self.type_id+"_"+lst_key) || [];

        }
        else
        {
            // todo 全部
        }

        init_logic_data();

        _self.$search_type_txt.val(type_str);




    });

    //搜索添加历史

    _self.$search_btn.on('click',function()
    {
        var searchText = _self.$search_text.val();
        if(!$.trim(searchText))
        {
            alert("请输入内容");
            console.log('请输入内容');
            return;
        }
        goto_search(searchText,_self.type);
        _self.$search_text.val("");
    });

    //服务类型切换显隐
    $('[data-role="search-type"]').on('click',function(ev)
    {
        ev.stopPropagation();
        if(_self.$type_select.hasClass('fn-hide'))
        {
            _self.$type_select.removeClass('fn-hide');
        }else
        {
            _self.$type_select.addClass('fn-hide');
        }
    });
    $(document).on('click',function(ev)
    {
        _self.$type_select.addClass('fn-hide');
    });


    /**
     * 搜索子项类
     */
    var search_items_child_class = function(options)
    {
        var self = this;

        options = options || {};

        self.options = options;

        self.init();
    };
    search_items_child_class.prototype =
    {
        init : function()
        {
            var self = this;

            var data = self.options.data || [];
            var key = self.options.key || '';


            self.$el = self.options.$el || {};

            if(_self.type == 'goods')
            {
                self.$el.lst_key = "service-history";
            }
            else if(_self.type == 'seller')
            {
                self.$el.lst_key = "seller-history";
            }

            var history_btn = utility.storage.get(_self.type_id+"_"+self.$el.lst_key) || [];

            var btn_list_str = self.render_items(data);



            // 插入内容
            self.$el.html(btn_list_str);

            _self.$btn = self.$el.find('[data-role="btn"]');
            _self.$btn.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var name = $cur_btn.attr('data-code-name');



                goto_search(name,_self.type);
            });

            //清除搜索历史事件
            _self.$empty.on('click',function()
            {
                if(_self.type == 'goods')
                {
                    var lst_key = "service-history";
                    _self.$history.addClass('fn-hide');

                }
                else if(_self.type == 'seller')
                {
                    var lst_key = "seller-history";
                    _self.$history.addClass('fn-hide');
                }

                utility.storage.remove(_self.type_id+"_"+lst_key);

                self.setup_empty();
            });

            /**
             var history_btn = utility.storage.get("history") || [];

             self.$el = self.options.$el || {};

             history_btn.reverse();

             if(history_btn.length < 1)
             {
                 _self.$history.html("");
                 _self.$empty.hide();
             }

             self.$el.html(btn_list_str);

             //清除搜索历史
             _self.$empty.on('click',function()
             {
                 utility.storage.remove("history");
                 self.setup_empty();
             });

             //搜索历史按钮
             _self.$btn = self.$el.find('[data-role="btn"]');
             _self.$btn.on('click',function(ev)
             {
                 var $cur_btn = $(ev.currentTarget);
                 var name = $cur_btn.attr('data-code-name');
                 console.log(name);

                 var repeat = false;
                 for(var n = 0;n<history_btn.length;n++){
                     if(history_btn[n].name==name){
                         repeat=true;
                     }
                 }
                 if(!repeat)
                 {
                     if(history_btn.length>=3)
                     {
                         history_btn.pop();
                     }
                     history_btn.unshift({name: name});

                     utility.storage.set("history", history_btn);
                 }
                 goto_search(name,_self.type);
             });
             **/

        },
        render_items : function(data)
        {
            var self = this;

            var html_str = search_tpl(data);

            // 模板渲染，返回数据 html字符串

            return html_str;
        },

        setup_search_click : function()
        {

        },

        setup_empty : function()
        {
            _self.$empty.addClass("fn-hide");

        }
    };

    // 服务热门子项
    _self.search_hot_service_items_child_obj = new search_items_child_class
    ({
        key : 'hot_service',
        data :
        {
            data :
            {
                btn_list : _self.service_btn_list
            }
        },
        title : '',
        $el : _self.$hot_service_content
    });

    // 服务历史子项
    _self.search_history_service_items_child_obj = new search_items_child_class
    ({
        key : 'history_service',
        data :
        {
            data :
            {
                btn_list:utility.storage.get(_self.type_id+"_"+"service-history") || []
            }
        },
        title : '',
        $el : _self.$history_service_content
    });

    // 商家热门子项
    _self.search_hot_seller_items_child_obj = new search_items_child_class
    ({
        key : 'hot_seller',
        data :
        {
            data :
            {
                btn_list : _self.seller_btn_list
            }
        },
        title : '',
        $el : _self.$hot_seller_content
    });

    // 商家历史子项
    _self.search_history_seller_items_child_obj = new search_items_child_class
    ({
        key : 'history_seller',
        data :
        {
            data :
            {
                btn_list:utility.storage.get(_self.type_id+"_"+"seller-history") || []
            }
        },
        title : '',
        $el : _self.$history_seller_content
    });

    /**
     * 初始化页面
     */
    function init_logic_data()
    {
        var hot_key = null;
        if(_self.type == 'goods')
        {
            var lst_key = "service-history";
            var hot_key = _self.service_tags;


            _self.$history_service_content.removeClass('fn-hide');
            _self.$history_seller_content.addClass('fn-hide');
        }
        else if(_self.type == 'seller')
        {
            var lst_key = "seller-history";
            var hot_key = _self.seller_tags;

            _self.$history_seller_content.removeClass('fn-hide');
            _self.$history_service_content.addClass('fn-hide');
        }

        var history_btn = utility.storage.get(_self.type_id+"_"+lst_key) || [];

        if(history_btn.length > 0)
        {
            _self.$empty.removeClass('fn-hide');
            _self.$history.removeClass('fn-hide');
        }
        else
        {
            _self.$empty.addClass('fn-hide');
            _self.$history.addClass('fn-hide');
        }
        //

        if(hot_key.length > 0)
        {
            _self.$hot.removeClass('fn-hide');
        }
        else
        {
            _self.$hot.addClass('fn-hide');
        }
    }

    function goto_search(name,type)
    {
        if(type == 'goods')
        {
            var lst_key = "service-history";
        }
        else if(type == 'seller')
        {
            var lst_key = "seller-history";
        }

        var history_btn = utility.storage.get(_self.type_id+"_"+lst_key) || [];

        var repeat = false;
        for(var n = 0;n<history_btn.length;n++){
            if(history_btn[n].name==name){
                repeat=true;
            }
        }
        if(!repeat)
        {
            if(history_btn.length>=10)
            {
                history_btn.pop();
            }
            history_btn.unshift({name: name});



            utility.storage.set(_self.type_id+"_"+lst_key, history_btn);
        }

        _self.$search_type_init.val(_self.type);

        var project_url = window.__index_url_link;
        project_url = project_url.replace('index.php','');

        window.location.href = project_url+"search/?"+"search_type="+encodeURIComponent(type)+"&keywords="+name+"&type_id="+_self.type_id;
    }

});
 
});