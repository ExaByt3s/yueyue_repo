/**
 * 公共顶栏
 */
define(function(require, exports, module) 
{
    var $ = require('jquery');
    var Handlebars = require('handlebars');
    var Cookie = require('matcha/cookie/1.0.0/cookie');

    var account_tpl = require('./tpl/account.handlebars');
        

    var action_url = 'http://www.yueus.com/action/get_user_info.php';
    // 初始化
    exports.init = function(options) 
    {
        var options = options || {};        

        var $container = options.container || {};
		var custom_logout = options.custom_logout || '';

		var login_id = Cookie.get('yue_member_id') || 0;


		if(parseInt(Cookie.get('yue_member_id')))
		{
			$.ajax
			({
				dataType : 'JSON',
				url : action_url+"?"+Cookie.get('yue_member_id'),
				timeout:10000,
				cache : true,
				beforeSend : function()
				{
					$container.html('<span>加载中...</span>');                    
				},
				success : function(res)
				{
					var response = res.result_data;

					var account_html_str = '';                    

					if(response.data)                    
					{
						account_html_str = account_tpl(response.data);
					}                

					$container.html(account_html_str);
					
					// 加载成功触发
					$(document).trigger('success:account',response);

					if(typeof options.success == 'function')                        
					{
						options.success.call(this,res);         
					}
				},
				error : function()
				{
					var account_html_str = account_tpl({login:0});

					$container.html(account_html_str);    

					if(typeof options.error == 'function')                        
					{
						options.error.call(this);         
					}                    
				}
			});

		}
		else
		{

			var account_html_str = account_tpl({login:0,custom_html : options.custom_html});

			$container.html(account_html_str);    
					
		}

        
        $(document).on('success:account',function()
        {
            var $logout = $('#logout');

            $logout.on('click',function()
            {
                if(confirm('是否退出登录'))
                {
					if(!custom_logout)
					{
						window.location.href = 'http://www.yueus.com/model/logout.php';
					}
					else
					{
						window.location.href = custom_logout;
					}
                    
                }
            });
        });

        

        return this;
    };

    function eventStop(event) {
        event && event.preventDefault() && event.stopPropagation();

    }
    
});