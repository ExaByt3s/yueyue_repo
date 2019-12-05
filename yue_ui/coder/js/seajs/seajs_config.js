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
	  path : {
	  	base : '../js/seajs/'
	  },
  
	  // 别名配置
	  alias: {
	    'jquery' : 'base/jquery',
	    'zepto' : 'base/zepto',
	    'app' : 'app'
	  },



});