<?php
//include_once('poco_app_common.inc.php');
//include_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
/**
 * 约拍提交处理  modify hai 20140911
 * @param array $enroll_data 
 * array(
 *  'user_id'=>'',  用户ID  [非空]
 *  'event_id'=>,   活动ID  [非空]
 *  'phone'=>'',    手机号码
 *  'email'=>'',    邮箱
 *  'remark'=>      备注
 * )
 * @param array $enroll_data  场次数据的二维数组  
 * array(
 *  0=>array(
 *                         
 *    'enroll_num'=>''  [非空]    报名人数
 *    'table_id'=>''    [非空]    场次自增ID
 *    'coupon_sn'=>''             优惠码
 *  
 *  ),
 *  1=>array(...
 * )
 * @param int    $user_balance 用户余额  用于判断用户是否停留页面太长时间没提交  而用户余额变动后再提交
 * @param int    $is_available_balance   0为余额支付 1为第三方支付   如果余额不够支付将需要继续跳转到第三方继续支付
 * @param string $third_code   第三方支付的标识 现暂时支持微信和支付宝钱包 alipay_purse、tenpay_wxapp 当用户使用余额全额支付时可为空
 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
 * @param string $notify_url 支付成功后异步的url，为空时使用配置文件中的处理页
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * 返回值 status_code 为状态 
 * status_code错误值 
	 * -1  参数错误
	 * -2  该活动不存在 
	 * -3  活动已结束
	 * -4  参与者为组织者  禁参与
	 * -5  参与人数非法
	 * -6  某一场已经报过名
     * -7  某一个场次已关闭 不允许再报名
     * -8  报名产生错误  报名失败
     * -9  报名已完成并支付  报名失败
	 * -10  用户余额有变动
	 * -11  余额支付失败
	 * -12 跳转到第三方支付产生错误
 * status_code正确值
 *   1为提交成功 待组织者审批
 *   2为余额支付成功   
 *   3为生成请求参数成功，待跳转到第三方。
 * message返回的消息 cur_balance 返回用户当前真实余额[当status_code==2或余额支付成功才有此key] 
 * request_data 第三方发起请求的字符串[需要发起请求时候才返回]
 *
 */
function add_enroll_op($enroll_data,$sequence_data,$user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url='')
{

    $enroll_data['user_id'] = intval($enroll_data['user_id']);
    if ( empty($enroll_data['user_id']) )  
    {
		$ret = array( 'status_code'=>-1,'message'=>'user_id 格式错误' );
		return $ret;
    }
    $enroll_data['event_id'] = intval($enroll_data['event_id']);
    if ( empty($enroll_data['event_id']) )  
    {
		$ret = array( 'status_code'=>-1,'message'=>'event_id 格式错误' );
		return $ret;
    }
    if ( empty($sequence_data) ) 
    {
		$ret = array( 'status_code'=>-1,'message'=>'sequence_data 格式错误' );
		return $ret;
    }
    if( !in_array($is_available_balance,array(0,1)) ){

    	$ret = array( 'status_code'=>-1,'message'=>'is_available_balance 格式错误' );
		return $ret;

    }
    
    $user_obj      = POCO::singleton('pai_user_class');
    $payment_obj   = POCO::singleton('pai_payment_class');
    $enroll_obj    = POCO::singleton('event_enroll_class');
    $account_info  = $payment_obj->get_user_account_info($enroll_data['user_id']);
	if( $account_info['available_balance'] != $user_balance  ){

		$user_info  = $user_obj->get_user_info_by_user_id($enroll_data['user_id']);
	    $ret 		= array( 'status_code'=>-10,'message'=>'用户余额有变动','user_info'=>$user_info );
		return $ret;    	

	}

	$enroll_data_v2				= $enroll_data;
	$enroll_data_v2['user_id']  = get_relate_poco_id($enroll_data['user_id']);
	//转换为poco id 入库
    $enroll_ret					= $enroll_obj->add_enroll_v3($enroll_data_v2,$sequence_data);
    if( $enroll_ret['status_code'] != 1 ){

		$ret = array( 'status_code'=>$enroll_ret['status_code'], 'message'=>$enroll_ret['message'], 'enroll_ret'=>$enroll_ret );
		return $ret;

    }
	if( $enroll_ret['join_mode_auth'] == false ){
		//没有权限的情况 pc版的需要审批的列表不出现  理论上不会出现(['join_mode_auth'] == false)这个情况
		$ret = array( 'status_code'=>1,'message'=>'报名成功 待组织者确认' );
		return $ret;

	}
	//等于1为参加活动成功
	$enroll_id_arr 		= $enroll_ret['enroll_id_arr'];
	$enroll_cost_detail = $enroll_obj->get_enroll_cost_by_arr( $enroll_id_arr );
	$sum_cost 			= $enroll_cost_detail['total_cost'];
	if( $sum_cost<=0 )
	{
		//日志
		pai_log_class::add_log(array( 'enroll_ret'=>$enroll_ret, 'enroll_cost_detail'=>$enroll_cost_detail), 'add_enroll_op error', 'enroll_act');
		
		$ret = array( 'status_code'=>-10, 'message'=>'总金额错误' );
		return $ret;
	}
	
	//日志
	pai_log_class::add_log(array( 'enroll_ret'=>$enroll_ret, 'enroll_cost_detail'=>$enroll_cost_detail), 'add_enroll_op ok', 'enroll_act');
	
	$sum_discount 		= $enroll_cost_detail['total_discount']; //计算优惠金额
	$sum_pending        = bcsub($sum_cost, $sum_discount, 2);
	if( $sum_pending<=0 )
	{
		$ret = array( 'status_code'=>-10, 'message'=>'优惠金额错误' );
		return $ret;
	}

	if($is_available_balance == 0 ){
		//0为余额支付
		if( bccomp($account_info['available_balance'],$sum_pending,2)==-1 ){
			//余额不足，跳转去充值
			$amount   		= $sum_pending - $account_info['available_balance'];
			$redirect_third = 1; 

		}	
		else{

			$ret =  $payment_obj->pay_enroll_by_balance($enroll_data['event_id'],$enroll_id_arr);
			if( $ret['error'] == 0 ){

				$user_info  = $user_obj->get_user_info_by_user_id($enroll_data['user_id']);
				$ret 		= array( 'status_code'=>2,'message'=>'余额支付成功！','user_info'=>$user_info);
				return $ret;
			
			}
			else{

				$ret = array( 'status_code'=>-11,'message'=>'余额支付失败' );
				return $ret;

			}
		
		}

	}
	else{
		//直接用支付宝支付
		$amount = $sum_pending;
		$redirect_third = 1; 

	}
	if($redirect_third){

	    if( !in_array($third_code,array('alipay_purse','alipay_wap','tenpay_wxapp','tenpay_wxpub')) ){

	    	$ret = array( 'status_code'=>-1,'message'=>'third_code 支付标识错误' );
			return $ret;    	

	    }
	    $more_info = array();
		$more_info['channel_return'] = $redirect_url;
		$more_info['channel_notify'] = $notify_url;
		/*获取openid*/
		$openid = '';
		if ($third_code == 'tenpay_wxpub')
		{
			
			$bind_weixin_obj = POCO::singleton ( 'pai_bind_weixin_class' );
			$bind_info 		 = $bind_weixin_obj->get_bind_info_by_user_id ( $enroll_data['user_id'] );
			$openid 		 = $bind_info ['open_id'];
			if (empty ( $openid ))
			{
				
				$ret = array ('status_code' => - 1, 'message' => '微信用户没绑定约约账号' );
				return $ret;
			
			}
		
		}
		$more_info ['openid'] = $openid;
		/*获取openid*/

		$enroll_id_str = implode(',', $enroll_id_arr);
		$recharge_ret  = $payment_obj->submit_recharge('activity',$enroll_data['user_id'],$amount,$third_code,$enroll_data['event_id'],$enroll_id_str,0,$more_info);	
		
		if( $recharge_ret['error']===0 )
		{
			$payment_no = trim($recharge_ret['payment_no']);
			$request_data = trim($recharge_ret['request_data']);
			$ret 		  = array( 'status_code'=>3,'message'=>'添加约拍成功,需跳转到第三方支付。','request_data'=>$request_data,'payment_no'=>$payment_no);
			return $ret;

		}
		else
		{
			$ret = array( 'status_code'=>-12,'message'=>$recharge_ret['message'],'recharge_ret'=>$recharge_ret );
			return $ret;
		}

	}

}	

