function enroll()
{
    var login_id = readCookie('member_id');
    if(login_id>0)
    {

        set_user_name('enroll');
        
    }else{
        loginApp.set({
            b_reload:true
        });
        loginApp.loginBox();
    }
}

function set_user_name(type)
{
    var pars = "timestamp=" + new Date().getTime();
    new Request(
    {
    'url':' ./module/event_get_login_user_name.js.php',
    'data' : pars,
    'method' : 'get',
    'onComplete' : function(res) {
        $('login_user_name').value = res;
        if(type=='enroll')
        {
            check_join_mode();
        }else{
            enroll_form("Y",'app');
        }
    }
    }).send();
}
function check_join_mode()
{
    var join_mode = $('join_mode').value;
    if(join_mode=="0")
    {
        event_can_enroll("Y");
    }else if(join_mode=="1")
    {
        var event_id = $('event_id').value;
        var pars = 'event_id=' + event_id + "&timestamp=" + new Date().getTime();
        new Request(
        {
        'url':' ./module/event_login_user_is_friend.js.php',
        'data' : pars,
        'method' : 'get',
        'onComplete' : function(res) {
            if(res=="Y")
            event_can_enroll("Y");
            else
            event_can_enroll("N");
        }
        }).send();
    }else if(join_mode=="2")
    {
        var event_id = $('event_id').value;
        var pars = 'event_id=' + event_id + "&timestamp=" + new Date().getTime();
        new Request(
        {
        'url':' ./module/event_invite_user.js.php',
        'data' : pars,
        'method' : 'get',
        'onComplete' : function(res) {
            if(res=="Y")
            event_can_enroll("Y");
            else
            event_can_enroll("N");
        }
        }).send();
    }else if(join_mode=="3")
    {
        var event_id = $('event_id').value;
        var pars = 'event_id=' + event_id + "&timestamp=" + new Date().getTime();
        new Request(
        {
        'url':' ./module/check_user_level.js.php',
        'data' : pars,
        'method' : 'get',
        'onComplete' : function(res) {
            if(res=="Y")
            event_can_enroll("Y");
            else
            event_can_enroll("N");
        }
        }).send();	
    }else{
        event_can_enroll("N");
    }
}
function event_can_enroll(b)
{
    enroll_form(b,'enroll');
}

