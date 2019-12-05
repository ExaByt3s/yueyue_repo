define('common/widget/page_control/page-control', function(require, exports, module){ 
var $ = require('components/jquery/jquery.js');
var utility = require('common/utility/index');


// 定义函数对象
function page_control(options)
{
    var self = this;
    var options = options || {} ;
    self.render_ele = options.container || document.body ;
    self.render_index_ele = options.index_btn_container || document.body;
    self.render_data = options.data || {} ;
    self.after_next_act = options.after_next_act || function(){};
    self.after_render = options.after_render || function(){};
    self.after_pre_act = options.after_pre_act || function(){};
    self.btn_index_max = options.btn_index_max || 5;
    self.default_index = options.default_index || 0;
    self.index_btn_trigger_show_lr_btn = options.index_btn_trigger_show_lr_btn || 5 ;
    self.index_btn_temp = options.index_btn_temp || Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, self=this, functionType="function", escapeExpression=this.escapeExpression;

function program1(depth0,data) {
  
  
  return "\n    <a title=\"上一页\" data-index=\"pre\">&lt;</a>\n";
  }

function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <a ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.cur), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " data-index=\"";
  if (helper = helpers.dex) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.dex); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\">";
  if (helper = helpers.num) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.num); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</a>\n";
  return buffer;
  }
function program4(depth0,data) {
  
  
  return "class=\"click\"";
  }

function program6(depth0,data) {
  
  
  return "\n    <a title=\"下一页\" data-index=\"next\">&gt;</a>\n";
  }

  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.has_pre), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.list), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.data)),stack1 == null || stack1 === false ? stack1 : stack1.has_next), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  return buffer;
  });
    self.list_temp = options.list_temp || Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var stack1, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n<div class=\"item clearfix\">\n    <div class=\"user-img\">\n        <div class=\"img\"><a href=\"#\"><img src=\"";
  if (helper = helpers.avatar) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.avatar); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" /></a></div>\n        <p class=\"id-num\">ID:";
  if (helper = helpers.from_user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.from_user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</p>\n    </div>\n    <div class=\"item-main\">\n        <div class=\"mod-star-item\" data-role=\"commit_stars\" data-stars=\"";
  if (helper = helpers.rating) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.rating); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\">\n            <div class=\"icon none-star\" data-role=\"stars\">\n                <div class=\"yellow none\"></div>\n            </div>\n            <div class=\"icon none-star\" data-role=\"stars\">\n                <div class=\"yellow none\"></div>\n            </div>\n            <div class=\"icon none-star\" data-role=\"stars\">\n                <div class=\"yellow none\"></div>\n            </div>\n            <div class=\"icon none-star\" data-role=\"stars\">\n                <div class=\"yellow none\"></div>\n            </div>\n            <div class=\"icon none-star\" data-role=\"stars\">\n                <div class=\"yellow none\"></div>\n            </div>\n        </div>\n        <div class=\"text-item\">\n            <p>";
  if (helper = helpers.comment) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.comment); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</p>\n        </div>\n        <div class=\"info-item\"><span class=\"name\">";
  if (helper = helpers.customer) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.customer); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</span><span class=\"date\">";
  if (helper = helpers.add_time) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.add_time); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</span> </div>\n    </div>\n</div>\n";
  return buffer;
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { return stack1; }
  else { return ''; }
  });
    self.per_page_amount = options.per_page_amount || 10 ;
    self.current_index = self.default_index;


    //  初始化
    self.init();
}

page_control.prototype =
{
    init : function() 
    {
        var self = this;

        //if(self.render_data.length == 0){return}
        self.render();
        // 安装事件
        self.setup_event();
    },
    setup_event:function()
    {
        var self = this;
    },
    after_show_event : function()
    {
        var self = this;

        $('[data-index]').on('click',function()
        {
            var con = $(this);

            var status = con.attr('data-index');

            switch (status)
            {
                case 'pre':self.pre_page();break;
                case 'next':self.next_page();break;
                default:self.current_index=parseInt(status);self.show_page(status);break;
            }

        })
    },

    render : function() 
    {
        var self = this;

        self.data_combine = self._data_init();

        self.index_data = self._index_init();

        self.show_page(self.current_index);
    },
    show_page : function(index)
    {
        var self = this;

        self.index_data = self._index_init();

        var str = '';

        var index_str = '';

        if(self.data_combine.length != 0)
        {
            str = self.list_temp({data:self.data_combine[index].list});

            index_str = self.index_btn_temp({data:self.index_data});
        }

        self.view = self.render_ele.html(str);

        self.index_view = self.render_index_ele.html(index_str);

        self.after_show_event();

        self.after_render(self);

    },
    next_page : function()
    {
        var self = this;

        self.show_page(++self.current_index);

        self.after_next_act(self);
    },
    pre_page : function()
    {
        var self = this;

        self.show_page(--self.current_index);

        self.after_pre_act(self);

    },
    _index_init : function()
    {
        var self = this;

        var pages = self.total_pages;

        var has_pre = true,
            has_next = true;


        if(self.current_index > pages)
        {
            self.current_index = 0;
        }


        if(self.current_index == 0)
        {
            has_pre = false;
            has_next = true;
        }
        if(self.current_index == pages-1)
        {
            has_pre = true;
            has_next = false;
        }
        if(self.index_btn_trigger_show_lr_btn > pages-1)
        {
            has_pre = false;
            has_next = false;
        }
        var index_btn =
        {
            has_pre:has_pre,
            has_next:has_next
        }

        var index_arr = [];

        for(var i = 0;i < pages;i++)
        {
            if(i == self.current_index)
            {
                index_arr.push({dex:i,num:i+1,cur:true})
            }
            else
            {
                index_arr.push({dex:i,num:i+1,cur:false})
            }

        }

        index_btn.list = index_arr

        //console.log(index_btn.list)

        return index_btn
    },
    _data_init : function()
    {
        var self = this;

        self.grouping_arr = [];

        var data = self.render_data;

        self.total_pages = Math.ceil(data.length/self.per_page_amount);

        for(var i =0 ; i < self.total_pages ; i++)
        {
            var obj =
            {
                list:[]
            };

            var num;

            data.length < self.per_page_amount? num = data.length:num = self.per_page_amount

            for(var k =0;k<self.per_page_amount;k++)
            {
                while(num)
                {
                    obj.list.push(data[0]);
                    data = data.slice(1,data.length);
                    num--;
                }
            }

            self.grouping_arr.push(obj);
        }



        //console.log(self.index_data);
        //console.log(self.grouping_arr);

        return self.grouping_arr
    }
}


return page_control;
 
});