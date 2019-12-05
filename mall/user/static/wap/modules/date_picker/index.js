define('date_picker/index', function(require, exports, module){ /**
 * @require modules/date_picker/frame.scss
 * @require modules/date_picker/days.scss
 */

var $ = require('components/zepto/zepto.js');

var frame_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<div class=\"poco-date-picker fn-hide\" data-role=\"poco-date-picker\" >\r\n    <div class=\"picker-header\">\r\n        <h1>测试头部</h1>\r\n        <div class=\"picker-return\" data-role=\"poco-date-picker-return\"><i class=\"nok\"></i></div>\r\n    </div>\r\n    <ul class=\"week_day\">\r\n        <li>日</li>\r\n        <li>一</li>\r\n        <li>二</li>\r\n        <li>三</li>\r\n        <li>四</li>\r\n        <li>五</li>\r\n        <li>六</li>\r\n    </ul>\r\n    <div class=\"days\" data-role=\"picker-days\"></div>\r\n</div>";
  });

var days_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n    <div class=\"picker-year\">\r\n        ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.months), {hash:{},inverse:self.noop,fn:self.programWithDepth(2, program2, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n    </div>\r\n";
  return buffer;
  }
function program2(depth0,data,depth1) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n            <div class=\"picker-month\">\r\n                <div class=\"month_title\">"
    + escapeExpression(((stack1 = (depth1 && depth1.year)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "年";
  if (helper = helpers.month_num) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.month_num); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "月</div>\r\n                <div class=\"days_contain\">\r\n                    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.days), {hash:{},inverse:self.noop,fn:self.programWithDepth(3, program3, data, depth0, depth1),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                </div>\r\n            </div>\r\n        ";
  return buffer;
  }
function program3(depth0,data,depth1,depth2) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                        <div class=\"picker-day ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.choosen), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.is_default), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.num), {hash:{},inverse:self.noop,fn:self.programWithDepth(8, program8, data, depth1, depth2),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">\r\n                            <div class=\"num\">";
  if (helper = helpers.num) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.num); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n                            <div class=\"confirm_tag\">\r\n                                <div class=\"txt\" data-role=\"confirm_day\">确定</div>\r\n                                <div class=\"delta\"></div>\r\n                            </div>\r\n                        </div>\r\n                    ";
  return buffer;
  }
function program4(depth0,data) {
  
  
  return "unchoose";
  }

function program6(depth0,data) {
  
  
  return "now_pick";
  }

