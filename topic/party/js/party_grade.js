function create_star_list(ele,ele_type,event_id)
{
    get_grade(ele,ele_type,event_id);
    var list_obj_arr = $(ele).getElements(ele_type);
    var len = list_obj_arr.length;
    for(i=0;i<len;i++)
    {
        new function(){
            var index = i;
            list_obj_arr[index].addEvent('mouseover',function()
                {
                    for(var j=0;j<len;j++)
                    {
                        list_obj_arr[j].className = "icon star-small-off";
                    }
                    for(var k=0;k<=index;k++)
                    {
                        list_obj_arr[k].className = "icon star-small-on";
                    }
                    $(ele+'_grade_text').innerHTML=display_text(index+1);
                    $(ele+'_count_grade_div').style.display="none";
                    $(ele+'_grade_text').style.display="";
                }
            );
            list_obj_arr[index].addEvent('mouseout',function()
                {
                    get_grade(ele,ele_type,event_id);
                    $(ele+'_grade_text').style.display="none";
                    $(ele+'_count_grade_div').style.display="";
                }
            );
            list_obj_arr[index].addEvent('click',function()
                {
                    grade_action(ele,ele_type,event_id,index);
                }
            );
        };
    }
}

function del_star_list(ele,ele_type)
{
    var list_obj_arr = $(ele).getElements(ele_type);
    var len = list_obj_arr.length;
    for(i=0;i<len;i++)
    {
        new function(){
            var index = i;
            list_obj_arr[index].removeEvents('mouseover');
            list_obj_arr[index].removeEvents('mouseout');
            list_obj_arr[index].removeEvents('click');
        };
    }
    
}

function grade_action(ele,ele_type,event_id,index)
{
    var login_id = readCookie('member_id');
    if(login_id>0)
    {
        var div='<div style="text-align:center;"><a href="javascript:void(0);" title="�ر�" class="fr alert_msg_box_close" onclick="close_msg_box();clearTimeout(t);return false;">�ر�</a><div id="enroll_msg_box">';      	
            div+='<p align="center"><img src="'+event_grade_ajax_pah +'images/loading.gif" width="40"/></p>'; 
            div+='</div></div>';
            show_msg_box({
                    width		:		440,
                    b_title		:		false,	
                    button		:		false,		
                    b_close_bnt	:		false,							
                    content		:		div
            });	
        var pars = "event_id=" +event_id+"&type=check"+"&timestamp=" + new Date().getTime();
        new Request(
        {
            'url':event_grade_ajax_pah + 'module/event_check_grade.js.php',
            'data' : pars,					
            'method' : 'get',
            'onComplete' : function(res) {	
                var msg="";
                var close_time = 2000;
                if(res=="E"){
                    msg='<p class="famyh f18 mb15" align="center"><strong>����û�б���������ʽ��Ա����������</strong></p>';
                }else if(res=="C"){
                    msg='<p class="famyh f18 mb15" align="center"><strong>����û�з���Ʒ����������</strong></p>';
                }else if(res=="D"){
                    msg='<p class="famyh f18 mb15" align="center"><strong>��Ѿ�����30�죬��������</strong></p>';
                }else if(res=="U"){
                    msg='<p class="famyh f18 mb15" align="center"><strong>���ǻ�����ߣ���������</strong></p>';
                }else if(res=="H"){
                    //msg='<p>���Ѿ������֣�����������</p>';
                    get_grade(ele,ele_type,event_id);
                    del_star_list(ele,ele_type);
                    $(ele+'_count_grade_div').style.display="none";
                    $(ele+'_grade_text').innerHTML="��������";
                    $(ele+'_grade_text').style.display="";		
                    
                }else if(res=="Y"){
                    var index_chn = change_index_chn(index+1);
                    msg='<p align="center">�����������Ϊ'+index_chn+'���ǡ�����Ҫ��������������ȡ����</p>';		

                    msg+='<p align="center"><textarea name="'+ele+'_grade_comment" id="'+ele+'_grade_comment" style="width: 310px; height: 85px;color:#8B8B8B;" onclick="clear_text(\''+ele+'_grade_comment\',\'˳��˵˵��Ի��֯�ߵ����(�������������)\');">˳��˵˵��Ի��֯�ߵ����(�������������)</textarea></p>';		
                    msg+='<p align="center" style="font-zize:16px;"><a href="javascript:void(0)" class="add_pop_btn" onclick="grade_confirm(\''+ele+'\',\''+ele_type+'\',\''+event_id+'\','+index+')">ȷ��</a> <a href="javascript:void(0)" class="add_pop_btn" onclick="close_msg_box()">ȡ��</a></p>';
                    close_time=200000;
                }else{
                    msg='<p class="famyh f18 mb15" align="center"><strong>����ʧ��</strong></p>';
                }
                if(msg!="")
                {
                    $('enroll_msg_box').innerHTML = msg;
                }else{
                    close_msg_box();
                    
                }
                t=setTimeout("close_msg_box();clearTimeout(t);",close_time);

            }		
        }).send();
    }else{
        loginApp.set({
            b_reload:true
        });
        loginApp.loginBox();
    }
}

