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
    var div='<div class="cg_mid" style="text-align:left;"><a href="javascript:void(0);" title="关闭" class="fr f12" onclick="close_msg_box();return false;">关闭</a><div id="enroll_msg_box">';
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
            //是否需要填写电话
            if($('is_show_phone_form'))
            {
                is_show_phone_form = $('is_show_phone_form').value;
            }
        }else{
            is_show_phone_form="1";
        }
        //是否需要填写email
        if($('is_show_email_form'))
        {
            is_show_email_form = $('is_show_email_form').value;
        }
        //是否需要填写地址
        if($('is_show_address_form'))
        {
            is_show_address_form = $('is_show_address_form').value;
        }
        //是否需要真实姓名
        if($('is_show_realname_form'))
        {
            is_show_realname_form = $('is_show_realname_form').value;
        }
        //是否需要身份证
        if($('is_show_idcard_form'))
        {
            is_show_idcard_form = $('is_show_idcard_form').value;
        }
        //汽车活动时出车型选择
        if($('is_show_car_type'))
        {
            is_show_car_type_form = $('is_show_car_type').value;
        }
        if(is_show_phone_form=="" && is_show_email_form=="" && is_show_address_form=="" && is_show_realname_form=="" && is_show_car_type_form==""){

                div+='<p class="famyh f18 mb15"><img src="images/loading.gif" width="40"/></p>';

        }else{
            var limit_enroll = $('limit_enroll').value;		//限制一次报名人数
            if(limit_enroll==0)limit_enroll=3;		//等于0时，表示不限制，但最多只能3人
            div+='<p class="famyh f18 mb15"><strong>'+nickname+'，欢迎报名参加活动！</strong></p>';
            //读取cookie，用户已经填过电话
            var login_id = readCookie('member_id');
            div+='<table width="300" >';
            if($('is_event_admin') || $('is_event_user'))
            {
                if($('is_event_admin').value=="1" || $('is_event_user').value=="1")
                {
                    div+='<tr><td width="70" valign="top" class="fb">POCO ID：</td><td width="230"><input class="input162" type="text" name="pocoid" id="pocoid" value="'+login_id+'"/></td></tr>';
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
                div+='<tr><td valign="top" class="fb">相机类型</td><td width="230"><select name="camera_type" id="camera_type"><option value="">请选择相机类型</option><option value="仅单反">仅单反</option><option value="仅微单">仅微单</option><option value="仅卡片机">仅卡片机</option><option value="单反+微单">单反+微单</option><option value="单反+卡片机">单反+卡片机</option><option value="微单+卡片机">微单+卡片机</option><option value="单反+微单+卡片机">单反+微单+卡片机</option><option value="其他">其他</option></select><span class="hdtc_ts"><span class="alert_error" id="camera_type_error"></span></span></td></tr>';
                div+='<tr><td valign="top" class="fb">预约日期</td><td width="230"><select name="booking_date" id="booking_date"><option value="">请选择日期</option><option value="7月12日">7月12日</option><option value="7月13日">7月13日</option><option value="7月14日">7月14日</option></select><span class="hdtc_ts"><span class="alert_error" id="booking_date_error"></span></span></td></tr>';
                div+='<tr><td valign="top" class="fb">预约时间</td><td width="230"><select name="booking_time" id="booking_time"><option value="">请选择时间</option><option value="10:30-12:30">10:30-12:30</option><option value="12:30-14:30">12:30-14:30</option><option value="14:30-16:30">14:30-16:30</option><option value="16:30-19:00">16:30-19:00</option></select><span class="hdtc_ts"><span class="alert_error" id="booking_time_error"></span></span></td></tr>';
            }
            
            if(submit_type=='enroll')
            {
                if(category=="2")
                {
                    div+='<tr><td valign="top" class="fb">报名人数：</td><td class="fb"><input class="input77" type="text" name="enroll_num" id="enroll_num" value="1"/>人<span class="hdtc_ts"><span class="alert_error" id="enroll_num_error"></span></span><span class="info">您可同时申请最多'+limit_enroll+'个报名名额</span></td></tr>';
                }else{
                    div+='<input type="hidden" name="enroll_num" id="enroll_num" value="1"/><span class="alert_error" id="enroll_num_error"></span>';
                }
            }else{
                div+='<input type="hidden" name="enroll_num" id="enroll_num" value="1"/><span class="alert_error" id="enroll_num_error"></span>';
            }
            
            div+='<tr><td colspan="2"><div class="mt15 w200"><input type="button" value="提 交" class="tj_btn f14 fb mr10 fl" onclick="'+submit_js+'"/><a href="javascript:void(0)" onclick="close_msg_box();return false;" style="position:relative; top:8px;">取 消</a></div></td></tr>';
            div+='<tr><td colspan="2">&nbsp;</td></tr>'
            div+='<tr><td colspan="2"><span class="info">(POCO网只是提供一个摄影活动信息发布平台，所有非POCO官方组织的网友活动，活动过程中产生的一切法律纠纷，均与POCO网无关，由网友活动组织者承担相关责任)</span></td></tr></table>';
        }
    }else{
        div+='<p class="famyh f18 mb15" align="center"><strong>对不起，您不符合此活动的参加条件。</strong></p>';
        var join_mode = $('join_mode').value;
        if(join_mode=="1")
        {	
            div+='<p align="center">(发布者好友参加，不是好友<a href="javascript:void(0)" onclick="close_msg_box();return app_enroll();" style="color:#6AC334;text-decoration:none;">点此申请参与</a>)</p>';
        }else if(join_mode=="2")
        {
            div+='<p align="center">(发布者邀请好友参加，没被邀请<a href="javascript:void(0)" onclick="close_msg_box();return app_enroll();" style="color:#6AC334;text-decoration:none;">点此申请参与</a>)</p>';
        }else if(join_mode=="3")
        {
            div+='<p align="center">(您的等级不够，可<a href="javascript:void(0)" onclick="close_msg_box();return app_enroll();" style="color:#6AC334;text-decoration:none;">点此申请参与</a>)</p>';
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
        //读取cookie，用户已经填过电话
        var cookie_phone = readCookie('event_enroll_phone_'+login_id);
        if(cookie_phone==null)
        {
            cookie_phone='';
        }
        var text = '您的电话';
        if(event_id==34657)
            {
                text = '您的手机';
            }
        html='<tr><td valign="top" class="fb">'+text+'</td><td width="230"><input class="input162" type="text" name="phone" id="phone" value="'+cookie_phone+'"/><span class="hdtc_ts"><span class="alert_error" id="phone_error"></span></span><span class="info">填写您的电话，方便活动联系</span></td></tr>';
    }
    if(type=="email")
    {
        html='<tr><td valign="top" class="fb">Email：</td><td width="230"><input class="input162" type="text" name="email" id="email" value=""/><span class="hdtc_ts"><span class="alert_error" id="email_error"></span></span></td></tr>';

    }
    if(type=="address")
    {

        html='<tr><td valign="top" class="fb">联系地址：</td><td width="230"><input class="input162" type="text" name="address" id="address" value=""/><span class="hdtc_ts"><span class="alert_error" id="address_error"></span></span></td></tr>';

    }
    if(type=="realname")
    {

        html='<tr><td valign="top" class="fb">真实姓名：</td><td width="230"><input class="input162" type="text" name="realname" id="realname" value=""/><span class="hdtc_ts"><span class="alert_error" id="realname_error"></span></span></td></tr>';

    }
    if(type=="idcard")
    {

        html='<tr><td valign="top" class="fb">身份证：</td><td width="230"><input class="input162" type="text" name="idcard" id="idcard" value=""/><span class="hdtc_ts"><span class="alert_error" id="idcard_error"></span></span></td></tr>';

    }
    if(type=="car_type")
    {
        html='<tr><td valign="top" class="fb">自驾车型：</td><td width="230">';
        html+='<label><input type="radio" name="car_drive_or_by" id="car_drive_car" value="drive_car" onclick="display_car_type(\'open\')" checked>选择车型</label>';
        html+=' 或 <label><input type="radio" name="car_drive_or_by" id="car_by_car" value="by_car" onclick="display_car_type(\'close\')">我要搭车</label><br/>';     
        html+='<span id="drive_car_span"><select subclass="1" id="car_brand_name" usedata="car_dataSrc" name="car_brand_name"><option value="">请选择</option></select><br/>';        
        html+='<select subclass="2" id="car_series_name" usedata="car_dataSrc" name="car_series_name" disabled=""><option value="">请选择</option></select><br/>';
        html+='<select subclass="3"  id="car_model_name" usedata="car_dataSrc" name="car_model_name" disabled=""><option value="">请选择</option></select><br/>';
        html+='可搭车位 <select  id="car_can_person" name="car_can_person" ><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option></select>人';
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
        
        //线下活动时，一定要填电话
        if(category=="1")
        {
            //是否需要填写电话
            if($('is_show_phone_form'))
            {
                is_show_phone_form = $('is_show_phone_form').value;
            }
        }else{
            is_show_phone_form = "1";
        }
        
        //是否需要填写email
        if($('is_show_email_form'))
        {
            is_show_email_form = $('is_show_email_form').value;
        }
        //是否需要填写地址
        if($('is_show_address_form'))
        {
            is_show_address_form = $('is_show_address_form').value;
        }
        //是否需要真实姓名
        if($('is_show_realname_form'))
        {
            is_show_realname_form = $('is_show_realname_form').value;
        }
        //是否需要身份证
        if($('is_show_idcard_form'))
        {
            is_show_idcard_form = $('is_show_idcard_form').value;
        }
        //汽车活动时出车型选择
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
                    alert_error('phone','您输入的电话有误');
                    return false;
                }else{
                    alert_error('phone','');
                }
            }
            if(is_show_email_form=="1"){
                email = $('email').value;
                if(email.trim()=="" || !checkEmail(email))
                {
                    alert_error('email','您输入的Email有误');
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
                    alert_error('address','您输入的联系地址有误');
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
                    alert_error('idcard','您输入的身份证有误');
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
                        alert_error('car_type','自驾车型');
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
                    alert_error('camera_type','请选择相机类型');
                    return false;
                }else{
                    alert_error('camera_type','');
                    camera_type = encodeURI(camera_type);
                }
                if(booking_date.trim()=="")
                {
                    alert_error('booking_date','请选择预约日期');
                    return false;
                }else{
                    alert_error('booking_date','');
                    booking_date = encodeURI(booking_date);
                }
                if(booking_time.trim()=="")
                {
                    alert_error('booking_time','请选择预约时间');
                    return false;
                }else{
                    alert_error('booking_time','');
                    booking_time = encodeURI(booking_time);
                }
            }
            
            
            var enroll_num = $('enroll_num').value;
            var limit_enroll = $('limit_enroll').value;		//限制一次报名人数
            if(limit_enroll==0)limit_enroll=3;		//等于0时，表示不限制，但最多只能3人
            if(!(/^\d+$/i).test(enroll_num)|| enroll_num.trim()=="" || enroll_num==0 || enroll_num>limit_enroll)
            {
                alert_error('enroll_num','您输入的参加人数有误');
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
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>报名成功！</p>";
                    $('enroll_button_div').innerHTML ='<a href="javascript:void(0)" onclick="check_add_commend();" class="fb_btn" title="发表作品">发表作品</a><span>报名成功！发表活动作品</span>';
                    if($('enroll_list'))
                    {
                        get_enroll_list();
                    }
                }else{
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>申请成功！</p>";
                }
                    
            }
            else if(res=="O")
            {
                $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>活动没开始或已经超过报名时间</p>";

            }
            else if(res=="D")
            {
                $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>您之前已经报名了，请耐心等候通知。</p>";

            }
            else if(res=="F")
            {
                
                if(submit_type=='enroll')
                {
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>人数已满，进入候补！</p>";
                }else{
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>申请成功！</p>";
                }
            }
            else if(res=="E")
            {
                var html = "<p align='center' class='famyh f18 mb15'>报名成功，请耐心等候通知！</p>";
                html+='<p align="center">此活动将由组织者直接从报名中挑选正选。</p>';
                $('enroll_msg_box').innerHTML = html;

            }
            else
            {
                if(submit_type=='enroll')
                {
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>报名失败！</p>";
                }else{
                    $('enroll_msg_box').innerHTML ="<p align='center' class='famyh f18 mb15'>申请失败！</p>";
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
            var div='<div style="text-align:center;"><a href="javascript:void(0);" title="关闭" class="fr alert_msg_box_close" onclick="close_msg_box();clearTimeout(t);return false;">关闭</a><div id="enroll_msg_box">';
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
                    div='<p class="famyh f18 mb15" align="center"><strong>申请报名成功！</strong></p>';
                }
                else if(res=="D")
                {
                    div='<p class="famyh f18 mb15" align="center"><strong>已经申请报名！</strong></p>';
                }
                else
                {
                    div='<p class="famyh f18 mb15" align="center"><strong>申请报名失败！</strong></p>';
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
            msg = "现在不能发表作品";
            var div='<div style="text-align:center;"><a href="javascript:void(0);" title="关闭" class="fr alert_msg_box_close" onclick="close_msg_box();clearTimeout(t);return false;">关闭</a><div id="enroll_msg_box">';
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
            msg = "操作成功";
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
            $('enroll_list').innerHTML = "<p>加载失败</p>"
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
