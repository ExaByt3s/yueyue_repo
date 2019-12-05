// �������
function escapeRegExp(str){
	return str.replace(/([-.*+?^${}()|[\]\/\\])/g, '\\$1');
}
/**
 * Cookieд��
 * ������ֵ������ʱ�䣨��λСʱ����������·��
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
 * Cookie��ȡ
 * ����
 */
function readCookie(key)
{
	var value = document.cookie.match('(?:^|;)\\s*' + escapeRegExp(key) + '=([^;]*)');
	return (value) ? decodeURIComponent(value[1]) : null;
}

/**
 * Cookieע��
 */
function delCookie(key,domain, path)
{
	writeCookie(key, '', -1, domain, path);
}