/**
 * 约拍报名，继续支付
 * @param array $enroll_data
 * array(
 *  'user_id'=>'',  用户ID  [非空]
 *  'event_id'=>,   活动ID  [非空]
 * )
 * @param array $enroll_id_arr  报名ID
 * array(
 *  1,2
 * )
 * @param int    $user_balance 用户余额  用于判断用户是否停留页面太长时间没提交  而用户余额变动后再提交
 * @param int    $is_available_balance   0为第三方支付  1为余额支付  如果余额不够支付将需要继续跳转到第三方继续支付
 * @param string $third_code   第三方支付的标识 现暂时支持微信和支付宝钱包 alipay_purse、tenpay_wxapp 当用户使用余额全额支付时可为空
 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
 * @param string $notify_url 支付成功后异步的url，为空时使用配置文件中的处理页
 * @param string $coupon_sn 优惠码
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * 返回值 status_code 为状态
 * status_code错误值
 * -1  参数错误
 * -2  该活动不存在
 * -3  活动已结束
 * -10 用户余额有变动
 * -11 余额支付失败
 * -12 跳转到第三方支付产生错误
 * status_code正确值
 *   1为余额支付成功
 *   2为生成请求参数成功，待跳转到第三方。
 * message返回的消息 cur_balance 返回用户当前真实余额[当status_code==2或余额支付成功才有此key]
 * request_data 第三方发起请求的字符串[需要发起请求时候才返回]
 *
 */
