//seaJs设置
seajs.config
({
	map: [
		[/^.*$/, function(url) 
			{
				if (typeof(seajs_no_cache)!="undefined" && seajs_no_cache && url.indexOf(noCachePrefix) === -1) 
				{
					url += (url.indexOf('?') === -1 ? '?' : '&') + noCacheTimeStamp
				}

				return url
			}
		],
		[ '.js', function(url,aaa,ssss)
			{
				if(typeof(seajs_uglify)!="undefined" && seajs_uglify)
				{
					return '.min.js'
				}
				else
				{
					return url
				}
			}
		]
	  ],
	paths :
	{
		base : 'mobile_wo/base',
		frame : 'mobile_wo/frame',
		wo : 'mobile_wo/wo',
		m_poco_wo : 'http://m.poco.cn/mobile/js/wo/'
	},
	alias: 
	{	
		//app文件
		'web_wo_init' : 'm_poco_wo/web_wo_init',
		'wo_config' : 'wo/wo_config',
		//'slider' : 'wo/module/slider',
		'commom_function' : 'wo/module/common_function',
		'load_more_btn' : 'wo/module/load_more_btn',
		'refresh_btn' : 'wo/module/refresh_btn',
		'user_list_view' : 'wo/module/user_list_view',
		'user_list_controler' : 'wo/module/user_list_controler',
		'photo_txt_view' : 'wo/module/photo_txt_view',
		'photowall_item_view' : 'wo/module/photowall_item_view',
		'photowall_controler' : 'wo/module/photowall_controler',
		'mobile_photo_collection' : 'wo/module/mobile_photo_collection',
		'user_list_collection' : 'wo/module/user_list_collection',
		'footer_view' : 'wo/module/footer_view',
		'follow_btn' : 'wo/module/follow_btn',
        'new_alert_v2' : 'wo/module/new_alert_v2',
        'page_back_btn' : 'wo/module/page_back_btn',
        
		'index' : 'wo/index',
		'theme' : 'wo/theme',
		'friend' : 'wo/friend',
		'my' : 'wo/my',
		'last' : 'wo/last',
		'theme_pic_list' : 'wo/theme_pic_list',
		'user_profile' : 'wo/user_profile',
		'follow' : 'wo/follow',
		'fans' : 'wo/fans',
		'cmt_notice' : 'wo/cmt_notice',
		'cmt' : 'wo/cmt',
		'login_and_reg' : 'wo/login_and_reg',
		'like_photo_list' : 'wo/like_photo_list',
		'like_notice' : 'wo/like_notice',
		'get_template' : 'wo/get_template',
		'login' : 'wo/login',
        'edit' : 'wo/edit',
        'edit_page' : 'wo/edit_page',
        'recommend' : 'wo/recommend',
        'invite_fans' : 'wo/invite_fans',
		'publish' : 'wo/publish',
        'new_tips' : 'wo/module/new_tips',
		'notice' : 'wo/module/notice',
		'user_authorize' : 'wo/module/user_authorize',
        'news' : 'wo/news',
        //'placard' : 'wo/placard',
        'setup' : 'wo/setup',
        //'complete_publish' : 'wo/complete_publish',
        
        'doorplate_list' : 'wo/doorplate_list',
        'doorplate_last' : 'wo/doorplate_last',
        'search_result' : 'wo/search_result',
        
        //测试页面
        'hdw_test' : 'wo/hdw_test',   
        
        
        
		//css文件
		'page_control_css' : 'frame/page_control.css',
		'view_scroll_css' : 'frame/view_scroll.css',

		'backbone': 'base/backbone',
		'underscore' : 'base/underscore',
		'jquery' : 'base/jquery',
		'zepto' : 'base/zepto',
		'jquery_form' : 'base/jquery_form',
		'mustache' : 'base/mustache',
		'iScroll' : 'base/iscroll',
		'iScroll5' : 'base/iscroll5',
		'hammer' : 'base/hammer.jq',
		'page_control' : 'frame/page_control',
		'page' : 'frame/page',
		'scroll' : 'frame/view_scroll',
		'ua' : 'base/ua',
		'cookies' : 'base/cookies',
		'base_package' : 'base/base_package',
		'megapix_img' : 'base/megapix_img',
		'img_process' : 'base/img_process',
		'exif' : 'base/exif'
    }
})