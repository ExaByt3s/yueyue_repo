<?php
/**
 * @desc:   pa目录下地推，引新注册类【主要由于推广有多少用户通过地推进行注册】
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/19
 * @Time:   11:17
 * version: 1.0
 */
class pai_pa_dt_register_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
        $this->setTableName( 'pai_pa_dt_register_log' );
    }

    /**
     * 添加数据进log表
     * @param array $insert_data
     * @return bool|int
     */
    private function add_pa_register_info($insert_data)
    {
        if (empty ($insert_data) || !is_array($insert_data)) return false;//这里加log可以直接使用false
        return $this->insert ( $insert_data,'IGNORE' );
    }

    /**
     * 通过ID添加log数据
     * @param int $user_id  说明:注册ID
     */
    public function add_register_log_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        $puid = trim($_COOKIE['tj_spread_regedit']);
        if(strlen($puid)<1 || $user_id <1) return false;
        $data['user_id'] = $user_id;
        $data['puid'] = $puid;
        $data['get_client_ip'] = get_client_ip();
        $data['add_time'] = time();
        $this->add_pa_register_info($data);
    }

    /*
	 * 获取 列表
	 * @param bool $b_select_count
	 * @param string $puid 推广码
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit
	 * @param string $fields 查询字段
	 *
	 * return array
	 */
    public function get_pa_dt_register_list($b_select_count = false,$puid,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $puid = trim($puid);
        if(strlen($puid)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "puid=:x_puid";
            sqlSetParam($where_str,'x_puid',$puid);
        }
        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $where_str );
        } else
        {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        }
        return $ret;
    }
}