<?php
/**
 * @desc:   城市开通类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/9
 * @Time:   11:13
 * version: 1.0
 */
class pai_location_open_class extends POCO_TDG
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_admin_db' );
        $this->setTableName ( 'pai_open_location_tbl' );
    }

    /**
     * 添加开通地区
     * @param int $location_id  //城市ID 例如广州location_id = 101029001
     * @param int $version_id   //开通版本外键ID
     * @return int
     * @throws App_Exception   这里必须传入location_id和$version_id 不然就会报错
     */
    public function add_info($location_id = 0,$version_id = 0)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/common/PinYin.class.php');//拼音类
        include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");//地区类
        $PinYin = new PinYin();

        global $yue_login_id;
        $location_id = intval($location_id);
        $version_id  = intval($version_id);
        if($location_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':城市ID不能为空' );
        }
        if($version_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':版本ID不能为空' );
        }
        $data['location_id'] = $location_id;
        $data['version_id']  = $version_id;
        $data['user_id']     = $yue_login_id;
        $data['add_time']    = date('Y-m-d H:i:s', time());

        $ret = get_poco_location_name_by_location_id($location_id,true,true);
        $chinese_name = trim($ret['level_1']['name']);
        $english_name = $PinYin->getFirstPY($chinese_name);
        $data['location_id'] = $location_id;
        $data['version_id']  = $version_id;
        $data['chinese_name']  = trim($chinese_name);
        $data['english_name']  = trim($english_name);
        return $this->insert($data,"IGNORE");
    }


    /**
     * 更新数据
     * @param  int $id            //自递增ID
     * @param  int $location_id   //城市ID  例如广州location_id = 101029001
     * @param  int $version_id    //版本ID  可以根据版本开通不同城市
     * @return int|blea           返回值
     * @throws App_Exception      //条件不符会报错
     */
    public function update_info($id =0,$location_id = 0,$version_id = 0)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/common/PinYin.class.php');//拼音类
        include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");//地区类
        $PinYin = new PinYin();

        $id = intval($id);
        $location_id = intval($location_id);
        $version_id  = intval($version_id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        if($location_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':城市ID不能为空' );
        }
        if($version_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':版本ID不能为空' );
        }
        $ret = get_poco_location_name_by_location_id($location_id,true,true);
        $chinese_name = trim($ret['level_1']['name']);
        $english_name = $PinYin->getFirstPY($chinese_name);
        $data['location_id'] = $location_id;
        $data['version_id']  = $version_id;
        $data['chinese_name']  = trim($chinese_name);
        $data['english_name']  = trim($english_name);
        return $this->update($data,"id={$id}");
    }

    /**
     * 删除开通的城市
     * @param  int $id        自递增ID
     * @return int|bool       返回值
     * @throws App_Exception  条件不符会报错
     */
    public function del_info($id = 0)
    {
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        return $this->delete("id={$id}");
    }

    /**
     * 获取一条数据
     * @param int $id    id
     * @return array|bool  返回值
     */
    public function get_info($id =0)
    {
        $id = intval($id);
        if($id <1) return false;
        return $this->find("id={$id}");
    }

    /**
     * 获取列表数据
     * @param bool $b_select_count  $b_select_count=true 查询条数|否则查询数据
     * @param int $version_id       版本ID
     * @param int $user_id          添加者ID
     * @param string $start_time    添加开始时间  例如:'2015-04-06'
     * @param string $end_time      添加结束时间  例如:'2015-04-07'
     * @param string $where_str     条件
     * @param string $order_by      排序
     * @param string $limit         循环
     * @param string $fields        查询字段
     * @return array|int            返回值
     */
    public function get_list($b_select_count = false,$version_id=0,$user_id = 0, $start_time = '',$end_time = '',$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        //参数整理
        $version_id = intval($version_id);
        $user_id    = intval($user_id);
        $start_time = trim($start_time);
        $end_time   = trim($end_time);
        $where_str  = trim($where_str);

        //参数加入sql中
        if($version_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "version_id = {$version_id}";
        }
        if($user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if(strlen($start_time) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(add_time),'%Y-%m-%d') >= :x_start_time";
            sqlSetParam($where_str,'x_start_time',$start_time);
        }
        if(strlen($end_time)>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(add_time),'%Y-%m-%d') <= :x_end_time";
            sqlSetParam($where_str,'x_end_time',$end_time);
        }
        if ($b_select_count == true)
        {
            return $this->findCount ( $where_str );
        }
        return $this->findAll ( $where_str, $limit, $order_by, $fields );
    }

    /**
     * 查询开通的版本地区
     *
     * @param string $version_num  //版本号
     * @return array|int
     */
    public function get_location_by_version_num($version_num = '')
    {
        $version_open_obj = POCO::singleton('pai_version_open_class');

        //查询版本ID
        $version_num = trim($version_num);
        if(strlen($version_num) <1) return array();
        $version_id = $version_open_obj->get_version_id_by_version_num($version_num);

        //查询地区
        $version_id = intval($version_id);
        if($version_id <1) return array();
        $ret = $this->get_list(false,$version_id,0,'','','','english_name ASC','0,99999999','location_id,chinese_name as name');

        if(!is_array($ret)) $ret = array();
        foreach($ret as $key=>$val)
        {
            $ret[$key]['name'] = str_replace('市','',$val['name']);
        }
        return $ret;
    }

}