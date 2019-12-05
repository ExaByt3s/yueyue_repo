<?php
/**
 * @desc:   ���п�ͨ��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/9
 * @Time:   11:13
 * version: 1.0
 */
class pai_location_open_class extends POCO_TDG
{
    /**
     * ���캯��
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_admin_db' );
        $this->setTableName ( 'pai_open_location_tbl' );
    }

    /**
     * ��ӿ�ͨ����
     * @param int $location_id  //����ID �������location_id = 101029001
     * @param int $version_id   //��ͨ�汾���ID
     * @return int
     * @throws App_Exception   ������봫��location_id��$version_id ��Ȼ�ͻᱨ��
     */
    public function add_info($location_id = 0,$version_id = 0)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/common/PinYin.class.php');//ƴ����
        include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");//������
        $PinYin = new PinYin();

        global $yue_login_id;
        $location_id = intval($location_id);
        $version_id  = intval($version_id);
        if($location_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
        }
        if($version_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�汾ID����Ϊ��' );
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
     * ��������
     * @param  int $id            //�Ե���ID
     * @param  int $location_id   //����ID  �������location_id = 101029001
     * @param  int $version_id    //�汾ID  ���Ը��ݰ汾��ͨ��ͬ����
     * @return int|blea           ����ֵ
     * @throws App_Exception      //���������ᱨ��
     */
    public function update_info($id =0,$location_id = 0,$version_id = 0)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/common/PinYin.class.php');//ƴ����
        include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");//������
        $PinYin = new PinYin();

        $id = intval($id);
        $location_id = intval($location_id);
        $version_id  = intval($version_id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
        }
        if($location_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
        }
        if($version_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�汾ID����Ϊ��' );
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
     * ɾ����ͨ�ĳ���
     * @param  int $id        �Ե���ID
     * @return int|bool       ����ֵ
     * @throws App_Exception  ���������ᱨ��
     */
    public function del_info($id = 0)
    {
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
        }
        return $this->delete("id={$id}");
    }

    /**
     * ��ȡһ������
     * @param int $id    id
     * @return array|bool  ����ֵ
     */
    public function get_info($id =0)
    {
        $id = intval($id);
        if($id <1) return false;
        return $this->find("id={$id}");
    }

    /**
     * ��ȡ�б�����
     * @param bool $b_select_count  $b_select_count=true ��ѯ����|�����ѯ����
     * @param int $version_id       �汾ID
     * @param int $user_id          �����ID
     * @param string $start_time    ��ӿ�ʼʱ��  ����:'2015-04-06'
     * @param string $end_time      ��ӽ���ʱ��  ����:'2015-04-07'
     * @param string $where_str     ����
     * @param string $order_by      ����
     * @param string $limit         ѭ��
     * @param string $fields        ��ѯ�ֶ�
     * @return array|int            ����ֵ
     */
    public function get_list($b_select_count = false,$version_id=0,$user_id = 0, $start_time = '',$end_time = '',$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        //��������
        $version_id = intval($version_id);
        $user_id    = intval($user_id);
        $start_time = trim($start_time);
        $end_time   = trim($end_time);
        $where_str  = trim($where_str);

        //��������sql��
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
     * ��ѯ��ͨ�İ汾����
     *
     * @param string $version_num  //�汾��
     * @return array|int
     */
    public function get_location_by_version_num($version_num = '')
    {
        $version_open_obj = POCO::singleton('pai_version_open_class');

        //��ѯ�汾ID
        $version_num = trim($version_num);
        if(strlen($version_num) <1) return array();
        $version_id = $version_open_obj->get_version_id_by_version_num($version_num);

        //��ѯ����
        $version_id = intval($version_id);
        if($version_id <1) return array();
        $ret = $this->get_list(false,$version_id,0,'','','','english_name ASC','0,99999999','location_id,chinese_name as name');

        if(!is_array($ret)) $ret = array();
        foreach($ret as $key=>$val)
        {
            $ret[$key]['name'] = str_replace('��','',$val['name']);
        }
        return $ret;
    }

}