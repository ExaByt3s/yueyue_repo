<?php
/**
 * @desc:   图片审核类, 命名规则 类名_操作 示例：pic_examine_pass 图片审核通过，
 * pic_pass_to_del 图片从审核通过到不通过
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/8
 * @Time:   14:02
 * version: 2.0
 */
class pai_pic_examine_v2_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
    }

    /**
     *  设置_图片审核表
     */
    private function set_pic_examine_tbl()
    {
        $this->setTableName( 'pic_examine_log' );
    }


    /**
     * 设置图片通过表
     * @param $date
     * @param bool $no_create_tbl 为true不创建表，并且返回false
     * @return bool
     * @throws App_Exception
     */
    private function set_pic_pass_tbl($date,$no_create_tbl = false)
    {
        $date = trim($date);
        if(strlen($date) <1 || date('Y-m-d',strtotime($date)) != $date) $date = date('Y-m-d',time());
        $table_num = date('Ym',strtotime($date));
        $table_name = "pic_examine_pass_log_{$table_num}";
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res)) //表不存在创建
        {
            if($no_create_tbl == true) return false;//没有数据
            $sql_str = "CREATE TABLE IF NOT EXISTS {$this->_db_name}.{$table_name} (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) NOT NULL,
                      `img_url` varchar(100) NOT NULL,
                      `add_time` datetime NOT NULL,
                      `audit_id` int(10) NOT NULL,
                      `audit_time` datetime NOT NULL,
                      `role` varchar(100) NOT NULL,
                      `img_type` varchar(100) NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `audit_id` (`audit_id`)
                    ) ENGINE=InnoDB;";
            $this->query($sql_str);
        }
        $this->setTableName( $table_name );
        return true;
    }

    /**
     * 设置图片删除表
     * @param string $date
     * @param bool $no_create_tbl 为true不创建表，并且返回false
     * @return bool
     * @throws App_Exception
     */
    private function set_pic_del_tbl($date,$no_create_tbl = false)
    {
        $date = trim($date);
        if(strlen($date) <1 || date('Y-m-d',strtotime($date)) != $date) $date = date('Y-m-d',time());
        $table_num = date('Ym',strtotime($date));
        $table_name = "pic_examine_del_log_{$table_num}";
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res)) //表不存在创建
        {
            if($no_create_tbl == true) return false;//没有数据
            $sql_str = "CREATE TABLE IF NOT EXISTS {$this->_db_name}.{$table_name} (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) NOT NULL,
                      `img_url` varchar(100) NOT NULL,
                      `add_time` datetime NOT NULL,
                      `audit_id` int(10) NOT NULL,
                      `audit_time` datetime NOT NULL,
                      `role` varchar(100) NOT NULL,
                      `img_type` varchar(100) NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `audit_id` (`audit_id`)
                    ) ENGINE=InnoDB;";
            $this->query($sql_str);
        }
        $this->setTableName( $table_name );
        return true;
    }

    /**
     * 获取图片_审核列表
     * @param bool $b_select_count
     * @param string $img_type 'work'作品图，merchandise商城图,head 为头像图
     * @param int $user_id   用户ID
     * @param string $where_str   查询条件
     * @param string $order_by   排序
     * @param string $limit
     * @param string $fields  查询字段
     * @return array|int
     */
    public function get_pic_examine_list($b_select_count = false,$img_type='work',$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_pic_examine_tbl();
        $img_type = trim($img_type);
        $user_id = intval($user_id);
        if(strlen($img_type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "img_type=:x_img_type";
            sqlSetParam($where_str,"x_img_type",$img_type);

        }
        if($user_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id='{$user_id}'";
        }
        if ($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str, $limit, $order_by, $fields);
        return $ret;
    }

    /**
     * 通过图片绝对路径获取图片url地址
     * @param string $img_path 图片绝对地址,实例: /disk/data/htdocs233/pic16/yueyue/20150908/20150908135937_291730_133732_10467.jpg
     * @return string
     */
    public static function change_img_url($img_path)
    {
        $img_url = '';
        $img_path = trim($img_path);
        if (strlen($img_path) <1) return '';
        $arr = explode('/', $img_path);
        $len = count($arr);
        if(file_exists($img_path))
        {
            if ($arr[$len-3] == 'icon')
            {
                $img_url = 'http://yue-icon-d.yueus.com/';
            }
            elseif($arr[$len-3] == 'seller_icon' || $arr[$len-4] == 'seller_icon')//商家头像
            {
                $img_url = 'http://seller-icon-d.yueus.com/';
            }
            else
            {
                $img_url = 'http://image19-d.yueus.com/yueyue/';
            }
        }
        else
        {
            if ($arr[$len-3] == 'icon')
            {
                $img_url = 'http://yue-icon.yueus.com/';
            }
            elseif($arr[$len-3] == 'seller_icon' || $arr[$len-4] == 'seller_icon')//商家头像
            {
                $img_url = 'http://seller-icon.yueus.com/';
            }
            else
            {
                $img_url = 'http://img16.poco.cn/yueyue/';
            }
        }
        for ($i= $len-2; $i < $len; $i++)
        {
            $img_url .= $arr[$i];
            if ($len -1 != $i)
            {
                $img_url .= '/';
            }

        }
        return $img_url;
    }
    /**
     * 获取图片审核表数据_通过图片ID
     * @param int $id
     * @return array|int
     */
    public function get_pic_examine_info_by_id($id)
    {
        $id = intval($id);
        if($id <1) return 0;
        $this->set_pic_examine_tbl();
        return $this->find("id={$id}");
    }

    /**
     * 删除图片待审核数据_通过ID
     * @param int $id
     * @return bool
     * @throws App_Exception
     */
    function pic_examine_del_by_id($id)
    {
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $this->set_pic_examine_tbl();
        return $this->delete("id={$id}");
    }

    ########################################上面是待审核的方法#######################################

    /**
     * 获取已审核图片_列表
     * @param bool $b_select_count
     * @param date string $date
     * @param string $img_type 'work'作品图，merchandise商城图,head 为头像图
     * @param int $audit_id
     * @param int $user_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_pic_pass_list($b_select_count = false,$date='',$img_type='work',$audit_id=0,$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $date = trim($date);
        $img_type = trim($img_type);
        $audit_id = intval($audit_id);
        $user_id = intval($user_id);
        $retID = $this->set_pic_pass_tbl($date,true);//如果不存在数据表
        $retID = intval($retID);
        if($retID <1) return false;
        if(strlen($img_type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "img_type=:x_img_type";
            sqlSetParam($where_str,"x_img_type",$img_type);
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($user_id>0)
        {
            if(strlen($where_str)) $where_str .= ' AND ';
            $where_str .="user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }
    /**
     * 插入通过表中数据
     * @param $insert_data
     * @param $date
     * @return int
     * @throws App_Exception
     */
    private function pic_insert_pass($insert_data,$date)
    {
        global $yue_login_id;
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $this->set_pic_pass_tbl($date);
        $insert_data['audit_time'] = date('Y-m-d H:i:s',time());
        $insert_data['audit_id'] = $yue_login_id;
        return $this->insert($insert_data);
    }

    /**
     * 接受要插入通过表中_数据
     * @param $data
     * @param string $date
     * @param bool $b_select_count true|false
     * @return int
     * @throws App_Exception
     */
    private function pic_insert_pass_info($data,$date ='',$b_select_count = false)
    {
        $date = trim($date);
        if(!is_array($data) || empty($data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $insert_data['user_id'] = $data['user_id'];
        $insert_data['role'] = $data['role'];
        if($b_select_count == true) $data['img_url'] = str_replace('.rm', '', $data['img_url']);
        $insert_data['img_url'] = $data['img_url'];
        $insert_data['add_time'] = $data['add_time'];
        $insert_data['img_type'] = $data['img_type'];
        return $this->pic_insert_pass($insert_data,$date);
    }

    /**
     * 通过ID_删除通过表中数据
     * @param int $id
     * @param string $date
     * @return bool
     * @throws App_Exception
     */
    private function pic_pass_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $this->set_pic_pass_tbl($date,true);
        return $this->delete("id={$id}");
    }

    /**
     * 通过ID_获取通过表中数据
     * @param int $id
     * @param string $date
     * @return array|bool
     * @throws App_Exception
     */
    private function get_pic_pass_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $retID = $this->set_pic_pass_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        return $this->find("id={$id}");
    }

######################################上面是审核通过数据的一下方法####################################

    /**
     * 获取删除图片_列表
     * @param bool $b_select_count
     * @param date string $date
     * @param string $img_type 'work'作品图，merchandise商城图,head 为头像图
     * @param int $audit_id
     * @param int $user_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_pic_del_list($b_select_count = false,$date='',$img_type='work',$audit_id=0,$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $date = trim($date);
        $img_type = trim($img_type);
        $audit_id = intval($audit_id);
        $user_id = intval($user_id);
        $retID = $this->set_pic_del_tbl($date,true);//如果不存在数据表
        $retID = intval($retID);
        if($retID <1) return false;
        if(strlen($img_type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "img_type=:x_img_type";
            sqlSetParam($where_str,"x_img_type",$img_type);
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($user_id>0)
        {
            if(strlen($where_str)) $where_str .= ' AND ';
            $where_str .="user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }
    /**
     * 插入删除表中
     * @param array $insert_data
     * @param $date
     * @return int
     * @throws App_Exception
     */
    private function pic_insert_del($insert_data,$date)
    {
        global $yue_login_id;
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $this->set_pic_del_tbl($date);
        $insert_data['audit_time'] = date('Y-m-d H:i:s',time());
        $insert_data['audit_id'] = $yue_login_id;
        return $this->insert($insert_data);
    }
    /**
     * 插入删除表中_接受作用
     * @param string $data
     * @param string $date
     * @return int
     * @throws App_Exception
     */
    private function pic_insert_del_info($data,$date ='')
    {
        $date = trim($date);
        if(!is_array($data) || empty($data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据不能为空' );
        }
        $insert_data['user_id'] = $data['user_id'];
        $insert_data['role'] = $data['role'];
        $insert_data['img_url'] = $data['img_url'].'.rm';
        $insert_data['add_time'] = $data['add_time'];
        $insert_data['img_type'] = $data['img_type'];
        return $this->pic_insert_del($insert_data,$date);
    }

    /**
     * 通过ID_获取删除表中数据
     * @param int $id
     * @param string $date
     * @return array|bool
     * @throws App_Exception
     */
    private function get_pic_del_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $retID = $this->set_pic_del_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        return $this->find("id={$id}");
    }

    /**
     * 通过ID_删除【图片删除】表中数据
     * @param int $id
     * @param string $date
     * @return bool
     * @throws App_Exception
     */
    private function pic_del_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        $this->set_pic_del_tbl($date,true);
        return $this->delete("id={$id}");
    }

