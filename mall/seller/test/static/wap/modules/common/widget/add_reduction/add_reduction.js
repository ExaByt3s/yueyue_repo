define('common/widget/add_reduction/add_reduction', function(require, exports, module){ /**
 *  汤圆
 *  加减组件
 */

 /**
  * @require modules/common/widget/add_reduction/add_reduction.scss
 **/


var $ = require('components/zepto/zepto.js');
var yue_ui = require('yue_ui/frozen');

// 定义函数对象
function add_reduction(options) 
{
    var options = options || {} ;
    this.ele_tpl = options.ele ;
    this.param_val = options.param_val||{} ;
    this.is_show_operate_btn = options.is_show_operate_btn ;
    this.name = options.name || 'add-reduction';

    // 初始化参数
    var defaults = 
    {
        input_val : 0,
        max_val : '',
        min_val : ''
    }
    this.data = $.extend(true,{},defaults,this.param_val);
    //  初始化
    this.init();

}

add_reduction.prototype = 
{
    init : function() 
    {
        var self = this;

        // 安装事件
        self.setup_event();

        // 渲染模板
        self.render(self.ele_tpl);

    },
    set : function(key,value)
    {
        var self = this;

        if(!self.data[key])
        {
            return false;
        }

        self.data[key] = value;
    },
    render : function(ele) 
    {
        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div class=\"add-reduction-mod\">\n            <!-- add-no reduce-no -->\n            <div class=\"reduce\" btn-reduce><i class=\"icon icon-reduce-32x32-m\"></i></div>  \n            <div class=\"input-area\">\n            <input type=\"tel\"  value=\"";
  if (helper = helpers.default_btn_value) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.default_btn_value); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" pattern=\"[0-9]*\"  class=\"input\" btn-value>\n            <input type=\"hidden\" name=\"";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" value=\"";
  if (helper = helpers.default_btn_value) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.default_btn_value); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"fn-hide\" data-role=\"real-text\">\n            </div>\n            <div class=\"add\" btn-add><i class=\"icon icon-add-32x32-m\"></i></div>\n</div>";
  return buffer;
  });  
        var view = ele.html( template({
            default_btn_value : self.data.input_val,
            name : self.name
        }) );

        self.ele = ele;

        self.reduce_ele = view.find('[btn-reduce]');
        self.add_ele  = view.find('[btn-add]');
        self.real_text_ele = view.find('[data-role="real-text"]');

        // 输入框的值ele
        self.input_val_ele =  view.find('[btn-value]');

        if (!self.is_show_operate_btn) 
        {
            self.hide_operate_btn();
            
        }

        self.input_val_ele.on('input',function()
        {
            var btn_value = self.input_val_ele.val();
            if (self.data.min_val && ( btn_value < self.data.min_val) ) 
            {
                $.tips
                ({
                    content: '所填数字不得小于'+self.data.min_val,
                    stayTime:3000,
                    type:'warn'
                });

                self.input_val_ele.val(btn_value);
               
                return ;
            }

            if (self.data.max_val && ( btn_value > self.data.max_val) ) 
            {
                $.tips
                ({
                    content: '所填数字不得大于'+self.data.max_val,
                    stayTime:3000,
                    type:'warn'
                });
                return ;
            }
            self.set_val(btn_value);
            self.reduce_ele.trigger('input',btn_value);            
        });

        self.reduce_ele.on('tap', function() {
            self.reduce();
        });

        self.add_ele.on('tap', function() {
            self.add();
        });
    },

    setup_event : function() 
    {
        var self = this;
    },

    // 减去操作
    reduce : function() 
    {
        var self = this;
        var btn_value = self.get_val();
        btn_value-- ;

        if (self.data.min_val && ( btn_value < self.data.min_val) ) 
        {
            $.tips
            ({
                content: '所填数字不得小于'+self.data.min_val,
                stayTime:3000,
                type:'warn'
            });
           
            return ;
        }

        // 少于0退出
        if (btn_value < 0) 
        {
            self.set_val(0);
            return ;
        }

        self.set_val(btn_value);
        self.reduce_ele.trigger('reduce',btn_value);
    },

    // 增加操作
    add : function() 
    {
        var self = this;
        
        var btn_value = self.get_val();
        btn_value++ ;
        if (self.data.max_val && ( btn_value > self.data.max_val) ) 
        {
            $.tips
            ({
                content: '所填数字不得大于'+self.data.max_val,
                stayTime:3000,
                type:'warn'
            });
            return ;
        }

        self.set_val(btn_value);
        self.add_ele.trigger('add',btn_value)
    },

    // 取值
    get_val : function() 
    {
        var self = this;
        var btn_value = self.input_val_ele.val();
        return btn_value ;
    },

    // 设置值
    set_val :  function(val) 
    {
        var self = this;
        self.input_val_ele.val(val);
        self.real_text_ele.val(val);
    },
    // 不显示 加减按钮
    hide_operate_btn : function() 
    {
        var self = this;
        self.reduce_ele.addClass('fn-hide');
        self.add_ele.addClass('fn-hide');
        self.input_val_ele.addClass('input-disabled');
        self.input_val_ele.attr('readonly', 'true');

    }

}

return add_reduction;
 
});