<?php
/**
 * @desc:   查询数据keywords
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/28
 * @Time:   09:29
 * version: 1.0
 */
class pai_search_keywords_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId( 22 );
        $this->setDBName( 'yueyue_log_tmp_v2_db' );
    }

    /**
     * 获取列表
     * @param bool $b_select_count 是否查询true|false
     * @param string $where_str  查询条件
     * @param string $order_by   排序
     * @param string $limit     循环条数
     * @param string $fields    查询字段
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

    public function  get_search_keywords_list($b_select_count = false,$start_date,$end_date,$type='',$type_id=0, $keyword='',$where_str ='',$order_by = 'visit_time DESC,id DESC', $limit = '0,10', $fields = '*',$group_by ='')
    {
        $start_date = trim($start_date);
        $end_date = trim($end_date);
        $type = trim($type);
        $type_id = (int)$type_id;
        $keyword = trim($keyword);
        //时间处理
        if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
        {
            if(strtotime($start_date)>strtotime(date('Y-m-d',time()-24*3600))) $start_date = date('Y-m-d',time()-24*3600);
            $start_date = date('Y-m-d',strtotime($start_date));
        }else
        {
            $start_date = date('Y-m-d',time()-24*3600);
        }
        if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
        {
            if(strtotime($end_date)>strtotime(date('Y-m-d',time()-24*3600))) $end_date = date('Y-m-d',time()-24*3600);
            $end_date = date('Y-m-d',strtotime($end_date));
        }else
        {
            $end_date = date('Y-m-d',time()-24*3600);
        }
        if(strlen($start_date)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "DATE_FORMAT(visit_time,'%Y-%m-%d')>=:x_start_date";
            sqlSetParam($where_str,"x_start_date",$start_date);
        }
        if(strlen($end_date)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "DATE_FORMAT(visit_time,'%Y-%m-%d')<=:x_end_date";
            sqlSetParam($where_str,"x_end_date",$end_date);
        }
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($type_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type_id= {$type_id}";
        }
        if(strlen($keyword)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "keyword=:x_keyword";
            sqlSetParam($where_str,'x_keyword',$keyword);
        }
        if(strlen($where_str)>0) $where_str = " WHERE {$where_str}";

        //整理多张数据表查询
        if(date('Y-m',strtotime($start_date)) == date('Y-m',strtotime($end_date))) //时间相同时
        {
            $table_name = 'yueyue_log_tmp_v2_db.yueyue_search_keyword_log_'.date('Ym',strtotime($start_date));
            $sql_str = "SELECT {$fields} FROM {$table_name} {$where_str}";
            if(strlen($group_by)>0 && $b_select_count != true) $sql_str .=" {$group_by} ORDER BY C DESC LIMIT {$limit}";
        }
        else//不同表时
        {
            $table_name = 'yueyue_log_tmp_v2_db.yueyue_search_keyword_log_'.date('Ym',strtotime($start_date));
            $sql_str = "(SELECT {$fields} FROM {$table_name} {$where_str}";
            if(strlen($group_by)>0 && $b_select_count != true)
            {
                $sql_str .=" {$group_by})";
            }
            else
            {
                $sql_str .= ")";
            }
            $end_table_name = 'yueyue_log_tmp_v2_db.yueyue_search_keyword_log_'.date('Ym',strtotime($end_date));
            if(strlen($sql_str)>0) $sql_str .= ' UNION ';
            $sql_str .= "SELECT {$fields} FROM {$end_table_name} {$where_str}";
            if(strlen($group_by)>0 && $b_select_count != true) $sql_str .=" {$group_by} ORDER BY C DESC LIMIT {$limit}";
        }

        /*if(date('Y-m',strtotime($start_date)) == date('Y-m',strtotime($end_date)))
        {
            $table_name = 'yueyue_log_tmp_v2_db.yueyue_search_keyword_log_'.date('Ym',strtotime($end_date));
            if(strlen($sql_str)>0) $sql_str .= ' UNION ';
            $sql_str .= "SELECT {$fields} FROM {$table_name} {$where_str}";
            if(strlen($group_by)>0 && $b_select_count != true) $sql_str .=" {$group_by}";
        }*/
        if($b_select_count == true)
        {
            $ret = db_simple_getdata($sql_str,true,22);
            return (int)$ret['C'];
        }
        //return $sql_str;
        $ret = db_simple_getdata($sql_str,false,22);
        return $ret;
    }


}