<?php
/**
 * tip 配置
 * 
 * @author Hai
 * 
 */
return array(

        'phone_is_null'             =>'手机号码为空',
		'phone_format_error'        =>'手机号码格式错误',
        'user_is_bind'              =>'用户已绑定',
        'phone_is_bind'             =>'号码已注册过',
		'role_choose_error'         =>'角色选择错误',
		'phone_not_reg'             =>'该号码未注册',
		
		'pwd_is_null'               =>'密码为空',
		'pwd_is_error'               =>'密码长度错误',
        'verify_code_sent_error'    =>'验证码发送错误',
        'verify_code_sent_success'  =>'验证码发送成功',
        //用于没绑定手机  却执行取消绑定操作给的提示
        'user_no_bind'              =>'用户没绑定过手机',
        'phone_no_bind'             =>'号码未绑定',
        /*验证码*/     
        'verify_code_error'        =>'验证码格式错误',
        'verify_code_check_error'  =>'验证码不正确或已过期',
        'verify_code_check_success'=>'验证码校验成功',
        /*验证码*/
        'bind_error'            =>'绑定错误',
        'bind_success'          =>'绑定成功',
        'change_bind_error'     =>'更改绑定错误',
        'change_bind_success'   =>'更改绑定成功',
        'phone_cant_bind_again' =>'手机号已绑定 不能重复绑定',

        'reg_error'         =>'注册失败',
        'reg_success'       =>'注册成功',
        'login_success'     =>'登陆成功',
        'login_error'       =>'账号或密码错误',
        /*密码更改*/
        'comfirm_password_error' =>'两次输入的新密码不一致',
        'password_format_error'  =>'密码格式错误 必须长度大于6位，32位以下',
        'password_check_faild'   =>'密码验证失败',
        'update_password_success'=>'更新密码成功',
        /*密码更改*/
        'type_error'        =>'类型错误',
        'role_select'       =>'请选择用户角色',
		'reset_pwd_success'=>'密码重置成功',
		'reset_pwd_fail'=>'密码重置失败',
		'phone_has_not_reg'=>'该手机还没注册',
		'not_seller'=>'你不是商家，请到PC版去认证',

);
?>