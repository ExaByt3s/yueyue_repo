<?php
/**
 * @desc:   文字审核类, 命名规则 类名_操作 示例：text_examine_pass 文字审核通过，
 * text_pass_to_del 图片从审核通过到不通过
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/10
 * @Time:   09:38
 * version: 2.0
 */
class pai_text_examine_v2_class extends POCO_TDG
{
    private $type_arr = array();//类型数组
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
        $this->type_arr = array(
            0=>array('type'=> 'nickname_text','name'=>'消费者昵称'),
            1=>array('type'=>'customer_remark','name'=>'消费者个人简介')
        );
    }

    /**
     *  设置_文字审核表
     */
    private function set_text_examine_tbl()
    {
        $this->setTableName( 'text_examine_log' );
    }

    /**
     * 获取类型数组
     * @return array
     */
    public function get_type_list()
    {
        return $this->type_arr;
    }

    /**
     * 通过类型名称获取类型名
     * @param string $type
     * @return string
     */
    public function  get_role_by_type($type)
    {
        $type = trim($type);
        if(strlen($type) <1) return '';
        foreach($this->type_arr as $key=>$val)
        {
            if($type == $val['type']) return $this->type_arr[$key]['name'];
        }
    }

    /**
     * 设置图片通过表
     * @param $date
     * @param bool $no_create_tbl 为true不创建表，并且返回false
     * @return bool
     * @throws App_Exception
     */
    private function set_text_pass_tbl($date,$no_create_tbl = false)
    {
        $date = trim($date);
        if(strlen($date) <1 || date('Y-m-d',strtotime($date)) != $date) $date = date('Y-m-d',time());
        $table_num = date('Ym',strtotime($date));
        $table_name = "text_examine_pass_log_{$table_num}";
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res)) //表不存在创建
        {
            if($no_create_tbl == true) return false;//没有数据
            $sql_str = "CREATE TABLE IF NOT EXISTS {$this->_db_name}.{$table_name} (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) unsigned NOT NULL,
                      `type` varchar(50) NOT NULL,
                      `before_edit` text NOT NULL,
                      `after_edit` text NOT NULL,
                      `add_time` datetime NOT NULL,
                      `audit_id` int(10) unsigned NOT NULL,
                      `audit_time` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `audit_id` (`audit_id`)
                    ) ENGINE=InnoDB";
            $this->query($sql_str);
        }
        $this->setTableName( $table_name );
        return true;
    }


    /**
     * 设置_文字删除表
     * @param $date
     * @param bool $no_create_tbl 为true不创建表，并且返回false
     * @return bool
     * @throws App_Exception
     */
    private function set_text_del_tbl($date,$no_create_tbl = false)
    {
        $date = trim($date);
        if(strlen($date) <1 || date('Y-m-d',strtotime($date)) != $date) $date = date('Y-m-d',time());
        $table_num = date('Ym',strtotime($date));
        $table_name = "text_examine_del_log_{$table_num}";
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res)) //表不存在创建
        {
            if($no_create_tbl == true) return false;//没有数据
            $sql_str = "CREATE TABLE IF NOT EXISTS {$this->_db_name}.{$table_name} (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) unsigned NOT NULL,
                      `type` varchar(50) NOT NULL,
                      `before_edit` text NOT NULL,
                      `after_edit` text NOT NULL,
                      `add_time` datetime NOT NULL,
                      `audit_id` int(10) unsigned NOT NULL,
                      `audit_time` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `audit_id` (`audit_id`)
                    ) ENGINE=InnoDB";
            $this->query($sql_str);
        }
        $this->setTableName( $table_name );
        return true;
    }

    #######################################################待审核部分操作##############################################################

    /**
     * 获取_文字审核_列表
     * @param bool $b_select_count true|false
     * @param string $type 类型
     * @param int $user_id 用户ID,示例:100000
     * @param string $where_str 条件，$where_str ="user_id=100000";
     * @param string $order_by  排序,$order_by="id DESC";
     * @param string $limit     循环，$limit="0,10"
     * @param string $fields    查询字段,fields="user_id,name";
     * @return array|int
     */
    public function get_text_examine_list($b_select_count = false,$type='',$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_text_examine_tbl();
        $type = trim($type);
        $user_id = intval($user_id);
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($user_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * @param $id
     * @return array|bool
     */
    private function get_text_examine_by_id($id)
    {
        $id = (int)$id;
        if($id <1) return false;
        $this->set_text_examine_tbl();
        return $this->find("id={$id}");
    }

    /**
     * @param $id
     * @return bool
     * @throws App_Exception
     */
    private function text_examine_del_by_id($id)
    {
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $this->set_text_examine_tbl();
        return $this->delete("id={$id}");
    }

    #####################################################################审核通过部分操作####################################################################

    /**
     * 获取_文字通过_列表
     * @param bool $b_select_count  true|false
     * @param string $date  日期,示例：'2015-09-12'
     * @param string $type  类型，示例：'yueseller'
     * @param int $audit_id 审核者ID，
     * @param int $user_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_text_pass_list($b_select_count = false,$date,$type,$audit_id=0,$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $date = trim($date);
        $type = trim($type);
        $audit_id = intval($audit_id);
        $user_id = intval($user_id);
        $retID = $this->set_text_pass_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($user_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * @param $id
     * @param $date
     * @return array|bool
     * @throws App_Exception
     */
    private function get_text_pass_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $retID = $this->set_text_pass_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        return $this->find("id={$id}");
    }

    /**
     * @param $insert_data
     * @param $date
     * @return int
     * @throws App_Exception
     */
    private function text_insert_pass($insert_data,$date)
    {
        global $yue_login_id;
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $this->set_text_pass_tbl($date);
        $insert_data['audit_time'] = date('Y-m-d H:i:s',time());
        $insert_data['audit_id'] = $yue_login_id;
        return $this->insert($insert_data);
    }

    /**
     * @param $data
     * @param string $date
     * @param bool $b_select_count
     * @return int
     * @throws App_Exception
     */
    private function text_insert_pass_info($data,$date ='',$b_select_count = false)
    {
        $date = trim($date);
        if(!is_array($data) || empty($data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $insert_data['user_id'] = intval($data['user_id']);
        $insert_data['type'] = trim($data['type']);
        $insert_data['before_edit'] = $data['before_edit'];
        $insert_data['after_edit'] = $data['after_edit'];
        $insert_data['add_time'] = $data['add_time'];
        return $this->text_insert_pass($insert_data,$date);
    }

    /**
     * 通过ID_删除通过文字的数据
     * @param int $id
     * @param string $date
     * @return bool
     * @throws App_Exception
     */
    private function text_delete_pass_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $this->set_text_pass_tbl($date,true);
        return $this->delete("id={$id}");
    }

    #####################################################################删除文字部分操作####################################################################

    /**
     * 获取_文字删除_列表
     * @param bool $b_select_count false|true
     * @param string $date  日期
     * @param string $type  类型
     * @param int $audit_id 审核
     * @param int $user_id 用户ID
     * @param string $where_str  条件
     * @param string $order_by   排序
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_text_del_list($b_select_count = false,$date ='',$type ='',$audit_id=0,$user_id =0,$where_str ='', $order_by = 'id DESC',$limit = '0,10',$fields = '*')
    {
        $date = trim($date);
        $type = trim($type);
        $audit_id = intval($audit_id);
        $user_id = intval($user_id);
        $retID = $this->set_text_del_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        if($retID <1) return false;
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($user_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * 通过ID_获取删除的一条数据
     * @param int $id
     * @param string $date
     * @return array|bool
     * @throws App_Exception
     */
    private function get_text_del_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $retID = $this->set_text_del_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        return $this->find("id={$id}");
    }

    /**
     * 通过ID_删除通过文字的数据
     * @param int $id
     * @param string $date
     * @return bool
     * @throws App_Exception
     */
    private function text_delete_del_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $this->set_text_del_tbl($date,true);
        return $this->delete("id={$id}");
    }

    /**
     * @param $insert_data
     * @param $date
     * @return int
     * @throws App_Exception
     */
    private function text_insert_del($insert_data,$date)
    {
        global $yue_login_id;
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $this->set_text_del_tbl($date);
        $insert_data['audit_time'] = date('Y-m-d H:i:s',time());
        $insert_data['audit_id'] = $yue_login_id;
        return $this->insert($insert_data);
    }

    /**
     * @param $data
     * @param string $date
     * @param bool $b_select_count
     * @return int
     * @throws App_Exception
     */
    private function text_insert_del_info($data,$date ='',$b_select_count = false)
    {
        $date = trim($date);
        if(!is_array($data) || empty($data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $insert_data['user_id'] = intval($data['user_id']);
        $insert_data['type'] = trim($data['type']);
        $insert_data['before_edit'] = $data['before_edit'];
        $insert_data['after_edit'] = $data['after_edit'];
        $insert_data['add_time'] = $data['add_time'];
        return $this->text_insert_del($insert_data,$date);
    }
    ##########################################操作部分########################################################################################

    /**
     * 从文字待审核_到通过表中
     * @param  array $ids 示例：array(1,2,3);
     * @return array  $return_data = array('code'=>0,'err'=>''); 如果code=1表示成功，否则失败
     * @throws App_Exception
     */
    public function text_examine_pass($ids)
    {
        $return_data = array('code'=>0);//返回值
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '传过来的数据为空';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_examine_by_id($id);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_pass_info($ret);
                    $del_ret = $this->text_examine_del_by_id($id);
                    if(!$insert_ret || !$del_ret){//事务回滚
                        POCO_TRAN::rollback($this->getServerId());
                    }else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //事务提交
                    }

                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * 从文字待审核_到删除表中
     * @param  array $ids 示例：array(1,2,3);
     * @return array  $return_data = array('code'=>0,'err'=>''); 如果code=1表示成功，否则失败
     * @throws App_Exception
     */
    public function text_examine_del($ids)
    {
        $return_data = array('code'=>0);//返回值
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '传过来的数据为空';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_examine_by_id($id);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_del_info($ret);
                    $del_ret = $this->text_examine_del_by_id($id);
                    if(!$insert_ret || !$del_ret){//事务回滚
                        POCO_TRAN::rollback($this->getServerId());
                    }else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //事务提交
                        $this->del_app_text_info($ret);
                    }

                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * 文字从通过表_删除表中
     * @param array $ids  示例：array(1,2,3);
     * @param string $date
     * @return array  $return_data = array('code'=>0,'err'=>''); 如果code=1表示成功，否则失败
     * @throws App_Exception
     */
    public function text_pass_to_del($ids,$date)
    {
        $return_data = array('code'=>0);//返回值
        $date = trim($date);
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '传过来的数据为空';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_pass_by_id($id,$date);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_del_info($ret);
                    $del_ret = $this->text_delete_pass_by_id($id,$date);
                    if(!$insert_ret || !$del_ret){//事务回滚
                        POCO_TRAN::rollback($this->getServerId());
                    }else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //事务提交
                        $this->del_app_text_info($ret);
                    }
                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * 从删除表中_到通过表中数据
     * @param array $ids
     * @param string $date
     * @return mixed
     * @throws App_Exception
     */
    public function text_del_to_pass($ids,$date)
    {
        $date = trim($date);
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '传过来的数据为空';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_del_by_id($id,$date);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_pass_info($ret,'',true);
                    $del_ret = $this->text_delete_del_by_id($id,$date);
                    if(!$insert_ret || !$del_ret){//事务回滚
                        POCO_TRAN::rollback($this->getServerId());
                    } else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //事务提交
                        $this->recover_app_text_info($ret);//恢复文字数据
                    }

                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

   ###################################################删除APP部分#################################################
    /**
     * 删除数据中转站
     * @param array $data 删除的数据
     * @return bool
     */
    private function del_app_text_info($data)
    {
        $type = trim($data['type']);
        if(strlen($type) <1) return false;
        if($type == 'nickname_text')//消费者昵称
        {
           return $this->del_app_nickname($data['user_id'],$data['after_edit']);
        }
        elseif($type == 'customer_remark')//消费者简介修改
        {
           return $this->del_app_intro($data['user_id'],$data['after_edit']);
        }
    }

    /**
     * 修改消费者昵称数据
     * @param int $user_id
     * @param $after_edit
     * @return bool
     */
    private function del_app_nickname($user_id,$after_edit)
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        if($user_res['nickname'] == $after_edit)
        {
            $nickname = '手机用户'.substr($user_res['cellphone'], -4);
            $user_obj->update_nickname($user_id,$nickname);
        }
        return true;
    }

    /**
     * 删除消费者原本备注信息
     * @param int $user_id  用户数据
     * @param string $after_edit
     * @return bool
     */
    private function del_app_intro($user_id,$after_edit ='')
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        if($user_res['remark'] == $after_edit)
        {
            $update_data ['remark'] = '';
            $user_obj->update_user($update_data, $user_id);
        }
        unset($update_data);
        return true;
    }

    ###################################################删除恢复APP部分#################################################

    /**
     * 恢复数据中转站
     * @param array $data 恢复的数据
     * @return bool
     */
    private function recover_app_text_info($data)
    {
        $type = trim($data['type']);
        if(strlen($type) <1) return false;
        if($type == 'nickname_text')//消费者昵称
        {
            return $this->recover_app_nickname($data['user_id'],$data['after_edit']);
        }
        elseif($type == 'customer_remark')//消费者简介修改
        {
            return $this->recover_app_intro($data['user_id'],$data['after_edit']);
        }
    }

    /**
     * 恢复消费者原本备注信息
     * @param int $user_id  用户数据
     * @param string $after_edit
     * @return bool
     */
    private function recover_app_nickname($user_id,$after_edit ='')
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        $nickname = '手机用户'.substr($user_res['cellphone'], -4);
        if($user_res['nickname'] == $nickname)
        {
            $user_obj->update_nickname($user_id,$after_edit);
        }
        return true;
    }

    /**
     * 恢复消费者原本备注信息
     * @param int $user_id  用户数据
     * @param string $after_edit
     * @return bool
     */
    private function recover_app_intro($user_id,$after_edit ='')
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        if($user_res['remark'] == '')
        {
            $update_data ['remark'] = $after_edit;
            $user_obj->update_user($update_data, $user_id);
        }
        unset($update_data);
        return true;
    }


}