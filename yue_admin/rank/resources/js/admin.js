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
* 全选 与不选
*/
function select_all( input_name,select_id ){
	
	//全选
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
* 检查已选checkbox的个数
*/
function check_select(input_name){

	var input_list = $("input[name='"+input_name+"']:checked");
	var input_len  = input_list.length;
	return input_len;

}

/*
* tab切换
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
 * 验证手机号码是否正确格式
*/

function checkphone($phone)
{
	var regPartton=/1[3-8]+\d{9}/;
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

function generateMixed(n) {
	var jschars = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    var res = "";
    for(var i = 0; i < n ; i ++) {
        var id = Math.ceil(Math.random()*35);
        res += jschars[id];
    }
    return res;
}

function del($url)
{
	    $.layer
   		({
         shade: [0],
         area: ['auto','auto'],
         dialog: 
         {
            msg: '请再确认一下信息再确定',
            btns: 2,                    
            type: 4,
            btn: ['确定','取消'],
            yes: function()
           {
             //layer.msg('重要', 1, 1);
             //$("#myform").submit();
             location.href = $url;
           }, 
           no: function()
           {
             layer.msg('取消成功', 1, 1);
           }
          }
       });
}

$(function(){
	$("#table-list tbody tr:odd").css("background-color","#eee");
})