function enroll_form(b,submit_type)
{
    var msg_width=360;
    var event_id = $('event_id').value;
    var submit_js = "enroll_submit('"+submit_type+"')";
    var div='<div class="cg_mid" style="text-align:left;"><a href="javascript:void(0);" title="�ر�" class="fr f12" onclick="close_msg_box();return false;">�ر�</a><div id="enroll_msg_box">';
    if(b=="Y")
    {
        var category = $('category').value;
        var nickname = $('login_user_name').value;
        var is_show_phone_form = "";
        var is_show_email_form = "";
        var is_show_address_form = "";
        var is_show_realname_form = "";
        var is_show_idcard_form = "";
        var is_show_car_type_form = "";
        
        if(category=='1'){
            //�Ƿ���Ҫ��д�绰
            if($('is_show_phone_form'))
            {
                is_show_phone_form = $('is_show_phone_form').value;
            }
        }else{
            is_show_phone_form="1";
        }
        //�Ƿ���Ҫ��дemail
        if($('is_show_email_form'))
        {
            is_show_email_form = $('is_show_email_form').value;
        }
        //�Ƿ���Ҫ��д��ַ
        if($('is_show_address_form'))
        {
            is_show_address_form = $('is_show_address_form').value;
        }
        //�Ƿ���Ҫ��ʵ����
        if($('is_show_realname_form'))
        {
            is_show_realname_form = $('is_show_realname_form').value;
        }
        //�Ƿ���Ҫ���֤
        if($('is_show_idcard_form'))
        {
            is_show_idcard_form = $('is_show_idcard_form').value;
        }
        //�����ʱ������ѡ��
        if($('is_show_car_type'))
        {
            is_show_car_type_form = $('is_show_car_type').value;
        }
        if(is_show_phone_form=="" && is_show_email_form=="" && is_show_address_form=="" && is_show_realname_form=="" && is_show_car_type_form==""){

                div+='<p class="famyh f18 mb15"><img src="images/loading.gif" width="40"/></p>';

        }else{
            var limit_enroll = $('limit_enroll').value;		//����һ�α�������
            if(limit_enroll==0)limit_enroll=3;		//����0ʱ����ʾ�����ƣ������ֻ��3��
            div+='<p class="famyh f18 mb15"><strong>'+nickname+'����ӭ�����μӻ��</strong></p>';
            //��ȡcookie���û��Ѿ�����绰
            var login_id = readCookie('member_id');
            div+='<table width="300" >';
            if($('is_event_admin') || $('is_event_user'))
            {
                if($('is_event_admin').value=="1" || $('is_event_user').value=="1")
                {
                    div+='<tr><td width="70" valign="top" class="fb">POCO ID��</td><td width="230"><input class="input162" type="text" name="pocoid" id="pocoid" value="'+login_id+'"/></td></tr>';
                }
            }
            
            if(is_show_phone_form!="")
            {
                div+= enroll_get_type_html("phone");
            }
            if(is_show_email_form!="")
            {
                div+= enroll_get_type_html("email");
            }
            if(is_show_address_form!="")
            {
                div+= enroll_get_type_html("address");
            }
            if(is_show_realname_form!="")
            {
                div+= enroll_get_type_html("realname");
            }
            if(is_show_idcard_form!="")
            {
                div+= enroll_get_type_html("idcard");
            }
            if(is_show_car_type_form!="")
            {
                div+= enroll_get_type_html("car_type");
            }
            
            if(event_id==34657)
            {
                div+='<tr><td valign="top" class="fb">�������</td><td width="230"><select name="camera_type" id="camera_type"><option value="">��ѡ���������</option><option value="������">������</option><option value="��΢��">��΢��</option><option value="����Ƭ��">����Ƭ��</option><option value="����+΢��">����+΢��</option><option value="����+��Ƭ��">����+��Ƭ��</option><option value="΢��+��Ƭ��">΢��+��Ƭ��</option><option value="����+΢��+��Ƭ��">����+΢��+��Ƭ��</option><option value="����">����</option></select><span class="hdtc_ts"><span class="alert_error" id="camera_type_error"></span></span></td></tr>';
                div+='<tr><td valign="top" class="fb">ԤԼ����</td><td width="230"><select name="booking_date" id="booking_date"><option value="">��ѡ������</option><option value="7��12��">7��12��</option><option value="7��13��">7��13��</option><option value="7��14��">7��14��</option></select><span class="hdtc_ts"><span class="alert_error" id="booking_date_error"></span></span></td></tr>';
                div+='<tr><td valign="top" class="fb">ԤԼʱ��</td><td width="230"><select name="booking_time" id="booking_time"><option value="">��ѡ��ʱ��</option><option value="10:30-12:30">10:30-12:30</option><option value="12:30-14:30">12:30-14:30</option><option value="14:30-16:30">14:30-16:30</option><option value="16:30-19:00">16:30-19:00</option></select><span class="hdtc_ts"><span class="alert_error" id="booking_time_error"></span></span></td></tr>';
            }
            
            if(submit_type=='enroll')
            {
                if(category=="2")
                {
                    div+='<tr><td valign="top" class="fb">����������</td><td class="fb"><input class="input77" type="text" name="enroll_num" id="enroll_num" value="1"/>��<span class="hdtc_ts"><span class="alert_error" id="enroll_num_error"></span></span><span class="info">����ͬʱ�������'+limit_enroll+'����������</span></td></tr>';
                }else{
                    div+='<input type="hidden" name="enroll_num" id="enroll_num" value="1"/><span class="alert_error" id="enroll_num_error"></span>';
                }
            }else{
                div+='<input type="hidden" name="enroll_num" id="enroll_num" value="1"/><span class="alert_error" id="enroll_num_error"></span>';
            }
            
            div+='<tr><td colspan="2"><div class="mt15 w200"><input type="button" value="�� ��" class="tj_btn f14 fb mr10 fl" onclick="'+submit_js+'"/><a href="javascript:void(0)" onclick="close_msg_box();return false;" style="position:relative; top:8px;">ȡ ��</a></div></td></tr>';
            div+='<tr><td colspan="2">&nbsp;</td></tr>'
            div+='<tr><td colspan="2"><span class="info">(POCO��ֻ���ṩһ����Ӱ���Ϣ����ƽ̨�����з�POCO�ٷ���֯�����ѻ��������в�����һ�з��ɾ��ף�����POCO���޹أ������ѻ��֯�߳е��������)</span></td></tr></table>';
        }
    }else{
        div+='<p class="famyh f18 mb15" align="center"><strong>�Բ����������ϴ˻�Ĳμ�������</strong></p>';
        var join_mode = $('join_mode').value;
        if(join_mode=="1")
        {	
            div+='<p align="center">(�����ߺ��Ѳμӣ����Ǻ���<a href="javascript:void(0)" onclick="close_msg_box();return app_enroll();" style="color:#6AC334;text-decoration:none;">����������</a>)</p>';
        }else if(join_mode=="2")
        {
            div+='<p align="center">(������������Ѳμӣ�û������<a href="javascript:void(0)" onclick="close_msg_box();return app_enroll();" style="color:#6AC334;text-decoration:none;">����������</a>)</p>';
        }else if(join_mode=="3")
        {
            div+='<p align="center">(���ĵȼ���������<a href="javascript:void(0)" onclick="close_msg_box();return app_enroll();" style="color:#6AC334;text-decoration:none;">����������</a>)</p>';
        }
    }
    div+='</div></div>';
    var msg_top = 100;
    
    show_msg_box({
        width		:		msg_width,
        top			:		msg_top,
        b_title		:		false,
        button		:		false,
        b_close_bnt	:		false,
        content		:		div
    });	
    if(category=='1' && is_show_phone_form=="" && is_show_email_form=="" && is_show_address_form=="" && is_show_realname_form=="" && is_show_car_type_form==""){
        enroll_submit(submit_type);
    }
    if(is_show_car_type_form)
    {
        get_user_car_type();
    }
}

