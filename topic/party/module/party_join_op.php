<?php
/** /* 
 * ��μӲ���ҳ
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
$details_obj  = POCO::singleton('event_details_class');
$payment_obj  = POCO::singleton('pai_payment_class');
$relate_obj   = POCO::singleton('pai_relate_poco_class');

$num          = (int)$_INPUT['num'];
$sequence_num = $_INPUT['sequence_num'];
$phone        = (int)$_INPUT['phone'];
$event_id     = (int)$_INPUT['event_id'];
$form_control = trim($_INPUT['form_control']);
$action_control = trim($_INPUT['action_control']);//(pay_first-�״α���֧��,pay_again-δ֧������֧��)
$pay_again_enroll_id = (int)$_INPUT['pay_again_enroll_id'];
//ͳһ�����鴦��
$pay_again_enroll_id_arr = array($pay_again_enroll_id);
$pay_again_enroll_id_str   = implode(',',$pay_again_enroll_id_arr);

//var_dump($pay_again_enroll_id_arr);
//var_dump($pay_again_enroll_id_str);
//var_dump($action_control);



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


//���ݱ�����������ж�
if($action_control=="pay_first")
{
    if( empty($num) )
    {
        echo "<script>parent.alert('����������������');</script>";
        exit();
    }
    if(  empty($phone ) ){

        echo "<script>parent.alert('�ֻ���������');</script>";
        exit();

    }

    if(empty($event_id))
    {
        echo "<script>parent.alert('���������');</script>";
        exit();
    }
}

$event_info  = $details_obj->get_event_by_event_id($event_id);

if(!empty($sequence_num))
{
    //�ж�֧���Ĵ���
    $join_mode_auth = $enroll_obj->check_join_mode_auth( $event_id,$poco_login_id );
    if($action_control=="pay_first")
    {
        foreach($sequence_num as $key => $value)
        {
            if( $value<1 )
            {
                continue;
            }
            $data['user_id']		= $poco_login_id;     //�û���POCOID��
            $data['event_id']		= $event_id;     //�ID       
            $data['phone']          = $phone;
            $data['enroll_num']		= $num;         
            $data['enroll_ip']      = ip2long($_INPUT['IP_ADDRESS']);
            $data['table_id']       = $value;

            if( $join_mode_auth )
            {
                    
                    $data['status'] = 3;
            }
            else
            {

                //û��join_mode�����뷽ʽ�� Ȩ��  ��Ҫ��֯�߽�������
                $data['status']     = 3;
                $data['is_accept']  = 0;
                $session            = $key + 1;
                $enroll_user        = POCO::execute(array('member.get_user_nickname_by_user_id'), array($data['user_id']));
                $msg                = "{$enroll_user} ���������Ļ \"{$event_info['title']}\" ��{$session}��  �����"; 
                POCO::execute(array('pm.add_new_notify_msg'), array($event_info['user_id'],'�֪ͨ',$msg,$notify_ext_par));

            }

            $enroll_id_tmp  = $enroll_obj->add_enroll_v2($data);
            if( $enroll_id_tmp ==-1 )
            {
                echo "<script>parent.alert('�ܱ�Ǹ����Ѿ���ʼ��,���ܽ��б�����');parent.reload();</script>";//��ʱ����
                exit();

            }
            if( $enroll_id_tmp ==-2 )
            {
                $changci = $key+1;
                echo "<script>parent.alert('��{$changci}���ظ�������');parent.reload();</script>";//��ʱ����
                exit();

            }
            if( $enroll_id_tmp ==-3 )
            {
                
                echo "<script>parent.alert('�û�Ϊ��֯�߲��ܱ���');parent.reload();</script>";//��ʱ����
                exit();

                
            }
            if( $enroll_id_tmp ==-5)
            {
                $changci = $key+1;
                echo "<script>parent.alert('��{$changci}���Ѿ��رգ����ܱ���');parent.reload();</script>";//��ʱ����
                exit();
            }
            $enroll_id_arr[] = $enroll_id_tmp;

        }
        if( empty($enroll_id_arr) )
        {
            echo "<script>parent.alert('��ѡ�񳡴�');</script>";
            exit();
        }
        if( !$join_mode_auth )
        {
            $location_href = "http://www.yueus.com/topic/party_sign_list.php?event_id={$event_id}";
            echo "<script>parent.alert('��ı����������ύ���ȴ���֯����ˡ�');</script>";
            echo '<script>parent.location.href="'.$location_href.'"</script>';
            die();

        }
    }
    else if($action_control=="pay_again")
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
    
    
}





//�ղش���
if($form_control=="collect")
{
    //����һ��COOKIE
    setcookie('enroll_res',"2",time()+3600,"/","event.poco.cn");
    $location_href = "http://event.poco.cn/event_sign_list.php?event_id={$event_id}";
    echo '<script>parent.location.href="'.$location_href.'"</script>';
    die('�ղسɹ�');
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
    
    $share_event_id = $_COOKIE['share_event_id'];
    $share_phone = $_COOKIE['share_phone'];
    $recharge_ret   = $payment_obj->submit_recharge('activity_pc', $yue_login_id, $amount, $third_code='alipay', $event_id, $enroll_id_str,0,array('channel_return'=>'http://www.yueus.com/topic/party/party_pay_middle_jump.php','channel_notify' => "http://event.poco.cn/activity_join_notify.php?share_event_id={$share_event_id}&share_phone={$share_phone}"));

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