######################################上面是审核不通过数据的一下方法####################################

    /**
     * 发送_图片删除消息
     * @param int $user_id 用户ID
     * @param string $img_type 类型
     * @param string $img_url
     * @param int $tpl_id
     * @param string $tpl_detail
     * @param string $role  yuebuyer|yueseller
     */
    private function send_pic_del_message($user_id,$img_type,$img_url,$tpl_id,$tpl_detail,$role='')
    {
        $send_message_obj = POCO::singleton('pai_pic_send_message_class');
        $pai_information_push_obj = POCO::singleton( 'pai_information_push' );
        $user_id = intval($user_id);
        $img_type = trim($img_type);
        $img_url = trim($img_url);
        $tpl_id = intval($tpl_id);
        $tpl_detail = trim($tpl_detail);
        $role = trim($role);
        if($user_id <1 || strlen($img_type) <1) return false;
        $role = $this->get_send_to_role($img_type,$img_url,$role); //获取角色
        if(!$role) return false;//角色不存在时退出

        //角色翻转,就是为了确定有那个角色发送到另外一个角色
        if($role == 'yuebuyer')
        {
            $role = "yueseller";
        }
        elseif($role == "yueseller")
        {
            $role = "yuebuyer";
        }
        $post_data['media_type'] = 'text';
        $post_data['content'] = $tpl_detail;
        $post_data['send_user_id'] = 10005;
        $post_data['to_user_id'] = $user_id;
        $post_data['send_user_role'] = $role;

        foreach($post_data AS &$val) //内容格式化
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }
        if ($tpl_id <1)
        {
            //$pai_information_push_obj->send_msg($post_data);
        }
        else
        {
            $add_time = date('Y-m-d',time());
            $retID = $send_message_obj->get_pic_send_message_info($user_id, $tpl_id,$add_time);
            if ($retID) //就发送
            {
                $send_message_obj->add_send_message($user_id, $tpl_id, $add_time);
                //$pai_information_push_obj->send_msg($post_data);
            }
        }
    }

    /**
     * 获取_角色
     * @param string $img_type 类型
     * @param string $img_url  图片地址
     * @param string $role  yuebuyer|yueseller
     * @return bool|string
     */
    public function get_send_to_role($img_type,$img_url,$role ='')
    {
        $img_type = trim($img_type);
        $img_url = trim($img_url);
        $role = trim($role);
        if(in_array($role,array('yuebuyer','yueseller'))) return $role;
        if($img_type <0) return false;
        if($img_type == 'works')
        {
            return 'yuebuyer';
        }
        elseif($img_type == 'merchandise')
        {
            return 'yueseller';
        }
        elseif($img_type == 'head')
        {
            if(strlen($img_url) <1) return false;
            $arr = explode('/', $img_url);
            $len = count($arr);
            if ($arr[$len-3] == 'icon' || $arr[$len-4] == 'icon')//消费者
            {
                return 'yuebuyer';
            }
            elseif($arr[$len-3] == 'seller_icon' || $arr[$len-4] == 'seller_icon')//商家头像
            {
                return 'yueseller';
            }
        }
        return false;
    }


    /**
     * 从图片审核表中_到图片通过表
     * @param array $ids
     * @return array
     */
    public function pic_examine_pass($ids)
    {
        $return_data = array('code'=>0);//返回值
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '传过来的数据为空';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_pic_examine_info_by_id($id);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->pic_insert_pass_info($ret);
                    $del_ret = $this->pic_examine_del_by_id($id);
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
     * 从审核中_进删除库中
     * @param string $img_type 类型
     * @param string $ids 字段数据
     * @param int $tpl_id 模板ID
     * @param string $tpl_detail 内容
     * @throws App_Exception
     * @return array
     */
    public function pic_examine_del($img_type,$ids,$tpl_id,$tpl_detail)
    {
        $return_data = array('code'=>0);//返回值
        $pai_pic_obj =  POCO::singleton('pai_pic_class');//works图片删除
        $img_type = trim($img_type);
        $ids = trim($ids);
        $tpl_id = intval($tpl_id);
        $tpl_detail = trim($tpl_detail);
        if(strlen($img_type) <1) return $return_data['err'] = '传过来的类型为空';
        if(strlen($ids) <1) return $return_data['err'] = '传过来的数据为空';
        $id_arr = explode(',',$ids);
        if(!is_array($id_arr) || empty($id_arr)) return $return_data['err'] = '传过来的数据为空';
        //创建删除表
        foreach($id_arr as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_pic_examine_info_by_id($id);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_start_time = microtime_float();
                    $insert_ret = $this->pic_insert_del_info($ret);
                    $insert_end_time = microtime_float();
                    $return_data['insert_time'] = $insert_end_time-$insert_start_time;
                    $del_ret = $this->pic_examine_del_by_id($id);
                    $return_data['del_time'] = microtime_float() - $insert_end_time;
                    if(!$insert_ret || !$del_ret) { //事务回滚
                        POCO_TRAN::rollback($this->getServerId());
                    }else{
                        POCO_TRAN::commmit($this->getServerId()); //事务提交
                        if ($img_type == 'works') //works是需要删除需
                        {
                            $img_url2 = str_replace(array('/disk/data/htdocs233/pic16/yueyue/','/disk/data/htdocs233/pic0/yueyue/'), '',$ret['img_url']);
                            $img_url2 = substr($img_url2 , 0,strrpos($img_url2, '.')) ;
                            $pai_pic_obj->del_audit_pic($ret['user_id'],$img_url2); //删除works数据
                            unset($img_url2);
                        }
                        $this->server_pic_rm($ret['img_url'],$img_type); //移除数据
                        $this->send_pic_del_message($ret['user_id'],$img_type,$ret['img_url'],$tpl_id,$tpl_detail,$ret['role']); //发送信息
                    }
                    unset($ret);
                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }


    /**
     * 审核通过的图片_到审核不通过
     * @param array $ids
     * @param string $date
     * @return array
     * @throws App_Exception
     */
    public function pic_pass_to_del($ids,$date)
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
                $ret = $this->get_pic_pass_by_id($id,$date);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_start_time = microtime_float();
                    $insert_ret = $this->pic_insert_del_info($ret);
                    $insert_end_time = microtime_float();
                    $return_data['insert_time'] = $insert_end_time-$insert_start_time;
                    $del_ret = $this->pic_pass_by_id($id,$date);
                    $return_data['del_time'] = microtime_float() - $insert_end_time;
                    if(!$insert_ret || !$del_ret){//事务回滚
                        POCO_TRAN::rollback($this->getServerId());
                    }else
                    {
                        $img_start_time = microtime_float();
                        POCO_TRAN::commmit($this->getServerId()); //事务提交
                        $this->server_pic_rm($ret['img_url'],$ret['img_type']); //删除图片
                        $return_data['rm_img_time'] = microtime_float() - $img_start_time;
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
    public function pic_del_to_pass($ids,$date)
    {
        $date = trim($date);
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '传过来的数据为空';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_pic_del_by_id($id,$date);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->pic_insert_pass_info($ret,'',true);
                    $del_ret = $this->pic_del_by_id($id,$date);
                    if(!$insert_ret || !$del_ret){//事务回滚
                        POCO_TRAN::rollback($this->getServerId());
                    } else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //事务提交
                        $this->server_pic_recover($ret['img_url'],$ret['img_type']);//恢复图片
                    }

                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * 软件去移除图片
     * @param  [string] $img_url [图片地址]
     * @param  [stirng] $img_type  [头像或者作品]
     * @return [type]            [description]
     */
    private function server_pic_rm($img_url,$img_type)
    {
        $gmclient= new GearmanClient();
        if(file_exists($img_url))  //新机房存在删除新机房数据
        {
            $gmclient->addServers("172.18.5.4:9210");
        }else
        {
            $gmclient->addServers("172.18.5.216:9870");
        }
        $gmclient->setTimeout(5000); // 设置超时
        $data[0] = $img_url;
        if($img_type == 'works' || $img_type== 'merchandise')
        {
            $data[1] = yueyue_resize_act_img_url($img_url,145);
            $data[2] = yueyue_resize_act_img_url($img_url,260);
            $data[3] = yueyue_resize_act_img_url($img_url,165);
            $data[4] = yueyue_resize_act_img_url($img_url,320);
            $data[5] = yueyue_resize_act_img_url($img_url,440);
            $data[6] = yueyue_resize_act_img_url($img_url,640);
        }
        elseif($img_type == 'head')
        {
            $data[1] = yueyue_resize_act_img_url($img_url,32);
            $data[2] = yueyue_resize_act_img_url($img_url,64);
            $data[3] = yueyue_resize_act_img_url($img_url,86);
            $data[4] = yueyue_resize_act_img_url($img_url,100);
            $data[5] = yueyue_resize_act_img_url($img_url,165);
            $data[6] = yueyue_resize_act_img_url($img_url,468);
        }
        //var_dump($data);
        foreach ($data as $key => $val)
        {
            $req_param['source_path'] = $val;
            $req_param['dest_path']   = $val.'.rm';
            //var_dump($req_param);exit;
            $result= $gmclient->do("file_rename",json_encode($req_param) );
            unset($req_param);
        }
        //print_r($data);exit;
        return $result;
    }

    /**
     * 软件去恢复图片
     * @param  [string] $img_url [图片地址]
     * @param  [stirng] $img_type  [头像或者作品]
     * @return [type]            [description]
     */
    private function server_pic_recover($img_url,$img_type)
    {
        $gmclient= new GearmanClient();
        if(file_exists($img_type)) //新机房存在删除新机房数据
        {
            $gmclient->addServers("172.18.5.4:9210");
        }else
        {
            $gmclient->addServers("172.18.5.216:9870");
        }
        $gmclient->setTimeout(5000); // 设置超时
        $data[0] = $img_url;
        //echo $img_url;
        if($img_type == 'works' || $img_type == 'merchandise')
        {
            $data[1] = yueyue_resize_act_img_url($img_url,145);
            $data[2] = yueyue_resize_act_img_url($img_url,260);
            $data[3] = yueyue_resize_act_img_url($img_url,165);
            $data[4] = yueyue_resize_act_img_url($img_url,320);
            $data[5] = yueyue_resize_act_img_url($img_url,440);
            $data[6] = yueyue_resize_act_img_url($img_url,640);
        }
        elseif($img_type == 'head')
        {
            $data[1] = yueyue_resize_act_img_url($img_url,32);
            $data[2] = yueyue_resize_act_img_url($img_url,64);
            $data[3] = yueyue_resize_act_img_url($img_url,86);
            $data[4] = yueyue_resize_act_img_url($img_url,100);
            $data[5] = yueyue_resize_act_img_url($img_url,165);
            $data[6] = yueyue_resize_act_img_url($img_url,468);
        }
        //var_dump($data);
        foreach ($data as $key => $val)
        {
            $req_param['source_path'] = $val;
            $req_param['dest_path']   = str_replace('.rm', '', $val);
            $result= $gmclient->do("file_rename",json_encode($req_param) );
            unset($req_param);
        }
        return $result;
    }

}