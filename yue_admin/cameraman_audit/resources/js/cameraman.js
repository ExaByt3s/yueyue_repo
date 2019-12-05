/*
* 摄影师部分的js
*/

//添加跟进信息
function add_cameraman_follow($user_id)
{
	$.layer({
            type: 2,
            title: '添加跟进信息',
            shade: [0.3,'#000'],
            maxmin: false,
            shadeClose: false, 
            area : ['500' , '320'],
            offset : ['200px', ''],
            iframe: {src: 'cameraman_follow_manage.php?user_id='+$user_id}
        }); 
}

//风格修改
function add_style($url)
{
   $.layer({
            type: 2,
            title: '修改风格信息',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['220' , '300'],
            offset : ['200px', ''],
            iframe: {src: $url}
        });  
}

//判断是否存在工作室
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

//提交页面
//删除相册
function delCameramanpic($url)
{
    $("#list_form").attr("action", $url).submit();
}

/*
*列出更多的跟进数据
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
            if (data.msg == 'success') 
            {
              var $list = data.list;
              var $str = "";
            //window.alert($list.length);
              for (var i = 0; i < $list.length; i ++) 
              {
                 $str += "<tr><td align='center'>"+$list[i]['follow_time']+"</td><td align='center'>"+$list[i]['follow_name']+"</td><td align='center'>"+$list[i]['result']+"</td><td align='center'>"+$list[i]['problem_type']+"</td><td align='center'>"+$list[i]['problem_content']+"</td><td align='center'><a href='cameraman_follow_manage.php?id="+$list[i]['id']+"&act=del' target='frame'>删除</a></td></tr>";
              }
              $("#stand_follow_data").html($str);
              $("#follow_more").css({'display': 'none'});
			  $("#follow_less").css({'display': ''});
            }else
            {
                layer.msg(data.msg, 1,1);
            }
            
        },
        error:function(data)
        {

        }
    });
}

/*
*列出缩小的跟进数据
*/
function list_cameraman_follow_less($url)
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
            if (data.msg == 'success') 
            {
              var $list = data.list;
              var $str = "";
            //window.alert($list.length);
              for (var i = 0; i < $list.length; i ++) 
              {
                 $str += "<tr><td align='center'>"+$list[i]['follow_time']+"</td><td align='center'>"+$list[i]['follow_name']+"</td><td align='center'>"+$list[i]['result']+"</td><td align='center'>"+$list[i]['problem_type']+"</td><td align='center'>"+$list[i]['problem_content']+"</td><td align='center'><a href='cameraman_follow_manage.php?id="+$list[i]['id']+"&act=del' target='frame'>删除</a></td></tr>";
              }
              $("#stand_follow_data").html($str);
              $("#follow_more").css({'display': ''});
			  $("#follow_less").css({'display': 'none'});
            }else
            {
                layer.msg(data.msg, 1,1);
            }
            
        },
        error:function(data)
        {

        }
    });
}


/*
*列出更多约拍信息
*/
function list_yues_by_user_id($url)
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
            if (data.msg == 'success') 
            {
              var $list = data.list;
              //window.alert($list);
              var $str = "";
            //window.alert($list.length);
              for (var i = 0; i < $list.length; i ++) 
              {
                //window.alert($list[i]['date_time']);
				//window.alert($list[i]['overall_score']);
				var $overall_score = typeof($list[i]['overall_score']) == "undefined" ? "暂无评分" : $list[i]['overall_score'];
                 $str += "<tr><td align='center'>"+$list[i]['date_time']+"</td><td align='center'>"+$list[i]['nickname']+"</td><td align='center'>"+$list[i]['date_style']+"</td><td align='center'>"+$overall_score+"</td><td align='center'>"+$list[i]['budget']+"</td></tr>";
              }
              //window.alert($str);
              $("#list_all_yue").html($str);
              $("#yue_more").css({'display': 'none'});
			  $("#yue_less").css({'display': ''});
            }else
            {
                layer.msg(data.msg, 1,1);
            }
            
        },
        error:function(data)
        {

        }
    });
}


/*
*列出约拍信息3条信息
*/
function list_yues_by_user_id_less($url)
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
            if (data.msg == 'success') 
            {
              var $list = data.list;
              //window.alert($list);
              var $str = "";
            //window.alert($list.length);
              for (var i = 0; i < $list.length; i ++) 
              {
                //window.alert($list[i]['date_time']);
				         var $overall_score = typeof($list[i]['overall_score']) == "undefined" ? "暂无评分" : $list[i]['overall_score'];
                 $str += "<tr><td align='center'>"+$list[i]['date_time']+"</td><td align='center'>"+$list[i]['nickname']+"</td><td align='center'>"+$list[i]['date_style']+"</td><td align='center'>"+$overall_score+"</td><td align='center'>"+$list[i]['budget']+"</td></tr>";
              }
              //window.alert($str);
              $("#list_all_yue").html($str);
			  $("#yue_less").css({'display': 'none'});
              $("#yue_more").css({'display': ''});
            }else
            {
                layer.msg(data.msg, 1,1);
            }
            
        },
        error:function(data)
        {

        }
    });
}