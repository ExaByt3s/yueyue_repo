define("common/ua/index",function(){var i={},e=window,s=e.navigator,a=s.appVersion;if(i.isMobile=/(iphone|ipod|android|ios|ipad|nokia|blackberry|tablet|symbian)/.test(s.userAgent.toLowerCase()),i.isAndroid=/android/gi.test(a),i.isIDevice=/iphone|ipad/gi.test(a),i.isTouchPad=/hp-tablet/gi.test(a),i.isIpad=/ipad/gi.test(a),i.otherPhone=!(i.isAndroid||i.isIDevice),i.is_uc=/uc/gi.test(a),i.is_chrome=/CriOS/gi.test(a)||/Chrome/gi.test(a),i.is_qq=/QQBrowser/gi.test(a),i.is_real_safari=/safari/gi.test(a)&&!i.is_chrome&&!i.is_qq,i.is_standalone=window.navigator.standalone?!0:!1,i.window_width=window.innerWidth,i.window_height=window.innerHeight,i.isAndroid){var n=parseFloat(a.slice(a.indexOf("Android")+8));i.android_version=n}else if(i.isIDevice){var o=a.match(/OS (\d+)_(\d+)_?(\d+)?/),t=o[1];o[2]&&(t+="."+o[2]),o[3]&&(t+="."+o[3]),i.ios_version=t}return i.is_iphone_safari_no_fullscreen=i.isIDevice&&i.ios_version<"7"&&!i.isIpad&&i.is_real_safari&&!i.is_standalone,i.is_yue_app=/yue_pai/.test(a),i.is_yueseller_app=/yueseller/.test(a),i.is_weixin=/MicroMessenger/gi.test(a),i.is_normal_wap=!i.is_yue_app&&!i.is_weixin,i});