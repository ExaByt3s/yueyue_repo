function sub()
{
	var $pwd_hash   = $("#pwd_hash").val();
	var $new_pwd    = $("#new_pwd").val();
	var $cofirm_pwd = $("#cofirm_pwd").val();
	if ($pwd_hash == '') 
	{
		window.alert('�����벻��Ϊ��');
		return false;
	}
	if ($new_pwd == '') 
	{
		window.alert('�����벻��Ϊ��');
		return false;
	}
	if ($new_pwd.length < 6) 
	{
		window.alert('�����볤�Ȳ�С����λ');
		return false;
	}
	if ($confirm_pwd != $new_pwd) 
	{
		window.alert('�������벻��ͬ');
		return false;
	};
}