function again_enroll_op($enroll_data, $enroll_id_arr, $user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url='',$coupon_sn='')
{
	$enroll_data['user_id'] = intval($enroll_data['user_id']);
	if ( empty($enroll_data['user_id']) )
	{
		$ret = array( 'status_code'=>-1,'message'=>'user_id 格式错误' );
		return $ret;
	}
	$enroll_data['event_id'] = intval($enroll_data['event_id']);
	if ( empty($enroll_data['event_id']) )
	{
		$ret = array( 'status_code'=>-1,'message'=>'event_id 格式错误' );
		return $ret;
	}
	if( !is_array($enroll_id_arr) || empty($enroll_id_arr) )
	{
		$ret = array( 'status_code'=>-1,'message'=>'enroll_id_arr 格式错误' );
		return $ret;
	}
	
	$payment_obj   = POCO::singleton('pai_payment_class');
	$details_obj   = POCO::singleton('event_details_class');
	$enroll_obj    = POCO::singleton('event_enroll_class');
	$user_obj      = POCO::singleton('pai_user_class');
	$event_info    = $details_obj->get_event_by_event_id($enroll_data['event_id']);
	 if(empty($event_info) ){

            $ret = array( 'status_code'=>-2,'message'=>'活动不存在 非法的event_id' );
            return $ret;

     }
    if($event_info['event_status'] != 0){

        $ret = array( 'status_code'=>-3,'message'=>'活动已处于开始或结束的状态  不可以再报名' );
        return $ret;

    }
    $account_info  = $payment_obj->get_user_account_info($enroll_data['user_id']);
	if( $account_info['available_balance'] != $user_balance  ){

		//http://pai.poco.cn/mobile/action/join_again_act.php 页面没有传 available_balance  需要阿鼎补上再去掉
		//$user_info  = $user_obj->get_user_info_by_user_id($enroll_data['user_id']);
	    //$ret 		= array( 'status_code'=>-10,'message'=>'用户余额有变动','user_info'=>$user_info[0] );
		//return $ret;    	

	}
	
	//处理优惠券，注意：此方法已经不支持多个报名ID，只支持一个报名ID了。
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$channel_module = 'waipai';
	$channel_oid = $enroll_id_arr[0];
	$module_type = 'waipai';
	
	//不使用
	$coupon_obj->not_use_coupon_by_oid($channel_module, $channel_oid);
	
	//使用优惠券
	$order_total_amount = $enroll_obj->get_enroll_cost($channel_oid);
	if( !empty($coupon_sn) )
	{
		$event_user_id = get_relate_yue_id($event_info['user_id']);
		$param_info = array(
			'module_type' => $module_type, //模块类型 waipai yuepai topic
			'order_total_amount' => $order_total_amount, //订单总金额
			'model_user_id' => 0, //模特用户ID（约拍、专题）
			'org_user_id' => $event_info['org_user_id'], //机构用户ID
			'location_id' => $event_info['location_id']*1, //地区ID
			'event_id' => $enroll_data['event_id'], //活动ID
			'event_user_id' => $event_user_id, //活动组织者用户ID
			'seller_user_id' => $event_user_id, //商家用户ID，兼容商城系统
		);
		$use_ret = $coupon_obj->use_coupon($enroll_data['user_id'], 1, $coupon_sn, $channel_module, $channel_oid, $param_info);
		if( $use_ret['result']!=1 )
		{
			$ret = array( 'status_code'=>-10, 'message'=>$use_ret['message'] );
			return $ret;
		}
		$discount_price = $use_ret['used_amount'];
		$is_use_coupon = 1;
	}
	else
	{
		//上次可能有使用优惠券，所以这次置零
		$discount_price = 0;
		$is_use_coupon = 0;
	}
	$enroll_obj->update_enroll(array('original_price'=>$order_total_amount, 'discount_price'=>$discount_price, 'is_use_coupon'=>$is_use_coupon), $channel_oid);
	
	//等于1为参加活动成功
	$enroll_cost_detail = $enroll_obj->get_enroll_cost_by_arr( $enroll_id_arr );
	$sum_cost 			= $enroll_cost_detail['total_cost'];
	if( $sum_cost<=0 )
	{
		//日志
		pai_log_class::add_log(array( 'enroll_id_arr'=>$enroll_id_arr, 'enroll_cost_detail'=>$enroll_cost_detail), 'again_enroll_op error', 'enroll_act');
		
		$ret = array( 'status_code'=>-10, 'message'=>'总金额错误' );
		return $ret;
	}
	
	//日志
	pai_log_class::add_log(array('enroll_id_arr'=>$enroll_id_arr, 'enroll_cost_detail'=>$enroll_cost_detail), 'again_enroll_op ok', 'enroll_act');
	
	$sum_discount 		= $enroll_cost_detail['total_discount']; //计算优惠金额
	$sum_pending        = bcsub($sum_cost, $sum_discount, 2);
	if( $sum_pending<=0 )
	{
		$ret = array( 'status_code'=>-10, 'message'=>'优惠金额错误' );
		return $ret;
	}
	
	if($is_available_balance){

		if( bccomp($account_info['available_balance'],$sum_pending,2)==-1 ){
			//余额不足，跳转去充值
			$amount   		= $sum_pending - $account_info['available_balance'];
			$redirect_third = 1;

		}
		else{

			$ret =  $payment_obj->pay_enroll_by_balance($enroll_data['event_id'],$enroll_id_arr);
			if( $ret['error'] == 0 ){

				$ret = array( 'status_code'=>1,'message'=>'余额支付成功！','cur_balance'=>$account_info['available_balance'] );
				return $ret;
					
			}
			else{

				$ret = array( 'status_code'=>-11,'message'=>'余额支付失败' );
				return $ret;

			}

		}

	}
	else{
		//直接用支付宝支付
		$amount = $sum_pending;
		$redirect_third = 1;

	}
	if($redirect_third){

		if( !in_array($third_code,array('alipay_purse','alipay_wap','tenpay_wxapp','tenpay_wxpub')) ){

			$ret = array( 'status_code'=>-1,'message'=>'third_code 支付标识错误' );
			return $ret;

		}
		$more_info = array();
		$more_info['channel_return'] = $redirect_url;
		$more_info['channel_notify'] = $notify_url;
		/*获取openid*/
		$openid = '';
		if ($third_code == 'tenpay_wxpub')
		{
			
			$bind_weixin_obj = POCO::singleton ( 'pai_bind_weixin_class' );
			$bind_info 		 = $bind_weixin_obj->get_bind_info_by_user_id ( $enroll_data['user_id'] );
			$openid 		 = $bind_info ['open_id'];
			if (empty ( $openid ))
			{
				
				$ret = array ('status_code' => - 1, 'message' => '微信用户没绑定约约账号' );
				return $ret;
			
			}
		
		}
		$more_info ['openid'] = $openid;
		/*获取openid*/
		$enroll_id_str = implode(',', $enroll_id_arr);
		$recharge_ret  = $payment_obj->submit_recharge('activity',$enroll_data['user_id'],$amount,$third_code,$enroll_data['event_id'],$enroll_id_str, 0, $more_info);

		if( $recharge_ret['error']===0 )
		{
			$payment_no = trim($recharge_ret['payment_no']);
			$request_data = trim($recharge_ret['request_data']);
			$ret 		  = array( 'status_code'=>2,'message'=>'继续支付,需跳转到第三方支付。','request_data'=>$request_data,'payment_no'=>$payment_no );
			return $ret;

		}
		else
		{
			$ret = array( 'status_code'=>-12,'message'=>$recharge_ret['message'],'recharge_ret'=>$recharge_ret );
			return $ret;
		}

	}

}

