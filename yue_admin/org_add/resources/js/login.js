	function sub()
	{
		var $nick_name = $("#nick_name").val();
		var $pwd_hash  = $("#pwd_hash").val();
		//var $cellphone = $("#cellphone").val();
		var regPartton=/^(1[3-9])\d{9}$/;
		//var $pregCell = /^1\d{10}$/;
		if ($nick_name == '') 
		{
			window.alert('����������Ϊ��!');
			return false;
		}
		if ($pwd_hash == '') 
		{
			window.alert('���벻��Ϊ��');
			return false;
		}
		if ($pwd_hash.length < 6) 
		{
			window.alert('���볤�Ȳ�С����λ');
			return false;
		};
		/*if ($cellphone == '') 
		{
			window.alert('�����绰����Ϊ��!');
			return false;
		}*/
		//�ύ
		$.layer
   		({
         shade: [0],
         area: ['auto','auto'],
         dialog: 
         {
            msg: '��������Ϊ'+$pwd_hash,
            btns: 2,                    
            type: 4,
            btn: ['ȷ��','ȡ��'],
            yes: function()
           {
             //layer.msg('��Ҫ', 1, 1);
             $("#myform").submit();
           }, 
           no: function()
           {
             layer.msg('ȡ���ɹ�', 1, 1);
           }
          }
       });
		/*if (!regPartton.test($cellphone)) 
		{
			window.alert('�����绰��ʽ����!');
			return false;
		}*/
		//var $address   = $("#address").val();
	}