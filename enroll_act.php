<?php
//include_once('poco_app_common.inc.php');
//include_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
/**
 * Լ���ύ����  modify hai 20140911
 * @param array $enroll_data 
 * array(
 *  'user_id'=>'',  �û�ID  [�ǿ�]
 *  'event_id'=>,   �ID  [�ǿ�]
 *  'phone'=>'',    �ֻ�����
 *  'email'=>'',    ����
 *  'remark'=>      ��ע
 * )
 * @param array $enroll_data  �������ݵĶ�ά����  
 * array(
 *  0=>array(
 *                         
 *    'enroll_num'=>''  [�ǿ�]    ��������
 *    'table_id'=>''    [�ǿ�]    ��������ID
 *    'coupon_sn'=>''             �Ż���
 *  
 *  ),
 *  1=>array(...
 * )
 * @param int    $user_balance �û����  �����ж��û��Ƿ�ͣ��ҳ��̫��ʱ��û�ύ  ���û����䶯�����ύ
 * @param int    $is_available_balance   0Ϊ���֧�� 1Ϊ������֧��   �������֧������Ҫ������ת������������֧��
 * @param string $third_code   ������֧���ı�ʶ ����ʱ֧��΢�ź�֧����Ǯ�� alipay_purse��tenpay_wxapp ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $notify_url ֧���ɹ����첽��url��Ϊ��ʱʹ�������ļ��еĴ���ҳ
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * ����ֵ status_code Ϊ״̬ 
 * status_code����ֵ 
	 * -1  ��������
	 * -2  �û������ 
	 * -3  ��ѽ���
	 * -4  ������Ϊ��֯��  ������
	 * -5  ���������Ƿ�
	 * -6  ĳһ���Ѿ�������
     * -7  ĳһ�������ѹر� �������ٱ���
     * -8  ������������  ����ʧ��
     * -9  ��������ɲ�֧��  ����ʧ��
	 * -10  �û�����б䶯
	 * -11  ���֧��ʧ��
	 * -12 ��ת��������֧����������
 * status_code��ȷֵ
 *   1Ϊ�ύ�ɹ� ����֯������
 *   2Ϊ���֧���ɹ�   
 *   3Ϊ������������ɹ�������ת����������
 * message���ص���Ϣ cur_balance �����û���ǰ��ʵ���[��status_code==2�����֧���ɹ����д�key] 
 * request_data ����������������ַ���[��Ҫ��������ʱ��ŷ���]
 *
 */
