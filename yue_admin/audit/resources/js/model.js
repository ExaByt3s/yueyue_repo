/*
* ģ��js
*/
function subphone()
{
	$phone = $("#phone").val();
	//var regPartton=/^(13[0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$/;
	var regPartton=/^(1[3-9])\d{9}$/;
	if (!$phone || $phone == null) 
	{
		window.alert('�ֻ����벻��Ϊ��!');
		return false;
	}
	else if (!regPartton.test($phone)) 
	{
		window.alert("�ֻ������ʽ����ȷ!");
		return false;
	}
	else
	{
		return true;
	}
}

/*
 * �鿴ͷ��
*/
function check_pic($url)
{
	$.layer({
            type: 2,
            title: '�鿴ͷ��',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['200' , '270'],
            offset : ['200px', ''],
            iframe: {src: $url}
        }); 
}
/*
 * ��Ӹ�����Ϣ
*/
function add_follow_data()
{
	$.layer({
            type: 2,
            title: '��Ӹ�����Ϣ',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '300'],
            offset : ['200px', ''],
            iframe: {src: 'add_follow.html'}
        }); 
}

//��ӷ��
function add_style($url)
{
	$.layer({
            type: 2,
            title: '���������',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '460'],
            offset : ['100px', ''],
            iframe: {src: $url}
        });
}

