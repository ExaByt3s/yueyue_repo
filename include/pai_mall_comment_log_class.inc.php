<?php
/**
 * @desc:   ����log������������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/31
 * @Time:   16:06
 * version: 1.0
 */
class pai_mall_comment_log_class extends POCO_TDG
{
    /**
     *
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_log_tmp_v2_db' );
    }

    /**
     * ���ö������������������ı�
     * @param $date
     * @return bool
     * @throws App_Exception
     */
    private function set_order_for_num_tbl($date)
    {
        $date = trim($date);
        if(strlen($date) <1 || $date != date('Y-m-d',strtotime($date))) $date = date('Y-m-d',time()-24*3600);
        $table_num = date('Ym',strtotime($date));
        $table_name = "yueyue_order_for_num_tbl_".$table_num;
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if(is_array($res) && !empty($res))
        {
            $this->setTableName($table_name);
            return true;
        }
        return false;
    }

    /**
     * ����������۱�
     * @param  string $date
     * @return bool
     * @throws App_Exception
     */
    private function set_comment_for_buyer_tbl($date)
    {
        $date = trim($date);
        if(strlen($date) <1 || $date != date('Y-m-d',strtotime($date))) $date = date('Y-m-d',time()-24*3600);
        $table_num = date('Ym',strtotime($date));
        $table_name = "yueyue_comment_for_buyer_tbl_".$table_num;
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if(is_array($res) && !empty($res))
        {
            $this->setTableName($table_name);
            return true;
        }
        return false;
    }

    /**
     * ���׶������ͳ��
     * @param bool $b_select_count
     * @param string $date �� ʾ��:'2015-08-12'
     * @param int $type_id  ����ID��ʾ��:40
     * @param string $where_str ���� ʾ��:$where_str = "add_time='2015-08-12'"
     * @param string $group_by
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array
     */
    public function get_order_num_list($b_select_count = false,$date,$type_id,$where_str = '',$group_by='' ,$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $date = trim($date);
        $type_id = intval($type_id);
        $where_str = trim($where_str);
        $res = $this->set_order_for_num_tbl($date);
        if($res == false) return false;
        if($type_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .="type_id={$type_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        if(strlen($group_by) >0)//group ����
        {
            $where_str .= strlen($where_str)>0 ? ' ': ' 1 ';
            $where_str .= $group_by;
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * ��ȡ�����б�
     * @param bool $b_select_count
     * @param string $date
     * @param int $type_id  ����ID��ʾ��:40
     * @param string $where_str ���� ʾ��:$where_str = "add_time='2015-08-12'"
     * @param string $group_by
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array
     */
    public function get_comment_for_buyer_list($b_select_count = false,$date,$type_id,$where_str = '',$group_by='' ,$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $date = trim($date);
        $type_id = intval($type_id);
        $where_str = trim($where_str);
        $res = $this->set_comment_for_buyer_tbl($date);//ѡ���
        if($res == false) return false;
        if($type_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .="type_id={$type_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        if(strlen($group_by) >0)//group ����
        {
            $where_str .= strlen($where_str)>0 ? ' ': ' 1 ';
            $where_str .= $group_by;
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * ��ȡ��������
     * @param string $date
     * @param int $id
     * @return array|bool
     */
    public function get_comment_info_by_id($date,$id)
    {
        $id = intval($id);
        if($id <1) return false;
        $res = $this->set_comment_for_buyer_tbl($date);
        if($res == false) return false;
        $ret = $this->find("id={$id}");
        return $ret;
    }
}