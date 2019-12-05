define('common/utility/index', function(require, exports, module){ /**
 * ���߼�
 * hdw  2014.8.13
 */


var $ = require('components/jquery/jquery.js');
var $win = $(window);
var storage = window.localStorage;
var cookie = require('common/cookie/index');
var ua = require('common/ua/index');

var login_id = parseInt(cookie.get('yue_member_id'));

var window_location = window.location;

// ����origin����������
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
	 * ת��Ϊ����
	 * @param s
	 * @returns {Number}
	 */
	int: function(s)
	{
		return parseInt(s, 10) || 0;
	},
	/**
	 * ת��Ϊ������
	 * @param s
	 * @returns {Number}
	 */
	float : function(s)
	{
		return parseFloat(s);
	},
	/**
	 * ����ָ��С��
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
	 * ��ȡhash
	 * @returns {string}
	 */
	getHash: function()
	{
		return window.location.hash.substr(1);
	},
	/**
	 * ��ȡ���ſ�
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
	 * ���ش洢��
	 */
	storage :
	{
		/**
		 * ǰ׺
		 */
		prefix: 'poco-yuepai-app-',
		/**
		 * ����
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

			storage.setItem(utility.storage.prefix + key, JSON.stringify(val));

			return val;
		},
		/**
		 * ��ȡ
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
		 * ɾ��
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
				//����
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

        console.log(async)

		$.ajax
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
	},
	auth :
	{
		is_login : function()
		{
			return utility.login_id>0;
		}
	},
	//ͼƬת��size
	matching_img_size : function(img_url,size)
	{

		var sort_size = size;

		return change_img_resize(img_url,sort_size);
	},               
	get_url_params : function (url, paramName )
	{
		var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
		//var oMatch = oRegex.exec( window.top.location.search ) ; //��ȡ��ǰ���ڵ�URL
		var oMatch = oRegex.exec( url ) ;
		if ( oMatch && oMatch.length > 1 )
		{
			return oMatch[1];   //����ֵ
		}
		else
		{
			return '' ;
		}

	},
	/**
	 * ͳ��ҳ��pv
	 */
	page_pv_stat_action : function(opt)
	{
		var opt = opt || {};
		
	},
	/**
	 * ˢ��ҳ��
	 */
	refresh_page : function()
	{
		window.location.reload();
	},
	fix_placeholder : function()
	{
		 $.support.placeholder = ('placeholder' in document.createElement('input'));

		 //fix for IE7 and IE8
		 $(function () {
			 if (!$.support.placeholder) {
				 $("[placeholder]").focus(function () {
					 if ($(this).val() == $(this).attr("placeholder")) $(this).val("");
				 }).blur(function () {
					 if ($(this).val() == "") $(this).val($(this).attr("placeholder"));
				 }).blur();

				 $("[placeholder]").parents("form").submit(function () {
					 $(this).find('[placeholder]').each(function() {
						 if ($(this).val() == $(this).attr("placeholder")) {
							 $(this).val("");
						 }
					 });
				 });
			 }
		 });
	},
    count_editor_str_num : function(editor_str)
    {
        //����༭����Ĵ��ַ�����

        editor_match_str = editor_str.replace(/<\/?[^>]*>/g,''); //ȥ��HTML tag
        editor_match_str = editor_match_str.replace(/[ | ]* /g,' '); //ȥ����β�հ�
        //str = str.replace(/ [\s| | ]* /g,' '); //ȥ���������
        editor_match_str=editor_match_str.replace(/ /ig,'');//ȥ��
        if(editor_match_str)
        {
            return editor_match_str.length;
        }
        else
        {
            return 0;
        }


    },
    count_editor_img_num : function(editor_str)
    {
        //����༭�����ͼƬ����
        var rgExp = /<img [^>]*src=['"]([^'"]+)[^>]*>/gi;
        var editor_str_arr = editor_str.match(rgExp);
        if(editor_str_arr)
        {
            return editor_str_arr.length;
        }
        else
        {
            return 0;
        }

    },


	login_id : login_id || 0,
	location_id : "0"


};




/**
 * �л�ͼƬsize
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
    // ����img_url

    var url_reg = /^http:\/\/(img|image)\d*(-c|-wap|-d)?(.poco.cn.*|.yueus.com.*)\.jpg|jpeg|gif|png|bmp/i;

    var reg = /_(32|64|86|100|145|165|260|320|440|468|640).(jpg|png|jpeg|gif|bmp)/i;

    if (url_reg.test(img_url))
    {
        if(reg.test(img_url))
        {
            img_url = img_url.replace(reg,size_str+'.$2');

        }
        else
        {
            img_url = img_url.replace('/(\.\d*).jpg|.jpg|.gif|.png|.bmp/i', size_str+".jpg");//����.jpgΪ����������ϴ�ͼƬ��

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








return utility; 
});