'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require ./header.scss
**/

var $ = require('jquery');
var cookie = require('../../cookie/index');

module.exports = 
{
    render: function (dom,user_role) {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择			
		
		var self = this;

		if(cookie.get('yue_member_id')>0)
		{
			self.get_user_info
			({
				beforeSend : function()
				{
					dom.innerHTML = '<a class="rbox frdi header-ajax-text" href="javascript:void();">加载中...</a>';
				},
				callback : function(res)
				{
					var template  = __inline('./header.tmpl');	

					dom.innerHTML = '';

					dom.innerHTML = template(
						{
							user_role : user_role ,
							user : res.data.user_info ,
							login : 1
						}, 
						{
							helpers : 
							{
								if_equal:is_equal
							}
						}
					);			
				},
				error : function()
				{
					dom.innerHTML = '<a class="rbox frdi" id="header-fail-load-btn" href="javascript:void();">网络异常</a>';
				}
			});
		}		
		

		
    },
	get_user_info : function(options)
	{
		$.ajax
		({
			url : 'http://www.yueus.com/task/module_common/get_login_info.header.json.php?callback=?',
			type : 'GET',
			data : 
			{
				user_id : cookie.get('yue_member_id')
			},
			dataType : 'JSONP',
			cache : true,
			beforeSend : function()
			{
				options.beforeSend && options.beforeSend.call(this);
			},
			success : function(res)
			{

				options.callback && options.callback.call(this,res);
					
			},
			error : function()
			{
				options.error && options.error.call(this);
			}
		});			
	}
};

/*
  判断是否相等的模板函数
*/
function is_equal(a,b,options)
{
	if(a == b)
	{
		return options.fn(this);
	}
	else
	{
		return options.inverse(this);
	}
}