//�����Ƭ����
function add_pUrl($url)
{
	$.layer({
		type  : 2,
		title : '�����Ƭ����',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}

//��ӱ�ǩ
function add_label($url)
{
	$.layer({
		type  : 2,
		title : '��ӱ�ǩ',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}

//���Χ
function add_activEnter($url)
{
	$.layer({
		type  : 2,
		title : '��ӻ��Χ',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}

//�����
function add_activJoin($url)
{
	$.layer({
		type  : 2,
		title : '��ӻ����',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}


//�鿴app���
function find_app_pic($url)
{
	$.layer({
		type  : 2,
		title : '�鿴app���',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['400' , '400'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}

//

/*
*  ģ�����
*/

/*function add_model()
{
	var name              = $("#name").val();
	var nick_name         = $("#nick_name").val();
	var weixin_name       = $("#weixin_name").val();
	var discuz_name       = $("#discuz_name").val();
	var poco_name         = $("#poco_name").val();
	var app_name          = $("#app_name").val();
	var phone             = $("#phone").val();
	var weixin_id         = $("#weixin_id").val();
	var qq                = $("#qq").val();
	var email             = $("#email").val();
	var poco_id           = $("#poco_id").val();
	//var d21               = $("#d21").val();
	var appearance_score  = $("#appearance_score").val();
	var p_url             = $("#p_url").val();
	var p_school          = $("#p_school").val();
	var expressiveness_score = $("#expressiveness_score").val();
	var p_specialty       = $("#p_specialty").val();
	var p_enter_school_time = $("#p_enter_school_time").val();
	var age              = $("#age").val();
	var height           = $("#height").val();
	var weight           = $("#weight").val();
	var cup              = $("#cup").val();
	var bwh              = $("#bwh").val();
	var shoe_size        = $("#shoe_size").val();
	if (!name || name == null) 
	{
		window.alert('�û�������Ϊ��!');
		$("#name").focus();
		return false;
	}
	if (!nick_name || nick_name == null) 
	{
		window.alert('�ǳƲ���Ϊ��!');
		$("#nick_name").focus();
		return false;
	}
	if (!weixin_name || weixin_name == null) 
	{
		window.alert("΢�����Ʋ���Ϊ��!");
		$("#weixin_name").focus();
		return false;
	}
	if (!discuz_name || discuz_name == null) 
	{
		window.alert("��̳������Ϊ��!");
		$("#discuz_name").focus();
		return false;
	}
	if (!poco_name || poco_name == null) 
	{
		window.alert("POCO���Ʋ���Ϊ��!");
		$("#poco_name").focus();
		return false;
	}
	if (!app_name || app_name == null) 
	{
		window.alert("APP�ǳƲ���Ϊ��!");
		$("#app_name").focus();
		return false;
	};
	if (!phone || phone == null) 
	{
		window.alert("�ֻ����벻��Ϊ��");
		$("#phone").focus();
		return false;
	}
	if (!weixin_id || weixin_id == null) 
	{
		window.alert("΢�Ų���Ϊ��!");
		$("#weixin_id").focus();
		return false;
	}
	if (!qq || qq == null) 
	{
		window.alert("QQ����Ϊ��!");
		$("#qq").focus();
		return false;
	}
	if (!email || email == null) 
	{
		window.alert("���䲻��Ϊ��!");
		$("#email").focus();
		return false;
	}
	if (!poco_id || poco_id == null) 
	{
		window.alert("POCOID����Ϊ��!");
		$("#poco_id").focus();
		return false;
	}
	if (!appearance_score || appearance_score == null) 
	{
		window.alert("��òֵ����Ϊ��!");
		$("#appearance_score").focus();
		return false;
	};
	if (!p_url || p_url == null) 
	{
		window.alert("��Ƭ���Ӳ���Ϊ��!");
		$("#p_url").focus();
		return false;
	}
	if (!p_school || p_school == null) 
	{
		window.alert("ѧУ���Ʋ���Ϊ��!");
		$("#p_school").focus();
		return false;
	}
	if (!p_specialty || p_specialty == null) 
	{
		window.alert("רҵ���Ʋ���Ϊ��!");
		$("#p_specialty").focus();
		return false;
	}
	if (!p_enter_school_time || p_enter_school_time == null) 
	{
		window.alert("��ѧ��ݲ���Ϊ��!");
		$("#p_enter_school_time").focus();
		return false;
	}
	if (!age || age == null) 
	{
		window.alert("���䲻��Ϊ��!");
		$("#age").focus();
		return false;
	}

	if (!height || height == null)
	{
		window.alert("��߲���Ϊ��!");
		$("#height").focus();
		return false;
	}
	if (!weight || weight == null) 
	{
		window.alert("���ز���Ϊ��!");
		$("#weight").focus();
		return false;
	}
	if (!cup || cup == null) 
	{
		window.alert("�ֱ������ؼ�����Ϊ��!");
		$("#cup").focus();
		return false;
	}
	if (!bwh || bwh == null) 
	{
		window.alert("��Χ����Ϊ��!");
		$("#bwh").focus();
		return false;
	}
	if (!shoe_size || shoe_size == null) 
	{
		window.alert("Ь�벻��Ϊ��!");
		$("#shoe_size").focus();
		return false;
	};


}*/



/*���������ύ*/
function sub_import($url)
{
	$("#list_form").attr("action", $url).submit();
}

/*
*�г�����ĸ�������
*/
function list_follow_data($url)
{
	//window.alert($url);
	$.ajax
	({
		type : "POST",
        url  : $url,
        dataType: "json",
        success:function(data)
        {
        	/*window.alert(data.msg);
        	window.alert(data.list);*/
        	var $list = data.list;
        	var $str = "";
        	//window.alert($list.length);
        	for (var i = 0; i < $list.length; i ++) 
        	{
        		$str += "<tr><td align='center'>"+$list[i]['follow_time']+"</td><td align='center'>"+$list[i]['follow_name']+"</td><td align='center'>"+$list[i]['result']+"</td><td align='center'>"+$list[i]['problem_type']+"</td><td align='center'>"+$list[i]['problem_content']+"</td></tr>";
        	}
        	$("#stand_follow_data").append($str);
        	$("#follow_more").css({'display': 'none'});
        },
        error:function(data)
        {

        }
	});
}


//ɾ�����
function delModelpic($url)
{
	$("#list_form").attr("action", $url).submit();
}