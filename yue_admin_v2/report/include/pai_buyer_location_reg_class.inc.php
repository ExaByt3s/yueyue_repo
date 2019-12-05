<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/19
 * @Time:   11:36
 * version: 1.0
 */

class pai_buyer_location_reg_class extends POCO_TDG
{
    /**
     *  ���캯��
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_userinfo_db' );
        $this->setTableName( 'yueyue_location_reg_userinfo_tbl' );
    }

    /**
     * ��ȡע������б��б�
     * @param bool $b_select_count true|false
     * @param string $where_str   ��ѯ����
     * @param string $group_by    group by ��ѯ
     * @param string $order_by    ����
     * @param string $limit       ѭ��������ѯ
     * @param string $fields      �ֶ�
     * @return array|int
     */
    public function get_buyer_location_reg_list($b_select_count=false,$where_str,$group_by='',$order_by='id DESC', $limit='0,20', $fields='*')
    {
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        if(strlen($group_by)>0)
        {
            if(strlen($where_str)>0)
            {
                $where_str .= ' ';
            }else{
                $where_str .= " 1 ";
            }
            $where_str .="{$group_by}";
        }
        return $this->findAll($where_str,$limit,$order_by,$fields);
    }

    /**
     * @param $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_reg_location_info($insert_data)
    {
        if (empty($insert_data) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '�����ݲ���Ϊ��' );
        }
        return $this->insert($insert_data, "REPLACE");
    }

    /**
     * ��ӷ���ʵʱ����
     */
    public function add_reg_userinfo_by_date()
    {
        $add_time = date('Y-m-d',time());
        $sql_str = "SELECT LEFT(location_id,6) AS location_id,COUNT(user_id) AS user_count,FROM_UNIXTIME(add_time,'%Y-%m-%d') AS date_time FROM pai_db.pai_user_tbl
        WHERE location_id>0 AND FROM_UNIXTIME(add_time,'%Y-%m-%d')='{$add_time}'  GROUP BY LEFT(location_id,6) ORDER BY user_count DESC";
        $ret = db_simple_getdata($sql_str, false, 101);

        if(is_array($ret))
        {
            foreach($ret as $val)
            {
                $this->add_reg_location_info($val);
            }
        }
    }
}