/**
 * 
 * 删除报名
 * @param $enroll_id 报名表主键
 * 
 * */
function del_enroll($enroll_id)
{
	$enroll_obj     = POCO::singleton('event_enroll_class');
	$details_obj    = POCO::singleton('event_details_class');
	$activity_code_obj    = POCO::singleton('pai_activity_code_class');
	$payment_obj   = POCO::singleton('pai_payment_class');
	$coupon_obj = POCO::singleton('pai_coupon_class');
    $enroll_id 		= (int)$enroll_id;
    //检查活动状态
    $enroll_info    = $enroll_obj->get_enroll_by_enroll_id($enroll_id);
    $event_id       = $enroll_info[0]['event_id'];
    $event_info     = $details_obj->get_event_by_event_id($event_id);
    
    $channel_module = "waipai";
    
    if( $event_info['event_status'] == 0 )
    {
    	//活动码是否有至少一个被扫过
    	$is_check = $activity_code_obj->check_code_scan($enroll_id);
    	
    	
        if(!$is_check && $enroll_info['pay_status'] == 1)
        {
            $payment_obj->closed_trade($enroll_id);
        }
        
        $coupon_obj->not_use_coupon_by_oid($channel_module, $enroll_id);
        

		$log_arr['enroll_info'] = $enroll_info;

		pai_log_class::add_log($log_arr, 'del_enroll', 'del_enroll');
        
        return $enroll_obj->del_enroll($enroll_id);              
    }
    else{
    	//活动开始后未付款可以取消报名，已付款不能取消报名
        if($enroll_info['pay_status'] == 0)
        {	
        	$coupon_obj->not_use_coupon_by_oid($channel_module, $enroll_id);
            return $enroll_obj->del_enroll($enroll_id);
        }else{
        	return false;
        }

    }
    

}

/**
 * 格式数据
 * @param array $row 
 * return array
 *
 */
function format_enroll_item( $rows ) {

	if( !empty($rows) ) {
		
		if(!is_array(current($rows)))
		{
			$rows = array($rows);
			$is_single = true;
		}
		foreach ($rows as $k=>$v)
		{
			$rows[$k]['user_id'] =  get_relate_yue_id( $v['user_id'] );
		}			
	}
	
	if($is_single)	
		return $rows[0];
	else
		return $rows;
	
}

/**
 * 取列表
 *
 * @param string $where_str    查询条件
 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
 * @param string $limit        查询条数
 * @param string $order_by     排序条件
 * @return array|int
 */
function get_enroll_list($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'enroll_id DESC',$fields="*")
{
    $enroll_obj = POCO::singleton('event_enroll_class');
    $list		= $enroll_obj->get_enroll_list($where_str, $b_select_count, $limit, $order_by,$fields);
	$list		= format_enroll_item($list);
	return $list;

}

function get_enroll_by_enroll_id($enroll_id)
{
	$enroll_obj  = POCO::singleton('event_enroll_class');
	$enroll_info = $enroll_obj->get_enroll_by_enroll_id($enroll_id);
	$enroll_info = format_enroll_item($enroll_info);
	return $enroll_info;

}


/*
 * 获取用户活动报名状态列表
 * @param int $user_id
 * @param string $status 未付款：unpaid 已付款：paid
 * @param bool $b_select_count
 * @param string $limit
 */
