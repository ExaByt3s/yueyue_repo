define('collect', function(require, exports, module){ /**
 * 收藏操作
 * 汤圆
 */
 /**
  * @require modules/collect/collect.scss
  **/

"use strict";
var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
// var App =  require('../common/I_APP/I_APP');
var items_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div class=\"collect-mod dib\" data-role=\"collect\">\r\n    <button class=\"ui-button  ui-button-block  ui-button-size-m ui-button-bd-ff6 ";
  if (helper = helpers.class_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.class_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"btn-collect\">\r\n        <span class=\"ui-button-content\" data-role=\"txt-collect\" >";
  if (helper = helpers.txt) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.txt); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\r\n    </button>\r\n</div>\r\n";
  return buffer;
  });


if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}


var _self = $({});

function collect(options) 
{
    var self = this;
    self.render_ele = options.ele ;
    self.target_id = options.target_id;
    self.target_type = options.target_type;
    self.value = options.value ;

    self.config = true ;
    self.$loading = {};

    //判断显示收藏，或者已收藏
    if (parseInt(self.value) == 0) 
    {
        self.data_txt = 
        {
            txt : '收藏',
            class_name : ''
        };

        self.operate  = 'follow';

    }
    else
    {
        self.data_txt = 
        {
            txt : '已收藏',
            class_name : 'has-collect'
        }

        self.operate  = 'unfollow';

    }

    self.init()

}

collect.prototype =
{
    refresh : function()
    {
        var self = this;
        var html_str = items_tpl(self.data_txt);
        var view = self.render_ele.html(html_str);
        self.click_ele = view.find('[data-role="collect"]');
        self.click_ele.unbind('click').on('click', function(event) {

            if (self.config) 
            {

                self.add_collect();
            }
                
        });
    },

    init : function()
    {

        var self = this;
        self.refresh();

    },

    add_collect : function() 
    {
        var self = this;
        var data = 
        {
            target_id : self.target_id ,
            target_type : self.target_type  ,
            operate : self.operate
        };
        $.ajax({
            url: '../ajax/collect.php',
            data: data,
            dataType: 'json',
            type: 'POST',
            cache: false,
            beforeSend: function() 
            {
                self.config = false ;
                self.$loading = $.loading
                ({
                    content:'加载中...'
                });
            },
            success: function(data) 
            {
                // debugger;
                self.$loading.loading("hide");
                self.config = true ;
                var ret = data.result_data.data ;

                if (ret.result == 1) 
                {
                    
                    $.tips({
                        content: ret.message,
                        stayTime:3000,
                        type:'success'
                    });

                    if (parseInt(self.value) == 0) 
                    {
                        self.click_ele.find('[data-role="btn-collect"]').addClass('has-collect');
                        self.click_ele.find('[data-role="txt-collect"]').html('已收藏');
                        self.operate  = 'unfollow';
                        self.value = 1 ;
                    }
                    else
                    {
                        self.click_ele.find('[data-role="btn-collect"]').removeClass('has-collect');
                        self.click_ele.find('[data-role="txt-collect"]').html('收藏');
                        self.operate  = 'follow';
                        self.value = 0 ;
                    }
                }
                else
                {
                    $.tips({
                        content: ret.message,
                        stayTime:3000,
                        type:'warn'
                    });
                }


            },    
            error: function() 
            {
                self.$loading.loading("hide");
                self.config = true ;
                $.tips({
                    content:'网络异常',
                    stayTime:3000,
                    type:'warn'
                });
                
            },    
            complete: function() 
            {
                self.$loading.loading("hide");
                self.config = true ;

            } 
        });
        
    }

    
};

return  collect;



 
});