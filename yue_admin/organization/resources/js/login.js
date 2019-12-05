function sub()
{
	var $pwd_hash   = $("#pwd_hash").val();
	var $new_pwd    = $("#new_pwd").val();
	var $cofirm_pwd = $("#cofirm_pwd").val();
	if ($pwd_hash == '') 
	{
		window.alert('旧密码不能为空');
		return false;
	}
	if ($new_pwd == '') 
	{
		window.alert('新密码不能为空');
		return false;
	}
	if ($new_pwd.length < 6) 
	{
		window.alert('新密码长度不小于六位');
		return false;
	}
	if ($confirm_pwd != $new_pwd) 
	{
		window.alert('两次密码不相同');
		return false;
	};
}