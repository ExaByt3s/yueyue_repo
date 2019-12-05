<?php
/**
 * @desc:   举报和黑名单类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/28
 * @Time:   15:59
 * version: 1.0
 */
class pai_log_inform_v2_class extends POCO_TDG
{
    /**
     * @var string 发送提示信息内容
     */
    private $send_message = '尊敬的约约用户，由于您的帐号涉嫌发送敏感信息被用户举报，系统已屏蔽您所发送的任何信息，如需申诉，请拨打 400-082-9003 联系客服处理！';

    /**
     * @var string
     */
    private $cause_str = '管理员手动拉黑';
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
    }

    /**
     * 设置举报表
     */
    private function set_inform_tbl()
    {
        $this->setTableName( 'pai_examine_inform_tbl' );
    }

    /**
     *  设置黑名单表
     */
    private function set_inform_shield_tbl()
    {
        $this->setTableName( 'pai_examine_inform_shield_tbl' );
    }

    /**
     * 获取举报列表数据
     * @param bool $b_select_count 如果需要获取总条数使用true，否则填写false
     * @param int $by_informer_id  举报者ID，示例：100293
     * @param int $to_informer_id  被举报者ID，示例：100580
     * @param string $where_str    查询条件，示例：$where_str ="DATE_FORMAT(add_time,'%Y-%m-%d')>='2015-10-28'"
     * @param string $order_by     排序
     * @param string $limit        循环条数，下标从0开始循环10，示例："0,10"
     * @param string $fields       查询字段，示例：$fields="id"或者$fields="id,add_time"
     * @return array|int           返回值
     */
    public function get_inform_list($b_select_count = false,$by_informer_id,$to_informer_id,$where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_inform_tbl();
        $by_informer_id = (int)$by_informer_id;
        $to_informer_id = (int)$to_informer_id;
        if($by_informer_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "by_informer={$by_informer_id}";
        }
        if($to_informer_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "to_informer={$to_informer_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str);
        }
        $ret = $this->findAll($where_str, $limit, $order_by, $fields);
        return $ret;
    }


    /**
     * 通过主键ID来获取举报信息
     * @param int $id 主键ID
     * @return array|bool
     */
    public function get_inform_info($id)
    {
        $id = (int)$id;
        if($id <1) return false;
        $this->set_inform_tbl();
        return $this->find("id = {$id}");
    }

    /**
     * 添加用户到举报名单中
     * @param array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_inform_info($insert_data)
    {
        if (empty($insert_data) || !is_array($insert_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
        }
        $this->set_inform_tbl();
        return $this->insert($insert_data);
    }

    /**
     * 通过用户ID拼凑数据准备添加到举报表中
     * @param int $user_id 被举报的用户ID
     * @param string $data_str 管理员添加黑名单备注
     * @return int
     * @throws App_Exception
     */
    public function add_inform_by_user_id($user_id,$data_str ='')
    {
        global $yue_login_id;
        $user_id = (int)$user_id;
        $data_str = trim($data_str);
        if($user_id <1)return false;
        $data['by_informer'] = $yue_login_id;
        $data['to_informer'] = $user_id;
        $data['cause_str'] = trim($this->cause_str);
        $data['state'] = 1;
        $data['data_str'] = trim($data_str);
        $data['add_time'] = date('Y-m-d H:i:s', time());
        return $this->add_inform_info($data);
    }

    /**
     * 通过被举报者ID来更新数据
     * @param array $data
     * @param int $to_informer 举报者ID
     * @return mixed
     * @throws App_Exception
     */
    private function update_info($data, $to_informer)
    {
        if (empty($data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
        }
        $to_informer = (int)$to_informer;
        if ($to_informer <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
        }
        $this->set_inform_tbl();
        $where_str = "to_informer = {$to_informer}";
        $ret = $this->update($data, $where_str);
        return $ret;
    }

    /**
     * 从举报中的移入黑名单数据
     * @param int $id
     * @param string $reason
     * @return string
     * @throws App_Exception
     */
    public function update_inform_by_id($id,$reason)
    {
        $return_data = array('code'=>0);//返回值
        $id = (int)$id;
        $reason = trim($reason);
        if($id <1) return $return_data['err'] = 'ID不能为空';
        //获取被举报者的ID
        $ret = $this->get_inform_info($id);
        $to_informer = (int)$ret['to_informer'];
        if($to_informer <1) return $return_data['err']= '被举报者ID获取不到';
        POCO_TRAN::begin($this->getServerId());//设置事务
        $ret = $this->update_info(array('state'=>1),$to_informer); //修改举报表状态
        $sheld_result = $this->insert_inform_shield_by_to_informer($to_informer,$id,$reason);

        if($ret && $sheld_result )//举报表和黑名单表都成功
        {
            POCO_TRAN::commmit($this->getServerId());//提交事务
            $send_msg = trim($this->send_message);
            send_message_for_10002($to_informer, $send_msg, '', 'all', 'sys_msg');
            $return_data['code'] = 1;
            return $return_data;
        }
        //其他事务回滚，并返回false
        POCO_TRAN::rollback($this->getServerId());
        return $return_data['err']= '系统提交失败';
    }

    ##############################################################获取黑名单数据##########################################################

    /**
     * 获取黑名单列表
     * @param bool $b_select_count true|false
     * @param int $user_id  黑名单ID
     * @param int $audit_id  审核人ID
     * @param string $where_str 条件
     * @param string $order_by 排序方式
     * @param string $limit 循环条数，示例：'0,10'
     * @param string $fields   查询字段
     * @return array|int
     */
    public function get_sheld_list($b_select_count = false,$user_id,$audit_id,$where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_inform_shield_tbl();
        $user_id = (int)$user_id;
        $audit_id = (int)$audit_id;
        if($user_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * 通过被举报者ID获取是否进入黑名单了
     * @param int $to_informer_id
     * @return bool 进入黑名单返回true，否则返回false
     */
    public function get_info_by_to_informer_id($to_informer_id)
    {
        $to_informer_id = (int)$to_informer_id;
        if($to_informer_id <1) return false;
        $this->set_inform_shield_tbl();
        $ret = $this->find("user_id={$to_informer_id}");
        if (is_array($ret) && !empty($ret)) return true;
        return false;
    }

    /**
     * 插入数据到黑名单中
     * @param  array $insert_data
     * @return bool
     * @throws App_Exception
     */
    private function insert_sheld_info($insert_data)
    {
        if (empty($insert_data) || !is_array($insert_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
        }
        $this->set_inform_shield_tbl();
        $this->insert($insert_data,"IGNORE");
        return true;
    }

    /**
     * 接收需要插入的数据并且被数据插入到黑名单中
     * @param int $to_informer 被举报的用户
     * @param int $id   举报主键ID
     * @param string $reason  移入黑名单原因（说明）
     * @return bool
     * @throws App_Exception
     */
    public function insert_inform_shield_by_to_informer($to_informer,$id,$reason)
    {
        global $yue_login_id;
        $data = array();
        $to_informer = (int)$to_informer;
        $id = (int)$id;
        $reason = trim($reason);
        if($to_informer <1) return false;
        $data['reason'] = $reason;
        $data['user_id'] = $to_informer;
        $data['inform_id'] = $id;
        $data['add_time'] = time();
        $data['audit_id'] = $yue_login_id;
        $ret = $this->insert_sheld_info($data);//插入数据进黑名单
        $ser_ret = $this->shield_user_v2($to_informer);
        if($ret && $ser_ret) return true;
        return false;
    }

    /**
     * 通过黑名单ID获取黑名单数据
     * @param int $id 黑名单主键ID
     * @return array|bool
     */
    public function get_shield_by_id($id)
    {
        $id = (int)$id;
        if($id <1)return false;
        $this->set_inform_shield_tbl();
        return $this->find("id={$id}");
    }

    /**
     * 通过黑名单主键ID删除数据
     * @param int $id  主键ID
     * @return bool
     * @throws App_Exception
     */
    public function delete_shield_by_id($id)
    {
        $id = (int)$id;
        if ($id<1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
        }
        $this->set_inform_shield_tbl();
        return $this->delete("id={$id}");
    }

    /**
     * 接收主键ID和举报ID,来删除黑名单数和服务器黑名单数据
     * @param int $id 主键ID
     * @param int $user_id  被举报的者的ID
     * @return bool
     * @throws App_Exception
     */
    public function delete_shield_by_id_info($id,$user_id)
    {
        $id = (int)$id;
        $user_id = (int)$user_id;
        if($id <1 || $user_id <1) return false;
        $result = $this->delete_shield_by_id($id);
        $server_result = $this->shield_user_v2($user_id,'remove');
        if($result && $server_result) return true;
        return false;
    }

    /**
     * 移出黑名单
     * @param int $id 移出主键的ID，黑名单的主键ID
     * @return array|string
     * @throws App_Exception
     */
    public function mouve_out_blacklist_by_id($id)
    {
        $return_data = array('code'=>0);//返回值
        $id = (int)$id;
        if($id <1) return $return_data['err'] = '非法操作';
        $ret = $this->get_shield_by_id($id);
        if(!is_array($ret)) $ret = array();
        $user_id = (int)$ret['user_id'];
        if($user_id <1) return $return_data['err'] = '黑名单不存在该用户';
        POCO_TRAN::begin($this->getServerId());//设置事务
        $ret = $this->update_info(array('state'=>0), $user_id);
        $delete_ret = $this->delete_shield_by_id_info($id,$user_id);
        if($ret && $delete_ret)
        {
            POCO_TRAN::commmit($this->getServerId());//提交事务
            $return_data['code'] = 1;
            return $return_data;
        }
        POCO_TRAN::rollback($this->getServerId()); //回滚数据
        return $return_data['err'] = '系统提交出错';
    }

    /**
     * 管理员手动拉黑用户
     * @param int $user_id 需要手动拉黑的用户ID
     * @param int $data_str 管理员添加黑名单备注
     * @return array|string
     */
    public function admin_add_user_id_into_blacklist($user_id,$data_str)
    {
        $return_data = array('code'=>0);//返回值
        $user_id = (int)$user_id;
        $data_str = trim($data_str);
        if($user_id <1) return $return_data['err'] = '用户ID不能为空';
        if($user_id <100000) return $return_data['err'] = '您无法拉黑系统用户';

        POCO_TRAN::begin($this->getServerId());//设置事务
        $inform_id = $this->add_inform_by_user_id($user_id,$data_str);//添加举报
        $this->update_info(array('state'=>1),$user_id); //如果举报表中有用户ID,修改举报表状态
        $shield_result = $this->insert_inform_shield_by_to_informer($user_id,$inform_id,$this->cause_str);//添加了黑名单
        if($inform_id && $shield_result)
        {
            POCO_TRAN::commmit($this->getServerId());//提交事务
            $send_msg = trim($this->send_message);
            send_message_for_10002($user_id, $send_msg, '', 'all', 'sys_msg');
            $return_data['code'] = 1;
            return $return_data;
        }
        POCO_TRAN::rollback($this->getServerId()); //回滚数据
        return $return_data['err'] = '系统提交出错';
    }


    ####################################################服务器端处理###########################################################################
    /**
     * 将用户移入服务器中的黑名单中
     * @param $user_id  $user_id [用户名ID]
     * @param string $type 类型
     * @param string $role yueseller yuebuyer both
     * @return bool
     */
    private function shield_user_v2($user_id, $type = 'add',$role='both')
    {
        $user_id = (int)$user_id;
        $type = trim($type);
        $role = trim($role);
        if (!$user_id){return false;}
        $gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // 设置超时
        do
        {
            $req_param['uid'] = $user_id;
            $req_param['role'] = $role;
            if($type == 'add')
            {
                $gmclient->doBackground("add_blacklist",json_encode($req_param) );
            }
            else
            {
                $gmclient->doBackground("del_blacklist",json_encode($req_param) );
            }
        }
        while(false);
        //while($gmclient->returnCode() != GEARMAN_SUCCESS);
        return true;
    }
}