function add_enroll_op($enroll_data,$sequence_data,$user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url='')
{

    $enroll_data['user_id'] = intval($enroll_data['user_id']);
    if ( empty($enroll_data['user_id']) )  
    {
		$ret = array( 'status_code'=>-1,'message'=>'user_id ��ʽ����' );
		return $ret;
    }
    $enroll_data['event_id'] = intval($enroll_data['event_id']);
    if ( empty($enroll_data['event_id']) )  
    {
		$ret = array( 'status_code'=>-1,'message'=>'event_id ��ʽ����' );
		return $ret;
    }
    if ( empty($sequence_data) ) 
    {
		$ret = array( 'status_code'=>-1,'message'=>'sequence_data ��ʽ����' );
		return $ret;
    }
    if( !in_array($is_available_balance,array(0,1)) ){

    	$ret = array( 'status_code'=>-1,'message'=>'is_available_balance ��ʽ����' );
		return $ret;

    }
    
    $user_obj      = POCO::singleton('pai_user_class');
    $payment_obj   = POCO::singleton('pai_payment_class');
    $enroll_obj    = POCO::singleton('event_enroll_class');
    $account_info  = $payment_obj->get_user_account_info($enroll_data['user_id']);
	if( $account_info['available_balance'] != $user_balance  ){

		$user_info  = $user_obj->get_user_info_by_user_id($enroll_data['user_id']);
	    $ret 		= array( 'status_code'=>-10,'message'=>'�û�����б䶯','user_info'=>$user_info );
		return $ret;    	

	}

	$enroll_data_v2				= $enroll_data;
	$enroll_data_v2['user_id']  = get_relate_poco_id($enroll_data['user_id']);
	//ת��Ϊpoco id ���
    $enroll_ret					= $enroll_obj->add_enroll_v3($enroll_data_v2,$sequence_data);
    if( $enroll_ret['status_code'] != 1 ){

		$ret = array( 'status_code'=>$enroll_ret['status_code'], 'message'=>$enroll_ret['message'], 'enroll_ret'=>$enroll_ret );
		return $ret;

    }
	if( $enroll_ret['join_mode_auth'] == false ){
		//û��Ȩ�޵���� pc�����Ҫ�������б�����  �����ϲ������(['join_mode_auth'] == false)������
		$ret = array( 'status_code'=>1,'message'=>'�����ɹ� ����֯��ȷ��' );
		return $ret;

	}
	//����1Ϊ�μӻ�ɹ�
	$enroll_id_arr 		= $enroll_ret['enroll_id_arr'];
	$enroll_cost_detail = $enroll_obj->get_enroll_cost_by_arr( $enroll_id_arr );
	$sum_cost 			= $enroll_cost_detail['total_cost'];
	if( $sum_cost<=0 )
	{
		//��־
		pai_log_class::add_log(array( 'enroll_ret'=>$enroll_ret, 'enroll_cost_detail'=>$enroll_cost_detail), 'add_enroll_op error', 'enroll_act');
		
		$ret = array( 'status_code'=>-10, 'message'=>'�ܽ�����' );
		return $ret;
	}
	
	//��־
	pai_log_class::add_log(array( 'enroll_ret'=>$enroll_ret, 'enroll_cost_detail'=>$enroll_cost_detail), 'add_enroll_op ok', 'enroll_act');
	
	$sum_discount 		= $enroll_cost_detail['total_discount']; //�����Żݽ��
	$sum_pending        = bcsub($sum_cost, $sum_discount, 2);
	if( $sum_pending<=0 )
	{
		$ret = array( 'status_code'=>-10, 'message'=>'�Żݽ�����' );
		return $ret;
	}

	if($is_available_balance == 0 ){
		//0Ϊ���֧��
		if( bccomp($account_info['available_balance'],$sum_pending,2)==-1 ){
			//���㣬��תȥ��ֵ
			$amount   		= $sum_pending - $account_info['available_balance'];
			$redirect_third = 1; 

		}	
		else{

			$ret =  $payment_obj->pay_enroll_by_balance($enroll_data['event_id'],$enroll_id_arr);
			if( $ret['error'] == 0 ){

				$user_info  = $user_obj->get_user_info_by_user_id($enroll_data['user_id']);
				$ret 		= array( 'status_code'=>2,'message'=>'���֧���ɹ���','user_info'=>$user_info);
				return $ret;
			
			}
			else{

				$ret = array( 'status_code'=>-11,'message'=>'���֧��ʧ��' );
				return $ret;

			}
		
		}

	}
	else{
		//ֱ����֧����֧��
		$amount = $sum_pending;
		$redirect_third = 1; 

	}
	if($redirect_third){

	    if( !in_array($third_code,array('alipay_purse','alipay_wap','tenpay_wxapp','tenpay_wxpub')) ){

	    	$ret = array( 'status_code'=>-1,'message'=>'third_code ֧����ʶ����' );
			return $ret;    	

	    }
	    $more_info = array();
		$more_info['channel_return'] = $redirect_url;
		$more_info['channel_notify'] = $notify_url;
		/*��ȡopenid*/
		$openid = '';
		if ($third_code == 'tenpay_wxpub')
		{
			
			$bind_weixin_obj = POCO::singleton ( 'pai_bind_weixin_class' );
			$bind_info 		 = $bind_weixin_obj->get_bind_info_by_user_id ( $enroll_data['user_id'] );
			$openid 		 = $bind_info ['open_id'];
			if (empty ( $openid ))
			{
				
				$ret = array ('status_code' => - 1, 'message' => '΢���û�û��ԼԼ�˺�' );
				return $ret;
			
			}
		
		}
		$more_info ['openid'] = $openid;
		/*��ȡopenid*/

		$enroll_id_str = implode(',', $enroll_id_arr);
		$recharge_ret  = $payment_obj->submit_recharge('activity',$enroll_data['user_id'],$amount,$third_code,$enroll_data['event_id'],$enroll_id_str,0,$more_info);	
		
		if( $recharge_ret['error']===0 )
		{
			$payment_no = trim($recharge_ret['payment_no']);
			$request_data = trim($recharge_ret['request_data']);
			$ret 		  = array( 'status_code'=>3,'message'=>'���Լ�ĳɹ�,����ת��������֧����','request_data'=>$request_data,'payment_no'=>$payment_no);
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
 * Լ�ı���������֧��
 * @param array $enroll_data
 * array(
 *  'user_id'=>'',  �û�ID  [�ǿ�]
 *  'event_id'=>,   �ID  [�ǿ�]
 * )
 * @param array $enroll_id_arr  ����ID
 * array(
 *  1,2
 * )
 * @param int    $user_balance �û����  �����ж��û��Ƿ�ͣ��ҳ��̫��ʱ��û�ύ  ���û����䶯�����ύ
 * @param int    $is_available_balance   0Ϊ������֧��  1Ϊ���֧��  �������֧������Ҫ������ת������������֧��
 * @param string $third_code   ������֧���ı�ʶ ����ʱ֧��΢�ź�֧����Ǯ�� alipay_purse��tenpay_wxapp ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $notify_url ֧���ɹ����첽��url��Ϊ��ʱʹ�������ļ��еĴ���ҳ
 * @param string $coupon_sn �Ż���
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * ����ֵ status_code Ϊ״̬
 * status_code����ֵ
 * -1  ��������
 * -2  �û������
 * -3  ��ѽ���
 * -10 �û�����б䶯
 * -11 ���֧��ʧ��
 * -12 ��ת��������֧����������
 * status_code��ȷֵ
 *   1Ϊ���֧���ɹ�
 *   2Ϊ������������ɹ�������ת����������
 * message���ص���Ϣ cur_balance �����û���ǰ��ʵ���[��status_code==2�����֧���ɹ����д�key]
 * request_data ����������������ַ���[��Ҫ��������ʱ��ŷ���]
 *
 */
function again_enroll_op($enroll_data, $enroll_id_arr, $user_balance,$is_available_balance,$third_code,$redirect_url,$notify_url='',$coupon_sn='')
{
	$enroll_data['user_id'] = intval($enroll_data['user_id']);
	if ( empty($enroll_data['user_id']) )
	{
		$ret = array( 'status_code'=>-1,'message'=>'user_id ��ʽ����' );
		return $ret;
	}
	$enroll_data['event_id'] = intval($enroll_data['event_id']);
	if ( empty($enroll_data['event_id']) )
	{
		$ret = array( 'status_code'=>-1,'message'=>'event_id ��ʽ����' );
		return $ret;
	}
	if( !is_array($enroll_id_arr) || empty($enroll_id_arr) )
	{
		$ret = array( 'status_code'=>-1,'message'=>'enroll_id_arr ��ʽ����' );
		return $ret;
	}
	
	$payment_obj   = POCO::singleton('pai_payment_class');
	$details_obj   = POCO::singleton('event_details_class');
	$enroll_obj    = POCO::singleton('event_enroll_class');
	$user_obj      = POCO::singleton('pai_user_class');
	$event_info    = $details_obj->get_event_by_event_id($enroll_data['event_id']);
	 if(empty($event_info) ){

            $ret = array( 'status_code'=>-2,'message'=>'������� �Ƿ���event_id' );
            return $ret;

     }
    if($event_info['event_status'] != 0){

        $ret = array( 'status_code'=>-3,'message'=>'��Ѵ��ڿ�ʼ�������״̬  �������ٱ���' );
        return $ret;

    }
    $account_info  = $payment_obj->get_user_account_info($enroll_data['user_id']);
	if( $account_info['available_balance'] != $user_balance  ){

		//http://pai.poco.cn/mobile/action/join_again_act.php ҳ��û�д� available_balance  ��Ҫ����������ȥ��
		//$user_info  = $user_obj->get_user_info_by_user_id($enroll_data['user_id']);
	    //$ret 		= array( 'status_code'=>-10,'message'=>'�û�����б䶯','user_info'=>$user_info[0] );
		//return $ret;    	

	}
	
	//�����Ż�ȯ��ע�⣺�˷����Ѿ���֧�ֶ������ID��ֻ֧��һ������ID�ˡ�
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$channel_module = 'waipai';
	$channel_oid = $enroll_id_arr[0];
	$module_type = 'waipai';
	
	//��ʹ��
	$coupon_obj->not_use_coupon_by_oid($channel_module, $channel_oid);
	
	//ʹ���Ż�ȯ
	$order_total_amount = $enroll_obj->get_enroll_cost($channel_oid);
	if( !empty($coupon_sn) )
	{
		$event_user_id = get_relate_yue_id($event_info['user_id']);
		$param_info = array(
			'module_type' => $module_type, //ģ������ waipai yuepai topic
			'order_total_amount' => $order_total_amount, //�����ܽ��
			'model_user_id' => 0, //ģ���û�ID��Լ�ġ�ר�⣩
			'org_user_id' => $event_info['org_user_id'], //�����û�ID
			'location_id' => $event_info['location_id']*1, //����ID
			'event_id' => $enroll_data['event_id'], //�ID
			'event_user_id' => $event_user_id, //���֯���û�ID
			'seller_user_id' => $event_user_id, //�̼��û�ID�������̳�ϵͳ
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
		//�ϴο�����ʹ���Ż�ȯ�������������
		$discount_price = 0;
		$is_use_coupon = 0;
	}
	$enroll_obj->update_enroll(array('original_price'=>$order_total_amount, 'discount_price'=>$discount_price, 'is_use_coupon'=>$is_use_coupon), $channel_oid);
	
	//����1Ϊ�μӻ�ɹ�
	$enroll_cost_detail = $enroll_obj->get_enroll_cost_by_arr( $enroll_id_arr );
	$sum_cost 			= $enroll_cost_detail['total_cost'];
	if( $sum_cost<=0 )
	{
		//��־
		pai_log_class::add_log(array( 'enroll_id_arr'=>$enroll_id_arr, 'enroll_cost_detail'=>$enroll_cost_detail), 'again_enroll_op error', 'enroll_act');
		
		$ret = array( 'status_code'=>-10, 'message'=>'�ܽ�����' );
		return $ret;
	}
	
	//��־
	pai_log_class::add_log(array('enroll_id_arr'=>$enroll_id_arr, 'enroll_cost_detail'=>$enroll_cost_detail), 'again_enroll_op ok', 'enroll_act');
	
	$sum_discount 		= $enroll_cost_detail['total_discount']; //�����Żݽ��
	$sum_pending        = bcsub($sum_cost, $sum_discount, 2);
	if( $sum_pending<=0 )
	{
		$ret = array( 'status_code'=>-10, 'message'=>'�Żݽ�����' );
		return $ret;
	}
	
	if($is_available_balance){

		if( bccomp($account_info['available_balance'],$sum_pending,2)==-1 ){
			//���㣬��תȥ��ֵ
			$amount   		= $sum_pending - $account_info['available_balance'];
			$redirect_third = 1;

		}
		else{

			$ret =  $payment_obj->pay_enroll_by_balance($enroll_data['event_id'],$enroll_id_arr);
			if( $ret['error'] == 0 ){

				$ret = array( 'status_code'=>1,'message'=>'���֧���ɹ���','cur_balance'=>$account_info['available_balance'] );
				return $ret;
					
			}
			else{

				$ret = array( 'status_code'=>-11,'message'=>'���֧��ʧ��' );
				return $ret;

			}

		}

	}
	else{
		//ֱ����֧����֧��
		$amount = $sum_pending;
		$redirect_third = 1;

	}
	if($redirect_third){

		if( !in_array($third_code,array('alipay_purse','alipay_wap','tenpay_wxapp','tenpay_wxpub')) ){

			$ret = array( 'status_code'=>-1,'message'=>'third_code ֧����ʶ����' );
			return $ret;

		}
		$more_info = array();
		$more_info['channel_return'] = $redirect_url;
		$more_info['channel_notify'] = $notify_url;
		/*��ȡopenid*/
		$openid = '';
		if ($third_code == 'tenpay_wxpub')
		{
			
			$bind_weixin_obj = POCO::singleton ( 'pai_bind_weixin_class' );
			$bind_info 		 = $bind_weixin_obj->get_bind_info_by_user_id ( $enroll_data['user_id'] );
			$openid 		 = $bind_info ['open_id'];
			if (empty ( $openid ))
			{
				
				$ret = array ('status_code' => - 1, 'message' => '΢���û�û��ԼԼ�˺�' );
				return $ret;
			
			}
		
		}
		$more_info ['openid'] = $openid;
		/*��ȡopenid*/
		$enroll_id_str = implode(',', $enroll_id_arr);
		$recharge_ret  = $payment_obj->submit_recharge('activity',$enroll_data['user_id'],$amount,$third_code,$enroll_data['event_id'],$enroll_id_str, 0, $more_info);

		if( $recharge_ret['error']===0 )
		{
			$payment_no = trim($recharge_ret['payment_no']);
			$request_data = trim($recharge_ret['request_data']);
			$ret 		  = array( 'status_code'=>2,'message'=>'����֧��,����ת��������֧����','request_data'=>$request_data,'payment_no'=>$payment_no );
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
 * ɾ������
 * @param $enroll_id ����������
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
    //���״̬
    $enroll_info    = $enroll_obj->get_enroll_by_enroll_id($enroll_id);
    $event_id       = $enroll_info[0]['event_id'];
    $event_info     = $details_obj->get_event_by_event_id($event_id);
    
    $channel_module = "waipai";
    
    if( $event_info['event_status'] == 0 )
    {
    	//����Ƿ�������һ����ɨ��
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
    	//���ʼ��δ�������ȡ���������Ѹ����ȡ������
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
 * ��ʽ����
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
 * ȡ�б�
 *
 * @param string $where_str    ��ѯ����
 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
 * @param string $limit        ��ѯ����
 * @param string $order_by     ��������
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
 * ��ȡ�û������״̬�б�
 * @param int $user_id
 * @param string $status δ���unpaid �Ѹ��paid
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
			$where_str = "user_id={$user_id} and pay_status=0 and status=3 and table_id!=0 and event_remark!='�ȡ��'";
		break;
		
		case "paid":
			$where_str = "user_id={$user_id} and pay_status=1 and status in (0,1) and table_id!=0 and event_remark!='�ȡ��'";
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
			
			//�Ƿ������ۻ
			$check_comment = $event_comment_log_obj->is_event_comment_by_user($val['event_id'], $val['table_id'],$yue_user_id);
			
			if($check_comment){
				$event_detail['is_comment'] = 1;
			}else{
				$event_detail['is_comment'] = 0;
			}
			
			
			if($status=='paid'){
		       
		        //�ж��Ƿ��ѽ���
		        if( $event_detail['event_status']!=0){
		        	$event_detail['is_end'] = 1;
		        }else{
		        	$event_detail['is_end'] = 0;
		        }
		        
		        
				//��Ƿ�������һ��������ɨ��
				$check_scan = $activity_code_obj->check_event_code_scan ( $val ['event_id'] );
				//�Ƿ���ȫ��ǩ��
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
				
				//1.0.5���ɽӿ��жϰ�ť
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
			//�����
			$event_detail['score'] = (int)$comment[0]['overall_score'];
			
			// ������������
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
			$event_detail['title'] = $event_detail['title']." ��".$table_num."��";
			 
			$ret[$k]['event_detail'] = $event_detail;
		
		}
	}
	
	return $ret;
}


/**
 * 
 * ���û�ó����Ƿ��Ѿ�����
 * @param $enroll_num ��������
 * @param $event_id   ��ɣ�
 * @param $table_id   ���ɣġ�
 * 
 * */
function check_is_full($enroll_num=1,$event_id, $table_id)
{
    $enroll_obj     = POCO::singleton('event_enroll_class');
    return $enroll_obj->check_is_full($enroll_num,$event_id, $table_id);
}

/**
 * ����û���ͬһ������Ƿ��Ѿ�����
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
 * ��ñ����ɣĵ�������
 * @param $enroll_id������������
 * 
 * */
function get_enroll_cost($enroll_id)
{
     $enroll_obj     = POCO::singleton('event_enroll_class');
     return $enroll_obj->get_enroll_cost($enroll_id);
}

/**
 * 
 * ���±������֧��״̬
 * @param $enroll_id  ����������
 * @param $status ֧��״̬��0=>��֧����1=>��֧��
 * 
 * */
function update_enroll_pay_status($enroll_id, $status)
{
    $enroll_obj     = POCO::singleton('event_enroll_class');
    return $enroll_obj->update_enroll_pay_status($enroll_id, $status);
}

/**
 * 
 *���Զ�״̬���½ӿ�
 *  ��ʱ������
 * 
 * */
function auto_event_enroll_handling($event_id)
{
    $enroll_obj     = POCO::singleton('event_enroll_class');
    return $enroll_obj->auto_event_enroll_handling($event_id);
}


/**
 * ���ر����б����ɣãϣ���Ϣ
 * @param Array $where_array
 * @param $type_icon
 * @param $limit ����
 * 
 * */
function get_enroll_list_and_event_info($where_array = '', $type_icon = '', $limit)
{
    $enroll_obj					= POCO::singleton('event_enroll_class');
	$where_array['user_id']		= get_relate_poco_id($where_array['user_id']);
    return $enroll_obj->get_enroll_list_and_event_info($where_array, $type_icon, $limit);
}

/*
 * ��ȡ�ȯ
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
		//ֻ��ʾ�����еĻȯ
		if($event_info['event_status']=='0'){
	
			$qr_code = $code_obj->create_qr_code($val['event_id'],$val['enroll_id']);
			$new_code_arr[$k]['qr_code'] = $qr_code;
			//��ȡδǩ���ȯ
			$code = $code_obj->get_code_by_enroll_id_by_status($val['enroll_id'], 0);
			$new_code_arr[$k]['code_arr'] = $code;
			$event_info['user_id']		  = get_relate_yue_id($event_info['user_id']);	//ת����yueyue id
			$event_info['nick_name']	  = get_user_nickname_by_user_id($event_info['user_id']);
			$event_info['start_time'] = date("Y-m-d",$event_info['start_time']);
			$event_info['end_time'] = date("Y-m-d",$event_info['end_time']);
			
			if($event_info['type_icon']=='yuepai_app'){
				$date_info = get_event_date("enroll_id", $val['enroll_id']);
				$event_info['title'] = "ģ��".$event_info['nick_name'];
				$event_info['start_time'] = date("Y-m-d",$date_info[0]['date_time']);
			}else{
				$enroll_arr = get_enroll_by_enroll_id($val['enroll_id']);
				$table_id = $enroll_arr['table_id'];
				$table_num_arr = $table_obj->get_event_table_num_array($val['event_id']);
				
				$event_info['title'] = $event_info['title']." ��".$table_num_arr[$table_id]."��";
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
 * ��ȡ�û��ȯ����
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
 * ����event_id enroll_id��ȡ�ȯ
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
 * ���ݻ���λ�ȡǩ���б�
 * @param $event_id���ID
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
				
				//�ҳ���ǩ������
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
		
		$table_arr[$k]['table_name'] = "��".$table_num_arr[$table_id]."�� ".date("m.d H:i",$val['begin_time'])."-".date("m.d H:i",$val['end_time']);
		$table_arr[$k]['enroll_arr'] = $new_status_arr;
		$table_arr[$k]['event_title'] = $event_info['title'];
		unset($check_user_arr,$new_status_arr);
	}

	return $table_arr;
}

/*
 * �ǩ���б�1.0.5�棩
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
					$enroll_list[$enroll_key]['nick_name']		=  'POCO�û�'.substr($enroll_val['enroll_id'],-4);
					$enroll_list[$enroll_key]['user_id']		=  '��';
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
				
				
				$enroll_list[$enroll_key]['text'] = "(��".$enroll_val['enroll_num']."��)";
				

				$count_checked = $code_obj->count_code_is_checked(true, $enroll_val['enroll_id'],'0,99999',true);
				
				if($count_checked>0)
				{				
					if($enroll_val['enroll_num']==1)
					{
						$enroll_list[$enroll_key]['mark_text'] = '��ǩ��';
					}
					else
					{
						$enroll_list[$enroll_key]['mark_text'] = '��ǩ�� '.$count_checked;
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
		
		$table_arr[$k]['table_name'] = "��".$table_num_arr[$table_id]."�� ".date("m.d H:i",$val['begin_time'])." - ".date("H:i",$val['end_time']);
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
	
	$enroll_info['table_info'] = "��".$table_num."��  ".date("m.d H:i",$table_info[0]['begin_time'])."-".date("m.d H:i",$table_info[0]['end_time']);
	
	$event_info = $details_obj->get_event_by_event_id($enroll_info['event_id']);
    //print_r($event_info);
    
    //���֯��
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
	
	//���ǩ����ʱ��
	$last_scan_info = $activity_code_obj->get_last_scan_by_enroll_id($enroll_id);
	
	if($last_scan_info['update_time'])
	{
		$enroll_info['scan_time'] = date("Y-m-d H:i:s",$last_scan_info['update_time']);
	}
	else
	{
		$enroll_info['scan_time'] = '';
	}
	
	//�Ƿ������ۻ
	$check_comment = $event_comment_log_obj->is_event_comment_by_user($enroll_info['event_id'], $enroll_info['table_id'],$enroll_info['user_id']);
	
	if($check_comment){
		$enroll_info['is_comment'] = 1;
	}else{
		$enroll_info['is_comment'] = 0;
	}
	
	//�Ƿ���ȫ��ǩ��
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
				$enroll_info['bar_text'] = '������ǩ��';
			}else{
				$enroll_info['enroll_code_button'] = 1;
				$enroll_info['bar_text'] = '�Ѹ��׼��ǩ��';
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
					$enroll_info['bar_text'] = '������';
				}
				else
				{
					$enroll_info['enroll_comment_button'] = 1;
					$enroll_info['bar_text'] = '������';
				}
			}
			else
			{
				$enroll_info['enroll_comment_button'] = 0;
				$enroll_info['bar_text'] = '������';
			}
			
			$enroll_info['enroll_code_button'] = 0;
		}
		elseif($event_info['event_status']==='3')
		{
			$enroll_info['bar_text'] = '���ȡ��';
			$enroll_info['enroll_comment_button'] = 0;
			$enroll_info['enroll_code_button'] = 0;
		}
		$enroll_info['enroll_pay_button'] = 0;
	}
	elseif($enroll_info['pay_status']==0)
	{
		$enroll_info['bar_text'] = '������';
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