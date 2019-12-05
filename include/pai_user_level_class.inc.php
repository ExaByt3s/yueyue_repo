<?php
/*
 * 用户等级操作类
 */

class pai_user_level_class extends POCO_TDG
{
	var $price = 300;
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		//$this->setServerId ( 101 );
		//$this->setDBName ( 'pai_db' );
		//$this->setTableName ( 'pai_city_tbl' );
	}
	
	/*
	 * 获取用户等级
	 */
	function get_user_level($user_id)
	{
		$user_id = (int)$user_id;
		
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );
		
		//身份证信息
		$id_info = $id_obj->get_id_info($user_id);
		
		//审核信息
		$id_audit_info = $id_audit_obj->get_audit_info($user_id);
		
		
		//信用金
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		if($available_balance>=$this->price && $id_info)
		{
			$level = 3;
		}elseif($id_info)
		{
			$level = 2;
		}else 
		{
			$level = 1;
		}
		
		return $level;
	}
	
	
	/*
	 * 等级认证列表
	 * 
	 */
	function level_list($user_id)
	{
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		
		$level = $this->get_user_level($user_id);
		
		if($level==1)
		{
			$level_arr[0]['level'] = "V1";
			$level_arr[0]['name'] = "手机认证";
			$level_arr[0]['status'] = "已认证";
			
			$level_arr[1]['level'] = "V2";
			$level_arr[1]['name'] = "实名认证";
			$level_arr[1]['status'] = "";
			
			$level_arr[2]['level'] = "V3";
			$level_arr[2]['name'] = "达人认证";
			$level_arr[2]['status'] = "";
		}elseif($level==2)
		{
			$level_arr[0]['level'] = "V1";
			$level_arr[0]['name'] = "手机认证";
			$level_arr[0]['status'] = "已认证";
			
			$level_arr[1]['level'] = "V2";
			$level_arr[1]['name'] = "实名认证";
			$level_arr[1]['status'] = "已认证";
			
			$level_arr[2]['level'] = "V3";
			$level_arr[2]['name'] = "达人认证";
			$level_arr[2]['status'] = "";
		}elseif($level==3)
		{
			$level_arr[0]['level'] = "V1";
			$level_arr[0]['name'] = "手机认证";
			$level_arr[0]['status'] = "已认证";
			
			$level_arr[1]['level'] = "V2";
			$level_arr[1]['name'] = "实名认证";
			$level_arr[1]['status'] = "已认证";
			
			$level_arr[2]['level'] = "V3";
			$level_arr[2]['name'] = "达人认证";
			$level_arr[2]['status'] = "已认证";
		}
		
		//身份证认证状态
		$id_audit_info = $id_audit_obj->get_audit_info($user_id);
		if($id_audit_info['status']==1)
		{
			$level_arr[1]['audit_status'] = "1";
		}
		else
		{
			$level_arr[1]['audit_status'] = "0";
		}
		
		//信用金
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		if($available_balance>=$this->price)
		{
			$level_arr[2]['balance_status'] = "1";
		}
		else
		{
			$level_arr[2]['balance_status'] = "0";
		}
		
		return $level_arr;
	}
	
	
	public function level_detail($user_id)
	{
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );

        $user_id = (int)$user_id;
		
		$id_info = $id_obj->get_id_info($user_id);
		$id_audit_info = $id_audit_obj->get_audit_list(false, "user_id={$user_id} and status=0");
		
		if($id_info)
		{
			$upload = 0;
			$text = "已审核";
			$img = $id_info['img'];
			$is_check = 1;
			$id_code = $id_info['id_code'];
			$name = $id_info['name'];
		}elseif($id_audit_info)
		{
			$upload = 0;
			$text = "上传成功，审核中";
			$img = $id_audit_info[0]['img'];
			$is_check = 0;
		}else {
			$upload = 1;
			$text = "";
			$img = "";
			$is_check = 0;
		}
		
		//信用金
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		if($available_balance>=$this->price)
		{
			$balance_status = 1;
		}else
		{
			$balance_status = 0;
		}
		
		$ret['upload'] = $upload;
		$ret['text'] = $text;
		$ret['img'] = $img;
		$ret['is_check'] = $is_check;
		$ret['balance_status'] = $balance_status;
		$ret['id_code'] = $id_code;
		$ret['name'] = $name;
		
		return $ret;
	}
	
	
	public function send_level_approval_msg($user_id)
	{
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$chat_user_obj = POCO::singleton('pai_chat_user_info');
		
		//信用金
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		//身份证信息
		$id_info = $id_obj->get_id_info($user_id);
		
		if($available_balance>=$this->price && $id_info )
		{
			$content = "好消息，你的【V3达人认证】申请已被通过，你可以发起对V3模特的邀约了，快来试试吧。";
		}elseif($id_info)
		{
			$content = "好消息，你的【V2实名认证】申请已被通过，你可以发起对V2模特的邀约了，快来试试吧。";
			
		}
		
		$chat_user_obj->redis_get_user_info($user_id);
		
		send_message_for_10005 ( $user_id, $content );
	}

	public function send_level_deny_msg($user_id)
	{
		
		$content = "亲：非常抱歉！你的【V2实名认证】申请未被通过。请本人手持身份证进行拍照（单独身份证照片不予通过）。图片标准：能通过图片看清你手中身份证的详细号码、名字、头像（身份证号码不全或看不清楚不予通过）！技巧：拍照时可以伸手让身份证靠近镜头一些哦！请检查图片后重新提交，等你哦！加油加油！";
		$url = "/mobile/app?from_app=1#mine/authentication/v2";
		send_message_for_10005 ( $user_id, $content,$url );
	}
}

?>