function grade_confirm(ele,ele_type,event_id,index)
{
    var grade = index+1;
    $(ele+'_grade').value = grade;
    var grade_comment = $(ele+'_grade_comment').value;

    if(grade_comment=='˳��˵˵��Ի��֯�ߵ����(�������������)')
    {
        grade_comment = '';
    }
    grade_comment = encodeURI(grade_comment);
    $('enroll_msg_box').innerHTML = '<p align="center"><img src="'+event_grade_ajax_pah +'images/loading.gif" width="40"/></p>';	
    var pars = "event_id=" +event_id+"&type=add"+"&grade="+grade+"&grade_comment="+grade_comment+"&timestamp=" + new Date().getTime();
    new Request(
    {
    'url':event_grade_ajax_pah + 'module/event_check_grade.js.php',
    'data' : pars,
    'method' : 'get',
    'evalScripts': true,	
    'onComplete' : function(res) {
        var msg="";
        var close_time = 2000;
        if(res=="E"){
            msg='<p class="famyh f18 mb15" align="center"><strong>����û�б���������ʽ��Ա����������</strong></p>';
        }else if(res=="C"){
            msg='<p class="famyh f18 mb15" align="center"><strong>����û�з���Ʒ����������</strong></p>';
        }else if(res=="D"){
            msg='<p class="famyh f18 mb15" align="center"><strong>��Ѿ�����30�죬��������</strong></p>';
        }else if(res=="U"){
            msg='<p class="famyh f18 mb15" align="center"><strong>���ǻ�����ߣ���������</strong></p>';
        }else if(res=="H"){
        //	msg='<p>���Ѿ������֣�����������</p>';
            get_grade(ele,ele_type,event_id);	
            del_star_list(ele,ele_type);
            $(ele+'_count_grade_div').style.display="none";
            $(ele+'_grade_text').innerHTML="��������";
            $(ele+'_grade_text').style.display="";
            
        }else if(res=="Y"){
            msg='<p class="famyh f18 mb15" align="center"><strong>���ֳɹ���</strong></p>';
            del_star_list(ele,ele_type);
            count_grade(ele,ele_type,event_id,res);	
        }else{
            msg='<p class="famyh f18 mb15" align="center"><strong>����ʧ��</strong></p>';
        }
        if(msg!="")
        {
            $('enroll_msg_box').innerHTML = msg;
        }else{
            close_msg_box();
            
        }
        
        t=setTimeout("close_msg_box();clearTimeout(t);",close_time);
        
    }
    }).send();
}

function count_grade(ele,ele_type,event_id,res)
{
    var pars = "event_id=" +event_id+"&timestamp=" + new Date().getTime();
    new Request(
    {
    'url':event_grade_ajax_pah + 'module/event_count_grade.js.php',
    'data' : pars,
    'method' : 'get',
    'evalScripts': true,	
    'onComplete' : function(res) {
        var msg = "";
        if(res=="N")
        {
            
        }else{
            var temp_arr = res.split("_");
            var average_grade = temp_arr[0];
            var grade_count = temp_arr[1];
            $(ele+'_count_grade_span').innerHTML = grade_count;
            $(ele+'_grade').value = average_grade;
            
        }
        get_grade(ele,ele_type,event_id);	
    }
    }).send();
}

