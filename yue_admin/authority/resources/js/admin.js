/*
* hide left 
*/
function toggle_leftbar(doit){
	if(doit==1){
		$('#Main_drop a.on').hide();
		$('#Main_drop a.off').show();
		$('#MainBox .main_box').css('margin-left','24px');
		$('#leftMenu').hide();
	}else{
		$('#Main_drop a.off').hide();
		$('#Main_drop a.on').show();
		$('#leftMenu').show();
		$('#MainBox .main_box').css('margin-left','224px');
	}
}
/*
* ȫѡ �벻ѡ
*/
function select_all( input_name,select_id ){
	
	//ȫѡ
	/*var setval = $("#"+select_id).attr("checked"); 
	$("input[name='"+input_name+"']").attr("checked",setval);*/
	var setval = $("#"+select_id).attr("checked");
	//window.alert(setval);
	if (setval == 'checked' || setval == true) 
	{
		$("input[name='"+input_name+"']").attr("checked",setval);
	}
	else
	{
		$("input[name='"+input_name+"']").removeAttr("checked");
	}

}

/*
* ȫѡ �벻ѡ
*/
function select_all_by_class( class_name,select_id ){
	
	//ȫѡ
	/*var setval = $("#"+select_id).attr("checked"); 
	$("input[name='"+input_name+"']").attr("checked",setval);*/
	var setval = $("#"+select_id).attr("checked");
	//window.alert(setval);
	if (setval == 'checked' || setval == true) 
	{
		$("."+class_name).attr("checked",setval);
	}
	else
	{
		$("."+class_name).removeAttr("checked");
	}

}


/*
* �����ѡcheckbox�ĸ���
*/
function check_select(input_name){

	var input_list = $("input[name='"+input_name+"']:checked");
	var input_len  = input_list.length;
	return input_len;

}

/*
* tab�л�
*/

function switch_tab( menu_contain,menu_pre,list_pre,cls,count ){

	for (i = 1;i <= count;i++ )
	{
		$("#"+menu_pre+i).click(function(){
					
			for (j=1;j<=count;j++ ){
				
				$("#"+menu_pre+j).removeClass(cls);
				$("#"+list_pre+j).hide();
			
			}
			var index = $("#"+menu_contain+" span").index(this)+1;
			$("#"+menu_pre+index).addClass(cls);
			$("#"+list_pre+index).show(100);
		
		});

	}

}


/*
 * ��֤�ֻ������Ƿ���ȷ��ʽ
*/

function checkphone($phone)
{
	var regPartton=/1[3-8]+\d{9}/;
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