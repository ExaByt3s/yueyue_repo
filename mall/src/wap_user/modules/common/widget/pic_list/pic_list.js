/**
 *  汤圆
 *  2015-7-13
 *  地区组件
 */
 /**
  * @require ./pic_list.scss
 **/


var $ = require('zepto');
var utility = require('../../utility/index');


// 定义函数对象
function pic_list_fn(options) 
{
    var self = this;
    var options = options || {} ;
    self.render_ele = options.ele || {} ;

    //  初始化
    self.init();
}

pic_list_fn.prototype = 
{
    init : function() 
    {
        var self = this;
        self.render();
        // 安装事件
        self.setup_event();
    },

    setup_event : function() 
    {
        var self = this;

    },

    render : function() 
    {
        var self = this;
        var template  = __inline('./pic_list.tmpl');  
        self.view = self.render_ele.html(template({}));

        self.render_item_ele = self.view.find('[data-role="item-ele"]');
        self.render_item_ele = self.view.find('[data-role="render_item_ele"]');

    },
    render_html : function(data,operate) 
    {
        var self = this;
        var template  = __inline('./item.tmpl');
        var view = template({
            data : data
        })
        self.render_item_ele[operate](view);
    }
}


return pic_list_fn;
