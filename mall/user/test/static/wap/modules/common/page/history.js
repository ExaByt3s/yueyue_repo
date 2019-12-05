define('common/page/history', function(require, exports, module){ /**
 * @Author      : hudw <hudw@poco.cn>
 * @Date        : 2015-11-05
 * @Description : 无刷新修改地址
 */
var win = window;
var history = win.history;
var location = win.location;

var history_state = 
{
	push_state : function(state,title,url)
	{
		history.pushState(state,title,url);
	},
	replace_state : function(state,title,url)
	{
		history.replaceState(state,title,url);
	}
};

return history_state;

 
});