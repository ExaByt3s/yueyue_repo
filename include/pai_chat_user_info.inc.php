<?php
/**
 * Date: 2015/4/14
 * Time: 9:54
 * SendServer用户信息同步
 */

class pai_chat_user_info extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_db' );
        $this->setTableName ( 'pai_user_tbl' );
    }


    public function introduction_all_user_data()
    {
        $sql_str = "SELECT user_id, add_time, role
                    FROM `pai_db`.`pai_user_tbl`
                    WHERE role IN ('cameraman', 'model') ;";
        $result = db_simple_getdata($sql_str, FALSE, 101);
        foreach($result AS $key=>$val)
        {
            $data = array();
            $data['user_id']    = $val['user_id'];
            $data['reg_time']   = $val['add_time'];
            $data['role']       = $val['role'];
            $data['order_num']  = $this->get_order_num($val['user_id'], $val['role']);
            if($val['role'] == 'model')
            {
                $data['chat_credit_lvl'] = $this->get_level($val['user_id'], $val['role']);
                $data['model_audit'] = $this->get_model_audit($val['user_id']);
            }else{
                $data['credit_lvl'] = $this->get_level($val['user_id'], $val['role']);
            }
            if($data['role'] == 'model')
            {
                $data['role'] = 2;
            }else{
                $data['role'] = 1;
            }

            if($data) $this->save_redis_yueinfo($data);
        }
    }

    public function redis_get_user_info($user_id)
    {
        $this->redis_get_user_info_v2($user_id);
        if((int)$user_id) {
            $sql_str = "SELECT * FROM pai_db.pai_user_tbl WHERE user_id=$user_id";
            $result = db_simple_getdata($sql_str, TRUE, 101);
            if ($result) {
                $data['user_id'] = $result['user_id'];
                $data['reg_time'] = $result['add_time'];
                $data['role'] = $result['role'];
                //$data['order_num'] = $this->get_order_num($result['user_id'], $result['role']);

                if ($result['role'] == 'model') {
                    $data['chat_credit_lvl'] = $this->get_level($result['user_id'], $result['role']);
                    $data['model_audit'] = $this->get_model_audit($result['user_id']);
                } else {
                    $data['credit_lvl'] = $this->get_level($result['user_id'], $result['role']);
                }

                if ($data['role'] == 'model') {
                    $data['role'] = 2;
                } else {
                    $data['role'] = 1;
                }

                if ($data) $this->save_redis_yueinfo($data);
                unset($data);

                return true;
            }
        }
    }

    public function redis_get_user_info_v2($user_id, $off = false)
    {
        if((int)$user_id) {
            $sql_str    = "SELECT * FROM pai_db.pai_user_tbl WHERE user_id=$user_id";
            $result     = db_simple_getdata($sql_str, TRUE, 101);
            if ($result) {
                $data['user_id']            = $result['user_id'];
                $data['reg_time']           = $result['add_time'];
                $data['role']               = $result['role'];
                //$data['order_num']          = $this->get_order_num_v2($result['user_id']);
                $data['chat_credit_lvl']    = 1; //是模特的限制聊天等级..摄影师不够这个等级就不能跟他聊天
                $data['model_audit']        = 1; //是模特审核状态..对应的值是你那边定义的.我这边只有1和3时,模特才能跟摄影师聊天
                $data['credit_lvl']         = $this->get_level($user_id, 'seller'); //就是摄影师的信用等级了.

                $chat_user_obj = POCO::singleton('pai_mall_seller_class');
                $result = $chat_user_obj->get_first_profile_m_level_by_user_id($user_id);
                if($result['m_level'])   $data['chat_credit_lv1'] = $result['m_level'];

                //尝试解决私聊问题 2015-08-07 11:34
                /*if ($result['role'] == 'model') {
                	$data['chat_credit_lvl']    = $this->get_level($result['user_id'], $result['role']);
                	//$data['model_audit']        = $this->get_model_audit($result['user_id']);
                } else {
                	$data['credit_lvl'] = $this->get_level($result['user_id'], $result['role']);
                }*/
                
                //需要身份限制2，不需要身份限制3
                $data['role']               = 3;
                if($result['type_id'])
                {
                    $type_array =  explode(',', $result['type_id']);
                    if(in_array(31, $type_array)) $data['role'] =2;//如果该模特有申请过模特服务，他就需要身份限制
                }
                if($off) var_dump($data);
                if ($data) $this->save_redis_yueinfo_v2($data);
                unset($data);

                return true;
            }
        }
    }

    public function get_order_num($user_id, $role)
    {
        $sql_str = "SELECT 	count(date_id) AS C
                FROM `event_db`.`event_date_tbl`
                WHERE date_status ='confirm'";
        if($role == 'model')
        {
            $sql_str .= " AND to_date_id = $user_id ";
        }else{
            $sql_str .= " AND from_date_id = $user_id ";
        }
        $result = db_simple_getdata($sql_str, TRUE);
        if($result['C']) return $result['C'];
        return 0;
    }

    public function get_order_num_v2($user_id)
    {
        $count = 0;
        if((int)$user_id)
        {
            $mall_order_obj = POCO::singleton('pai_mall_order_class');
            $count = $mall_order_obj->get_order_list_for_buyer($user_id, 0, '-1', true);
            return $count;
        }
        return $count;
    }

    public function get_level($user_id, $role)
    {
        if($role == 'model')
        {
            $sql_str = "SELECT level_require FROM pai_db.pai_model_card_tbl WHERE user_id = $user_id";
            $result = db_simple_getdata($sql_str, TRUE, 101);
            $level = $result['level_require'];
        }else{
            $level_obj = POCO::singleton('pai_user_level_class');
            $level = $level_obj->get_user_level($user_id);
        }
        return $level;
    }

    public function save_redis_yueinfo($data)
    {
        /*$gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // 设置超时
        do
        {
            if($data['user_id'])            $req_param['pocoid']            = iconv('gbk', 'utf-8', $data['user_id']);
            if($data['reg_time'])           $req_param['reg_time']          = iconv('gbk', 'utf-8', $data['reg_time']);
            if($data['order_num'])          $req_param['success_order']     = iconv('gbk', 'utf-8', $data['order_num']);
            if($data['role'])               $req_param['role']              = iconv('gbk', 'utf-8', $data['role']);
            if($data['credit_lvl'])         $req_param['credit_lvl']        = iconv('gbk', 'utf-8', $data['credit_lvl']);
            if($data['chat_credit_lvl'])    $req_param['chat_credit_lvl']   = iconv('gbk', 'utf-8', $data['chat_credit_lvl']);
            if($data['model_audit'])        $req_param['model_audit']       = iconv('gbk', 'utf-8', $data['model_audit']);

            $gmclient->do("save_yueinfo_for_blacklist",json_encode($req_param) );
            //$gmclient->do("save_business_info",json_encode($req_param) );

        }
        while( false );
        //while($gmclient->returnCode() != GEARMAN_SUCCESS);*/

        if($data['user_id'])            $req_param['pocoid']            = iconv('gbk', 'utf-8', $data['user_id']);
        if($data['reg_time'])           $req_param['reg_time']          = iconv('gbk', 'utf-8', $data['reg_time']);
        if($data['order_num'])          $req_param['success_order']     = iconv('gbk', 'utf-8', $data['order_num']);
        if($data['role'])               $req_param['role']              = iconv('gbk', 'utf-8', $data['role']);
        if($data['credit_lvl'])         $req_param['credit_lvl']        = iconv('gbk', 'utf-8', $data['credit_lvl']);
        if($data['chat_credit_lvl'])    $req_param['chat_credit_lvl']   = iconv('gbk', 'utf-8', $data['chat_credit_lvl']);
        if($data['model_audit'])        $req_param['model_audit']       = iconv('gbk', 'utf-8', $data['model_audit']);

        $gmclient = POCO::singleton('pai_gearman_base_class');
        $gmclient->connect('172.18.5.211', '9830');
        $result = $gmclient->_do("save_yueinfo_for_blacklist", $req_param);
    }

    public function save_redis_yueinfo_v2($data)
    {
       /* $gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // 设置超时
        do
        {
            if($data['user_id'])            $req_param['pocoid']            = iconv('gbk', 'utf-8', $data['user_id']);
            if($data['reg_time'])           $req_param['reg_time']          = iconv('gbk', 'utf-8', $data['reg_time']);
            if($data['order_num'])          $req_param['success_order']     = iconv('gbk', 'utf-8', $data['order_num']);
            if($data['role'])               $req_param['role']              = iconv('gbk', 'utf-8', $data['role']);
            if($data['credit_lvl'])         $req_param['credit_lvl']        = iconv('gbk', 'utf-8', $data['credit_lvl']);
            if($data['chat_credit_lvl'])    $req_param['chat_credit_lvl']   = iconv('gbk', 'utf-8', $data['chat_credit_lvl']);
            if($data['model_audit'])        $req_param['model_audit']       = iconv('gbk', 'utf-8', $data['model_audit']);

            //$gmclient->do("save_yueinfo_for_blacklist",json_encode($req_param) );
            $gmclient->do("save_business_info",json_encode($req_param) );

        }
        while(false);
        //while($gmclient->returnCode() != GEARMAN_SUCCESS);*/

        if($data['user_id'])            $req_param['pocoid']            = iconv('gbk', 'utf-8', $data['user_id']);
        if($data['reg_time'])           $req_param['reg_time']          = iconv('gbk', 'utf-8', $data['reg_time']);
        if($data['order_num'])          $req_param['success_order']     = iconv('gbk', 'utf-8', $data['order_num']);
        if($data['role'])               $req_param['role']              = iconv('gbk', 'utf-8', $data['role']);
        if($data['credit_lvl'])         $req_param['credit_lvl']        = iconv('gbk', 'utf-8', $data['credit_lvl']);
        if($data['chat_credit_lvl'])    $req_param['chat_credit_lvl']   = iconv('gbk', 'utf-8', $data['chat_credit_lvl']);
        if($data['model_audit'])        $req_param['model_audit']       = iconv('gbk', 'utf-8', $data['model_audit']);

        $gmclient = POCO::singleton('pai_gearman_base_class');
        $gmclient->connect('172.18.5.211', '9830');
        $result = $gmclient->_do("save_business_info", $req_param);
    }

    public function get_model_audit($id)
    {
        $sql_str = "SELECT user_id, is_approval
                    FROM `pai_db`.`pai_model_audit_tbl`
                    WHERE user_id=$id";
        $result = db_simple_getdata($sql_str, TRUE, 101);
        return $result['is_approval'];
    }

    public function save_all_event_by_user_id()
    {
        $sql_str = "SELECT user_id FROM event_db.event_details_tbl WHERE type_icon!='yuepai_app' and new_version=2 GROUP BY user_id;";
        $result = db_simple_getdata($sql_str, FALSE);
        foreach($result AS $key=>$val)
        {
            $user_id = $this->get_yueyue_id_by_poco_id($val[user_id]);
            $this->set_redis_for_event_id($user_id);
        }
    }

    public function get_yueyue_id_by_poco_id($poco_id)
    {
        $sql_str = "SELECT `user_id` FROM `pai_db`.`pai_relate_poco_tbl` WHERE poco_id=$poco_id;";
        $result = db_simple_getdata($sql_str, TRUE, 101);
        return $result['user_id'];
    }

    public function set_redis_for_event_id($user_id)
    {
        if($user_id) {
            /*$gmclient = new GearmanClient();
            $gmclient->addServers("172.18.5.211:9830");
            $gmclient->setTimeout(5000); // 设置超时
            do {
                $req_param['pocoid'] = iconv('gbk', 'utf-8', $user_id);

                //var_dump(($req_param));
                //echo "<BR>";
                $gmclient->do("add_pubboth_user",json_encode($req_param) );

            }
            while(false);*/
            //while ($gmclient->returnCode() != GEARMAN_SUCCESS);
            //var_dump($gmclient->returnCode());

            $req_param['pocoid'] = iconv('gbk', 'utf-8', $user_id);

            $gmclient = POCO::singleton('pai_gearman_base_class');
            $gmclient->connect('172.18.5.211', '9830');
            $result = $gmclient->_do("add_pubboth_user", $req_param);
        }
    }
}
?>