function get_enroll_list_by_status($user_id,$status='unpaid',$b_select_count=false,$limit ='0,10')
{	
	$yue_user_id	= $user_id;
	$user_id		= get_relate_poco_id($user_id);
	$table_obj      = POCO::singleton('event_table_class');
	$details_obj    = POCO::singleton('event_details_class');
	$event_comment_log_obj    = POCO::singleton('pai_event_comment_log_class');
	$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$enroll_obj = POCO::singleton('event_enroll_class');
	
	if(!in_array($status,array("unpaid","paid")))
		return false;
		
	$user_id = (int)$user_id;
	
	switch ($status) {
		case "unpaid":
			$where_str = "user_id={$user_id} and pay_status=0 and status=3 and table_id!=0 and event_remark!='活动取消'";
		break;
		
		case "paid":
			$where_str = "user_id={$user_id} and pay_status=1 and status in (0,1) and table_id!=0 and event_remark!='活动取消'";
		break;
	}
	
	$ret = get_enroll_list($where_str, $b_select_count, $limit);
	
	if($b_select_count==false){
		foreach($ret as $k=>$val){

			$event_detail				= $details_obj->get_event_by_event_id ( $val['event_id'] );
			$event_detail['user_id']    = $event_detail['user_id'];
			$event_detail['nickname']   = get_user_nickname_by_user_id(get_relate_yue_id( $event_detail['user_id']));
			$event_detail['start_time'] = date("Y-m-d",$event_detail['start_time']);
			
			if ($event_detail ['is_authority'] == 1) {
				$event_detail ['is_authority'] = 1;
				$event_detail ['is_recommend'] = 0;
				$event_detail ['is_free'] = 0;
			} elseif ($event_detail ['is_recommend'] == 1) {
				$event_detail ['is_authority'] = 0;
				$event_detail ['is_recommend'] = 1;
				$event_detail ['is_free'] = 0;
			} elseif (( int ) $event_detail ['budget'] == 0) {
				$event_detail ['is_authority'] = 0;
				$event_detail ['is_recommend'] = 0;
				$event_detail ['is_free'] = 1;
			} else {
				$event_detail ['is_authority'] = 0;
				$event_detail ['is_recommend'] = 0;
				$event_detail ['is_free'] = 0;
			}
			
			//是否已评价活动
			$check_comment = $event_comment_log_obj->is_event_comment_by_user($val['event_id'], $val['table_id'],$yue_user_id);
			
			if($check_comment){
				$event_detail['is_comment'] = 1;
			}else{
				$event_detail['is_comment'] = 0;
			}
			
			
			if($status=='paid'){
		       
		        //判断是否活动已结束
		        if( $event_detail['event_status']!=0){
		        	$event_detail['is_end'] = 1;
		        }else{
		        	$event_detail['is_end'] = 0;
		        }
		        
		        
				//活动是否有至少一个报名者扫码
				$check_scan = $activity_code_obj->check_event_code_scan ( $val ['event_id'] );
				//是否已全部签到
				$check_all_scan = $activity_code_obj->check_is_all_scan($val ['enroll_id']);
				
				if ($check_all_scan)
				{
					$event_detail ['is_scan'] = 2;
				} elseif($check_scan)
				{
					$event_detail ['is_scan'] = 1;
				}else{
					$event_detail ['is_scan'] = 0;
				}
				
				//1.0.5改由接口判断按钮
				if($event_detail['event_status']==='0')
				{
					if(!$check_all_scan)
					{
						$event_detail ['enroll_code_button'] = 1;
					}
					else
					{
						$event_detail ['enroll_code_button'] = 0;
					}
					
				}elseif($event_detail['event_status']==='2')
				{
					$event_detail ['enroll_code_button'] = 0;
					
					$check_code_scan = $activity_code_obj->check_code_scan($val ['enroll_id']);
					
					if($check_code_scan)
					{
						if($check_comment)
						{
							$event_detail ['enroll_comment_button'] = 0;
						}
						else
						{
							$event_detail ['enroll_comment_button'] = 1;
						}
					}
					else
					{
						$event_detail ['enroll_comment_button'] = 0;
					}
				}
				else
				{
					$event_detail ['enroll_code_button'] = 0;
					$event_detail ['enroll_comment_button'] = 0;
				}

			}
			elseif ($status=='unpaid')
			{
				if($event_detail['event_status']==='0')
				{
					$event_detail ['enroll_pay_button'] = 1;
					$event_detail ['enroll_cancel_button'] = 1;
					$event_detail ['enroll_code_button'] = 0;
				}
				else
				{
					$event_detail ['enroll_pay_button'] = 0;
					$event_detail ['enroll_cancel_button'] = 0;
					$event_detail ['enroll_code_button'] = 0;
				}
			}
			
			
			$comment = $event_comment_log_obj->get_comment_list(false, 'event_id='.$val['event_id'].' and table_id='.$val['table_id'].' and user_id='.$yue_user_id);
			//活动评分
			$event_detail['score'] = (int)$comment[0]['overall_score'];
			
			// 评价评分星星
			$comment_has_star = intval($comment[0]['overall_score']);
			$comment_miss_star = 5 - $comment_has_star;
		
			for ($i=0; $i < 5; $i++) 
			{
				if($comment_has_star>0)
				{
					$event_detail['stars_list'][$i]['is_red'] = 1; 	
					$comment_has_star--;
				}
				else
				{
					$event_detail['stars_list'][$i]['is_red'] = 0; 	
					$comment_miss_star--;						
				}
			}
			

			$limit_num = $table_obj->sum_table_num($val['event_id']);
			
			$join_num = get_enroll_list( "event_id=".$val['event_id']." and status in (0,1)", true);
			
			$join_num = $enroll_obj->sum_enroll_num($val['event_id'],0,'0,1');
			
			$event_detail ['event_join'] = $join_num.'/'.$limit_num;
			
			
			$event_detail['table_id'] = $val['table_id'];
			
			
			$table_arr     = $table_obj->get_event_table_num_array($val['event_id']);
			$table_num     = $table_arr[$val['table_id']];
			$event_detail['title'] = $event_detail['title']." 第".$table_num."场";
			 
			$ret[$k]['event_detail'] = $event_detail;
		
		}
	}
	
	return $ret;
}


/**
 * 
 * 检查该活动该场次是否已经满人
 * @param $enroll_num 报名人数
 * @param $event_id   活动ＩＤ
 * @param $table_id   活动表ＩＤ　
 * 
 * */
function check_is_full($enroll_num=1,$event_id, $table_id)
{
    $enroll_obj     = POCO::singleton('event_enroll_class');
    return $enroll_obj->check_is_full($enroll_num,$event_id, $table_id);
}

/**
 * 检查用户在同一个活动中是否已经报名
 *
 * @param int $user_id
 * @param int $event_id
 * @param string $status
 * @param int $table_id
 * @return bool
 */
function check_duplicate($user_id,$event_id,$status="all", $table_id=0)
{
	$user_id		= get_relate_poco_id($user_id);
	$enroll_obj     = POCO::singleton('event_enroll_class');
	return $enroll_obj->check_duplicate($user_id,$event_id,$status, $table_id);
}

/**
 * 
 * 活动该报名ＩＤ的所需金额
 * @param $enroll_id　报名表主键
 * 
 * */
function get_enroll_cost($enroll_id)
{
     $enroll_obj     = POCO::singleton('event_enroll_class');
     return $enroll_obj->get_enroll_cost($enroll_id);
}

/**
 * 
 * 更新报名表的支付状态
 * @param $enroll_id  报名表主键
 * @param $status 支付状态，0=>代支付，1=>已支付
 * 
 * */
function update_enroll_pay_status($enroll_id, $status)
{
    $enroll_obj     = POCO::singleton('event_enroll_class');
    return $enroll_obj->update_enroll_pay_status($enroll_id, $status);
}

/**
 * 
 *　自动状态更新接口
 *  暂时不可用
 * 
 * */
function auto_event_enroll_handling($event_id)
{
    $enroll_obj     = POCO::singleton('event_enroll_class');
    return $enroll_obj->auto_event_enroll_handling($event_id);
}


/**
 * 返回报名列表与活动ＩＣＯＮ信息
 * @param Array $where_array
 * @param $type_icon
 * @param $limit 条数
 * 
 * */