function enroll_get_type_html(type)
{
    var login_id = readCookie('member_id');
    var html;
    if(type=="phone")
    {
        //��ȡcookie���û��Ѿ�����绰
        var cookie_phone = readCookie('event_enroll_phone_'+login_id);
        if(cookie_phone==null)
        {
            cookie_phone='';
        }
        var text = '���ĵ绰';
        if(event_id==34657)
            {
                text = '�����ֻ�';
            }
        html='<tr><td valign="top" class="fb">'+text+'</td><td width="230"><input class="input162" type="text" name="phone" id="phone" value="'+cookie_phone+'"/><span class="hdtc_ts"><span class="alert_error" id="phone_error"></span></span><span class="info">��д���ĵ绰��������ϵ</span></td></tr>';
    }
    if(type=="email")
    {
        html='<tr><td valign="top" class="fb">Email��</td><td width="230"><input class="input162" type="text" name="email" id="email" value=""/><span class="hdtc_ts"><span class="alert_error" id="email_error"></span></span></td></tr>';

    }
    if(type=="address")
    {

        html='<tr><td valign="top" class="fb">��ϵ��ַ��</td><td width="230"><input class="input162" type="text" name="address" id="address" value=""/><span class="hdtc_ts"><span class="alert_error" id="address_error"></span></span></td></tr>';

    }
    if(type=="realname")
    {

        html='<tr><td valign="top" class="fb">��ʵ������</td><td width="230"><input class="input162" type="text" name="realname" id="realname" value=""/><span class="hdtc_ts"><span class="alert_error" id="realname_error"></span></span></td></tr>';

    }
    if(type=="idcard")
    {

        html='<tr><td valign="top" class="fb">���֤��</td><td width="230"><input class="input162" type="text" name="idcard" id="idcard" value=""/><span class="hdtc_ts"><span class="alert_error" id="idcard_error"></span></span></td></tr>';

    }
    if(type=="car_type")
    {
        html='<tr><td valign="top" class="fb">�Լݳ��ͣ�</td><td width="230">';
        html+='<label><input type="radio" name="car_drive_or_by" id="car_drive_car" value="drive_car" onclick="display_car_type(\'open\')" checked>ѡ����</label>';
        html+=' �� <label><input type="radio" name="car_drive_or_by" id="car_by_car" value="by_car" onclick="display_car_type(\'close\')">��Ҫ�</label><br/>';     
        html+='<span id="drive_car_span"><select subclass="1" id="car_brand_name" usedata="car_dataSrc" name="car_brand_name"><option value="">��ѡ��</option></select><br/>';        
        html+='<select subclass="2" id="car_series_name" usedata="car_dataSrc" name="car_series_name" disabled=""><option value="">��ѡ��</option></select><br/>';
        html+='<select subclass="3"  id="car_model_name" usedata="car_dataSrc" name="car_model_name" disabled=""><option value="">��ѡ��</option></select><br/>';
        html+='�ɴλ <select  id="car_can_person" name="car_can_person" ><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option></select>��';
        html+='<input type="hidden" name="extend_id" id="extend_id" value="" /></span>';
        html+='<span class="hdtc_ts"><span class="alert_error" id="car_type_error"></span></span></td></tr>';
    }

    
    
    return html;
}

