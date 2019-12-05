<?php
/** /* 
 * 活动参加操作页（暂不使用）
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
$payment_obj  = POCO::singleton('pai_payment_class');
$relate_obj   = POCO::singleton('pai_relate_poco_class');
$event_id     = 

$action_control = trim($_INPUT['again_action_control']);//(pay_first-首次报名支付,pay_again-未支付的再支付)
$pay_again_enroll_id = (int)$_INPUT['again_pay_again_enroll_id'];
$event_id = (int)$_INPUT['again_pay_again_event_id'];
//统一用数组处理
$pay_again_enroll_id_arr = array($pay_again_enroll_id);
$pay_again_enroll_id_str   = implode(',',$pay_again_enroll_id_arr);





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




    //判断支付的处理
    if($action_control=="pay_again")
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