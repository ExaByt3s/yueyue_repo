<?php
class pai_blacklist_class extends POCO_TDG
{
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_db' );
        $this->setTableName ( 'pai_user_blacklist_tbl' );
    }

    public function set_blacklist($user_id, $blacklist_id, $set)
    {
        /*$gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // 设置超时

        $req_param['uid']="$user_id";
        $req_param['shield_id']="$blacklist_id";

        if($set ==  'on')
        {
            $gm_str = 'add_shield_user';
        }elseif($set == 'off'){
            $gm_str = 'del_shield_user';
        }

        $result= $gmclient->do($gm_str,json_encode($req_param) );

        return $result;*/

        $greaman_obj = POCO::singleton('pai_gearman_base_class');
        $greaman_obj->connect('172.18.5.211', '9830');

        $req_param['uid']="$user_id";
        $req_param['shield_id']="$blacklist_id";

        if($set ==  'on')
        {
            $gm_str = 'add_shield_user';
        }elseif($set == 'off'){
            $gm_str = 'del_shield_user';
        }
        $result =  $greaman_obj->_do($gm_str, $req_param, 'json');
        return $result;
    }

    public function get_blacklist($user_id, $blacklist_id)
    {
        $sql_str = "SELECT * FROM pai_db.pai_user_blacklist_tbl WHERE user_id=$user_id AND blacklist_id=$blacklist_id";
        $result = db_simple_getdata($sql_str, TRUE, 101);

        if($result){
            return 'on';
        }else{
            return 'off';
        }
    }

    public function get_blacklist_list($user_id)
    {
        /*$gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // 设置超时
        $req_param['uid']=$user_id;
        $result= $gmclient->do("get_shield_user",json_encode($req_param) );

        return $result;*/

        $greaman_obj = POCO::singleton('pai_gearman_base_class');
        $greaman_obj->connect('172.18.5.211', '9830');

        $req_param['uid']=$user_id;
        $result = $greaman_obj->_do("get_shield_user", $req_param, 'json');
        return $result;
    }
}
?>