function program8(depth0,data,depth2,depth3) {
  
  var buffer = "", stack1, helper;
  buffer += "data-the-day=\""
    + escapeExpression(((stack1 = (depth3 && depth3.year)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "-"
    + escapeExpression(((stack1 = (depth2 && depth2.month_num)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "-";
  if (helper = helpers.num) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.num); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" ";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.choosen), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  }
function program9(depth0,data) {
  
  
  return "data-role-picker-day-tap=\"day\"";
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.tree), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { return stack1; }
  else { return ''; }
  });

$('body').append(frame_tpl({title:'测试呢'}));

var view_obj = $('[data-role="poco-date-picker"]');

var days_insert_obj = $('[data-role="picker-days"]');

var is_show = false;

var MonthEnNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];
var MonthCnNames = ["一月", "二月", "三月", "四月", "五月", "六月",
    "七月", "八月", "九月", "十月", "十一月", "十二月"
];
var WeekDays = ["周日","周一","周二","周三","周四","周五","周六"];

function init_days_by_range(range)
{
    if(range.length != 2)throw 'range number error';

    var range_arr = range;

    if(Sign_compare(range_arr[0],'>',range_arr[1]))
    {
        throw 'range[1] error';
    }

    var after = Sign_to_YM(range_arr[1]),
        begin = Sign_to_YM(range_arr[0]);

    var months_total = (13-begin.mm) + (after.yy-begin.yy-1)*12 + (parseInt(after.mm));

    var tree = [];

    var month_list = [];

    for(var i = 0;i< months_total;i++)
    {
        var obj = new Date(begin.yy,parseInt(begin.mm)-1+i,1);

        var day_num = new Date(begin.yy,parseInt(begin.mm)+i,0).getDate();

        var mm_d_tree = [];

        var first_day_week = obj.getDay();
/*
        for(var w=0;w< first_day_week;w++)
        {
            mm_d_tree.push
            ({
                empty:'true'
            })
        }
*/
        for(var j=0; j < day_num;j++)
        {
            var week = new Date(obj.getFullYear(),obj.getMonth(),j+1).getDay();


            mm_d_tree.push
            ({
                num : j+1,
                weekday : WeekDays[week],
                week_index : week
            });
        }

        month_list.push
        ({
            year : obj.getFullYear(),
            month_data :
            {
                num : obj.getMonth(),
                month_num :obj.getMonth()+1,
                cn_name : MonthCnNames[obj.getMonth()],
                en_name : MonthEnNames[obj.getMonth()],
                days : mm_d_tree
            }
        })
    }

    var year_list = [];

    for(var k = 0 ; k < month_list.length ; k++)
    {
        if(year_list.indexOf(month_list[k].year) == -1)
        {
            year_list.push(month_list[k].year)
        }
    }

    for(var l = 0; l < year_list.length; l++)
    {
        var year_index = year_list[l];

        var m_in_y = [];

        for(var m = 0; m < month_list.length;m++)
        {
            if(month_list[m].year == year_index)
            {
                m_in_y.push(month_list[m].month_data)
            }
        }

        tree.push
        ({
            year:year_index,
            months: m_in_y
        })
    }
    return tree;
}
function slide_hidden()
{
    $('main').css('display','inherit');

    view_obj.removeClass('show');

    is_show = false;
}
function Date_to_Sign(Date_Obj)
{
    var _year = Date_Obj.getFullYear(),
        _month = Date_Obj.getMonth() + 1,
        _day = Date_Obj.getDate();

    return {
        yy:_year,
        mm:_month,
        dd:_day,
        sign:_year + '-' + _month + '-' + _day
    }

}
function Sign_to_YM(sign){

    var _str = sign,
        first_ = _str.indexOf('-'),
        yy,
        mm;

    if(first_ == -1 || first_!= 4)
    {
        throw 'DEFAULT_DAY error';
    }
    else
    {
        yy = _str.slice(0,first_);
        _str = _str.substring(first_ + 1,_str.length);
        mm = _str;
    }

    return {yy:yy,mm:mm};

}

function Sign_to_YMD(sign){

    var _str = sign,
        first_ = _str.indexOf('-'),
        second_,
        yy,
        mm,
        dd;

    if(first_ == -1 || first_!= 4)
    {
        throw 'DEFAULT_DAY error';
    }
    else
    {
        yy = _str.slice(0,first_);
        _str = _str.substring(first_ + 1,_str.length);
    }

    second_ = _str.indexOf('-');

    if(second_  == -1)
    {
        throw 'DEFAULT_DAY error';
    }
    else
    {
        mm = _str.slice(0,second_);
        _str = _str.substring(second_+1,_str.length);
        dd = _str;
    }

    if(mm.length ==1)
    {
        mm = '0'+mm
    }

    if(dd.length ==1)
    {
        dd = '0'+dd
    }

    return {yy:yy,mm:mm,dd:dd};

}

function Sign_compare(sign1,control,sign2)
{
    var left = sign1,
        right = sign2,
        ret;

    switch(control)
    {
        case '<':ret = (left < right);break;
        case '==':ret = (left == right);break;
        case '>':ret = (left > right);break;
        case '!=':ret = (left != right);break;
    }


    return ret;
}
function Tree_control_add_empty_day(tree)
{
    var tree_source = tree;

    for(key in tree_source)
    {
        for(y in tree_source[key])
        {
            if(y == 'months')
            {
                for(m in tree_source[key][y])
                {
                    for(d in tree_source[key][y][m])
                    {
                        if(d == 'days')
                        {
                            var empty_arr = [];

                            for(var w=0;w< tree_source[key]['months'][m]['days'][0].week_index;w++)
                            {
                                empty_arr.push({
                                    empty:'true'
                                })
                            }
                            tree_source[key]['months'][m]['days'] = empty_arr.concat(tree_source[key]['months'][m]['days'])
                        }
                    }
                }
            }
        }
    }

    return tree_source
}

function Tree_control_can_not_choice_a_day(tree,day_sign)
{
    var tree_source = tree;

    var YMD = Sign_to_YMD(day_sign);

    var y_limit,m_limit,d_limit;

    for(var i =0;i < tree_source.length;i++)
    {
        if(tree_source[i].year == parseInt(YMD.yy))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm))
                {
                    for(var k = 0; k<tree_source[i]['months'][j]['days'].length;k++)
                    {
                        //tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';

                        if(tree_source[i]['months'][j]['days'][k].num == parseInt(YMD.dd))
                        {
                            y_limit = i;
                            m_limit = j;
                            d_limit = k;

                            tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';

                            break;
                        }
                    }
                }
            }
        }
    }

    return tree_source;
}

