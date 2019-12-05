/*
* 模特js
*/
function subphone()
{
	$phone = $("#phone").val();
	//var regPartton=/^(13[0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$/;
	var regPartton=/^(1[3-9])\d{9}$/;
	if (!$phone || $phone == null) 
	{
		window.alert('手机号码不能为空!');
		return false;
	}
	else if (!regPartton.test($phone)) 
	{
		window.alert("手机号码格式不正确!");
		return false;
	}
	else
	{
		return true;
	}
}

/*
 * 查看头像
*/
function check_pic($url)
{
	$.layer({
            type: 2,
            title: '查看头像',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['200' , '270'],
            offset : ['200px', ''],
            iframe: {src: $url}
        }); 
}
/*
 * 添加跟进信息
*/
function add_follow_data()
{
	$.layer({
            type: 2,
            title: '添加跟进信息',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '300'],
            offset : ['200px', ''],
            iframe: {src: 'add_follow.html'}
        }); 
}

//添加风格
function add_style($url)
{
	$.layer({
            type: 2,
            title: '添加拍摄风格',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '460'],
            offset : ['100px', ''],
            iframe: {src: $url}
        });
}

//添加样片链接
function add_pUrl($url)
{
	$.layer({
		type  : 2,
		title : '添加样片链接',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}

//添加标签
function add_label($url)
{
	$.layer({
		type  : 2,
		title : '添加标签',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}

//活动入围
function add_activEnter($url)
{
	$.layer({
		type  : 2,
		title : '添加活动入围',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}

//活动报名
function add_activJoin($url)
{
	$.layer({
		type  : 2,
		title : '添加活动报名',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}


//查看app相册
function find_app_pic($url)
{
	$.layer({
		type  : 2,
		title : '查看app相册',
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
*  模特添加
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
		window.alert('用户名不能为空!');
		$("#name").focus();
		return false;
	}
	if (!nick_name || nick_name == null) 
	{
		window.alert('昵称不能为空!');
		$("#nick_name").focus();
		return false;
	}
	if (!weixin_name || weixin_name == null) 
	{
		window.alert("微信名称不能为空!");
		$("#weixin_name").focus();
		return false;
	}
	if (!discuz_name || discuz_name == null) 
	{
		window.alert("论坛名不能为空!");
		$("#discuz_name").focus();
		return false;
	}
	if (!poco_name || poco_name == null) 
	{
		window.alert("POCO名称不能为空!");
		$("#poco_name").focus();
		return false;
	}
	if (!app_name || app_name == null) 
	{
		window.alert("APP昵称不能为空!");
		$("#app_name").focus();
		return false;
	};
	if (!phone || phone == null) 
	{
		window.alert("手机号码不能为空");
		$("#phone").focus();
		return false;
	}
	if (!weixin_id || weixin_id == null) 
	{
		window.alert("微信不能为空!");
		$("#weixin_id").focus();
		return false;
	}
	if (!qq || qq == null) 
	{
		window.alert("QQ不能为空!");
		$("#qq").focus();
		return false;
	}
	if (!email || email == null) 
	{
		window.alert("邮箱不能为空!");
		$("#email").focus();
		return false;
	}
	if (!poco_id || poco_id == null) 
	{
		window.alert("POCOID不能为空!");
		$("#poco_id").focus();
		return false;
	}
	if (!appearance_score || appearance_score == null) 
	{
		window.alert("样貌值不能为空!");
		$("#appearance_score").focus();
		return false;
	};
	if (!p_url || p_url == null) 
	{
		window.alert("样片链接不能为空!");
		$("#p_url").focus();
		return false;
	}
	if (!p_school || p_school == null) 
	{
		window.alert("学校名称不能为空!");
		$("#p_school").focus();
		return false;
	}
	if (!p_specialty || p_specialty == null) 
	{
		window.alert("专业名称不能为空!");
		$("#p_specialty").focus();
		return false;
	}
	if (!p_enter_school_time || p_enter_school_time == null) 
	{
		window.alert("入学年份不能为空!");
		$("#p_enter_school_time").focus();
		return false;
	}
	if (!age || age == null) 
	{
		window.alert("年龄不能为空!");
		$("#age").focus();
		return false;
	}

	if (!height || height == null)
	{
		window.alert("身高不能为空!");
		$("#height").focus();
		return false;
	}
	if (!weight || weight == null) 
	{
		window.alert("体重不能为空!");
		$("#weight").focus();
		return false;
	}
	if (!cup || cup == null) 
	{
		window.alert("罩杯或者胸肌不能为空!");
		$("#cup").focus();
		return false;
	}
	if (!bwh || bwh == null) 
	{
		window.alert("三围不能为空!");
		$("#bwh").focus();
		return false;
	}
	if (!shoe_size || shoe_size == null) 
	{
		window.alert("鞋码不能为空!");
		$("#shoe_size").focus();
		return false;
	};


}*/



/*导出数据提交*/
function sub_import($url)
{
	$("#list_form").attr("action", $url).submit();
}

/*
*列出更多的跟进数据
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


//删除相册
function delModelpic($url)
{
	$("#list_form").attr("action", $url).submit();
}