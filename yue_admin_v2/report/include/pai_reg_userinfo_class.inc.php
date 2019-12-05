<?php
/**
 * @desc:   ע����Ϣ
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/18
 * @Time:   16:53
 * version: 1.0
 */
class pai_reg_userinfo_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId( 22 );
        $this->setDBName( 'yueyue_userinfo_db' );
        $this->setTableName( 'yueyue_reg_userinfo_tbl' );
    }

    /**
     * ��ȡ�б�
     * @param bool $b_select_count �Ƿ��ѯtrue|false
     * @param string $where_str  ��ѯ����
     * @param string $order_by   ����
     * @param string $limit     ѭ������
     * @param string $fields    ��ѯ�ֶ�
     * @return array|int
     */
    public function get_reg_list($b_select_count = false,$where_str = '', $order_by = 'add_time DESC,id DESC', $limit = '0,10', $fields = '*')
    {
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * �������
     * @param  array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_info($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        return $this->insert ($insert_data,'REPLACE');
    }

    /**
     * ��ӽ��켴ʱ����
     * @return int
     * @throws App_Exception
     */
    public function add_reg_info()
    {
        $date = date('Y-m-d',time());
        $sql_str ="SELECT reg_from, COUNT(reg_from) AS reg_count FROM pai_db.pai_user_tbl WHERE DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m-%d') = '".mysql_escape_string($date)."' GROUP BY reg_from ORDER BY reg_from DESC";
        $ret = db_simple_getdata($sql_str,true,101);
        $data['add_time'] = $date;
        $data['weixin_reg'] = $ret[0]['reg_count'];
        $data['pc_reg'] = $ret[1]['reg_count'];
        $data['app_reg'] = $ret[2]['reg_count'];
        $data['other_reg'] = $ret[3]['reg_count'];
        return $this->add_info($data) ;
    }
}