function display_car_type(act)
{
    if(act=="open")
    {
        $('drive_car_span').style.display="";
    }else{
        $('drive_car_span').style.display="none";
    }
}
function get_user_car_type()
{

    
    var pars = 'timestamp=' + new Date().getTime();

    new Request.JSON(
    {
    'url':' ./module/get_user_car_type.js.php',
    'data' : pars,
    'method' : 'get',
    'evalScripts': true,
    'onComplete' : function(data) {
        if(data)
        {
            
            if(data.res=="Y")
            {
                var car_brand_name = data.car_brand_name;
                var car_series_name = data.car_series_name;
                var car_model_name = data.car_model_name;
                $('extend_id').value = data.extend_id;
            }
            else
            {
                var car_brand_name ="";
                var car_series_name ="";
                var car_model_name ="";
                $('extend_id').value = "";
            }

            
        }
        show_locate_select_v2([car_brand_name,car_series_name,car_model_name],"car_dataSrc",Car_MenuInfoArr);
    }
    }).send();
    
}

function enroll_submit(submit_type)
{
    var login_id = readCookie('member_id');
    if(login_id>0)
    {
        var category = $('category').value;
        var event_id = $('event_id').value;
        if(submit_type=='enroll')
        {
            var s_t = 0;
        }else{
            var s_t = 1;
        }
        var is_show_phone_form = "";
        var is_show_email_form = "";
        var is_show_address_form = "";
        var is_show_realname_form = "";
        var is_show_idcard_form = "";
        var is_show_car_type_form="";
        
        //���»ʱ��һ��Ҫ��绰
        if(category=="1")
        {
            //�Ƿ���Ҫ��д�绰
            if($('is_show_phone_form'))
            {
                is_show_phone_form = $('is_show_phone_form').value;
            }
        }else{
            is_show_phone_form = "1";
        }
        
        //�Ƿ���Ҫ��дemail
        if($('is_show_email_form'))
        {
            is_show_email_form = $('is_show_email_form').value;
        }
        //�Ƿ���Ҫ��д��ַ
        if($('is_show_address_form'))
        {
            is_show_address_form = $('is_show_address_form').value;
        }
        //�Ƿ���Ҫ��ʵ����
        if($('is_show_realname_form'))
        {
            is_show_realname_form = $('is_show_realname_form').value;
        }
        //�Ƿ���Ҫ���֤
        if($('is_show_idcard_form'))
        {
            is_show_idcard_form = $('is_show_idcard_form').value;
        }
        //�����ʱ������ѡ��
        if($('is_show_car_type'))
        {
            is_show_car_type_form = $('is_show_car_type').value;
        }
        
        if(is_show_phone_form=="1" || is_show_email_form=="1" || is_show_address_form=="1" || is_show_realname_form=="1" || is_show_idcard_form=="1" || is_show_car_type_form=="1")
        {
            var phone = "";
            var email = "";
            var address = "";
            var realname = "";
            var idcard = "";
            if(is_show_phone_form=="1"){
                phone = $('phone').value;
                if(!validate_num(phone)|| phone.trim()=="" || phone==0)
                {
                    alert_error('phone','������ĵ绰����');
                    return false;
                }else{
                    alert_error('phone','');
                }
            }
            if(is_show_email_form=="1"){
                email = $('email').value;
                if(email.trim()=="" || !checkEmail(email))
                {
                    alert_error('email','�������Email����');
                    return false;
                }else{
                    alert_error('email','');
                    email = encodeURI(email);
                }
            }
            if(is_show_address_form=="1"){
                address = $('address').value;
                if(address.trim()=="")
                {
                    alert_error('address','���������ϵ��ַ����');
                    return false;
                }else{
                    alert_error('address','');
                    address = encodeURI(address);
                }
            }
            if(is_show_realname_form=="1"){
                realname = $('realname').value;
                realname = encodeURI(realname);
                
            }
            if(is_show_idcard_form=="1"){
                idcard = $('idcard').value;
                if(idcard.trim()=="")
                {
                    alert_error('idcard','����������֤����');
                    return false;
                }else{
                    alert_error('idcard','');
                    idcard = encodeURI(idcard);
                }
            }
            if(is_show_car_type_form=="1"){
                if($('car_drive_car').checked)
                {
                    var car_brand_name = $('car_brand_name').value;
                    var car_series_name = $('car_series_name').value;
                    var car_model_name = $('car_model_name').value;
                    if(car_brand_name.trim()=="" && car_series_name.trim()=="" && car_model_name.trim()=="")
                    {
                        alert_error('car_type','�Լݳ���');
                        return false;
                    }else{
                        alert_error('car_type','');
                        if(car_model_name)
                        {
                            var car_type_id = car_model_name
                        }else if(car_series_name)
                        {
                            var car_type_id = car_series_name
                        }else{
                            var car_type_id = car_brand_name
                        }
                    }
                    var car_can_person = $('car_can_person').value;
                }
            }
            
            if(event_id==34657)
            {
                var camera_type = $('camera_type').value;
                var booking_date = $('booking_date').value;
                var booking_time = $('booking_time').value;
                
                if(camera_type.trim()=="")
                {
                    alert_error('camera_type','��ѡ���������');
                    return false;
                }else{
                    alert_error('camera_type','');
                    camera_type = encodeURI(camera_type);
                }
                if(booking_date.trim()=="")
                {
                    alert_error('booking_date','��ѡ��ԤԼ����');
                    return false;
                }else{
                    alert_error('booking_date','');
                    booking_date = encodeURI(booking_date);
                }
                if(booking_time.trim()=="")
                {
                    alert_error('booking_time','��ѡ��ԤԼʱ��');
                    return false;
                }else{
                    alert_error('booking_time','');
                    booking_time = encodeURI(booking_time);
                }
            }
            
            
            var enroll_num = $('enroll_num').value;
            var limit_enroll = $('limit_enroll').value;		//����һ�α�������
            if(limit_enroll==0)limit_enroll=3;		//����0ʱ����ʾ�����ƣ������ֻ��3��
            if(!(/^\d+$/i).test(enroll_num)|| enroll_num.trim()=="" || enroll_num==0 || enroll_num>limit_enroll)
            {
                alert_error('enroll_num','������Ĳμ���������');
                return false;
            }else{
                alert_error('enroll_num','');
            }
            
            var pars = 'event_id=' + event_id + '&user_id=' + login_id + '&phone=' + phone + '&email=' + email + '&address=' + address + '&realname=' + realname + '&idcard=' + idcard + '&enroll_num=' + enroll_num + '&s_t=' + s_t + "&timestamp=" + new Date().getTime();
            if($('is_event_admin'))
            {
                if($('is_event_admin').value=="1")
                {
                    var pocoid = $('pocoid').value;		
                    pars = pars + '&pocoid=' + pocoid;
                }
            }
            if(is_show_car_type_form=="1")
            {
                
                if( $('car_drive_car').checked)
                {
                    var extend_id = $('extend_id').value;
                    pars = pars + '&drive_car=1' + '&car_type_id=' + car_type_id + '&extend_id=' + extend_id + '&car_can_person=' + car_can_person;
                }else{
                    pars = pars + '&drive_car=0' ;
                }
            }
            
            if(event_id==34657)
            {
                pars = pars + '&camera_type='+camera_type+'&booking_date='+booking_date+'&booking_time='+booking_time ;
            }
            
            
        }else{
            var pars = 'event_id=' + event_id + '&user_id=' + login_id + '&enroll_num=1' + '&s_t=' + s_t + "&timestamp=" + new Date().getTime();
        }
        try{
            if(ta_phone)
            pars+='&ta_phone='+ta_phone;
        
        }catch(e){
            
        }
        
        
        $('enroll_msg_box').innerHTML = "<p align='center'><img src='images/loading.gif' width='40'/></p>";
        new Request.JSON(
        {
        'url':' ./module/event_enroll.js.php',
        'data' : pars,
        'method' : 'get',
        'onComplete' : function(data) {
            var event_id = $('event_id').value;
            var res = data.res;
            if(res=="Y")
            {
                if(submit_type=='enroll')
                {
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>�����ɹ���</p>";
                    $('enroll_button_div').innerHTML ='<a href="javascript:void(0)" onclick="check_add_commend();" class="fb_btn" title="������Ʒ">������Ʒ</a><span>�����ɹ���������Ʒ</span>';
                    if($('enroll_list'))
                    {
                        get_enroll_list();
                    }
                }else{
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>����ɹ���</p>";
                }
                    
            }
            else if(res=="O")
            {
                $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>�û��ʼ���Ѿ���������ʱ��</p>";

            }
            else if(res=="D")
            {
                $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>��֮ǰ�Ѿ������ˣ������ĵȺ�֪ͨ��</p>";

            }
            else if(res=="F")
            {
                
                if(submit_type=='enroll')
                {
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>��������������򲹣�</p>";
                }else{
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>����ɹ���</p>";
                }
            }
            else if(res=="E")
            {
                var html = "<p align='center' class='famyh f18 mb15'>�����ɹ��������ĵȺ�֪ͨ��</p>";
                html+='<p align="center">�˻������֯��ֱ�Ӵӱ�������ѡ��ѡ��</p>';
                $('enroll_msg_box').innerHTML = html;

            }
            else
            {
                if(submit_type=='enroll')
                {
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>����ʧ�ܣ�</p>";
                }else{
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>����ʧ�ܣ�</p>";
                }
                
            }
            t=setTimeout("close_msg_box();clearTimeout(t);",2000);
        }
        }).send();
    }else{
        close_msg_box();
        loginApp.set({
            b_reload:true
        });
        loginApp.loginBox();
    }
}

