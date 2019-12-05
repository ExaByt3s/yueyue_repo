<?php
class pai_nfc_class extends POCO_TDG 
{
	/**
	 * 构造函数
	 *
	 */
	public function __construct() {
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_nfc_db' );
		$this->setTableName ( 'yuepai_mobile_info_tbl' );
	}
    
    public function add_mobile_info($user_id, $os, $os_ver, $appver, $machinecode, $is_developer)
    {
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
            
            /*$gmclient= new GearmanClient();
            $gmclient->addServers("172.18.5.211:9830");
            $gmclient->setTimeout(5000); // 设置超时
            do
            {
            	
            	$req_param['pocoid']=$user_id;
            	$req_param['os']=$os;
            	$req_param['osver']=$osver;
            	$req_param['appver']=$appver ;
            	$req_param['machinecode']=$machinecode;
            	$req_param['ios_app_type']='pro'; //'dev','pro'
                if($is_developer == 1) $req_param['ios_app_type']='dev';
                $req_param['is_logout']=0;
                $result= $gmclient->doBackground("save_pushtoken",json_encode($req_param) );
            }
            while(false);*/
            //while($gmclient->returnCode() != GEARMAN_SUCCESS);

            $gmclient = POCO::singleton('pai_gearman_base_class');
            $gmclient->connect('172.18.5.211', '9830');

            $req_param['pocoid']        =$user_id;
            $req_param['os']            =$os;
            $req_param['osver']         =$osver;
            $req_param['appver']        =$appver ;
            $req_param['machinecode']   =$machinecode;
            $req_param['ios_app_type']  ='pro'; //'dev','pro'
            if($is_developer == 1) $req_param['ios_app_type']='dev';
            $req_param['is_logout']=0;

            $gmclient->_doBackground("save_pushtoken",$req_param);
            
        }
    } 
    
    public function del_mobile_info($user_id)
    {
        if($user_id)
        {
            $sql_str = "DELECT FROM pai_nfc_db.yuepai_mobile_info_tbl WHERE pocoid = $user_id";
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }   
    
    public function mobile_logout($user_id, $role)
    {
        /*$gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // 设置超时
        do
        {
        	
        	$req_param['pocoid']=$user_id;
            if($role) $req_param['app_role'] = $role;
            $req_param['is_logout']=1;
            if($req_param['app_role'])
            {
                $result= $gmclient->doBackground("save_base_info",json_encode($req_param) );
            }else{
                $result= $gmclient->doBackground("save_pushtoken",json_encode($req_param) );
            }
            //$result= $gmclient->doBackground("save_pushtoken",json_encode($req_param) );
        }
        while(false);
        //while($gmclient->returnCode() != GEARMAN_SUCCESS);
        return $gmclient->returnCode();*/

        $gmclient = POCO::singleton('pai_gearman_base_class');
        $gmclient->connect('172.18.5.211', '9830');

        $req_param['pocoid']    =$user_id;
        if($role) $req_param['app_role'] = $role;
        $req_param['is_logout']=1;

        if($req_param['app_role'])
        {
            $gmclient->_doBackground("save_base_info",$req_param);
        }else{
            $gmclient->_doBackground("save_pushtoken",$req_param);
        }

    }
}