function get_enroll_list_and_event_info($where_array = '', $type_icon = '', $limit)
{
    $enroll_obj					= POCO::singleton('event_enroll_class');
	$where_array['user_id']		= get_relate_poco_id($where_array['user_id']);
    return $enroll_obj->get_enroll_list_and_event_info($where_array, $type_icon, $limit);
}

/*
 * 获取活动券
 * @param bool $b_select_count 
 * @param int $user_id
 * @param string $limit
 */
function get_act_ticket($b_select_count=false,$user_id,$limit='0,1000')
{
	$user_id		= get_relate_poco_id($user_id);
	$code_obj		= POCO::singleton('pai_activity_code_class');
	$details_obj    = POCO::singleton('event_details_class');
	$table_obj		= POCO::singleton('event_table_class');
	$enroll_list = get_enroll_list("user_id=$user_id and status in (0,1)", false, '0,10000', 'enroll_id DESC',"enroll_id");
	
	foreach($enroll_list as $val){
		$enroll_id_arr[] = $val['enroll_id'];
	}
	
	$enroll_id_str = implode(",",$enroll_id_arr);
	
	if($enroll_id_str){

		$where_code = "enroll_id in ({$enroll_id_str}) and is_checked=0 group by event_id,enroll_id";
		$code_arr = $code_obj->get_code_list(false, $where_code, 'id DESC', $limit, 'event_id,enroll_id');

	}
	foreach($code_arr as $k=>$val){

		$event_info = $details_obj->get_event_by_event_id($val['event_id']);
		//只显示报名中的活动券
		if($event_info['event_status']=='0'){
	
			$qr_code = $code_obj->create_qr_code($val['event_id'],$val['enroll_id']);
			$new_code_arr[$k]['qr_code'] = $qr_code;
			//获取未签到活动券
			$code = $code_obj->get_code_by_enroll_id_by_status($val['enroll_id'], 0);
			$new_code_arr[$k]['code_arr'] = $code;
			$event_info['user_id']		  = get_relate_yue_id($event_info['user_id']);	//转换成yueyue id
			$event_info['nick_name']	  = get_user_nickname_by_user_id($event_info['user_id']);
			$event_info['start_time'] = date("Y-m-d",$event_info['start_time']);
			$event_info['end_time'] = date("Y-m-d",$event_info['end_time']);
			
			if($event_info['type_icon']=='yuepai_app'){
				$date_info = get_event_date("enroll_id", $val['enroll_id']);
				$event_info['title'] = "模特".$event_info['nick_name'];
				$event_info['start_time'] = date("Y-m-d",$date_info[0]['date_time']);
			}else{
				$enroll_arr = get_enroll_by_enroll_id($val['enroll_id']);
				$table_id = $enroll_arr['table_id'];
				$table_num_arr = $table_obj->get_event_table_num_array($val['event_id']);
				
				$event_info['title'] = $event_info['title']." 第".$table_num_arr[$table_id]."场";
			}
			
			$new_code_arr[$k]['event_info'] = $event_info;
		
		}
	}
	
	
    if($b_select_count==true)
    {
    	return (int)count($new_code_arr);
    }


    return $new_code_arr;
    
}

/*
 * 获取用户活动券总数
 */
function count_act_ticket($user_id)
{
	$user_id = (int)$user_id;
	$code_obj = POCO::singleton('pai_activity_code_class');
	
	$where = "enroll_user_id={$user_id} and is_checked=0";
	$code_arr = $code_obj->get_code_list(false, $where ,'id DESC', '0,10000', 'event_id');

    return curl_event_data('event_api_class','count_act_ticket',array($code_arr));

}


/*
 * 根据event_id enroll_id获取活动券
 * @param int $user_id
 * return array
 */
function get_act_ticket_detail($event_id,$enroll_id){
	$event_id = (int)$event_id;
	$enroll_id = (int)$enroll_id;
	
	$code_obj = POCO::singleton('pai_activity_code_class');
	
	$code_arr = $code_obj->get_code_by_enroll_id_by_status($enroll_id,0);
	$qr_code_arr = $code_obj->create_qr_code($event_id,$enroll_id);
	 
	foreach ($code_arr as $k=>$val){
		$new_code_arr[$k]['qr_code'] = $qr_code_arr[$k];
		$new_code_arr[$k]['code'] = $val['code'];
		
		$code = $val['code'];
		$event_id = $val['event_id'];
		$enroll_id = $val['enroll_id'];
		$hash = qrcode_hash ( $event_id, $enroll_id, $code );	
		$jump_url = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$event_id}&enroll_id={$enroll_id}&code={$code}&hash={$hash}";
		
		$new_code_arr[$k]['qr_code_url'] = $jump_url;
	}
	
	return $new_code_arr;
}



/*
 * 根据活动场次获取签到列表
 * @param $event_id　活动ID
 */
