// 正则过滤
function escapeRegExp(str){
	return str.replace(/([-.*+?^${}()|[\]\/\\])/g, '\\$1');
}
/**
 * Cookie写入
 * 键名，值，持续时间（单位小时），域名，路径
 */
function writeCookie(key, value, duration, domain, path)
{
	value = encodeURIComponent(value);
	if (domain) value += '; domain=' + domain;
	if (path) value += '; path=' + path;
	if(duration)
	{
		var date = new Date();
		date.setTime(date.getTime() + duration * 60 * 60 * 1000);
		value += '; expires=' + date.toGMTString();
	}
	document.cookie = key + "=" + value;
}

/**
 * Cookie读取
 * 键名
 */
function readCookie(key)
{
	var value = document.cookie.match('(?:^|;)\\s*' + escapeRegExp(key) + '=([^;]*)');
	return (value) ? decodeURIComponent(value[1]) : null;
}

/**
 * Cookie注销
 */
function delCookie(key,domain, path)
{
	writeCookie(key, '', -1, domain, path);
}