<?php
/** /* 
 * ��μӲ���ҳ���ݲ�ʹ�ã�
 * 
 * author ����
 * 
 * 2014-8-1
 */

include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//ȡ��Ӧ�ò�������ʵ��
define("G_DB_GET_REALTIME_DATA",1);
$enroll_obj   = POCO::singleton('event_enroll_class');
$payment_obj  = POCO::singleton('pai_payment_class');
$relate_obj   = POCO::singleton('pai_relate_poco_class');
$event_id     = 

$action_control = trim($_INPUT['again_action_control']);//(pay_first-�״α���֧��,pay_again-δ֧������֧��)
$pay_again_enroll_id = (int)$_INPUT['again_pay_again_enroll_id'];
$event_id = (int)$_INPUT['again_pay_again_event_id'];
//ͳһ�����鴦��
$pay_again_enroll_id_arr = array($pay_again_enroll_id);
$pay_again_enroll_id_str   = implode(',',$pay_again_enroll_id_arr);





if(empty($yue_login_id))
{
    echo "<script>parent.alert('�㻹δ��½');</script>";
    exit();
}

if(empty($action_control))
{
    echo "<script>parent.alert('����״̬������ˢ���ٱ�');</script>";
    exit();
}
//��ѯ��Ӧ��poco_id
$poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);




    //�ж�֧���Ĵ���
    if($action_control=="pay_again")
    {
        if(empty($pay_again_enroll_id))
        {
            echo "<script>parent.alert('��ѡ��δ֧���ĳ���');</script>";
            exit();
        }
        
        $where_str 		 = " user_id={$poco_login_id} AND enroll_id IN({$pay_again_enroll_id_str})"; 
        $enroll_list 	 = $enroll_obj->get_enroll_list($where_str);
        /*ȷ���Ƿ�ǰ�û��������Ļ*/
        if( empty($enroll_list)){

            echo "<script>alert('����id�Ƿ� ��ȷ���Ƿ�Ϊ���˲���Ļ');</script>";
            $location_href = 'index.php';
            echo '<script>parent.location.href="'.$location_href.'"</script>';
            exit();

        }
        
        /*��ֹ���ȥ֧����ʱ�� ֧���ɹ���ҳ��ûˢ�£��û����ȥ�ٴν���֧����*/
        foreach($enroll_list AS $k=>$v){

            if( $v['status'] == 3 && $v['is_accept']==1 ){
                
                $__enroll_list[] =  $v;       

            }

        }
        
        //var_dump($enroll_list);
        //var_dump($__enroll_list);
        //die();
        
        if( count( $enroll_list ) != count( $__enroll_list ) ){

            echo "<script>alert('��Ҫ֧���ĳ�������,��ȷ���Ƿ���֧���ɹ���');</script>";
            $location_href = 'index.php';
            echo '<script>parent.location.href="'.$location_href.'"</script>';
            exit();

        }
        /*��ֹ���ȥ֧����ʱ�� ֧���ɹ���ҳ��ûˢ�£��û����ȥ�ٴν���֧����*/
        $enroll_id_str = $pay_again_enroll_id_str;
        $enroll_id_arr = $pay_again_enroll_id_arr;
        
    }
    
    





//������Ǯ����
$sum_cost = 0;
foreach($enroll_id_arr AS $key=>$enroll_id)
{
    $cost       = $enroll_obj->get_enroll_cost($enroll_id);
    $sum_cost  += $cost;

}


if( $sum_cost > 0 ){
    
    //����ѻ�����
    $amount         = $sum_cost;
    $enroll_id_str  = implode(',', $enroll_id_arr);
    $recharge_ret   = $payment_obj->submit_recharge('activity_pc', $yue_login_id, $amount, $third_code='alipay', $event_id, $enroll_id_str,0,array('channel_return'=>'http://www.yueus.com/topic/party/party_pay_middle_jump.php'));

    if( $recharge_ret['error']===0 )
    {
        $request_data = trim($recharge_ret['request_data']);
        echo '<script>parent.location.href="'.$request_data.'"</script>';
    }
    else
    {
        print_r($recharge_ret);
       
    }

}
else{
    
    die('���������');
    $ret = $payment_obj->pay_enroll_by_balance($event_id,$enroll_id_arr);
    if( $ret['error'] == 0 ){

        //����һ��COOKIE
        setcookie('enroll_res',"1",time()+3600,"/","www.yueus.com");
        echo "<script>parent.location.href='http://www.yueus.com/topic/party_sign_list.php?event_id={$event_id}'</script>";
    
    }
    else{

        echo "<script>parent.alert('����ʧ��');</script>";
        exit();

    }
}

?>