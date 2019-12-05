//seaJs
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
		[ '.js', function(url)
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
		],
		[ '.css', function(url)
			{
				if(typeof(seajs_uglify)!="undefined" && seajs_uglify)
				{
					return '.min.css'
				}
				else
				{
					return url
				}
			}
		]
	  ],
    debug : true,
	
	paths: 
	{
		
		app: './app',
		
    },
	alias: 
	{	
		// 基础文件
		'backbone': 'base/backbone',
		'underscore' : 'base/underscore',
		'jquery' : 'base/jquery',
		'zepto' : 'base/zepto',
		'mustache' : 'base/mustache',
		'handlebars' : 'base/handlebars',		
		'iScroll' : 'base/iscroll',
		'hammer' : 'base/hammer.jq',
		'ua' : 'base/ua',
		'cookies' : 'base/cookies',
		'megapix_img' : 'base/megapix_img',
		'img_process' : 'base/img_process',
		'exif' : 'base/exif',
		// 框架文件
		'page' : 'frame/page',
		'page_control' : 'frame/page_control',	

		// 通用类文件
		'View' : 'common/view',
		'template-helpers' : 'common/template-helpers',
		
		// 页面文件
		'hot'  : './hot/index'
		 
    },
	
	//base : 'http://pai.poco.cn/mobile/js/'
	base : './js/'
	
})