function get_mark_list($event_id){

	$user_obj    = POCO::singleton('pai_user_class');
	$table_obj   = POCO::singleton('event_table_class');
	$code_obj    = POCO::singleton('pai_activity_code_class');
	$details_obj = POCO::singleton('event_details_class');
	$event_info  = $details_obj->get_event_by_event_id($event_id);
	$table_arr = $table_obj->get_event_table($event_id,0);
	$status_arr = array("0"=>"first","1"=>"backup","3"=>"onlook");
	$table_num_arr = $table_obj->get_event_table_num_array($event_id);
	
	foreach($table_arr as $k=>$val){
		$table_id = $val['id'];
		$check_num_key = 0;
		foreach($status_arr as $status=>$status_name){

			$where_str = "event_id={$event_id} and status={$status} and table_id={$table_id}";
			$enroll_list = get_enroll_list($where_str, false, '0,10000', 'enroll_id DESC',"*");
			foreach($enroll_list as $enroll_key=>$enroll_val){

				$enroll_list[$enroll_key]['user_icon_165']  = get_user_icon($enroll_val['user_id'],165);
				$enroll_list[$enroll_key]['user_icon_468']  = get_user_icon($enroll_val['user_id'],468);
				$enroll_list[$enroll_key]['nick_name']		=  get_user_nickname_by_user_id($enroll_val['user_id']);
				$enroll_user_info = $user_obj->get_user_info($enroll_val['user_id']);
				$enroll_list[$enroll_key]['role'] = $enroll_user_info['role'];
				
				//找出已签到的人
				$get_is_checked_user = $code_obj->count_code_is_checked(false,$enroll_val['enroll_id'],'0,10000');
				//print_r($get_is_checked_user);
				foreach($get_is_checked_user as $checked_val){

					$check_user_arr[$check_num_key]['enroll_id']	  = $checked_val['enroll_id'];
					$check_user_arr[$check_num_key]['is_checked_num'] = $checked_val['c'];
					$enroll_info									  = get_enroll_by_enroll_id($checked_val['enroll_id']);
					$check_user_arr[$check_num_key]['user_icon_165']  = get_user_icon($enroll_info['user_id'],165);
					$check_user_arr[$check_num_key]['user_icon_468']  = get_user_icon($enroll_info['user_id'],468);;
					$check_user_arr[$check_num_key]['nick_name']	  = get_user_nickname_by_user_id($enroll_info['user_id']);
					$check_user_info = $user_obj->get_user_info($enroll_info['user_id']);
					$check_user_arr[$check_num_key]['role']  = $check_user_info['role'];
					$check_num_key++;

				}

			}
			$new_status_arr["is_checked"]['enroll_list'] = $check_user_arr;
			$new_status_arr[$status_name]['enroll_list'] = $enroll_list;
			$new_status_arr[$status_name]['status_name'] = $status_arr[$status];
		}
		
		$table_arr[$k]['table_name'] = "第".$table_num_arr[$table_id]."场 ".date("m.d H:i",$val['begin_time'])."-".date("m.d H:i",$val['end_time']);
		$table_arr[$k]['enroll_arr'] = $new_status_arr;
		$table_arr[$k]['event_title'] = $event_info['title'];
		unset($check_user_arr,$new_status_arr);
	}

	return $table_arr;
}

/*
 * 活动签到列表（1.0.5版）
 */
function get_mark_list_v2($event_id){
	$user_obj    = POCO::singleton('pai_user_class');
	$table_obj   = POCO::singleton('event_table_class');
	$code_obj    = POCO::singleton('pai_activity_code_class');
	$details_obj = POCO::singleton('event_details_class');
	$event_info  = $details_obj->get_event_by_event_id($event_id);
	$table_arr = $table_obj->get_event_table($event_id,0);
	$status_arr = array("0"=>"first","1"=>"backup","3"=>"onlook");
	$table_num_arr = $table_obj->get_event_table_num_array($event_id);
	
	global $yue_login_id;
	$yue_login_id = (int)$yue_login_id;
	
	foreach($table_arr as $k=>$val){
		$table_id = $val['id'];
		$table_num = $val['num'];
		$check_num_key = 0;
		foreach($status_arr as $status=>$status_name){

			$where_str = "event_id={$event_id} and status={$status} and table_id={$table_id}";
			$enroll_list = get_enroll_list($where_str, false, '0,10000', 'enroll_id DESC',"*");
			foreach($enroll_list as $enroll_key=>$enroll_val){

				$enroll_list[$enroll_key]['user_icon_165']  = get_user_icon($enroll_val['user_id'],165);
				$enroll_list[$enroll_key]['user_icon_468']  = get_user_icon($enroll_val['user_id'],468);
				
				if($enroll_val['user_id'])
				{
					$enroll_list[$enroll_key]['nick_name']		=  get_user_nickname_by_user_id($enroll_val['user_id']);
					$enroll_list[$enroll_key]['user_id']		=  $enroll_val['user_id'];
				}
				else
				{
					$enroll_list[$enroll_key]['nick_name']		=  'POCO用户'.substr($enroll_val['enroll_id'],-4);
					$enroll_list[$enroll_key]['user_id']		=  '空';
				}
				
				$enroll_user_info = $user_obj->get_user_info($enroll_val['user_id']);
				$enroll_list[$enroll_key]['role'] = $enroll_user_info['role'];
				
				unset($enroll_list[$enroll_key]['phone']);
				
				$yue_poco_id = get_relate_poco_id($yue_login_id);
				
				if($event_info['user_id']==$yue_poco_id)
				{
					$enroll_list[$enroll_key]['cellphone'] =  $enroll_user_info['cellphone'];
				}
				else
				{
					$enroll_list[$enroll_key]['cellphone'] =  '';
				}
				
				
				$enroll_list[$enroll_key]['text'] = "(共".$enroll_val['enroll_num']."人)";
				

				$count_checked = $code_obj->count_code_is_checked(true, $enroll_val['enroll_id'],'0,99999',true);
				
				if($count_checked>0)
				{				
					if($enroll_val['enroll_num']==1)
					{
						$enroll_list[$enroll_key]['mark_text'] = '已签到';
					}
					else
					{
						$enroll_list[$enroll_key]['mark_text'] = '已签到 '.$count_checked;
					}
				}
				
				if(in_array($status_name,array('first','backup')))
				{
					$total_join += $enroll_val['enroll_num'];
				}
				
				$enroll_num += $enroll_val['enroll_num'];
				
			}
			
			$enroll_num = (int)$enroll_num;
					
			$new_status_arr[$status_name]['enroll_num'] = $enroll_num;
			$new_status_arr[$status_name]['enroll_list'] = $enroll_list;
			$new_status_arr[$status_name]['status_name'] = $status_arr[$status];
			
			unset($enroll_num);
		}
		
		$total_join = (int)$total_join;
		
		$table_arr[$k]['table_name'] = "第".$table_num_arr[$table_id]."场 ".date("m.d H:i",$val['begin_time'])." - ".date("H:i",$val['end_time']);
		$table_arr[$k]['enroll_arr'] = $new_status_arr;
		$table_arr[$k]['event_title'] = $event_info['title'];
		$table_arr[$k]['event_status'] = $event_info['event_status'];
		$table_arr[$k]['event_organizers'] = get_relate_yue_id($event_info['user_id']);
		$table_arr[$k]['event_total_join'] = $total_join.'/'.$table_num;
		
		unset($check_user_arr,$new_status_arr,$total_join);
	}

	return $table_arr;

}

