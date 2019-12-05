/**
 * @require ./frame.scss
 * @require ./days.scss
 */

var $ = require('zepto');
var time_picker = require('../time_picker/index');
var IScroll = require('../scroll/index');

var frame_tpl = __inline('./frame.tmpl');

var days_tpl = __inline('./days.tmpl');

var scroll_obj = '';

var view_obj = {};

var $big_view_obj = {};

var days_insert_obj = {};

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


    var months_total = (13-begin.mm) + (after.yy-begin.yy-1)*12 + (parseInt(after.mm,10));

    var tree = [];

    var month_list = [];

    for(var i = 0;i< months_total;i++)
    {
        var obj = new Date(begin.yy,parseInt(begin.mm,10)-1+i,1);

        var day_num = new Date(begin.yy,parseInt(begin.mm,10)+i,0).getDate();

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
    view_obj.removeClass('show');

    setTimeout(function()
    {
        //$('main').css('display','inherit')
        
        view_obj.addClass('fn-hide');
    },0);

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
        if(tree_source[i].year == parseInt(YMD.yy,10))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm,10))
                {
                    for(var k = 0; k<tree_source[i]['months'][j]['days'].length;k++)
                    {
                        //tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';

                        if(tree_source[i]['months'][j]['days'][k].num == parseInt(YMD.dd,10))
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
        if(tree_source[i].year == parseInt(YMD.yy,10))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm,10))
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
        begin_date = new Date(begin_obj.yy,parseInt(begin_obj.mm,10)-1,1),
        end_date = new Date(end_obj.yy,end_obj.mm,0),
        the_day = new Date(YMD.yy,parseInt(YMD.mm,10)-1,YMD.dd),
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
        if(tree_source[i].year == parseInt(YMD.yy,10))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm,10))
                {
                    for(var k = 0; k<tree_source[i]['months'][j]['days'].length;k++)
                    {
                        tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';

                        if(tree_source[i]['months'][j]['days'][k].num == parseInt(YMD.dd,10))
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
        if(tree_source[i].year == parseInt(YMD.yy,10))
        {
            for(var j = 0; j<tree_source[i]['months'].length;j++)
            {
                if(tree_source[i]['months'][j].num + 1 == parseInt(YMD.mm,10))
                {
                    for(var k = 0; k<tree_source[i]['months'][j]['days'].length;k++)
                    {
                        //tree_source[i]['months'][j]['days'][k].choosen = 'un_choose';

                        if(tree_source[i]['months'][j]['days'][k].num == parseInt(YMD.dd,10))
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
            var end_date = new Date(end_obj.yy,parseInt(end_obj.mm,10)-1,end_obj.dd);
            var end_Sign = Date_to_Sign(end_date);

            for(var k = 0;Date_to_Sign(new Date(begin_obj.yy,parseInt(begin_obj.mm,10)-1,parseInt(begin_obj.dd,10)+k)).sign != end_Sign.sign;k++)
            {
                sign_list.push(Date_to_Sign(new Date(begin_obj.yy,parseInt(begin_obj.mm,10)-1,parseInt(begin_obj.dd,10)+k)).sign)
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
    var self = this;

    //console.log(options);
    var now_date_obj = new Date();
    var now = Date_to_Sign(new Date());

    var yesterday = Date_to_Sign(new Date(now_date_obj.getFullYear(),now_date_obj.getMonth(),now_date_obj.getDate()-1));


    var CAN_NOT_CHOOSE_A_DAY_BEFORE = options.CAN_NOT_CHOOSE_A_DAY_BEFORE || false,
        CHOOSE_PAST_DAYS = options.CHOOSE_PAST_DAYS || false,
        DEFAULT_DAY = options.DEFAULT_DAY || now.sign,
        MONTH_RANGE = options.MONTH_RANGE || ['2015-01','2015-12'],
        RETURN_SEPARATOR = options.RETURN_SEPARATOR || '-',
        USE_HOUR = options.USE_HOUR == null ? true : false,
        BEGINS_HOUR = options.BEGINS_HOUR || new Date().getHours(),
        SKIP_DAYS = options.SKIP_DAYS || [];

    self.$el = options.$el||$('body');
    self.$cur_page_view = options.$cur_page_view || $('body').children();

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

    self.$el.append(frame_tpl({title:'测试呢'}));

    days_insert_obj = self.$el.find('[data-role="picker-days"]');

    days_insert_obj.append(days_str);

    view_obj = self.$el.find('[data-role="poco-date-picker"]');
    
    view_obj.append(days_insert_obj);

    self.USE_HOUR = USE_HOUR;
    self.BEGINS_HOUR = BEGINS_HOUR;

    if(self.USE_HOUR)
    {
        self.time_picker_obj = time_picker.create
        ({
            span : 60,
            custom_time : [],
            begins_time : self.BEGINS_HOUR+1,
            container : $('[data-role="date-container-time"]'),
            title: '请选择具体时间'
        });

        
    }

    
    
    // 使用局部滚动
    //view_obj.find('[data-role="picker-days"]').addClass('native-scroll').height(self.$cur_page_view.height() - 25);

    self.setup_event();
    
}

Picker.prototype.get_obj = function()
{
    return view_obj
}

Picker.prototype.slide_show = function()
{

    var self = this;
    //window.location.hash = '#date';

    //$('main').css('display','none');

    view_obj.removeClass('fn-hide');

    self.$cur_page_view.addClass('fn-hide');

    setTimeout(function()
    {
        view_obj.addClass('show'); is_show = true;

    },0);

   view_obj.find('[data-role="scroll-wrapper"]').height(window.innerHeight-70);
   $('html,body').css('overflow','hidden');
    
}

Picker.prototype.slide_hide = function()
{
    var self = this;

    if(self.USE_HOUR)
    {
        // 时间隐藏
        time_picker.hide();
    }

    slide_hidden();

    self.$cur_page_view.removeClass('fn-hide');

    $('html,body').css('overflow','');
}

Picker.prototype.setup_event = function()
{
    var self = this;

    $('[data-role-picker-day-tap="day"]').on('click',function()
    {
        $('.now_pick').removeClass('now_pick');

        $(this).addClass('now_pick');
    })

    $('[data-role="confirm_day"]').on('click',function()
    {
        self.full_time = '';

        var full_time = $(this).parents('.now_pick').attr('data-the-day');

        self.full_time = full_time;

        if(self.USE_HOUR)
        {
            time_picker.show();

            if(new Date(self.full_time).getDate() == new Date().getDate())
            {
                self.time_picker_obj.set(self.BEGINS_HOUR);
            }
            else
            {
                self.time_picker_obj.set(0);
            }
        }

        //console.log($(this).parents('.now_pick').attr('data-the-day'));
    })

    time_picker.get_obj().on('time-hour-finish',function(event,str_time)
    {
        var time = self.full_time+' '+str_time;

        self.slide_hide();

        view_obj.trigger('finish',time);

        console.log(time);

    });

    view_obj.on('webkitTransitionEnd',function()
    {
        if(!is_show)
        {
            view_obj.addClass('fn-hide');
        }
    });

    view_obj.find('[data-role="poco-date-picker-return"]').on('click',function()
    {
        //window.location.hash = '';

        slide_hidden();
    });
}



return Picker;
