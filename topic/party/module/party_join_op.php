<?php
/** /* 
 * 活动参加操作页
 * 
 * author 星星
 * 
 * 2014-8-1
 */

include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
//取得应用操作对象实例
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
$action_control = trim($_INPUT['action_control']);//(pay_first-首次报名支付,pay_again-未支付的再支付)
$pay_again_enroll_id = (int)$_INPUT['pay_again_enroll_id'];
//统一用数组处理
$pay_again_enroll_id_arr = array($pay_again_enroll_id);
$pay_again_enroll_id_str   = implode(',',$pay_again_enroll_id_arr);

//var_dump($pay_again_enroll_id_arr);
//var_dump($pay_again_enroll_id_str);
//var_dump($action_control);



if(empty($yue_login_id))
{
    echo "<script>parent.alert('你还未登陆');</script>";
    exit();
}

if(empty($action_control))
{
    echo "<script>parent.alert('处理状态出错，请刷新再报');</script>";
    exit();
}
//查询对应的poco_id
$poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);


//根据报名情况处理判断
if($action_control=="pay_first")
{
    if( empty($num) )
    {
        echo "<script>parent.alert('参与人数参数有误');</script>";
        exit();
    }
    if(  empty($phone ) ){

        echo "<script>parent.alert('手机号码有误');</script>";
        exit();

    }

    if(empty($event_id))
    {
        echo "<script>parent.alert('活动参数有误');</script>";
        exit();
    }
}

$event_info  = $details_obj->get_event_by_event_id($event_id);

if(!empty($sequence_num))
{
    //判断支付的处理
    $join_mode_auth = $enroll_obj->check_join_mode_auth( $event_id,$poco_login_id );
    if($action_control=="pay_first")
    {
        foreach($sequence_num as $key => $value)
        {
            if( $value<1 )
            {
                continue;
            }
            $data['user_id']		= $poco_login_id;     //用户的POCOID　
            $data['event_id']		= $event_id;     //活动ID       
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

                //没有join_mode（参与方式） 权限  需要组织者进行审批
                $data['status']     = 3;
                $data['is_accept']  = 0;
                $session            = $key + 1;
                $enroll_user        = POCO::execute(array('member.get_user_nickname_by_user_id'), array($data['user_id']));
                $msg                = "{$enroll_user} 申请参与你的活动 \"{$event_info['title']}\" 第{$session}场  请审核"; 
                POCO::execute(array('pm.add_new_notify_msg'), array($event_info['user_id'],'活动通知',$msg,$notify_ext_par));

            }

            $enroll_id_tmp  = $enroll_obj->add_enroll_v2($data);
            if( $enroll_id_tmp ==-1 )
            {
                echo "<script>parent.alert('很抱歉，活动已经开始了,不能进行报名了');parent.reload();</script>";//暂时处理
                exit();

            }
            if( $enroll_id_tmp ==-2 )
            {
                $changci = $key+1;
                echo "<script>parent.alert('第{$changci}场重复报名了');parent.reload();</script>";//暂时处理
                exit();

            }
            if( $enroll_id_tmp ==-3 )
            {
                
                echo "<script>parent.alert('用户为组织者不能报名');parent.reload();</script>";//暂时处理
                exit();

                
            }
            if( $enroll_id_tmp ==-5)
            {
                $changci = $key+1;
                echo "<script>parent.alert('第{$changci}场已经关闭，不能报名');parent.reload();</script>";//暂时处理
                exit();
            }
            $enroll_id_arr[] = $enroll_id_tmp;

        }
        if( empty($enroll_id_arr) )
        {
            echo "<script>parent.alert('请选择场次');</script>";
            exit();
        }
        if( !$join_mode_auth )
        {
            $location_href = "http://www.yueus.com/topic/party_sign_list.php?event_id={$event_id}";
            echo "<script>parent.alert('你的报名申请已提交，等待组织者审核。');</script>";
            echo '<script>parent.location.href="'.$location_href.'"</script>';
            die();

        }
    }
    else if($action_control=="pay_again")
    {
        if(empty($pay_again_enroll_id))
        {
            echo "<script>parent.alert('请选择未支付的场次');</script>";
            exit();
        }
        
        $where_str 		 = " user_id={$poco_login_id} AND enroll_id IN({$pay_again_enroll_id_str})"; 
        $enroll_list 	 = $enroll_obj->get_enroll_list($where_str);
        /*确认是否当前用户所报名的活动*/
        if( empty($enroll_list)){

            echo "<script>alert('参与id非法 请确认是否为本人参与的活动');</script>";
            $location_href = 'index.php';
            echo '<script>parent.location.href="'.$location_href.'"</script>';
            exit();

        }
        
        /*防止点击去支付的时候 支付成功后。页面没刷新，用户点击去再次进行支付。*/
        foreach($enroll_list AS $k=>$v){

            if( $v['status'] == 3 && $v['is_accept']==1 ){
                
                $__enroll_list[] =  $v;       

            }

        }
        
        //var_dump($enroll_list);
        //var_dump($__enroll_list);
        //die();
        
        if( count( $enroll_list ) != count( $__enroll_list ) ){

            echo "<script>alert('需要支付的场次有误,请确认是否已支付成功。');</script>";
            $location_href = 'index.php';
            echo '<script>parent.location.href="'.$location_href.'"</script>';
            exit();

        }
        /*防止点击去支付的时候 支付成功后。页面没刷新，用户点击去再次进行支付。*/
        $enroll_id_str = $pay_again_enroll_id_str;
        $enroll_id_arr = $pay_again_enroll_id_arr;
        
    }
    
    
}





//收藏处理
if($form_control=="collect")
{
    //设置一个COOKIE
    setcookie('enroll_res',"2",time()+3600,"/","event.poco.cn");
    $location_href = "http://event.poco.cn/event_sign_list.php?event_id={$event_id}";
    echo '<script>parent.location.href="'.$location_href.'"</script>';
    die('收藏成功');
}



//公共给钱处理
$sum_cost = 0;
foreach($enroll_id_arr AS $key=>$enroll_id)
{
    $cost       = $enroll_obj->get_enroll_cost($enroll_id);
    $sum_cost  += $cost;

}
if( $sum_cost > 0 ){
    
    //非免费活动的情况
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
    
    die('不可以免费');
    $ret = $payment_obj->pay_enroll_by_balance($event_id,$enroll_id_arr);
    if( $ret['error'] == 0 ){

        //设置一个COOKIE
        setcookie('enroll_res',"1",time()+3600,"/","www.yueus.com");
        echo "<script>parent.location.href='http://www.yueus.com/topic/party_sign_list.php?event_id={$event_id}'</script>";
    
    }
    else{

        echo "<script>parent.alert('处理失败');</script>";
        exit();

    }
}

?>