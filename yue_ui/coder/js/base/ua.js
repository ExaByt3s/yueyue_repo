define('base/ua',['base_package'],function(require, exports)
{
	var $ = require('zepto')

	var ua = {};
	var win = window;
	var nav = win.navigator;
	var app_version = nav.appVersion
	
	
	//�ֻ�ϵͳ
	ua.isAndroid = (/android/gi).test(app_version)
	ua.isIDevice = (/iphone|ipad/gi).test(app_version)
	ua.isTouchPad = (/hp-tablet/gi).test(app_version)
	ua.isIpad = (/ipad/gi).test(app_version)

	ua.otherPhone = !(ua.isAndroid || ua.isIDevice)
	

	//�����Ʒ������
	ua.is_uc = (/uc/gi).test(app_version)
	ua.is_chrome = (/CriOS/gi).test(app_version)
	ua.is_qq = (/QQBrowser/gi).test(app_version)
	ua.is_real_safari = (/safari/gi).test(app_version) && !ua.is_chrome && !ua.is_qq			//������ԭ��IOS safari�����
	

	//iphone safari�Ƿ�ȫ��
	ua.is_standalone = (window.navigator.standalone)? true : false
	ua.is_iphone_safari_no_fullscreen = ua.isIDevice && !ua.isIpad && ua.is_real_safari && !ua.is_standalone
	

	ua.window_width = window.innerWidth
	ua.window_height = window.innerHeight

	//ua.window_height = $(window).height()
	
	//�ֻ��汾
	if(ua.isAndroid)
	{
		var android_version = parseFloat(app_version.slice(app_version.indexOf("Android")+8)); 
		ua.android_version = android_version
	}
	else if(ua.isIDevice)
	{
		var v = (app_version).match(/OS (\d+)_(\d+)_?(\d+)?/);
		
		var ios_version = v[1]
		
		if(v[2]) ios_version += '.'+v[2]
		if(v[3]) ios_version += '.'+v[3]

		ua.ios_version = ios_version
	}

	
	
	return ua;
})