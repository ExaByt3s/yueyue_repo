<?php
include_once("../poco_app_common.inc.php");

$sql_str = "SELECT app_id FROM pai_nfc_db.yuepai_update_notice_tbl 
            WHERE state = 0";
echo $sql_str;
$result  = db_simple_getdata($sql_str, TRUE, 101);

foreach($result AS $key=>$val)
{
    if(exeute_get_app_version_by_user_id($val['app_id']) == '1.0.2')
    {
        $data['send_user_id']   = '10002';
        $data['to_user_id']     = (string)$val['app_id'];  
        $data['media_type']     = 'notify';
        $data['content']        = iconv('gbk', 'utf-8', '你好，系统已经升级，新增主次风格、时间单位和续拍价格，请点击“我的”—>“更新模特卡”—>“修改价格”进行设置，补充风格、时间单位和续拍价格！');
        $data['link_url']       = 'http://yp.yueus.com/mobile/app?from_app=1#model_date/model_card/edit_all/model_price';
        $data['wifi_url']       = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_date/model_card/edit_all/model_price';
        
        include_once("../include/pai_information_push.inc.php");
        $send_obj = new pai_information_push();
        $result = $send_obj->send_msg($data);
        if($result['code'] == 1)
        {
            $sql_str = "UPDATE pai_nfc_db.yuepai_update_notice_tbl 
                        SET state = 1 
                        WHERE app_id=$val[app_id]";
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }
}

function exeute_get_app_version_by_user_id($user_id)
{
    $user_id = (int)$user_id;
    if($user_id)
    {
        $sql_str = "SELECT appver FROM pai_nfc_db.yuepai_mobile_info_tbl 
                    WHERE pocoid=$user_id"; 
        $result  = db_simple_getdata($sql_str, TRUE, 101);
        
        if($result['appver'])
        {
            return $result['appver'];
        }
                  
    }
    
    return FALSE;

}


function exeute_get_app_version_by_user_id_v2($user_id)
{
    $gmclient= new GearmanClient();
    $gmclient->addServers("113.107.204.233:9830");
    do
    {
    	
    	$req_param['pocoid']='6468095';
        //echo json_encode($req_param);	
        $result= $gmclient->do("get_pushtoken",json_encode($req_param) );
    }
    while($gmclient->returnCode() != GEARMAN_SUCCESS);
    
    $result_array = json_decode($result, TRUE);
    
    if($result_array['retcode'] == '0000')
    {
        return $result_array['appver'];
    }
    
    return FALSE;
}
?>