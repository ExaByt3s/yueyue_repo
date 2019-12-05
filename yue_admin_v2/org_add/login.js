	function sub()
	{
		var $nick_name = $("#nick_name").val();
		var $pwd_hash  = $("#pwd_hash").val();
		//var $cellphone = $("#cellphone").val();
		var regPartton=/^(1[3-9])\d{9}$/;
		//var $pregCell = /^1\d{10}$/;
		if ($nick_name == '') 
		{
			window.alert('机构名不能为空!');
			return false;
		}
		if ($pwd_hash == '') 
		{
			window.alert('密码不能为空');
			return false;
		}
		if ($pwd_hash.length < 6) 
		{
			window.alert('密码长度不小于六位');
			return false;
		};
		/*if ($cellphone == '') 
		{
			window.alert('机构电话不能为空!');
			return false;
		}*/
		//提交
		$.layer
   		({
         shade: [0],
         area: ['auto','auto'],
         dialog: 
         {
            msg: '您的密码为'+$pwd_hash,
            btns: 2,                    
            type: 4,
            btn: ['确定','取消'],
            yes: function()
           {
             //layer.msg('重要', 1, 1);
             $("#myform").submit();
           }, 
           no: function()
           {
             layer.msg('取消成功', 1, 1);
           }
          }
       });
		/*if (!regPartton.test($cellphone)) 
		{
			window.alert('机构电话格式有误!');
			return false;
		}*/
		//var $address   = $("#address").val();
	}