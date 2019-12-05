define('time_picker/index', function(require, exports, module){ /**
 * @require modules/time_picker/frame.scss
 * @require modules/time_picker/hour.scss
 */

var $ = require('components/zepto/zepto.js');

var frame_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div class=\"poco-time-picker fn-hide\" data-role=\"poco-time-picker\" data-time-picker=\"";
  if (helper = helpers.index) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.index); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\r\n    <div class=\"title\">";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n    <div class=\"hour_contain\" data-time-hour-picker=\"";
  if (helper = helpers.index) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.index); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"></div>\r\n</div>";
  return buffer;
  });

var hour_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n    <div class=\"time-picker-hour ";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.enable), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.enable), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " >";
  if (helper = helpers.val) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.val); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n";
  return buffer;
  }
function program2(depth0,data) {
  
  
  return "disable";
  }

function program4(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "data-time-carry=\"";
  if (helper = helpers.val) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.val); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-time-tap=\"hour\"";
  return buffer;
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { return stack1; }
  else { return ''; }
  });

module.exports =
{
    hour_data : [],
    full_day_seconds : 86400,
    create : function(options)
    {
        var self = this;

        self.init(options);

        return self
    },
    init : function(options)
    {
        var self = this;

        self.span = options.span || 60;

        self.custom_time = options.custom_time || [];

        self.container = options.container || $('body');

        self.title = options.title || '';

        self.begins_time = options.begins_time || '';

        if(!self.custom_time.length)
        {
            //无自定义时间
            if(!self.span)throw 'span cant not empty';

            self._format_hour_data(self.span,self.begins_time);
        }
        else
        {
            //有自定义时间
            self._check_custom_data(self.custom_time);
        }

        self.render();

        self.setup_event();
    },
    setup_event : function()
    {
        var self = this;

        $('[data-time-tap="hour"]').on('click',function()
        {
            var time = $(this).attr('data-time-carry');

            self.ele.trigger('time-hour-finish',time)
        })
    },
    set : function(begins_time)
    {
        var self = this;

        self._format_hour_data(self.span,begins_time);

        self._render_hour();

        self.setup_event();
    },
    render : function()
    {
        var self = this;

        self._render_frame();

        self._render_hour();
    },
    _render_frame : function()
    {
        var self = this;

        self.render_index = parseInt(Math.random()*10000);

        var frame_str = frame_tpl({index : self.render_index,title : self.title});

        self.container.append(frame_str);
    },
    _render_hour : function()
    {
        var self = this;

        var hour_str = hour_tpl({data : self.hour_data});

        self.ele = $('[data-time-picker="'+ self.render_index + '"]');

        self.hour_insert = $('[data-time-hour-picker="' + self.render_index + '"]');

        self.hour_insert.html(hour_str);
    },
    show : function()
    {
        var self = this;

        self.ele.removeClass('fn-hide');
    },
    hide : function()
    {
        var self = this;

        self.ele.addClass('fn-hide');
    },
    _format_hour_data : function(span,begins)
    {
        var self = this;

        var hour_span = span || self.span;

        var begin_hour = begins || '00';

        var end_hour = self.ends || '24';

        var pre_second = hour_span*60;

        var begin_insert = parseInt(begin_hour);

        self.hour_data = [];

        /*var i = parseInt(begin_hour)*60*60;
        var len = parseInt(end_hour)*60*60;
        

        for(;i< len ;i = i + pre_second)
        {
            var time = self._second_to_hour(begin_insert);
            
            self.hour_data.push(time);

            begin_insert = begin_insert + pre_second;
        }*/

        var start = begin_hour;

        // hudw
        // 2015.10.23
        for(var i=0;i<24;i++)
        {  
            
            self.hour_data.push
            ({
                val : i + ' ' + ':00',
                enable : i >=begin_hour
            });
        }
    },
    _second_to_hour : function(s)
    {
        var self = this;

        var hour = self._fix_single(parseInt(s/60/60));

        var minus = self._fix_single(parseInt(s/60%60));

        return hour +  ':'  + minus;
    },
    _fix_single : function(s)
    {
        var str = s.toString();

        if(str.length != 2)
        {
            str = '0' + str;
        }

        return str
    },
    _str_to_hour_min : function(str)
    {
        var index = str.indexOf(':');

        if(str.length != 5)throw str + ' is un valid => length ';

        if(index != 2)throw str + ' is un valid => : ';

        var hour = parseInt(str.substring(0,index));

        if(hour >= 24 || hour < 0)throw str + ' is un valid => hour 0~23';

        var min = parseInt(str.substring(index+1,str.length));

        if(min >= 60 || min < 0)throw str + ' is un valid => min 0~59';
    },
    _check_custom_data : function(c)
    {
        var self = this;

        for(var i = 0 ; i < c.length;i++)
        {
            self._str_to_hour_min(c[i])
        }

        self.hour_data = c
    },
    get_obj : function()
    {
        var self = this;

        return self.ele
    }
}
 
});