function get_enroll_detail_info($enroll_id){

	$table_obj             = POCO::singleton('event_table_class');
	$details_obj           = POCO::singleton('event_details_class');
	$activity_code_obj     = POCO::singleton ( 'pai_activity_code_class' );
	$event_comment_log_obj = POCO::singleton('pai_event_comment_log_class');
	
	$enroll_info   = get_enroll_by_enroll_id($enroll_id);
	
	if(empty($enroll_info))
	{
		return array();
	}
	$table_arr     = $table_obj->get_event_table_num_array($enroll_info['event_id']);
	$table_num     = $table_arr[$enroll_info['table_id']];
	
	$table_info = $table_obj->get_event_table($enroll_info['event_id'],$enroll_info['table_id']);
	
	$enroll_info['table_info'] = "第".$table_num."场  ".date("m.d H:i",$table_info[0]['begin_time'])."-".date("m.d H:i",$table_info[0]['end_time']);
	
	$event_info = $details_obj->get_event_by_event_id($enroll_info['event_id']);
    //print_r($event_info);
    
    //活动组织者
	$enroll_info['event_organizers'] =  get_relate_yue_id($event_info['user_id']);
    $enroll_info['cover_image']       = $event_info['cover_image'];
	$enroll_info['event_title'] = $event_info['title'];
	$enroll_info['total_budget'] =floatval($event_info['budget'])*intval($enroll_info['enroll_num']);
	$enroll_info['budget'] = $event_info['budget'];
	$enroll_info['order_id'] = date("Ymd",$enroll_info['enroll_time']).$enroll_info['enroll_id'];
	if($enroll_info['pay_time'])
	{
		$enroll_info['pay_time'] = date("Y-m-d H:i:s",$enroll_info['pay_time']);
	}
	else
	{
		$enroll_info['pay_time'] = '';
	}
	
	$enroll_info['event_status'] = $event_info ['event_status'];
	
	//最后签到的时间
	$last_scan_info = $activity_code_obj->get_last_scan_by_enroll_id($enroll_id);
	
	if($last_scan_info['update_time'])
	{
		$enroll_info['scan_time'] = date("Y-m-d H:i:s",$last_scan_info['update_time']);
	}
	else
	{
		$enroll_info['scan_time'] = '';
	}
	
	//是否已评价活动
	$check_comment = $event_comment_log_obj->is_event_comment_by_user($enroll_info['event_id'], $enroll_info['table_id'],$enroll_info['user_id']);
	
	if($check_comment){
		$enroll_info['is_comment'] = 1;
	}else{
		$enroll_info['is_comment'] = 0;
	}
	
	//是否已全部签到
	$check_all_scan = $activity_code_obj->check_is_all_scan($enroll_id);
	if($check_all_scan){
		$enroll_info['is_all_scan'] = 1;
	}else{
		$enroll_info['is_all_scan'] = 0;
	}
	
	
	if($enroll_info['pay_status']==1)
	{
		if($event_info['event_status']==='0')
		{
			if($check_all_scan){
				$enroll_info['enroll_code_button'] = 0;
				$enroll_info['bar_text'] = '已现在签到';
			}else{
				$enroll_info['enroll_code_button'] = 1;
				$enroll_info['bar_text'] = '已付款，准备签到';
			}
			
			$enroll_info['enroll_comment_button'] = 0;
		}
		elseif($event_info['event_status']==='2')
		{
			$check_code_scan = $activity_code_obj->check_code_scan($enroll_info ['enroll_id']);
		
			if($check_code_scan)
			{
				if($check_comment)
				{
					$enroll_info['enroll_comment_button'] = 0;
					$enroll_info['bar_text'] = '已评价';
				}
				else
				{
					$enroll_info['enroll_comment_button'] = 1;
					$enroll_info['bar_text'] = '活动已完成';
				}
			}
			else
			{
				$enroll_info['enroll_comment_button'] = 0;
				$enroll_info['bar_text'] = '活动已完成';
			}
			
			$enroll_info['enroll_code_button'] = 0;
		}
		elseif($event_info['event_status']==='3')
		{
			$enroll_info['bar_text'] = '活动已取消';
			$enroll_info['enroll_comment_button'] = 0;
			$enroll_info['enroll_code_button'] = 0;
		}
		$enroll_info['enroll_pay_button'] = 0;
	}
	elseif($enroll_info['pay_status']==0)
	{
		$enroll_info['bar_text'] = '待付款';
		$enroll_info['enroll_comment_button'] = 0;
		$enroll_info['enroll_code_button'] = 0;
		$enroll_info['enroll_pay_button'] = 1;
		$enroll_info['enroll_cancel_button'] = 1;
	}
	
	return $enroll_info;
}

function count_waipai_order_num($user_id)
{
	$user_id = (int)$user_id;
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	
	$user_id = get_relate_poco_id($user_id);
	$enroll_list = get_enroll_list("user_id={$user_id} and pay_status=0 and table_id!=0", false, '0,10000', 'enroll_id DESC',"event_id");
	
	foreach($enroll_list as $val)
	{
		$event_info = $event_details_obj->get_event_by_event_id($val['event_id']);
		
		if($event_info['event_status']==='0' && $event_info['new_version']=='2')
		{
			$count++;
		}
	}
	$count = (int)$count;
	return $count;
}


?>