/*
* ��Ӱʦ���ֵ�js
*/

//��Ӹ�����Ϣ
function add_cameraman_follow()
{
	$.layer({
            type: 2,
            title: '��Ӹ�����Ϣ',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '320'],
            offset : ['200px', ''],
            iframe: {src: 'cameraman_add_follow.php'}
        }); 
}

//����޸�
function add_style($url)
{
   $.layer({
            type: 2,
            title: '�޸ķ����Ϣ',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['220' , '300'],
            offset : ['200px', ''],
            iframe: {src: $url}
        });  
}

//�ж��Ƿ���ڹ�����
$(function(){
    $(".selStudio").change(function(){
        var $studio_value = $(this).val();
        if ($studio_value == 0 || $studio_value == 'undefined')
        {
            $(".is_studio_on").css('display','');
        }
        else
        {
            $(".is_studio_on").css('display','none');
        }
    });
});

//�ύҳ��
//ɾ�����
function delCameramanpic($url)
{
    $("#list_form").attr("action", $url).submit();
}

/*
*�г�����ĸ�������
*/
function list_cameraman_follow_data($url)
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