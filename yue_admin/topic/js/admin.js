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
* ȫѡ
*/
/*
* ȫѡ �벻ѡ
*/
function select_all( input_name,select_id ){
	//ȫѡ
	/*var setval = $("#"+select_id).attr("checked"); 
	$("input[name='"+input_name+"']").attr("checked",setval);*/
	var setval = $("#"+select_id).attr("checked");
	if (setval == 'checked') 
	{
		$("input[name='"+input_name+"']").attr("checked",setval);
	}
	else
	{
		$("input[name='"+input_name+"']").removeAttr("checked");
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