<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$os             = $client_data['data']['param']['os'];
$osver          = $client_data['data']['param']['osver'];
$appver         = $client_data['data']['param']['appver'];
$machinecode    = $client_data['data']['param']['machinecode'];
$is_developer   = $client_data['data']['param']['is_developer'];
$model          = $client_data['data']['param']['model'];

if($user_id)
{
    $sql_str = "REPLACE INTO pai_nfc_db.yuepai_mobile_info_tbl(pocoid, os, osver, appver, machinecode)
                VALUES (:x_user_id, :x_os_xx, :x_osver, :x_appver, :x_machinecode)";
    sqlSetParam($sql_str, 'x_user_id',			$user_id);
    sqlSetParam($sql_str, 'x_os_xx',			$os);
    sqlSetParam($sql_str, 'x_osver',			$osver);
    sqlSetParam($sql_str, 'x_appver',			$appver);
    sqlSetParam($sql_str, 'x_machinecode',	$machinecode);
    db_simple_getdata($sql_str, TRUE, 101);

    $gmclient= new GearmanClient();
    $gmclient->addServers("172.18.5.211:9830");
    $gmclient->setTimeout(5000); // ÉèÖÃ³¬Ê±
    do
    {

        $req_param['pocoid']=$user_id;
        $req_param['os']=$os;
        $req_param['osver']=$osver;
        $req_param['appver']=$appver ;
        $req_param['machinecode']=$machinecode;
        $req_param['model']=$model;

        if($is_developer == 1)
        {
            $req_param['ios_app_type']='dev';
        }

        if($is_developer == 0)
        {
            $req_param['ios_app_type']='pro';
        }

        if($is_developer == 'ent_pro')
        {
            $req_param['ios_app_type']='ent_pro';
        }

        if($is_developer == 'ent_dev')
        {
            $req_param['ios_app_type']='ent_dev';
        }

        $req_param['is_logout']=0;
        $result= $gmclient->doBackground("save_pushtoken",json_encode($req_param) );
    }
    while(false);
    //while($gmclient->returnCode() != GEARMAN_SUCCESS);

}


$options['data'] = array('result'=>'OK');

$cp->output($options);
?>