/**
 * 工具集
 * hdw  2014.8.13
 */
define(function(require, exports, module)
{
	var $ = require('$');
	var $win = $(window);
    var storage = window.localStorage;
    var cross_domain_storage = require('./cross_domain_storage');
    var cookie = require('cookie');
    var underscrore = require('underscore');
    var ua = require('../frame/ua');

    var login_id = parseInt(cookie.get('yue_member_id'));



    var _ = underscrore._;

    var window_location = window.location;

    // 处理origin兼容性问题
    if (!window_location.origin)
    {
        window_location.origin = window_location.protocol + '//' + window_location.hostname + (window_location.port ? (':' + window_location.port) : '');
    }

    if(/wifi/.test(window_location.origin))
    {
        var origin = window_location.origin.replace('-wifi','');
    }
    else
    {
        var split_origin = window_location.origin.split('.');

        var origin = split_origin[0]+"-wifi" + '.' + split_origin[1]+'.'+split_origin[2];
    }

    console.log(origin)

    var cdstorage = new cross_domain_storage(origin, "/mobile/crossd_iframe.html");

    console.log(cdstorage)

    // 此函数用于回调获取跨域的localstroage
    function cdstorage_callback(key, val)
    {

    }

    var utility =
	{
		get_view_port_height : function(type)
		{
			var nav_bar_height = 45;
			var tab_bar_height = 45;
			var height = $win.height();

			switch(type)
			{
				case 'nav' :
					height = height - tab_bar_height;
					break;
				case 'tab' :
					height = height - nav_bar_height;
					break;
				case 'all' :
					height = height - tab_bar_height - nav_bar_height;	
					break;
				default :
					height

			}

			return height;
		},
		get_view_port_width : function()
		{
			return $win.width();
		},
        /**
         * 转化为整型
         * @param s
         * @returns {Number}
         */
        int: function(s)
        {
            return parseInt(s, 10) || 0;
        },
        /**
         * 转换为浮点型
         * @param s
         * @returns {Number}
         */
        float : function(s)
        {
            return parseFloat(s);
        },
        /**
         * 保留指定小数
         * @param number
         * @param fractionDigits
         * @returns {number}
         */
        format_float :function (number, fractionDigits)
        {
            fractionDigits = fractionDigits || 0;

            var pow = Math.pow(10, fractionDigits);
            return (Math.round(number * pow) / pow) || 0;
        },

        /**
         * 获取hash
         * @returns {string}
         */
        getHash: function()
        {
            return window.location.hash.substr(1);
        },
        /**
         * 换取缩放宽
         * @param ori_width
         * @param ori_height
         * @param zoom_width
         * @returns {Number}
         */
        get_zoom_height_by_zoom_width : function(ori_width,ori_height,zoom_width)
        {
            return parseInt( (ori_height * zoom_width)/ori_width )
        },
        /**
         * 本地存储器
         */
        storage :
        {
            /**
             * 前缀
             */
            prefix: 'poco-yuepai-app-',
            /**
             * 设置
             * @param key
             * @param val
             * @returns {*}
             */
            set: function(key, val)
            {
                if (typeof val == 'undefined')
                {
                    return utility.storage.remove(key);
                }

                cdstorage.setItem(utility.storage.prefix + key, JSON.stringify(val));

                storage.setItem(utility.storage.prefix + key, JSON.stringify(val));

                return val;
            },
            /**
             * 获取
             * @param key
             * @returns {*}
             */
            get: function(key)
            {
                var item = storage.getItem(utility.storage.prefix + key);

                if(!item)
                {
                    return item;
                }
                else
                {
                    return JSON.parse(item);
                }

            },
            /**
             * 删除
             * @param key
             * @returns {*}
             */
            remove: function(key)
            {
                return storage.removeItem(utility.storage.prefix + key);
            }
        },
        select_time :
        {
            year : function(end_year)
            {
                var date = new Date();

                var year = date.getFullYear();

                var end_year = end_year || year+3

                var data = get_arr_by_start_and_end(year,end_year);

                var arr = [];

                for(var i=0;i<data.length;i++)
                {
                    arr.push
                    ({
                        text : data[i],
                        value : data[i]
                    });
                }

                return arr;

            },
            month : function()
            {
                var data = get_arr_by_start_and_end(1,12);

                var arr = [];

                for(var i=0;i<data.length;i++)
                {
                    arr.push
                    ({
                        text : data[i],
                        value : data[i]
                    });
                }

                return arr;
            },
            day : function(cur_year,cur_month)
            {
                var d = new Date();

                var cur_year = cur_year || d.getFullYear();

                var cur_month = cur_month || d.getMonth()+1;

                if((cur_month % 2 != 0) || cur_month == 7 || cur_month == 8)
                {
                    var data = get_arr_by_start_and_end(1,31);
                }
                else
                {
                    if(cur_month == 2)
                    {
                        if(!utility.select_time.is_leap_year(cur_year))
                        {
                            var data = get_arr_by_start_and_end(1,28);
                        }
                        else
                        {
                            var data = get_arr_by_start_and_end(1,29);
                        }
                    }
                    else
                    {
                        var data = get_arr_by_start_and_end(1,30);
                    }


                }

                var arr = [];

                for(var i=0;i<data.length;i++)
                {

                    arr.push
                    ({
                        text : data[i],
                        value : data[i]
                    });
                }


                return arr;
            },
            hour : function()
            {
                var data = get_arr_by_start_and_end(0,23);

                var arr = [];

                for(var i=0;i<data.length;i++)
                {


                    arr.push
                    ({
                        text : data[i],
                        value : data[i]
                    });
                }

                return arr;
            },
            area_time_arr : function()
            {
                var self = this;

                var arr = [{text:'上午',value:'morning',selected:true},{text:'下午',value:'afternoon'},{text:'晚上',value:'night'}];

                return arr;
            },
            min : function()
            {
                return [{value:"00"},{value:"15"},{value:"30"},{value:"45"}];
            },
            is_leap_year:function(year)
            {
                return ((year%400==0) || (year%100!=0) && (year%4==0));
            },
            today_is : function()
            {
                var d = new Date();

                var cur_year = d.getFullYear();
                var cur_month = d.getMonth()+1;
                var cur_date = d.getDate();

                return {
                    year : cur_year,
                    month : cur_month,
                    date : cur_date,
                    full_date : cur_year+"-"+cur_month+"-"+cur_date

                };
            },
            mix_date : function()
            {
                var self = this;

                var d = new Date();

                var cur_month = new Date().getMonth()+1;
                var cur_day = new Date().getDate();
                var cur_year = utility.select_time.today_is().year;
                var cur_hour = d.getHours();
                var cur_min = d.getMinutes();

                var year = utility.select_time.year();
                var month = utility.select_time.month();

                var hour = utility.select_time.hour();
                var min = utility.select_time.min();



                var date_arr = {};
                var hour_arr = {};
                var min_arr = {};
                var temp_month_arr_1 = [];
                var temp_month_arr_2 = [];
                var month_arr = [];

                /*****************整合今年和明年的时间，但不是最优的解决方案*********************/


                if(cur_month == 12)
                {
                    temp_month_arr_1.push( month[month.length-1]);
                    temp_month_arr_1.push( month[0]);
                }
                else
                {
                    temp_month_arr_1.push( month[cur_month-1]);
                    temp_month_arr_1.push( month[cur_month]);
                }

                month_arr = temp_month_arr_1;

                /*****************整合今年和明年的时间，但不是最优的解决方案*********************/

                // 整合日期
                for(var n=0;n<month_arr.length;n++)
                {
                    var day = utility.select_time.day(cur_year,month_arr[n].value);

                    for(var m=0;m<day.length;m++)
                    {
                        var date = month_arr[n].value+'月'+day[m].value+'日';
                        var selected_cur_year = cur_year;

                        /****************今年和明年的时间整理，但不是最好的算法******************/
                        if(cur_month < month_arr[n].value )
                        {
                            if(day[m].value>cur_day)
                            {
                                selected_cur_year = cur_year
                            }

                        }
                        else if (month_arr[n].value == cur_month)
                        {
                            if(day[m].value>=cur_day)
                            {
                                selected_cur_year = cur_year
                            }
                            else
                            {
                                selected_cur_year = cur_year+1;

                                continue;
                            }
                        }
                        else
                        {
                            selected_cur_year = cur_year+1;


                        }
                        /****************今年和明年的时间整理，但不是最好的算法******************/

                        //console.log('日期:'+selected_cur_year + "--" + month_arr[n].value+'月'+day[m].value+'日');

                        var obj =
                        {
                            value :selected_cur_year+"-" + month_arr[n].value+'-'+day[m].value,
                            text : date,
                            selected : cur_month == month_arr[n].value &&  cur_day == day[m].value
                        };

                        date_arr[date] = obj;
                    }
                }




                // 整合时
                for(var n=0;n<hour.length;n++)
                {
                    hour[n].selected = n == 0

                    hour_arr[hour[n].value+"时"] =
                    {
                        value : hour[n].value,
                        text : hour[n].value+"时",
                        selected : n == 0
                    };



                }

                // 整合分
                for(var n=0;n<min.length;n++)
                {
                                     
                    min[n].selected = n == 0;

                    min_arr[hour[n].value+"分"] =
                    {
                        value : min[n].value,
                        text : min[n].value+"分",
                        selected : n == 0
                    };

                }

                return{
                    date_arr : date_arr,
                    hour_arr : hour_arr,
                    min_arr  : min_arr
                }
            }
        },
        /**
         * 订单信息
         */
        date_info :
        {
            set : function(info)
            {
                var self = this;

                utility.storage.set('date-info',info);
            },
            get : function()
            {
                var self = this;

                return utility.storage.get('date-info');
            },
            clear : function()
            {
                var self = this;

                utility.storage.remove('date-info');
            }
        },
        is_bigger_time : function(t1,t2)
        {
            var d1 = new Date(t1.replace(/\-/g, "\/"));
            var d2 = new Date(t2.replace(/\-/g, "\/"));

            return d1>d2;
        },
        obj_Equal : function(obj_a,obj_b)
        {
            return _.isEqual(obj_a,obj_b)

        },
        is_empty : function(check_obj)
        {
            var obj_type = typeof(check_obj)

            //console.log(obj_type)
            switch(obj_type)
            {
                case "undefined" :
                    var is_empty = true;

                    break;

                case "boolean" :
                    var is_empty = !check_obj
                    break

                case "number" :
                    if(check_obj>0)
                    {
                        var is_empty = false
                    }
                    else
                    {
                        var is_empty = true
                    }
                    break;
                case "string" :

                    if(check_obj=="" || ( check_obj<="0" && !isNaN(parseInt(check_obj)) )  )
                    {
                        var is_empty = true
                    }
                    else
                    {
                        var is_empty = false
                    }

                    break
                case "object" :
                    if(_.isEmpty(check_obj))
                    {
                        var is_empty = true;
                    }
                    //数组
                    else if( check_obj instanceof Array )
                    {
                        if(check_obj.length == 0)
                        {
                            var is_empty = true
                        }
                        else
                        {
                            var is_empty = false
                        }
                    }
                    else
                    {
                        var is_empty = true

                        for (var name in check_obj)
                        {
                            is_empty = false
                        }
                    }

                    break

                default :
                    var is_empty = false
            }

            return is_empty;
        },
        ajax_request : function(options)
        {
            var options = options || {};

            var url = options.url;
            var data = options.data || {};
            var cache = options.cache || false;
            var beforeSend = options.beforeSend || function(){};
            var success = options.success || function(){};
            var error = options.error || function(){};
            var complete = options.complete || function(){};
            var type = options.type || 'GET';
            var dataType = options.dataType || 'json';

            $.ajax
            ({
                url: url,
                type : type,
                data : data,
                cache: cache,
                dataType : dataType,
                beforeSend: beforeSend,
                success: success,
                error:error,
                complete: complete
            });
        },
        auth :
        {
            is_login : function()
            {
                return utility.login_id>0;
            }
        },
        //图片转换size
        matching_img_size : function(img_url,size)
        {

            var sort_size = size;

            return change_img_resize(img_url,sort_size);
        },
        /**
         * 设置不可返回页面
         * @param hash
         */
        set_no_page_back : function(hash)
        {
            // 设置后退按钮是否可用
            if (ua.isAndroid)
            {
                var hash_obj = {};

                hash_obj[hash] = 1;

                window._rootHash = $.extend(window._rootHash,hash_obj);

            }


        },
        get_grid_size : function(type)
        {
            var width = 0;
            var height = 0;
            var special_size = 0;

            var temp_size = 0;

            var view_width = utility.get_view_port_width();

            /*if(view_width%3 == 0)
             {
             temp_size = view_width/3;
             }
             else if ((view_width+1)%3 == 0)
             {
             temp_size = (view_width+1)/3;
             }
             else if((view_width+2)%3 == 0)
             {
             temp_size = (view_width+2)/3;
             }*/

            special_size = utility.int(view_width/3);


            switch(type)
            {
                case 'one' :
                case 'special' :
                    width = special_size;
                    height = view_width - special_size*2;
                    break;
                case 'middle' :

                    width = special_size - 2;
                    height = view_width - special_size*2;
                    break;
                case 'double' :
                    width = special_size*2 - 1;
                    height = (view_width - special_size*2)*2+1
                    break;
            }




            return {
                width : width,
                height : height
            };
        },
        /**
         * 在线状态更变
         * @param fn
         */
        online_change: function(fn)
        {
            typeof fn == 'function' && onlineChangeEvent.push(fn);

        },
        get_url_params : function (url, paramName )
        {
            var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
            //var oMatch = oRegex.exec( window.top.location.search ) ; //获取当前窗口的URL
            var oMatch = oRegex.exec( url ) ;
            if ( oMatch && oMatch.length > 1 )
            {
                return oMatch[1];   //返回值
            }
            else
            {
                return '' ;
            }

        },
        /**
         * 统计页面pv
         */
        page_pv_stat_action : function(opt)
        {
            var opt = opt || {};

            var tj_point = opt.tj_point || 'page';

            var url_hash = window.location.hash;
            url_hash = url_hash.replace("#","");

            var tj_query = 'hash_params='+url_hash;

            tj_query = encodeURIComponent(tj_query);

            if(tj_point == 'page')
            {
                var stat_url = 'http://imgtj.yueus.com/pocotj_touch.css?url=touch://yueyue/app/'+tj_point+'?'+tj_query+"&t="+(new Date().getTime());
            }
            else
            {
                var stat_url = 'http://imgtj.yueus.com/pocotj_touch.css?url=touch://yueyue/app/'+tj_point+'?'+"t="+(new Date().getTime());
            }


            var stat_img = new Image();

            stat_img.src = stat_url;
        },
        /**
         * 错误日志
         * @param lv
         * @param url
         * @param err_str
         */
        err_log : function(lv,url,err_str)
        {
            var self = this;

            var lv = lv || 1;
            var url = encodeURIComponent(url) || encodeURIComponent(window.location.href);
            var err_str = encodeURIComponent(err_str) || '';

            var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?from_str=app&err_level='+lv+'&url='+url;

            var img = new Image();

            img.src = err_log_src+'&err_str='+err_str;
        },
        /**
         * 获取url参数
         * @param param
         * @returns {*}
         */
        get_url_params : function(param)
        {
            var query = location.search.substring(1).split('&');
            var obj = {};
            for(var i=0;i<query.length;i++){
                var kv = query[i].split('=');
                if(!param)
                {
                    obj[kv[0]] = kv[1];
                }
                if(kv[0] == param){
                    return kv[1];
                }
            }
            return obj;
        },
        /**
         * 刷新页面
         */
        refresh_page : function()
        {
            window.location.reload();
        },
        // 获取当前窗口url中param参数的值
        get_param : function(param){
            var query = location.search.substring(1).split('&');
            for(var i=0;i<query.length;i++){
                var kv = query[i].split('=');
                if(kv[0] == param){
                    return kv[1];
                }
            }
            return null;
        },

        user : {},
        login_id : login_id || 0,
        location_id : "0"


    };

    var onlineChangeEvent = [];
    $win.on('online offline', function(event)
    {
        for (var i = 0, len = onlineChangeEvent.length; i < len; i++)
        {
            onlineChangeEvent[i].call(this, event);
        }
    });



    /**
     * 切换图片size
     * @param img_url
     * @param size
     * @returns {*}
     */
    function change_img_resize(img_url,size)
	{
		var size_str = '';

		size = size || '';

		if($.inArray(size, [120,165,640,600,145,440,230,260]) != -1)
		{
			size_str = '_' +size;
		}
		else
		{
			size_str = '';
		}
		// 解析img_url

		var url_reg = /^http:\/\/(img|image)\d*(-c|-wap|-d)?\.poco\.cn.*\.jpg|gif|png|bmp/i;

		var reg = /_165.jpg|_640.jpg|_120.jpg|_600.jpg|_145.jpg|_260.jpg|_440.jpg/i;

		if (url_reg.test(img_url))
		{
			if(reg.test(img_url))
			{
				img_url = img_url.replace(reg,size_str+'.jpg');
				
			}
			else
			{
				img_url = img_url.replace('/(\.\d*).jpg|.jpg|.gif|.png|.bmp/i', size_str+".jpg");//两个.jpg为兼容软件的上传图片名

			}
		}


		
		return img_url;
	}


    function get_arr_by_start_and_end(start,end)
    {
        var arr = [];

        for(start;start<=end;start++)
        {
            arr.push(start);
        }

        return arr;
    }

    module.exports = utility;
});