function get_grade(ele,ele_type,event_id)
{
    var list_obj_arr = $(ele).getElements(ele_type);
    var len = list_obj_arr.length;
    for(var i=0;i<len;i++)
    {
        list_obj_arr[i].className = "icon star-small-off";
    }
    var grade = $(ele+'_grade').value;
    var temp = parseInt(grade);

    for(var j=0;j<temp;j++)
    {
        list_obj_arr[j].className = "icon star-small-on";
    }
    var t1 = grade-temp;
    var t = Math.round(t1*10000)/10000;

    if(t>0.2 && t<0.8)
    {
        list_obj_arr[temp].className = "icon star-small-half";
        average_grade = temp + 0.5;
    }else if(t>=0.8){
        list_obj_arr[temp].className = "icon star-small-on";
        average_grade = temp + 1;
        var a_str = average_grade.toString();
        average_grade = a_str+".0";
    }else{
        if(temp>0)
        {
            var a_str = temp.toString();
            average_grade = a_str+".0";
        }else{
            average_grade = temp.toString();
        }
    }
    
    //$(ele+'_grade_text').innerHTML=display_text(temp);
    $(ele+'_grade_text').innerHTML="";
    $(ele+'_average_grade_span').innerHTML = average_grade;
    $(ele+'_grade_div').style.display="";
}

function get_grade2(ele,ele_type,event_id)
{
    var list_obj_arr = $(ele).getElements(ele_type);
    var len = list_obj_arr.length;
    for(var i=0;i<len;i++)
    {
        list_obj_arr[i].className = "icon star-small-off";
    }
    var grade = $(ele+'_grade').value;
    var temp = parseInt(grade);

    for(var j=0;j<temp;j++)
    {
        list_obj_arr[j].className = "icon star-small-on";
    }
    var t1 = grade-temp;
    var t = Math.round(t1*10000)/10000;

    if(t>0.2 && t<0.8)
    {
        list_obj_arr[temp].className = "icon star-small-half";
        average_grade = temp + 0.5;
    }else if(t>=0.8){
        list_obj_arr[temp].className = "icon star-small-on";
        average_grade = temp + 1;
        var a_str = average_grade.toString();
        average_grade = a_str+".0";
    }else{
        if(temp>0)
        {
            var a_str = temp.toString();
            average_grade = a_str+".0";
        }else{
            average_grade = temp.toString();
        }
    }
    var login_id = readCookie('member_id');
    if(login_id>0)
    {
        var pars = "event_id=" +event_id+"&type=check"+"&timestamp=" + new Date().getTime();
        new Request(
        {
        'url':event_grade_ajax_pah + 'module/event_check_grade.js.php',
        'data' : pars,
        'method' : 'get',
        'onComplete' : function(res) {
            var msg="";
            if(res=="E"){
                msg='����û�б���������ʽ��Ա����������';
                del_star_list(ele,ele_type);
            }else if(res=="C"){
                msg='����û�з���Ʒ����������';
                del_star_list(ele,ele_type);
            }else if(res=="D"){
                msg='��Ѿ�����30�죬��������';
                del_star_list(ele,ele_type);
            }else if(res=="U"){
                msg='���ǻ�����ߣ���������';
                del_star_list(ele,ele_type);
            }else if(res=="H"){
                msg='���Ѿ������֣�����������';
                del_star_list(ele,ele_type);
            }else if(res=="Y"){
                msg='(������Ǹ������)';
            }else{
                msg='';
            }
            $(ele+'_grade_text').innerHTML=msg;
            $(ele+'_average_grade_span').innerHTML = average_grade;
            $(ele+'_grade_div').style.display="";
        }
        }).send();
    }else{
        
        $(ele+'_grade_text').innerHTML='����û��<a hrfe="javascript:void(0)" onclick="return check_login();">��¼</a>����������';
        $(ele+'_average_grade_span').innerHTML = average_grade;
        $(ele+'_grade_div').style.display="";
    }
    
    
    
}

function display_text(value)
{
    var text = "";
    switch (value) {
        
        case 1:
            text = "һ��";
        break;
        case 2:
            text = "����";
        break;
        case 3:
            text = "��";
        break;
        case 4:
            text = "�ܺ�";
        break;
        case 5:
            text = "�ǳ���";
        break;		
        default:
            text = "һ��";
        
    }
    return text;
}

function change_index_chn(grade)
{
    var msg="";
    switch (grade) {
        
        case 1:
            msg="һ";
        break;
        case 2:
            msg="��";
        break;
        case 3:
            msg="��";
        break;
        case 4:
            msg="��";
        break;
        case 5:
            msg="��";
        break;		
        default:
            msg="һ";
        
    }
    return msg;
}