function Tree_control_can_not_choice_a_month(tree,month_sign)
{
    //某个月不能选 例:2015-03
    var tree_source = tree;

    var YMD = Sign_to_YM(month_sign);

    for(var i =0;i < tree_source.length;i++)
    {
        if(tree_source[i].year == parseInt(YMD.yy))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm))
                {
                    for(var k = 0; k<tree_source[i]['months'][j]['days'].length;k++)
                    {
                        if(tree_source[i]['months'][j]['days'][k].num)
                        {
                            tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';
                        }
                    }
                }
            }
        }
    }
    return tree_source;
}

function a_day_in_range(sign,range)
{
    var YMD = Sign_to_YMD(sign),
        begin_obj = Sign_to_YM(range[0]),
        end_obj = Sign_to_YM(range[1]),
        begin_date = new Date(begin_obj.yy,parseInt(begin_obj.mm)-1,1),
        end_date = new Date(end_obj.yy,end_obj.mm,0),
        the_day = new Date(YMD.yy,parseInt(YMD.mm)-1,YMD.dd),
        the_range = [];

    if(the_day.getTime() < begin_date.getTime())
    {

    }
    else if(begin_date.getTime() < the_day.getTime() && the_day.getTime() < end_date.getTime())
    {

        the_range.push(Date_to_Sign(begin_date).sign + '~' +Date_to_Sign(the_day).sign)
    }
    else
    {
        the_range.push(Date_to_Sign(begin_date).sign + '~' +Date_to_Sign(end_date).sign)
    }

    return the_range
}
function Tree_control_can_not_choice_a_day_before(tree)
{
    var tree_source = tree;

    //console.log(the_range)

    return tree_source
}

function Tree_control_can_not_choice_pass_days(tree)
{
    var tree_source = tree;

    var YMD = Sign_to_YMD(Date_to_Sign(new Date()).sign);

    //console.log(tree_source)
    //console.log(YMD);
    var y_limit,m_limit,d_limit;

    for(var i =0;i < tree_source.length;i++)
    {
        if(tree_source[i].year == parseInt(YMD.yy))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm))
                {
                    for(var k = 0; k<tree_source[i]['months'][j]['days'].length;k++)
                    {
                        tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';

                        if(tree_source[i]['months'][j]['days'][k].num == parseInt(YMD.dd))
                        {
                            y_limit = i;
                            m_limit = j;
                            d_limit = k;
                            //console.log(i,j,k);

                                /*
                                for(var e_j=0;e_j<j;e_j++)
                                {
                                    for(var e_k=0;e_k<k;e_k++)
                                    {
                                        console.log(e_i,e_j,e_k)
                                        tree_source[e_i]['months'][e_j]['days'][e_k].choosen = 'un_choose';
                                    }
                                }
                                */

                            break;
                            //console.log(tree_source[i]['months'][j]['days'][k])
                        }
                    }
                }
            }
        }
    }
    for(key in tree_source)
    {
        //console.log(tree_source[key]);
    }

    return tree_source;
}

function Tree_control_set_default_day(day_sign,tree)
{
    //设置默认日
    var tree_source = tree;

    var YMD = Sign_to_YMD(day_sign);

    var y_limit,m_limit,d_limit;

    for(var i =0;i < tree_source.length;i++)
    {
        if(tree_source[i].year == parseInt(YMD.yy))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm))
                {
                    for(var k = 0; k<tree_source[i]['months'][j]['days'].length;k++)
                    {
                        //tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';

                        if(tree_source[i]['months'][j]['days'][k].num == parseInt(YMD.dd))
                        {
                            y_limit = i;
                            m_limit = j;
                            d_limit = k;

                            tree_source[i]['months'][j]['days'][k].is_default = 'default';

                            break;
                        }
                    }
                }
            }
        }
    }

    return tree_source;
}

function parse_string_to_Sign(signTosign)
{
    //时间区间表达式转换成[开始，结束]格式
    var str = signTosign;

    var spec_index = str.indexOf('~');

    if(spec_index == -1)throw 'Sign_range ~ error!'

    var sign_1 = str.substring(0,spec_index);

    var sign_2 = str.substring(spec_index+1,str.length);

    if(sign_1.length == 9){sign_1 = fixed_Sign(sign_1)}
    if(sign_2.length == 9){sign_2 = fixed_Sign(sign_2)}

    //if(Sign_compare(sign_2,'<',sign_1))throw 'sign2 must bigger than sign1';

    return [sign_1,sign_2]
}

