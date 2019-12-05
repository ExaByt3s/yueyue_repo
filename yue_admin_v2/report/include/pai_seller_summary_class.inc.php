<?php
/**
 * @desc:   �̼��ܼ�
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/16
 * @Time:   11:23
 * version: 1.0
 */
class pai_seller_summary_class extends POCO_TDG
{
    /**
     *  ���캯��
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_log_tmp_v2_db' );
        $this->setTableName( 'yueyue_seller_summary_tbl' );
    }

    /**
     * ��ȡ�ܼ�����
     * @param bool $b_select_count
     * @param int $type_id  ����ID
     * @param string $where_str   ����
     * @param string $order_by ����
     * @param string $limit ѭ������
     * @param string $fields ��ѯ�ֶ�
     * @return array|int
     */
    public function get_seller_summary_list($b_select_count=false,$type_id,$where_str,$order_by='id DESC', $limit='0,20', $fields='*')
    {
        $type_id = (int)$type_id;
        $where_str = trim($where_str);
        if($type_id >=0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type_id ={$type_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        else
        {
            $ret = $this->findAll($where_str,$limit,$order_by,$fields);
            return $ret;
        }
    }
}