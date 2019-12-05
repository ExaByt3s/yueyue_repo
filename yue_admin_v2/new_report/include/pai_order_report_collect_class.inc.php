<?php
/**
 * @desc:   订单汇总表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/20
 * @Time:   13:51
 * version: 1.0
 */
class pai_order_report_collect_class extends POCO_TDG
{
    /**
     *  构造函数
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_log_tmp_v2_db' );
        $this->setTableName( 'yueyue_order_report_tbl' );
    }

    /**
     * 获取某一月的数据
     * @param bool $b_select_count 是否查询条数
     * @param string $type        类型 周类型|月类型
     * @param string $type_id     分类ID
     * @param string $where_str   添加，示例： $where_str ="user_id = 100000";
     * @param string $order_by    排序
     * @param string $limit       查询条数,示例:'0,10'
     * @param string $fields      查询字段,示例:'user_id';
     * @return array|int
     */
    public function get_order_report_list($b_select_count = false,$type,$type_id,$where_str ='',$order_by='date_time DESC,id DESC',$limit = '0,20', $fields = '*')
    {
        $type = trim($type);
        $type_id = intval($type_id);
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0)  $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($type_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type_id={$type_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }
}