function fixed_Sign(Sign)
{
    var _s = Sign

    var index = _s.indexOf('-');

    var be = _s.substring(0,index+1);

    var af = _s.substring(index+1,_s.length);

    return be+'0'+af;
}
function parse_arr_to_Sing_list(arr)
{
    //日期表达数组转换成日期数组
    var sign_list=[];

    for(var i = 0;i<arr.length;i++)
    {
        if(arr[i].indexOf('~') != -1)
        {
            var ss = parse_string_to_Sign(arr[i]);
            var begin_obj = Sign_to_YMD(ss[0]);
            var end_obj = Sign_to_YMD(ss[1]);
            var begin_date = new Date(begin_obj.yy,begin_obj.mm,begin_obj.dd);
            var end_date = new Date(end_obj.yy,parseInt(end_obj.mm)-1,end_obj.dd);
            var end_Sign = Date_to_Sign(end_date);

            for(var k = 0;Date_to_Sign(new Date(begin_obj.yy,parseInt(begin_obj.mm)-1,parseInt(begin_obj.dd)+k)).sign != end_Sign.sign;k++)
            {
                sign_list.push(Date_to_Sign(new Date(begin_obj.yy,parseInt(begin_obj.mm)-1,parseInt(begin_obj.dd)+k)).sign)
            }
            sign_list.push(end_Sign.sign);
        }
        else
        {
            sign_list.push(arr[i]);
        }
    }
    return sign_list
}

function Picker(options)
{
    //console.log(options);
    var now_date_obj = new Date();
    var now = Date_to_Sign(new Date());

    var yesterday = Date_to_Sign(new Date(now_date_obj.getFullYear(),now_date_obj.getMonth(),now_date_obj.getDate()-1));

    var CAN_NOT_CHOOSE_A_DAY_BEFORE = options.CAN_NOT_CHOOSE_A_DAY_BEFORE || false,
        CHOOSE_PAST_DAYS = options.CHOOSE_PAST_DAYS || false,
        DEFAULT_DAY = options.DEFAULT_DAY || now.sign,
        MONTH_RANGE = options.MONTH_RANGE || ['2015-01','2015-12'],
        RETURN_SEPARATOR = options.RETURN_SEPARATOR || '-',
        SKIP_DAYS = options.SKIP_DAYS || [];

    var DAY_TREE = init_days_by_range(MONTH_RANGE);

    DAY_TREE = Tree_control_add_empty_day(DAY_TREE);

    var skip_list = parse_arr_to_Sing_list(SKIP_DAYS);

    if(skip_list.length != 0)
    {
        for(var y = 0;y<skip_list.length;y++)
        {
            DAY_TREE = Tree_control_can_not_choice_a_day(DAY_TREE,skip_list[y]);
        }
    }


    if(CAN_NOT_CHOOSE_A_DAY_BEFORE)
    {
        var range_list = parse_arr_to_Sing_list(a_day_in_range(CAN_NOT_CHOOSE_A_DAY_BEFORE,MONTH_RANGE));

        if(range_list.length != 0)
        {
            for(var y = 0;y<range_list.length;y++)
            {
                DAY_TREE = Tree_control_can_not_choice_a_day(DAY_TREE,range_list[y]);
            }
        }
    }

    if(!CHOOSE_PAST_DAYS)
    {
        var k_list = parse_arr_to_Sing_list(a_day_in_range(yesterday.sign,MONTH_RANGE));

        if(k_list.length != 0)
        {
            for(var y = 0;y<k_list.length;y++)
            {
                DAY_TREE = Tree_control_can_not_choice_a_day(DAY_TREE,k_list[y]);
            }
        }
    }

    DAY_TREE = Tree_control_set_default_day(DEFAULT_DAY,DAY_TREE);

    var days_str = days_tpl({tree :DAY_TREE});

    days_insert_obj.html(days_str);

    setup_event()
    //console.log(DAY_TREE);
}

Picker.prototype.get_obj = function()
{
    return view_obj
}

Picker.prototype.slide_show = function()
{
    $('main').css('display','none');

    view_obj.removeClass('fn-hide');

    //console.log(view_obj);

    setTimeout(function(){view_obj.addClass('show'); is_show = true;},0)
}

Picker.prototype.slide_hide = function()
{
    slide_hidden();
}

view_obj.on('webkitTransitionEnd',function()
{
    if(!is_show)
    {
        view_obj.addClass('fn-hide');
    }
});

view_obj.find('[data-role="poco-date-picker-return"]').on('click',function()
{
    slide_hidden();
});

return Picker;

function setup_event()
{
    $('[data-role-picker-day-tap="day"]').on('click',function()
    {
        $('.now_pick').removeClass('now_pick');

        $(this).addClass('now_pick');
    })

    $('[data-role="confirm_day"]').on('click',function()
    {
        var str = $(this).parents('.now_pick').attr('data-the-day');

        view_obj.trigger('finish',[str])

        //console.log($(this).parents('.now_pick').attr('data-the-day'));
    })
} 
});