function checkEmail(email)
{	
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))
    {
        return true;
    }else{ 
        return false;
    }
    
}

function validate_num(val){
    var digits="0123456789-";
    var temp;

    for (var i=0;i<val.length;i++){
        temp=val.substring(i,i+1);
        if (digits.indexOf(temp)==-1){
            return (false);
        }
    }
    return (true);
}
function app_enroll()
{
    var login_id = readCookie('member_id');
    if(login_id>0)
    {
        
        var category = $('category').value;
        if(category=="2")
        {
            set_user_name('app');
        }else{
            var div='<div style="text-align:center;"><a href="javascript:void(0);" title="�ر�" class="fr alert_msg_box_close" onclick="close_msg_box();clearTimeout(t);return false;">�ر�</a><div id="enroll_msg_box">';
            div+='<p align="center"><img src="images/loading.gif" width="40"/></p>';
            div+='</div></div>';
            show_msg_box({
                width		:		200,
                b_title		:		false,
                button		:		false,
                b_close_bnt	:		false,
                content		:		div
            });
            var event_id = $('event_id').value;
            var pars = 'event_id=' + event_id + '&user_id=' + login_id + '&enroll_num=1' + '&s_t=1' + '&timestamp=' + new Date().getTime();
    
            new Request(
            {
            'url':' ./module/event_enroll.js.php',
            'data' : pars,
            'method' : 'get',
            'onComplete' : function(res) {
    
                if(res=="Y")
                {
                    div='<p class="famyh f18 mb15" align="center"><strong>���뱨���ɹ���</strong></p>';
                }
                else if(res=="D")
                {
                    div='<p class="famyh f18 mb15" align="center"><strong>�Ѿ����뱨����</strong></p>';
                }
                else
                {
                    div='<p class="famyh f18 mb15" align="center"><strong>���뱨��ʧ�ܣ�</strong></p>';
                }
    
                $('enroll_msg_box').innerHTML = div;
                t=setTimeout("close_msg_box();clearTimeout(t);",2000);
            }
            }).send();
        }
    }else{
        loginApp.set({
            b_reload:true
        });
        loginApp.loginBox();
    }
}

