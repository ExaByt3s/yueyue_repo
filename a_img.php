<?php
require_once('poco_app_common.inc.php');

$os             = $_GET['os'];
$osver          = $_GET['osver'];
$appver         = $_GET['appver'];
$machinecode    = $_GET['machinecode'];
$is_developer   = $_GET['is_developer'];
$model          = $_GET['model']?$_GET['model']:'';


if(version_compare($appver, '1.1.0', '>=')) return 0;

$user_id	    = $_COOKIE['yue_member_id'];
if($user_id)
{
    $sql_str = "REPLACE INTO pai_nfc_db.yuepai_mobile_info_tbl(pocoid, os, osver, appver, machinecode) 
                VALUES (:x_user_id, :x_os_xx, :x_osver, :x_appver, :x_machinecode)";
	sqlSetParam($sql_str, 'x_user_id',			$user_id);
	sqlSetParam($sql_str, 'x_os_xx',			$os);
	sqlSetParam($sql_str, 'x_osver',			$osver);
	sqlSetParam($sql_str, 'x_appver',			$appver);
	sqlSetParam($sql_str, 'x_machinecode',		$machinecode);	
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
    	//$req_param['ios_app_type']='pro'; //'dev','pro'
        
        //if($is_developer == 1) $req_param['ios_app_type']='dev';
        $req_param['is_logout']=0;
//        print_r($req_param);
//        echo "--------------";
        $result= $gmclient->doBackground("save_pushtoken",json_encode($req_param) );
    }
    while(false);
    //while($gmclient->returnCode() != GEARMAN_SUCCESS);
    
}

//echo "OK";

?>