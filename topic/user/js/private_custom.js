$(document).ready(function() {


    $("#J_input").focus(function() 
    {
        WdatePicker({ dateFmt:'yyyy-MM-dd HH:00:00' });
    });

    // 主题风格 多选
    $("#style_sec li").click(function()
    {
        $(this).toggleClass('cur');
    })

    //身高
    $("#height li").click(function()
    {
        $("#height li").removeClass('cur');
        $(this).addClass('cur');
    })

    // 体重
    $("#weight li").click(function()
    {
        $("#weight li").removeClass('cur');
        $(this).addClass('cur');
    })


    $("#btn_submit").click(function()
    {

        var user_name = $("#user_name").val();
        var user_phone = $("#user_phone").val();
        var user_url = $("#user_url").val();
        var clothing_requirements = $("#clothing_requirements").val();

        var personal_role = $("input[name='RadioGroup1']:checked").val();

        // var province = $("#province").val();
        var city = $("#city").val();

        var street_address = $("#street_address").val();

        var input_date_time =   $("#J_input").val();

        var sec_time = $("#sec_time").val();
        var model_num = $("#model_num").val();
        var personl_price = $("#personl_price").val();
        var measurements = $("#measurements").val();
        var appearance = $("#appearance").val();

        if( $.trim(user_name) == '')
        {
            alert('请填写您的姓名或常用昵称');
			$("html,body").animate({scrollTop:$("#user_name").offset().top},1000);
			$("#user_name").focus();
            return ;
        }

        if( $.trim(user_phone) == '')
        {
            alert('请输入11数字手机号码');
			$("html,body").animate({scrollTop:$("#user_phone").offset().top},1000);
			$("#user_phone").focus();
            return ;
        }
        var phone_reg = new RegExp(/^1\d{10}$/);
        if (!phone_reg.test(user_phone)) 
        {
            alert("请输入正确的手机号");
			$("html,body").animate({scrollTop:$("#user_phone").offset().top},1000);
			$("#user_phone").focus();
            return ;
        };

        var style_sec_arr = [] ;
        $("#style_sec li").each(function() {
            if ($(this).hasClass('cur'))
            {
                style_sec_arr.push($(this).attr('data-style'))
            }
        });
        if (style_sec_arr.length<1) 
        {
            alert("请选择主题风格！");   
			$("html,body").animate({scrollTop:$("#style_sec").offset().top},1000);
            return ; 
        };

        if ( input_date_time =='' || !input_date_time) 
        {
            alert('请选择拍摄时间！');
			$("html,body").animate({scrollTop:$("#J_input").offset().top},1000);
            return ;
        };

        if ( sec_time =='' || !sec_time) 
        {
            alert('请选择时长！');
			$("html,body").animate({scrollTop:$("#sec_time").offset().top},1000);
			$("#sec_time").focus();
            return ;
        };

        if( $.trim(personl_price) == '')
        {
            alert('报酬，请填写每人时价');
			$("html,body").animate({scrollTop:$("#personl_price").offset().top},1000);
			$("#personl_price").focus();
            return ;
        }

        var height_require_val ,
            weight_require_val ;

        $("#height li").each(function() {
            if ($(this).hasClass('cur'))
            {
                height_require_val = $(this).attr('data-height');
            }
        });

        $("#weight li").each(function() {
            if ($(this).hasClass('cur'))
            {
                weight_require_val = $(this).attr('data-weight');
            }
        });

        //收集数据
        var data = 
        {
            cameraman_phone : user_phone ,
            cameraman_realname : user_name,
            home_page : user_url ,
            style : style_sec_arr,
            clothes_require : clothing_requirements,
            clothes_provide : personal_role ,
            location_id : city,
            date_address : street_address,
            date_time : input_date_time ,
            hour : sec_time ,
            model_num : model_num ,
            budget : personl_price ,
            bwh_require : measurements,
            height_require : height_require_val ,
            weight_require : weight_require_val ,
            looks_require :  appearance ,
        }

        console.log(data);

        $.ajax({
            type: "POST",
            url: "private_custom.php",
            data: data,
            dataType : "html",
            cache : false ,
            success: function(data_back)
            {
				txt = ( parseInt(data_back) > 1) ? '提交成功！' : '你的订制订单正在审核中'
                $("#fade_txt").html(txt);

           
                $("#fade").show();
                $("#pop-succeed").show();
                $("#close").click(function () 
                {
                    $("#fade").hide();
                    $("#pop-succeed").hide();
                })
                     
            }
        });
    })

});