function check_add_commend()
{
    var event_id = $('event_id').value;
    var pars = "event_id=" +event_id+"&timestamp=" + new Date().getTime();
    new Request(
    {
    'url':' ./module/event_check_add_commend.js.php',
    'data' : pars,
    'method' : 'get',
    'onComplete' : function(res) {
        if(res=="N" || res=="")
        {
            msg = "���ڲ��ܷ�����Ʒ";
            var div='<div style="text-align:center;"><a href="javascript:void(0);" title="�ر�" class="fr alert_msg_box_close" onclick="close_msg_box();clearTimeout(t);return false;">�ر�</a><div id="enroll_msg_box">';
            div+='<p class="famyh f18 mb15" align="center"><strong>'+msg+'</strong></p>';
            div+='</div></div>';
            show_msg_box({
                width		:		200,
                b_title		:		false,
                button		:		false,
                b_close_bnt	:		false,
                content		:		div
            });
            t=setTimeout("close_msg_box();clearTimeout(t);",2000);
            
            
        }else{
            var type_icon = res;
            msg = "�����ɹ�";
            var category = $('category').value;
            var event_id = $('event_id').value;
            if(category=="1")
            {
                url = "add_commend.php?event_id=" + event_id;
                window.location = url;
                //open_new_page(url);
            }else if(category=="2")
            {
                switch(type_icon)
                {
                    case "photo":
                        url = "http://my.poco.cn/blog_v2/blog_add.php?publish_type=photo&event_id=" + event_id;
                    break;	
                    case "food":
                        url = "http://my.poco.cn/blog_v2/blog_add.php?publish_type=food&event_id=" + event_id;
                    break;		
                    case "pet":
                        url = "http://my.poco.cn/blog_v2/blog_add.php?publish_type=pet&event_id=" + event_id;
                    break;		
                    case "travel":
                        url = "http://my.poco.cn/blog_v2/blog_add.php?publish_type=travel&event_id=" + event_id;
                    break;		
                    default:
                        url = "http://my.poco.cn/manage/manage_act_release.php?event_id=" + event_id;
                    break;	
                }
                window.location.href=url;
                //open_new_page(url);
            }else{
                return false;
            }
        }

    }
    }).send();
}

function get_enroll_list()
{
    var event_id = $('event_id').value;
    var pars = "event_id=" +event_id+"&timestamp=" + new Date().getTime();
    new Request(
    {
    'url':' ./module/event_get_enroll_list.js.php',
    'data' : pars,
    'method' : 'get',
    'onComplete' : function(res) {
        if(res=="N")
        {
            $('enroll_list').innerHTML = "<p>����ʧ��</p>"
        }else{
            $('enroll_list').innerHTML = res;
        }

    }
    }).send();
}

function checkRadioValue(radio_name){
    var obj=document.getElementsByName(radio_name);

    for (var i=0;i<obj.length;i++)
    {
        if(obj[i].checked)
        return obj[i].value;
    }
    
    return false;
}
