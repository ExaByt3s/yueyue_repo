<?php
/**
 * �������
 *
 * @author tom
 * @copyright 2010-12-31
 */



class event_enroll_class extends POCO_TDG
{
    /**
     * ���һ�δ�����ʾ
     * @var string
     */
    protected $_last_err_msg = null;
    
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        //$this->setServerId(false);
        //$this->setDBName('event_db');
        //$this->setTableName('event_enroll_tbl');
    }
    
    /**
     * ���ô�����ʾ
     * @param string $msg
     */
    protected function set_err_msg($msg)
    {
        $this->_last_err_msg = $msg;
    }
    
    /**
     * ��ȡ������ʾ
     */
    public function get_err_msg()
    {
        return $this->_last_err_msg;
    }

    /**
     * ��ȡ����
     *
     * @param int $enroll_id
     * @return array
     */
    public function get_enroll_by_enroll_id($enroll_id)
    {
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_by_enroll_id',$param);
        return $ret;
    }
    /**
     * ��ȡ����
     *
     * @param int $event_id
     * @param int $user_id
     * @return array
     */
    public function get_enroll_by_event_id_and_user_id($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_by_event_id_and_user_id',$param);
        return $ret;
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
    public function get_enroll_list($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'enroll_id DESC',$fields="*")
    {
        $param[] = $where_str;
        $param[] = $b_select_count;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('event_enroll_class','get_enroll_list',$param);
        return $ret;
    }	
    
    /**
     * д������
     *
     * @param array $data 		���Ϣ����
     * 				array(
     * 				'user_id'=>0			�����û�id
     * 				'enroll_location_id'=>0	�����û����ڵ���
     * 				'event_id'=>0			�ID	  				
     * 				'enroll_time'=>0		����ʱ��	  
     * 				'phone'=>''				�����û��绰����Ϊ��	  
     * 				'enroll_num'=>1			�������� 				
     * 				'enroll_ip'=>0			�����û�IP	  
     * 				'email'	=>''			�����û�EMAIL����Ϊ��	  
     * 				'remark'=>''			��ע����Ϊ��	
     * 				)
     * @return enroll_id
     */
    public function add_enroll($data)
    {
        $param[] = $data;
        $ret = curl_event_data('event_enroll_class','add_enroll',$param);
        return $ret;
    }

    /**
     * �»�����ӿ� 20140731
     * 
     * @param array $data 		���Ϣ����
     * 	array(
     * 	'user_id'=>0			�����û�id

     
     * 	'event_id'=>0			�ID	  					  
     * 	'phone'=>''				�����û��绰����Ϊ��	  
     * 	'enroll_num'=>1			��������  					  
     * 	'email'	=>''			�����û�EMAIL����Ϊ��	  
     * 	'remark'=>''			��ע����Ϊ��
     *  'enroll_ip'=>'',		IP��ַ  �������ʹ��ip2long($_INPUT['IP_ADDRESS'])
     * 	'table_id'=>''		 	��������ID 
     * 	)
     * @return int enroll_id ����ID   ����������  -1 ��ѹ��� -2���û��Ѿ����� -3�û�Ϊ��֯�߲��ܱ��� -4���ʼ�л��ѽ��� -5 �����ѹر�
     */
    public function add_enroll_v2($event_data){

        $enroll_obj     		  = POCO::singleton('event_enroll_class');
        $details_obj    		  = POCO::singleton('event_details_class');
        $table_obj                = POCO::singleton('event_table_class');
        $event_data['event_id']   = (int)$event_data['event_id'];
        $event_data['user_id']    = (int)$event_data['user_id'];
        $event_info               = $details_obj->get_event_by_event_id($event_data['event_id']);
        if ( empty($event_data['user_id']) || empty($event_data['event_id']) ) 
        {
            $this->set_err_msg('��������ȷ');
            return false;
        }
        /*�жϻ�Ƿ��Ѿ���ʼ�����*/
        if( G_POCO_EVENT_PAY == 1  ){
            //֧����
            if( $event_info['event_status'] != 0 ){
                //0 ���ڽ���  1��ȷ�� 2�ѽ���  3��ȡ��
                return -4;

            }

        }
        else{

            if( $event_info['status'] != 0 ){

                return -4;                
                
            }

        }
        /*�жϻ�Ƿ��Ѿ���ʼde�����*/
        $is_over          = $details_obj->check_date_is_over($event_data['event_id']);  //����Ƿ����
        if( $is_over )
            return -1;
        $is_duplicate 	  = $this->check_duplicate($event_data['user_id'],$event_data['event_id'], 'all',$event_data['table_id']);	//����Ƿ��Ѿ�����
        if( $is_duplicate )
            return -2;
        
        if($event_info['user_id'] == $event_data['user_id'])
            return -3;
        $can_enroll = $table_obj->check_event_table_enroll($event_data['table_id']);
        if( !$can_enroll ){
            //�����ѹر�
            return -5;

        }

        $user_info 							= POCO::execute(array('member.get_user_info_by_user_id'), array($event_data['user_id']));
        $event_data['enroll_location_id'] 	= $user_info['location_id'];
        $event_data['enroll_time'] 			= time();
        //����Ĭ��ȫ���������֧����Ϻ󣬸���֧��ʱ���ٽ�����ѡ����趨  ����״̬��Ĭ��0�ɹ���1�򲹣�2�ŷɻ� 3������  
        $enroll_id = $enroll_obj->add_enroll($event_data);
        return $enroll_id;

    }

    /**
     * �»�����ӿ� 20140912
     * @param enroll_data ��������   
     * array(
     *  'user_id'=>'',  �û�ID  [�ǿ�]
     *  'event_id'=>,   �ID  [�ǿ�]
     *  'phone'=>'',    �ֻ�����
     *  'email'=>'',    ����
     *  'remark'=>      ��ע
     * )
     * @param array $sequence_data ����� ��ά����
     *  array(                         
     *      'enroll_num'=>''        ��������                      
     *      'table_id'=>''          ��������ID 
     *      'coupon_sn'=>''         �Ż���
     *  )
     * @return array enroll_id_arr 
     * ����ֵ 
     * -1 ��������
     * -2 �û������ 
     * -3 ��Ѵ��ڿ�ʼ�������״̬ 
     * -4 ������Ϊ��֯��  ������
     * -5 ���������Ƿ�
     * -6 ĳһ���Ѿ�������
     * -7 ĳһ�������ѹر� �������ٱ���
     * -8 ������������  ����ʧ��
     * -9
     * -10�Ż�ȯʹ��ʧ��
     * 
     */
    public function add_enroll_v3($enroll_data,$sequence_data){

        $enroll_data['user_id'] = intval($enroll_data['user_id']);
        if ( empty($enroll_data['user_id']) )
        {
            $ret = array( 'status_code'=>-1,'message'=>'user_id ��������ȷ' );
            return $ret;
        }
        $enroll_data['event_id'] = intval($enroll_data['event_id']);
        if ( empty($enroll_data['event_id']) )
        {
            $ret = array( 'status_code'=>-1,'message'=>'event_id ��������ȷ' );
            return $ret;
        }
        if ( empty($sequence_data) )
        {
            $ret = array( 'status_code'=>-1,'message'=>'sequence_data ��������ȷ' );
            return $ret;
        }
        $enroll_obj    = POCO::singleton('event_enroll_class');
        $details_obj   = POCO::singleton('event_details_class');
        $table_obj     = POCO::singleton('event_table_class');
        $event_info    = $details_obj->get_event_by_event_id($enroll_data['event_id']);
        if( empty($event_info) ){

            $ret = array( 'status_code'=>-2,'message'=>'�û������' );
            return $ret;

        }
        if($event_info['event_status'] != 0){

            $ret = array( 'status_code'=>-3,'message'=>'��Ѵ��ڿ�ʼ�������״̬  �������ٱ���' );
            return $ret;

        }

        if($event_info['new_version'] != 2){

            $ret = array( 'status_code'=>-15,'message'=>'�û����֧����' );
            return $ret;

        }
        if($event_info['user_id'] == $enroll_data['user_id'] ){

            $ret = array( 'status_code'=>-4,'message'=>'���ܱ����Լ������Ļ' );
            return $ret;

        }
        foreach( $sequence_data as $k=>$v ){

            $enroll_num = intval($v['enroll_num']);
            if( empty( $enroll_num ) ){

                $ret = array( 'status_code'=>-5,'message'=>'������������Ϊ��' );
                return $ret;

            }
            $can_enroll = $table_obj->check_event_table_enroll($v['table_id']);
            if( !$can_enroll ){

                $ret = array( 'status_code'=>-7,'message'=>'ĳһ�������ѹر� �������ٱ���' );
                return $ret;

            }

        }

        include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
        $coupon_obj = POCO::singleton('pai_coupon_class');

        $join_mode_auth = $enroll_obj->check_join_mode_auth( $enroll_data['event_id'],$enroll_data['user_id'] );
        $ip = ip2long($_INPUT['IP_ADDRESS']);
        $user_info            = curl_event_data('event_api_class','get_poco_excute',array("member.get_user_info_by_user_id",array($enroll_data['user_id'])));
        $enroll_location_id   = $user_info['location_id'];

        foreach( $sequence_data as $k=>$v ){

            $enroll_info['user_id']               = $enroll_data['user_id'];    //�û���POCOID��
            $enroll_info['event_id']              = $enroll_data['event_id'];   //�ID
            $enroll_info['phone']                 = $enroll_data['phone'];
            $enroll_info['email']                 = $enroll_data['email'];
            $enroll_info['remark']                = $enroll_data['remark'];
            $enroll_info['enroll_ip']             = $ip;
            $enroll_info['enroll_location_id']    = $enroll_location_id;
            $enroll_info['enroll_time']           = time();
            $enroll_info['enroll_num']            = $v['enroll_num'];
            $enroll_info['table_id']              = $v['table_id'];
            $enroll_info['original_price']        = $event_info['budget']*$v['enroll_num']; //�����ܽ��
            $enroll_info['source']                = $enroll_data['source'];

            if( $join_mode_auth ){

                  $enroll_info['status'] = 3;

             }
             else{
                //û��join_mode�����뷽ʽ�� Ȩ��  ��Ҫ��֯�߽�������
                $enroll_info['status']     = 3;
                $enroll_info['is_accept']  = 0;
                $session            = $key + 1;
                $enroll_user        = curl_event_data('event_api_class','get_poco_excute',array("member.get_user_info_by_user_id",array($enroll_data['user_id'])));
                $msg                = "{$enroll_user} ���������Ļ \"{$event_info['title']}\" ��{$session}��  �����";
                POCO::execute(array('pm.add_new_notify_msg'), array($event_info['user_id'],'�֪ͨ',$msg,$notify_ext_par));

             }
             $is_duplicate     = $this->check_duplicate($enroll_info['user_id'],$enroll_info['event_id'], 'all',$enroll_info['table_id']);
             if( $is_duplicate ){
                //����ѱ�����  ����¾ɵ�����   ��ֹ�û���ת��֧��������������˻����ٴα�����֧��
                 $where_str  = "user_id = {$enroll_info['user_id']} AND event_id = {$enroll_info['event_id']} AND table_id = {$enroll_info['table_id']}";
                 $row        = curl_event_data('event_enroll_class','find_api',array($where_str));
                if( !empty($row) && $row['pay_status'] == 0 ) {
                    //��ֹ�Ѹ��� Ȼ������ٴα���
                    $enroll_id_arr[] = $row['enroll_id'];

                    //�����Ż�ȯ
                    $channel_module = 'waipai';
                    $channel_oid = $row['enroll_id'];
                    $module_type = 'waipai';

                    //��ʹ��
                    $coupon_obj->not_use_coupon_by_oid($channel_module, $channel_oid);

                    //ʹ���Ż�ȯ
                    if( !empty($v['coupon_sn']) )
                    {
                    	$relate_yue_id = get_relate_yue_id($enroll_info['user_id']);
                    	$event_user_id = get_relate_yue_id($event_info['user_id']);
                    	$param_info = array(
                    		'module_type' => $module_type, //ģ������ waipai yuepai topic
                    		'order_total_amount' => $enroll_info['original_price'], //�����ܽ��
                    		'model_user_id' => 0, //ģ���û�ID��Լ�ġ�ר�⣩
                    		'org_user_id' => $event_info['org_user_id'], //�����û�ID
                    		'location_id' => $event_info['location_id']*1, //����ID
                    		'event_id' => $enroll_info['event_id'], //�ID
                    		'event_user_id' => $event_user_id, //���֯���û�ID
                    		'seller_user_id' => $event_user_id, //�̼��û�ID�������̳�ϵͳ
                    	);
                    	$use_ret = $coupon_obj->use_coupon($relate_yue_id, 1, $v['coupon_sn'], $channel_module, $channel_oid, $param_info);
                    	if( $use_ret['result']!=1 )
                    	{
                    		$ret = array( 'status_code'=>-10,'message'=>$use_ret['message'] );
                    		return $ret;
                    	}
                    	$enroll_info['discount_price'] = $use_ret['used_amount'];
                    	$enroll_info['is_use_coupon'] = 1;
                    }
                    else
                    {
                    	//�ϴο�����ʹ���Ż�ȯ�������������
                    	$enroll_info['discount_price'] = 0;
                    	$enroll_info['is_use_coupon'] = 0;
                    }

                    $this->update_enroll($enroll_info,$row['enroll_id']);

                }
                else{


                    $ret = array( 'status_code'=>-9,'message'=>'������������  �ñ�����֧��' );
                    return $ret;

                }

             }
             else{

                $enroll_id  = $enroll_obj->add_enroll($enroll_info);
                !empty($enroll_id)&&$enroll_id_arr[]=$enroll_id;
                if(empty($enroll_id_arr)){

                    $ret = array( 'status_code'=>-8,'message'=>'������������  ����ʧ��' );
                    return $ret;

                 }

                 //�����Ż�ȯ
                 $channel_module = 'waipai';
                 $channel_oid = $enroll_id;
                 $module_type = 'waipai';

                 //ʹ���Ż�ȯ�����±������Żݽ��
                 if( !empty($v['coupon_sn']) )
                 {
                 	$relate_yue_id = get_relate_yue_id($enroll_info['user_id']);
                 	$event_user_id = get_relate_yue_id($event_info['user_id']);
                 	$param_info = array(
                 		'module_type' => $module_type, //ģ������ waipai yuepai topic
                 		'order_total_amount' => $enroll_info['original_price'], //�����ܽ��
                 		'model_user_id' => 0, //ģ���û�ID��Լ�ġ�ר�⣩
                 		'org_user_id' => $event_info['org_user_id'], //�����û�ID
                 		'location_id' => $event_info['location_id']*1, //����ID
                 		'event_id' => $enroll_info['event_id'], //�ID
                 		'event_user_id' => $event_user_id, //���֯���û�ID
                 		'seller_user_id' => $event_user_id, //�̼��û�ID�������̳�ϵͳ
                 	);
                 	$use_ret = $coupon_obj->use_coupon($relate_yue_id, 1, $v['coupon_sn'], $channel_module, $channel_oid, $param_info);
                 	if( $use_ret['result']!=1 )
                 	{
                 		$ret = array( 'status_code'=>-10,'message'=>$use_ret['message'] );
                 		return $ret;
                 	}
                 	$enroll_data = array(
                 		'discount_price' => $use_ret['used_amount'],
                 		'is_use_coupon' => 1,
                 	);
                 	$this->update_enroll($enroll_data, $enroll_id);
                 }

             }

        }
        $ret = array( 'status_code'=>1,'message'=>'�����ɹ�','enroll_id_arr'=>$enroll_id_arr,'join_mode_auth'=>$join_mode_auth );
        return $ret;

    }
    /**
     * ��������
     *
     * @param array $data
     * @param int $enroll_id
     * @return bool
     */
    public function update_enroll($data, $enroll_id)
    {
        $param[] = $data;
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','update_enroll',$param);
        return $ret;
        
    }
    
    /*
     * �����û�ID��������
     */
    public function update_enroll_by_user_id($data,$user_id)
    {
        $param[] = $data;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','update_enroll_by_user_id',$param);
        return $ret;
    }
    
    /**
     * ɾ������
     *
     * @param int $enroll_id
     * @return bool
     */
    public function del_enroll($enroll_id)
    {
        $enroll_id = (int)$enroll_id;
        if( $enroll_id < 1 )
        {
            $this->set_err_msg('enroll_id����ȷ');
            return false;
        }
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','del_enroll',$param);
        return $ret;
    }


    /**
     * ���±���״̬
     *
     * @param int $enroll_id
     * @param int $value	0��ʾ�ɹ���1��ʾ�򲹣�2��ʾ�ŷɻ�
     * @return bool
     */
    public function update_enroll_status($enroll_id, $value=0)
    {
        $param[] = $enroll_id;
        $param[] = $value;
        $ret = curl_event_data('event_enroll_class','update_enroll_status',$param);
        return $ret;
        
    }	
    
    /**
     * ����û���ͬһ������Ƿ��Ѿ�����
     *
     * @param int $user_id
     * @param int $event_id
     * @param string $status
     * @return bool
     */
    public function check_duplicate($user_id,$event_id,$status="all", $table_id=0)
    {

        $param[] = $user_id;
        $param[] = $event_id;
        $param[] = $status;
        $param[] = $table_id;
        $ret = curl_event_data('event_enroll_class','check_duplicate',$param);
        return $ret;
    }	
    
     /**
     * ���û��ı����б�
     *
     * @param int $user_id
     * @param string $status	����״̬��Ĭ��ȫ����0��ʾ�����ɹ���1��ʾ���������У�2��ʾ������ͨ��
     * @param bool $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_enroll_list_by_user_id($user_id, $status = '', $b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC")
    {
        $param[] = $user_id;
        $param[] = $status;
        $param[] = $b_select_count;
        $param[] = $limit;
        $param[] = $order_by;
        $ret = curl_event_data('event_enroll_class','get_enroll_list_by_user_id',$param);
        return $ret;
    }

     /**
     * �û���ĳ��������б�
     *
     * @param int    $user_id
     * @param string $event_id
     * @param bool   $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_event_enroll_list($user_id,$event_id,$b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC"){

        $event_id = (int)$event_id;
        $user_id = (int)$user_id;
        if( $event_id<1 || $user_id<1)
        {
            $this->set_err_msg('��������ȷ');
            return false;
        }

		if($user_id)  $where_str = "user_id = {$user_id}"; 
		if($event_id) $where_str .= " AND event_id = {$event_id}";

        $rows = $this->get_enroll_list($where_str,$b_select_count,$limit,$order_by);
        return $rows;

    }

    /**
     * ���û��ı����б�
     *
     * @param int $user_id
     * @param string $status    ����״̬��Ĭ��ȫ����0��ʾ��ѡ  1��ʾ�� 3��ʾ������
     * @param bool $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_enroll_list_by_user_id_v2($user_id, $status=0, $b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC")
    {
        $user_id = (int)$user_id;
        if( $user_id < 1 )
        {
            $this->set_err_msg('user_id����ȷ');
            return false;
        }
        $where_str     = "user_id = {$user_id}";
        if( !is_array($status) )
            $status    = array($status);
        $status        = array_map('intval',$status);
        $status_gather = implode( ',',$status);
        $where_str    .= " AND status IN({$status_gather})";
        $rows          = $this->get_enroll_list($where_str,$b_select_count,$limit,$order_by);
        return $rows;

    }    	
     /**
     * ����ı����б�
     *
     * @param int $event_id
     * @param string $status		����״̬��Ĭ��ȫ����0��ʾ�����ɹ���1��ʾ����������/�油��2��ʾ�ŷɻ�/��ͨ����3��ʾ��ʽ+��
     * @param bool $b_select_count
     * @param string $limit
     * @param string $order_by
     * @return array
     */

    public function get_enroll_list_by_event_id($event_id, $status = '', $b_select_count = false, $limit = "0,10", $order_by = "enroll_id DESC")
    {
        $event_id = (int)$event_id;
        if( $event_id < 1 )
        {
            $this->set_err_msg('event_id����ȷ');
            return false;
        }
        $where_str = "event_id = {$event_id}";
        if($status!=='')
        {
            $status = (int)$status;
            if (in_array($status, array(0, 1, 2))) 
            {
                $where_str .= " AND status = {$status}";
            }else{
                if($status==3)
                {
                    $where_str .= " AND status < 2";	//��ʽ�������
                }else{
                    $this->set_err_msg('status����ȷ');
                    return false;
                }
            }
        }

        $rows = $this->get_enroll_list($where_str,$b_select_count,$limit,$order_by);
        return $rows;
    }

     /**
     * ����ı����ɹ���
     *
     * @param int $event_id
     * @return int $count
     */

    public function get_enroll_count_by_event_id($event_id,$status=0)
    {
        $event_id = (int)$event_id;
        if( $event_id < 1 )
        {
            $this->set_err_msg('event_id����ȷ');
            return false;
        }
        $where_str = "event_id = {$event_id}";
        if($status!=="")
        {
            $status = (int)$status;
            if (in_array($status, array(0, 1, 2)))
            {
                $where_str .= " AND status = {$status}";
            }else{
                if($status==3)
                {
                    $where_str .= " AND status < 2";	//��ʽ�������
                }else{
                    return false;
                }
            }

        }
        $count = $this->get_enroll_list($where_str,false,'0,1','','sum(enroll_num) as c');
        $total = (int)$count[0]['c'];
        return $total;
    }

    
     /**
     * ����ĳ��α����ɹ���
     *
     * @param int $event_id
     * @param array $status
     * @param int $table_id
     * @return int $count
     * 
     */

    public function get_enroll_count_by_event_id_v2($event_id,$status=array(0,1),$table_id=0)
    {
        $param[] = $event_id;
        $param[] = $status;
        $param[] = $table_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_count_by_event_id_v2',$param);
        return $ret;
    }
    
    /**
     * ��������Ƿ�����
     *
     * @param int $enroll_num	��������
     * @param int $event_id		��������id
     * @return bool
     */

    public function check_is_full($enroll_num=1,$event_id, $table_id,$status=array(0,1))
    {
        $param[] = $enroll_num;
        $param[] = $event_id;
        $param[] = $table_id;
        $param[] = $status;
        $ret = curl_event_data('event_enroll_class','check_is_full',$param);
        return $ret;
    }
            

    /**
     * ͬ�����»�������������ʽ��
     *
     * @param int $event_id	�id
     * @return bool
     */

    public function update_event_join_count_by_event_id($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','update_event_join_count_by_event_id',$param);
        return $ret;
    }	
    
    /**
     * ���»active_time
     *
     * @param int $event_id	�id
     * @return bool
     */

    public function update_event_active_time_by_event_id($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','update_event_active_time_by_event_id',$param);
        return $ret;
    }	
    
    
    /**
     * ���ӻ��̬
     *
     * @param int $event_id		�id
     * @param int $user_id		�û�id
     * @return bool
     */

    public function update_event_feed_by_event_id_and_user_id($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','update_event_feed_by_event_id_and_user_id',$param);
        return $ret;
    }	

    /**
     * ���ݻID���û�ID����ȡ��������
     *
     * @param int $event_id
     * @param int $user_id
     * @return array
     */
    public function get_enroll_info_by_event_id_and_user_id($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_info_by_event_id_and_user_id',$param);
        return $ret;
    }

    
    /**
     * ���ݻID���û�ID����ȡ��������(���û����б�������)
     *
     * @param int $event_id
     * @param int $user_id
     * @return array
     */
    public function get_enroll_info_by_event_id_and_user_id_v2($event_id,$user_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_info_by_event_id_and_user_id_v2',$param);
        return $ret;
    }	
    
    
    
    /**
     * ���ݻID���û�ID������ID����ȡ��������
     *
     * @param int $event_id
     * @param int $user_id
     * @param int $table_id
     * @return array
     */
    public function get_enroll_info_by_event_id_and_user_id_and_table_id($event_id,$user_id,$table_id)
    {
        $param[] = $event_id;
        $param[] = $user_id;
        $param[] = $table_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_info_by_event_id_and_user_id_and_table_id',$param);
        return $ret;
    }
    

    
    /**
     * ����������Ƿ���ϱ����ʸ�
     *
     * @param int $user_id		�û�id
     * @param int $event_id		��������id
     * @param string $join_mode	������ʽ
     * @return bool	
     */

    public function check_join_mode_enroll($user_id, $event_id)
    {
        $param[] = $user_id;
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','check_join_mode_enroll',$param);
        return $ret;
    }


    /**
     * ��������Ƿ����,�Ƿ��Ѿ�����
     *
     * @param int $user_id		�û�id
     * @param int $event_id		��������id
     * @return string
     * ���ء�D����ʾ�˻���û��Ѿ�������;
     * ���ء�O����ʾ�Ѿ�����ʱ����ڣ�����ʧ��;
     * ���ء�N����ʾδ����;
     * ���ء�F����ʾ�˻������������������ʧ��;
     *
     */
    public function check_enroll_full($user_id, $event_id)
    {
        $param[] = $user_id;
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','check_enroll_full',$param);
        return $ret;
    }

    /**
     * ��������Ƿ����,�Ƿ��Ѿ�����
     *
     * @param int $user_id		�û�id
     * @param int $event_id		��������id
     * @param int $enroll_num	��������
     * @return string	���ء�O����ʾ�Ѿ�����ʱ����ڣ�����ʧ��;���ء�D����ʾ�˻���û��Ѿ�������������ʧ��;���ء�N����ʾδ���������Ա���
     */


    public function check_enroll($user_id, $event_id)
    {
        $param[] = $user_id;
        $param[] = $event_id;
        $ret = curl_event_data('event_enroll_class','check_enroll',$param);
        return $ret;
    }		


    /**
     *
     * ��ȡ�������
     *
     *
     * */
     public function get_enroll_cost($enroll_id)
    {
        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','get_enroll_cost',$param);
        return $ret;

    }

    /**
     * ͨ�����enroll_id ��ȡ���������Ϣ
     * @param  array  $enroll_id_arr
     * @param  string $pay_status  
     * @return array
     */
    function get_enroll_cost_by_arr( $enroll_id_arr,$pay_status=0 ){

        $param[] = $enroll_id_arr;
        $param[] = $pay_status;
        $ret = curl_event_data('event_enroll_class','get_enroll_cost_by_arr',$param);
        return $ret;
    }

    /**
     * ��ȡĳ������Ӧ�ĳ���
     *
     * @param int $enroll_id ����ID
     * @return  $enroll_rel_table ����
     *
     * */       
    public function get_event_table_by_enroll_id($enroll_id){

        $param[] = $enroll_id;
        $ret = curl_event_data('event_enroll_class','get_event_table_by_enroll_id',$param);
        return $ret;

    }

    /**
     *
     *���±���֧��״̬ 0=>��֧�� 1=>��֧�� 2=>��ȡ��
     *
     **/
    public function update_enroll_pay_status($enroll_id, $status)
    {
        $enroll_id = (int)$enroll_id;
        $where_str = "enroll_id = $enroll_id";
        switch($status)
        {	
            case 0:
                $data['pay_status'] = 0;
                $data['pay_time']	= time();
                curl_event_data('event_enroll_class','update_api',array($data,$where_str));
                break;
            case 1:
                $data['pay_status']         = 1;
                $data['pay_time']	        = time();
                $enroll_info                = $this->get_enroll_by_enroll_id($enroll_id);
                //��鱨����������ѡ���򲹣��Ƿ�����
                $return                     = $this->check_is_full($enroll_info['enroll_num'],$enroll_info['event_id'],$enroll_info['table_id'],array(0));
                $return ? $data['status']   = 1:$data['status'] = 0;
                $ret                        = curl_event_data('event_enroll_class','update_api',array($data,$where_str));


                $log_arr['ret'] = $ret;
                $log_arr['enroll_id'] = $enroll_id;
                $log_arr['status'] = $status;
                pai_log_class::add_log($log_arr, 'ret', 'update_enroll');

                if( $ret ){
                    //���ɶ�ά��
                    include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
                    $code_obj       = POCO::singleton('pai_activity_code_class');
                    $event_obj      = POCO::singleton('event_details_class');
                    $pai_sms_obj    = POCO::singleton('pai_sms_class');
                    $pai_user_obj   = POCO::singleton('pai_user_class');
                    $pai_config_obj                = POCO::singleton('pai_config_class' );
                    $special_event_arr             = $pai_config_obj->big_waipai_event_id_arr();
                    $special_big_waipai_event_arr  = $pai_config_obj->big_waipai_event_id_arr('big_waipai');
                    $event_info         = $event_obj->get_event_by_event_id($enroll_info['event_id']);
                    $enroll_table       = $this->get_event_table_by_enroll_id($enroll_id);
                    $enroll_user_id     = get_relate_yue_id($enroll_info['user_id']);
                    $enroll_user_phone  = $pai_user_obj->get_phone_by_user_id($enroll_user_id);
                    $event_user_id      = get_relate_yue_id($event_info['user_id']);
                    //ecpay_log_class::add_log(array_merge($enroll_info,array('share_event_id'=>$_REQUEST['share_event_id'],'share_phone'=>$_REQUEST['share_phone'])), __FILE__ . '::' . __LINE__, 'info20150306');
                    

                    if( $data['status'] == 0 ){
                        //��ѡ����Ҫ��  
                        $table_obj     = POCO::singleton('event_table_class');
                        //�ó������Ƶ�����
                        $limit_num     = $table_obj->get_table_num($enroll_info['event_id'],$enroll_info['table_id']);
                        //��ȡ�ó����Ѳμӵ�����
                        $enroll_count  = $this->get_enroll_count_by_event_id_v2($enroll_info['event_id'],array(0),$enroll_info['table_id']);
                        $enroll_count -= $enroll_info['enroll_num'];
                        //�ӿڷ��ص���������  Ҫ��ȥ��ǰ�μӵģ��ó�����ǰ������Ѳμӵ�������
                        //ecpay_log_class::add_log(array('limit_num'=>$limit_num,'enroll_count'=>$enroll_count,'enroll_info'=>$enroll_info), __FILE__ . '::' . __LINE__, '210data1');

                        if( ($limit_num > $enroll_count) && ($enroll_count+$enroll_info['enroll_num']) >= $limit_num ){
                            //����Ϣ����֯��  ֪ͨ�������Ѵ��
                            $app_msg    = "�㷢����{$event_info['title']}���{$enroll_table}�����������Ѵﵽ���Ҫ��";
                            $url        = "/mall/user/act/detail.php?event_id={$enroll_info['event_id']}";
                            send_message_for_10002 ( $event_user_id,$app_msg,$url );

                        }

                    }

					//��ʱ����   ��ά�����Ҫ��pai �� user_id
                    $code_obj->create_multi_code($enroll_info['enroll_num'],$event_user_id,$event_info['event_id'],$enroll_id);
                    //��ȡ��֤��
                    /*special_event_arr ����Ļ����Ҫɨ��  ����Ҫ������*/
                    $activity_code_arr = $code_obj->get_code_by_enroll_id($enroll_id);
                    if( !empty($activity_code_arr)  )
                    {
                        $leader_info_arr=unserialize($event_info['leader_info']);
                        $leader_info = $leader_info_arr[0]['mobile'];

                         foreach( $activity_code_arr as $k=>$v ){

                             //$v['code'] = activity_code_transfer($v['code']);


                             if( $data['status'] == 0 ){
                                //��ѡҪ  ����+appС����
                                $group_key   = 'G_PAI_WAIPAI_CODE_NUMBER';
                                $sms_data    = array(

                                     'event_title'   => $event_info['title'],
                                     'activity_code' => $v['code'],
                                     'table_num'     => $enroll_table,
                                     'cellphone'     => $leader_info

                                );
                                $pai_sms_obj->send_sms($enroll_user_phone, $group_key,$sms_data);
                                $app_msg   = "��ϲ��,{$event_info['title']}��ĵ�{$enroll_table}�������ɹ�,������ͨ��APP��ʾ��ά����ṩ��������{$v['code']}ǩ������֯����ϵ��ʽ��".$leader_info;
                                $url = "/mall/user/act/detail.php?event_id={$enroll_info['event_id']}";
                                send_message_for_10002 ( $enroll_user_id,$app_msg,$url,'yuebuyer' );
  
                             }
                             elseif( $data['status'] == 1 ){

                                 $app_msg   = "��ϲ��,�ѳ�Ϊ�{$event_info['title']}��{$enroll_table}���ĺ򲹣�������ͨ��APP��ʾ��ά����ṩ��������{$v['code']}ǩ������֯����ϵ��ʽ��".$leader_info;

                                 $url = "/mall/user/act/detail.php?event_id={$enroll_info['event_id']}";
                                 send_message_for_10002 ( $enroll_user_id,$app_msg,$url,'yuebuyer' );
                                 
                                $group_key   = 'G_PAI_WAIPAI_CODE_NUMBER_B';
                                $sms_data    = array(

                                     'event_title'   => $event_info['title'],
                                     'activity_code' => $v['code'],
                                     'table_num'     => $enroll_table,
                                     'cellphone'     => $leader_info

                                );
                                $pai_sms_obj->send_sms($enroll_user_phone, $group_key,$sms_data);
                             }

                         }

                    }

                }
                return $ret;
                break;
            case 2:
                $data['pay_status'] = 2;
                $data['pay_time']	= time();
                curl_event_data('event_enroll_class','update_api',array($data,$where_str));
                break;
            default:
                return false;

        }
    }

    public function update_enroll_join_event($enroll_id, $status)
    {
        $enroll_id = (int)$enroll_id;
        $where_str = "enroll_id = $enroll_id";

        $data['join_event'] = $status;
        return curl_event_data('event_enroll_class','update_api',array($data,$where_str));
    }

    
    /**
     * ����û��Ƿ�߱�������Ȩ��
     * @param int $event_id       �ID
     * @param int $enroll_user_id ������ID
     * @return bool
     */
    public function check_join_mode_auth( $event_id,$enroll_user_id ){

        $param[] = $event_id;
        $param[] = $enroll_user_id;
        $ret = curl_event_data('event_enroll_class','check_join_mode_auth',$param);
        return $ret;

    }
    
    public function sum_enroll_num($event_id,$table_id=0,$status='0')
    {

        $param[] = $event_id;
        $param[] = $table_id;
        $param[] = $status;
        $ret = curl_event_data('event_enroll_class','sum_enroll_num',$param);
        return $ret;
    }
    
}
?>