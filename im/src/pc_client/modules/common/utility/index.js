/**
 * 工具集
 * hdw  2014.8.13
 */



var $ = require('zepto');
var $win = $(window);
var storage = window.localStorage;
var cookie = require('../cookie/index');
var ua = require('../ua/index');
var yue_ui = require('../../yue_ui/frozen');

// 超时时间
var ajaxSettings =
{
    timeout: 10000
};

$.extend($.ajaxSettings, ajaxSettings);



var login_id = parseInt(cookie.get('yue_member_id'));

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

/**
 * 定义App 触发 js事件
 * @param key 函数名
 * @param params 参数 {}
 * @private
 */
window.$_AppCallJSObj  = $({});

window._AppCallJSFunc = function(key,params)
{
    var self = this;

    $_AppCallJSObj.trigger(key,params);
};

// App call js接口
$_AppCallJSObj.on('set_location_cookie',function(event,data)
{
    cookie.set('yue_location_id',data.location_id);
});

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
			try
			{
				if(!storage || typeof storage == 'undefined')
				{
					return false;
				}

				if (typeof val == 'undefined')
				{
					return utility.storage.remove(key);
				}

				storage.setItem(utility.storage.prefix + key, JSON.stringify(val));

				return val;
			}
			catch(err)
			{
				console.warn(err);

				return false;
			}

			
		},
		/**
		 * 获取
		 * @param key
		 * @returns {*}
		 */
		get: function(key)
		{
			try
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
			}
			catch(err)
			{
				console.warn(err);

				return false;
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
        var async = (options.async == null)?true:false;

		var ajax_obj = $.ajax
		({
			url: url,
			type : type,
			data : data,
			cache: cache,
            async : async,
			dataType : dataType,
			beforeSend: beforeSend,
			success: success,
			error:error,
			complete: complete
		});

        ajax_obj = $.extend(ajax_obj,{xhr_data : data});

        console.log(async)

        return ajax_obj;
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
	 * 刷新页面
	 */
	refresh_page : function()
	{
		window.location.reload();
	},
    /**
     *
     * @param content 内容
     * @param options
     * @returns {*}
     */
    dialog : function(options)
    {
        var options = options || {};
        var dia=$.dialog
        ({
            title: options.title || '',
            content: options.content || '温馨提示内容',
            button:options.buttons ||["取消","确认"]
        });
        dia.on("dialog:action",function(e)
        {
            if(e.index == 1)
            {
                dia.trigger('confirm',e)

            }
            else
            {
                dia.trigger('cancel',e)
            }

        })
        .on("dialog:hide",function(e)
        {
            options.hide && options.hide.call(this,e);
        })
        .on("dialog:show",function(e)
        {
            options.show && options.show.call(this,e);
        });
        return dia;
    },

    dialog_show : function(options) 
    {
        var options = options || {};
        var title = options.title || '';
        var content = options.content || '';
        var template  = __inline('./dialog_show.tmpl');  
        var view = template({
            title: title,
            content : content
        });
        $("body").append(view);
        var ele = $("body").find('[data-role="ui-dialog"]');
        ele.addClass('show');
        ele.find('[data-role="ui-dialog-button"]').on('click', function(event) {
            event.preventDefault();
            $(this).removeClass('show');
            ele.remove();
        });
        return ele;

    },
    /**
     * 请求app数据
     * @param options
     */
    ajax_request_app : function(options)
    {
        var self = this;

        options = options || {};

        var curl_data =
        {
            path : options.path,
            location_id : options.location_id || self.storage.get('location').location_id,
            user_id : options.user_id || cookie.get('yue_member_id')
        };

        options.url  = options.url || window.$__ajax_common_url;
        options.data = $.extend(curl_data,options.data);
        options.type = options.type || 'POST';

        self.ajax_request(options);
    },
    /**
     * 根据图片地址获取size
     * @param url
     * @returns {{width: string, height: string}}
     */
    get_yue_img_size_from_url : function (url)
    {
        var width  = '';
        var height = '';

        var m  = url.match(/\?(.*)/);

        if (!m) 
        {
            return {
                width : width,
                height : height
            };
        };


        if (m[1])
        {

            var result = m[1];

            var img_size_arr = result.match(/(\d+)x(\d+)_(\d+)/);

            if(img_size_arr[1])
            {
                width = img_size_arr[1];
            }
            if(img_size_arr[2])
            {
                height = img_size_arr[2];
            }

            return {
                width : width,
                height : height
            };


        }

        return {
            width : width,
            height : height
        };
    },



    $_AppCallJSObj : $_AppCallJSObj,
	login_id : login_id || 0,
	location_id : "0"


};




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

	if($.inArray(size, [120,320,165,640,600,145,440,230,260]) != -1)
	{
		size_str = '_' +size;
	}
	else
	{
		size_str = '';
	}
	// 解析img_url

	var url_reg = /^http:\/\/(img|image)\d*(-c|-wap|-d)?(.poco.cn.*|.yueus.com.*)\.jpg|gif|png|bmp/i;

	var reg = /_(32|64|86|100|145|165|260|320|440|468|640).(jpg|png|jpeg|gif|bmp)/i;

	if (url_reg.test(img_url))
	{
		if(reg.test(img_url))
		{
			img_url = img_url.replace(reg,size_str+'.$2');
			
		}
		else
		{
			img_url = img_url.replace('/(\.\d*).jpg|.jpg|.gif|.png|.bmp/i', size_str+".jpg");//两个.jpg为兼容软件的上传图片名

		}
	}


	
	return img_url;
}

var App = require('../I_APP/I_APP');

if(App.isPaiApp)
{
    App.closeloading();
}

// 设置默认地区
var default_location =
{
    location_id : 101029001,
    location_name : '广州'
};
// 缓存地区
var cache_location = utility.storage.get('location');

if(!cache_location)
{
    utility.storage.set('location',default_location);
}



window.__Util_Tools = utility;

return utility;