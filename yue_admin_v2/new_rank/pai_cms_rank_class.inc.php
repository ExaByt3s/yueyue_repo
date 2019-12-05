<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/9/1
 * Time: 15:20
 * �°񵥿�����
 */

class pai_cms_rank_class
{
    /**
     *
     */
    public function pai_cms_rank_class(){}

    /**
     * ������������
     * @param int $location_id ����ID
     * @param string $page_type  ҳ������
     * @param string $module_type   ģ������
     * @param int $type_id   ����ID
     * @param int $versions_id
     * @param string $title       ����
     * @param int $order   ����
     * @param string $link ����
     * @param string $img_url ͼƬ��ַ
     * @param string $remark  ��ע
     * @param string $switch  ����
     * @param array $option  ��չ���� $option = array('rank_id'=>1);
     * @return int|string
     */
    public function create_main_rank($location_id, $page_type, $module_type,$type_id,$versions_id=0, $title, $order=0, $link='', $img_url='', $remark='',$switch ='on',$option = array())
    {
        global $yue_login_id;
        $return_data = array('code'=>0);//����ֵ
        $array_page_type = array('index', 'category_index', 'list');//λ��
        $array_module_type = array('type_1', 'type_2', 'type_3','type_4');//ģ��
        $location_id = (int)$location_id;
        $page_type = trim($page_type);
        $module_type = trim($module_type);
        $type_id = intval($type_id);
        $versions_id = intval($versions_id);
        $title = trim($title);
        $order = intval($order);
        $link = trim($link);
        $img_url = trim($img_url);
        $remark = trim($remark);
        $switch = trim($switch);
        $option = (array)$option;

        if($location_id <1) return $return_data['err'] = '����Ϊ��';
        if(!in_array($page_type, $array_page_type))   return $return_data['err'] = 'ҳ�����ʹ���';
        if(!in_array($module_type, $array_module_type)) return $return_data['err'] = 'ҳ�����ʹ���';
        if(empty($title)) return $return_data['err'] = '���ⲻ��Ϊ��';
        $insert_array['location_id'] = $location_id;
        $insert_array['page_type']   = $page_type;
        $insert_array['module_type'] = $module_type;
        $insert_array['type_id'] = $type_id;
        $insert_array['versions_id'] = $versions_id;
        $insert_array['title']       = $title;
        $insert_array['order']       = $order;
        $insert_array['link']        = $link;
        $insert_array['img_url']     = $img_url;
        $insert_array['remark']  = $remark;
        $insert_array['switch']  = $switch;
        $insert_array['add_user_id'] = $yue_login_id;
        $insert_array['add_time'] = time();

        $table_name = 'pai_cms_db.pai_rank_main_tbl';
        $retId = $this->insert_sql($table_name, $insert_array);

        $return_data['code'] = $retId;
        return $return_data;
    }

    /**
     * ͨ���汾ID��ȡ�汾��
     * @param int $versions_id
     * @return bool
     */
    public function get_versions_name_by_id($versions_id)
    {
        global $versions_list;
        $versions_id = intval($versions_id);
        foreach($versions_list as $val)
        {
            if($val['versions_id'] == $versions_id) return $val['name'];
        }
        return false;
    }

    /**
     * ͨ������id��ȡ��������
     * @param int $id
     * @return array|int
     */
    public function get_main_info_by_id($id)
    {
        $id = intval($id);
        if($id <1) return array();
        $sql_str = "SELECT * FROM pai_cms_db.pai_rank_main_tbl WHERE id='{$id}' limit 0,1";
        return db_simple_getdata($sql_str, TRUE, 101);
    }

    /**
     * ��ȡ���������б�
     * @param bool $b_select_count true|false
     * @param string $page_type  λ��
     * @param int $type_id ����ID
     * @param int $versions_id   �汾ID
     * @param string $switch     ����
     * @param int $add_user_id  ����û�ID
     * @param string $where_str ����
     * @param string $order_by  ����
     * @param string $limit    ѭ������
     * @param string $fields   �ֶ�
     * @return array
     */
    public function get_main_rank_list($b_select_count = false,$page_type,$type_id,$versions_id,$switch,$add_user_id,$where_str = '',$order_by = 'id DESC', $limit = '0,30', $fields = '*')
    {
        $page_type = trim($page_type);
        $type_id = intval($type_id);
        $versions_id = intval($versions_id);
        $switch = trim($switch);
        $add_user_id = trim($add_user_id);
        $where_str = trim($where_str);
        if(strlen($page_type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "page_type=:x_page_type";
            sqlSetParam($where_str,"x_page_type",$page_type);
        }
        if($type_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "type_id = {$type_id}";
        }
        if($versions_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "versions_id = {$versions_id}";
        }
        if(strlen($switch)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "switch=:x_switch";
            sqlSetParam($where_str,"x_switch",$switch);
        }
        if($add_user_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "add_user_id={$add_user_id}";
        }
        $table_name = 'pai_cms_db.pai_rank_main_tbl';
        $whereby = $where_str != '' ? "WHERE {$where_str}" : '';
        if($b_select_count == TRUE)//��ȡ����
        {
            $sql_str = "SELECT count($fields) AS c FROM {$table_name} {$whereby} LIMIT 0,1";
            $ret = db_simple_getdata($sql_str, true, 101);
            return intval($ret['c']);
        }
        // ��������
        $sortby = $order_by != '' ? " ORDER BY {$order_by}" : '';
        // ���� $limit
        if (!empty($limit) && preg_match("/^\d+,\d+$/i", $limit)) {
            list($length, $offset) = explode(',', $limit);
        }
        else {
            $length = 0;
            $offset = 1000;
        }
        $sql_str = "SELECT {$fields} FROM {$table_name} {$whereby} {$sortby}";
        if (null !== $length || null !== $offset)
        {
            $sql_str = sprintf('%s LIMIT %d,%d', $sql_str, $length, (int)$offset);
        }
        $ret = db_simple_getdata($sql_str, false, 101);
        return $ret;
    }
    /**
     * ��ȡ����������
     * @param $select_array
     * @param string $field
     * @param string $limit
     * @return array
     */
    function get_main_rank($select_array, $field = '*', $limit = '0,20')
    {
        if(is_array($select_array))
        {
            $select_str = '';
            $sql_str = "SELECT " . $field . " FROM pai_cms_db.pai_rank_main_tbl  WHERE 1=1 ";
            foreach($select_array AS $key=>$val)
            {
                $select_str .= " AND `" . $key . "`='" . $val . "'";
            }
            $sql_str .= $select_str . ' LIMIT ' . $limit;

            return db_simple_getdata($sql_str, FALSE, 101);
        }

    }

    /**
     * ͨ����ID������������
     * @param int $id ����ID
     * @param int $location_id ����ID
     * @param string $page_type  ҳ������
     * @param string $module_type   ģ������
     * @param int $type_id   ����ID
     * @param int $versions_id
     * @param string $title       ����
     * @param int $order   ����
     * @param string $link ����
     * @param string $img_url ͼƬ��ַ
     * @param string $remark  ��ע
     * @param string $switch  ����
     * @param array $option  ��չ���� $option = array('rank_id'=>1);
     * @return int|string
     */
    public function update_main_rank_info($id,$location_id, $page_type, $module_type,$type_id=0,$versions_id=0, $title, $order=0, $link='', $img_url='', $remark='',$switch ='on',$option = array())
    {
        $return_data = array('code'=>0);//����ֵ
        $array_page_type = array('index', 'category_index', 'list');//λ��
        $array_module_type = array('type_1', 'type_2', 'type_3','type_4');//ģ��
        $id = (int)$id;
        $location_id = (int)$location_id;
        $page_type = trim($page_type);
        $module_type = trim($module_type);
        $type_id = (int)$type_id;
        $versions_id = (int)$versions_id;
        $title = trim($title);
        $order = (int)$order;
        $link = trim($link);
        $img_url = trim($img_url);
        $remark = trim($remark);
        $switch = trim($switch);
        $option = (array)$option;
        if($id <1) return $return_data['err'] = 'IDΪ��';
        if($location_id <1) return $return_data['err'] = '����Ϊ��';
        if(!in_array($page_type, $array_page_type))   return $return_data['err'] = 'ҳ�����ʹ���';
        if(!in_array($module_type, $array_module_type)) return $return_data['err'] = 'ҳ�����ʹ���';
        if($page_type == 'list' && $type_id <1)  return $return_data['err'] = '����ID����';
        if(empty($title)) return $return_data['err'] = '���ⲻ��Ϊ��';
        $update_array['location_id'] = $location_id;
        $update_array['page_type']   = $page_type;
        $update_array['module_type'] = $module_type;
        $update_array['type_id'] = $type_id;
        $update_array['versions_id'] = $versions_id;
        $update_array['title']       = $title;
        $update_array['order']       = $order;
        $update_array['link']        = $link;
        $update_array['img_url']     = $img_url;
        $update_array['remark']      = $remark;
        $update_array['switch']      = $switch;
        $this->update_main_rank($id, $update_array);
        $return_data['code'] = 1;
        return $return_data;
    }


    /**
     * ��������������
     * @param $id
     * @param $update_data
     */
    private function update_main_rank($id, $update_data)
    {
        if($id && is_array($update_data))
        {
            $update_str = '';
            $sql_str = "UPDATE pai_cms_db.pai_rank_main_tbl SET ";
            foreach($update_data AS $key=>$val)
            {
                $update_str .= "`" . $key ."`='" . $val ."',";
            }
            $update_str = trim($update_str, ',');
            $sql_str .=  $update_str . " WHERE id=$id";
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }

    /**
     * ͨ��ģ���ʾ��ȡģ����
     * @param string $module_type
     * @return bool
     */
    public function get_module_name_by_type($module_type)
    {
        $module_type = trim($module_type);
        if(strlen($module_type) <1)return false;
        $module_list = include('/disk/data/htdocs232/poco/pai/yue_admin_v2/new_rank/module_onfig.inc.php');
        foreach($module_list as $v)
        {
           if($v['module_type'] == $module_type) return $v['name'];
        }
    }

    /**
     * ɾ������������
     * @param int $id
     */
    function del_main_rank($id)
    {
        if($id)
        {
            $sql_str = "DELETE FROM pai_cms_db.pai_rank_main_tbl WHERE id=$id";
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }


   ###############################################���񵥲������������#################################################


    /**
     * ��ȡrank_info ����
     * @param bool $b_select_count TRUE|FALSE
     * @param int $main_id ��ID
     * @param string $type ����
     * @param string $switch on|off
     * @param int $add_user_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array
     */
    public function get_rank_info_list($b_select_count = false,$main_id,$type ='',$switch ='on',$add_user_id = 0,$where_str = '',$order_by = 'id DESC', $limit = '0,30', $fields = '*')
    {
        $main_id = intval($main_id);
        $type = trim($type);
        $switch = trim($switch);
        $add_user_id = intval($add_user_id);
        if($main_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "main_id={$main_id}";
        }
        if(strlen($type) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,"x_type",$type);
        }
        if(strlen($switch)>0)
        {
            if(strlen($where_str)>0)$where_str .= ' AND ';
            $where_str .="switch=:x_switch";
            sqlSetParam($where_str,"x_switch",$switch);
        }
        if($add_user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "add_user_id={$add_user_id}";
        }
        $table_name = 'pai_cms_db.pai_rank_info_tbl';
        $whereby = $where_str != '' ? "WHERE {$where_str}" : '';
        if($b_select_count == TRUE)//��ȡ����
        {
            $sql_str = "SELECT count($fields) AS c FROM {$table_name} {$whereby} LIMIT 0,1";
            $ret = db_simple_getdata($sql_str, true, 101);
            return intval($ret['c']);
        }
        // ��������
        $sortby = $order_by != '' ? " ORDER BY {$order_by}" : '';
        // ���� $limit
        if (!empty($limit) && preg_match("/^\d+,\d+$/i", $limit)) {
            list($length, $offset) = explode(',', $limit);
        }
        else {
            $length = 0;
            $offset = 1000;
        }
        $sql_str = "SELECT {$fields} FROM {$table_name} {$whereby} {$sortby}";
        if (null !== $length || null !== $offset)
        {
            $sql_str = sprintf('%s LIMIT %d,%d', $sql_str, $length, (int)$offset);
        }
        $ret = db_simple_getdata($sql_str, false, 101);
        return $ret;
    }

    /**
     * ͨ��rank��id��ȡ��������
     * @param int $id
     * @return array|int
     */
    public function get_rank_info_by_id($id)
    {
        $id = intval($id);
        if($id <1) return array();
        $sql_str = "SELECT * FROM pai_cms_db.pai_rank_info_tbl WHERE id='{$id}' limit 0,1";
        return db_simple_getdata($sql_str, TRUE, 101);
    }

    /**
     * ͨ��rank��id��ȡ��������
     * @param int $main_id
     * @return array|int
     */
    public function get_rank_info_by_main_id($main_id)
    {
        $main_id = intval($main_id);
        if($main_id <1) return array();
        $sql_str = "SELECT * FROM pai_cms_db.pai_rank_info_tbl WHERE main_id='{$main_id}' limit 0,1";
        return db_simple_getdata($sql_str, TRUE, 101);
    }

    /**
     * ����ΰ�����
     * @param int $main_id
     * @param string $type
     * @param int $rank_type
     * @param int $rank_id
     * @param string $cms_type
     * @param int $pid
     * @param string $title
     * @param string $content
     * @param string $img_url
     * @param string $link_url
     * @param string $remark
     * @param int  $order
     * @param string $switch
     * @param array $option ��չ���飬ʾ���� $option = array('main_id'=>1);
     * @return string
     */
    function create_info_rank($main_id, $type,$rank_type =0,$rank_id =0,$cms_type='',$pid, $title, $content = '', $img_url = '', $link_url = '', $remark = '', $order = 0, $switch = 'on',$option = array())
    {
        global $yue_login_id;
        $return_data = array('code'=>0);
        $main_id = (int)$main_id;
        $type = trim($type);
        $rank_type = intval($rank_type);
        $rank_id = intval($rank_id);
        $cms_type = trim($cms_type);
        $pid = intval($pid);
        $title = trim($title);
        $content = trim($content);
        $img_url = trim($img_url);
        $link_url = trim($link_url);
        $remark = trim($remark);
        $option = (array)$option;
        if(empty($main_id)) return $return_data['err'] = '��ID����Ϊ��';

        $type_array = array('','general','banner');
        if(!in_array($type, $type_array)) return $return_data['err'] = '����Ϊ��';

        if(empty($title)) return $return_data['err'] = '���ⲻ��Ϊ��';

        $insert_data['main_id']     = $main_id;
        $insert_data['type']        = $type;
        $insert_data['rank_type'] = $rank_type;
        $insert_data['rank_id']  = $rank_id;
        $insert_data['cms_type'] = $cms_type;
        $insert_data['pid']   = $pid;
        $insert_data['title'] = $title;
        $insert_data['content'] = $content;
        $insert_data['img_url'] = $img_url;
        $insert_data['link_url'] = $link_url;
        $insert_data['remark'] = $remark;
        $insert_data['order']  = $order;
        $insert_data['switch'] = $switch;
        $insert_data['add_user_id'] = $yue_login_id;
        $insert_data['add_time'] = time();
        $insert_data['update_time'] = time();
        $table_name = 'pai_cms_db.pai_rank_info_tbl';
        $this->insert_sql($table_name, $insert_data);
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * ���°���������
     * @param int $id ����
     * @param int $main_id
     * @param string $type
     * @param int $rank_type
     * @param int $rank_id
     * @param string $cms_type
     * @param int $pid
     * @param string $title
     * @param string $content
     * @param string $img_url
     * @param string $link_url
     * @param string $remark
     * @param int  $order
     * @param string $switch
     * @param array $option ��չ���飬ʾ���� $option = array('main_id'=>1);
     * @return string
     */
    function update_info_rank_info($id,$main_id, $type,$rank_type =0,$rank_id =0,$cms_type='',$pid, $title, $content, $img_url, $link_url, $remark,$order,$switch,$option = array())
    {
        $return_data = array('code'=>0);
        $id = (int)$id;
        $main_id = (int)$main_id;
        $type = trim($type);
        $rank_type = intval($rank_type);
        $rank_id = intval($rank_id);
        $cms_type = trim($cms_type);
        $pid = intval($pid);
        $title = trim($title);
        $content = trim($content);
        $img_url = trim($img_url);
        $link_url = trim($link_url);
        $remark = trim($remark);
        $order = trim($order);
        $switch = trim($switch);
        $option = (array)$option;
        if($id <1) return $return_data['err'] = 'ID����Ϊ��';
        if(empty($main_id)) return $return_data['err'] = '��ID����Ϊ��';
        $type_array = array('','general','banner');
        if(!in_array($type, $type_array)) return $return_data['err'] = '����Ϊ��';
        if(empty($title)) return $return_data['err'] = '���ⲻ��Ϊ��';

        $update_array['main_id'] = $main_id;
        $update_array['type'] = $type;
        $update_array['rank_type'] = $rank_type;
        $update_array['rank_id']  = $rank_id;
        $update_array['cms_type'] = $cms_type;
        $update_array['pid'] = $pid;
        $update_array['title'] = $title;
        $update_array['content'] = $content;
        $update_array['img_url'] = $img_url;
        $update_array['link_url'] = $link_url;
        $update_array['remark'] = $remark;
        $update_array['order'] = $order;
        $update_array['switch'] = $switch;
        $update_array['update_time'] = time();
        $this->update_info_rank($id, $update_array);
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * ������������
     * @param int $id
     * @param array $update_array
     */
    private function update_info_rank($id, $update_array)
    {
        if($id && is_array($update_array))
        {
            $update_str = '';
            $sql_str = "UPDATE pai_cms_db.pai_rank_info_tbl SET ";
            foreach($update_array AS $key=>$val)
            {
                $update_str .= "`" . $key ."`='" . $val ."',";
            }
            $update_str = trim($update_str, ',');
            $sql_str .=  $update_str . " WHERE id=$id";
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }

    /**
     * @param $select_array
     * @param string $field
     * @param string $limit
     * @return array
     */
    function get_info_rank($select_array, $field = '*', $limit = '0,20')
    {
        if(is_array($select_array))
        {
            $select_str = '';
            $sql_str = "SELECT " . $field . " FROM pai_cms_db.pai_rank_info_tbl  WHERE 1=1 ";
            foreach($select_array AS $key=>$val)
            {
                $select_str .= " AND `" . $key . "`='" . $val . "'";
            }
            $sql_str .= $select_str . ' LIMIT ' . $limit;

            return db_simple_getdata($sql_str, FALSE, 101);
        }

    }

    /**
     * ɾ������������
     * @param int $id
     * @return bool
     */
    function del_info_rank($id)
    {
        $id = (int)$id;
        if($id)
        {
            $sql_str = "DELETE FROM pai_cms_db.pai_rank_info_tbl WHERE id=$id";
            db_simple_getdata($sql_str, TRUE, 101);
            return true;
        }
        return false;
    }


    #######################ȫ�񵥹��õĺ���#################################
    /**
     * ɾ������������
     * @return bool|int
     */
    public function dell_new_rank()
    {
        $sql_str = "DELETE FROM pai_cms_db.pai_rank_info_tbl";
        $sql_str2 = "DELETE FROM pai_cms_db.pai_rank_main_tbl";
        db_simple_getdata($sql_str, TRUE, 101);
        db_simple_getdata($sql_str2, TRUE, 101);
    }

    /**
     * @param $table
     * @param $insert_array
     * @param string $insert_type
     * @return mixed
     */
    function insert_sql($table, $insert_array,$insert_type='')
    {
        if ($insert_type=="REPLACE")
        {
            $sql_str = "REPLACE INTO " . $table;
        }
        elseif ($insert_type=="IGNORE")
        {
            $sql_str = "INSERT IGNORE INTO " . $table;
        }
        else
        {
            $sql_str    = "INSERT INTO " . $table;
        }
        $key_str    = '';
        $value_str  = '';
        foreach($insert_array AS $key=>$val)
        {
            $key_str    .= "`" . $key . "`,";
            $value_str  .= "'" . $val . "',";
        }
        $key_str    = trim($key_str, ",");
        $value_str  = trim($value_str, ",");

        $sql_str  .= '(' . $key_str . ') VALUES (' . $value_str . ')';
        db_simple_getdata($sql_str, TRUE, 101);
        return db_simple_get_insert_id();
    }

    #################################log�������#############################################
    /**
     * ��Ӱ����ݺ�log
     * @param int $main_id ������
     * @param int $rank_id ������
     * @param string $action     �񵥲���
     * @return array
     * @throws App_Exception     �����쳣
     */
    public function add_info_and_log($main_id,$rank_id,$action='insert')
    {
        global $yue_login_id;
        $new_insert_arr = array('insert','update','del','restore');
        $action = trim($action);
        $main_id = intval($main_id);
        $rank_id = intval($rank_id);
        if(!in_array($action,$new_insert_arr)) return false;
        $main_data = $this->get_main_rank_list(false,'',0,0,'',0,'','id DESC','0,99999999','*');
        $rank_info_data = $this->get_rank_info_list(false,0,'','',0,'','id DESC','0,99999999');
        $insert_data['main_data_log'] = serialize($main_data);
        $insert_data['rank_info_data_log'] = serialize($rank_info_data);
        $insert_data['act'] = $action;
        $insert_data['main_id'] = $main_id;
        $insert_data['rank_info_id'] = $rank_id;
        $insert_data['audit_time'] = time();
        $insert_data['audit_id'] = $yue_login_id;
        $table_name = 'pai_log_db.pai_rank_event_v3_log';
        $this->insert_sql($table_name, $insert_data);
        return true;
    }

    /**
     * @param $id
     * @return array|bool
     */
    public function get_rank_cms_log_info($id)
    {
        $id = intval($id);
        if($id <1) return false;
        $sql_str = "SELECT * FROM pai_log_db.pai_rank_event_v3_log WHERE id={$id}";
        $ret = db_simple_getdata($sql_str, true, 101);
        return $ret;
    }

    /**
     * ��ѯlog����
     * @param bool $b_select_count
     * @param $audit_id
     * @param string $action
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array
     */
    public  function get_rank_cms_log_list($b_select_count = false,$audit_id,$action='',$where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $audit_id = intval($audit_id);
        $action = trim($action);
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id = {$audit_id}";
        }
        if(strlen($action)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "act =:x_action";
            sqlSetParam($where_str,"x_action",$action);
        }
        $table_name = 'pai_log_db.pai_rank_event_v3_log';
        $whereby = $where_str != '' ? "WHERE {$where_str}" : '';
        if($b_select_count == TRUE)//��ȡ����
        {
            $sql_str = "SELECT count($fields) AS c FROM {$table_name} {$whereby} LIMIT 0,1";
            $ret = db_simple_getdata($sql_str, true, 101);
            return intval($ret['c']);
        }
        // ��������
        $sortby = $order_by != '' ? " ORDER BY {$order_by}" : '';
        // ���� $limit
        if (!empty($limit) && preg_match("/^\d+,\d+$/i", $limit)) {
            list($length, $offset) = explode(',', $limit);
        }
        else {
            $length = 0;
            $offset = 1000;
        }
        $sql_str = "SELECT {$fields} FROM {$table_name} {$whereby} {$sortby}";
        if (null !== $length || null !== $offset)
        {
            $sql_str = sprintf('%s LIMIT %d,%d', $sql_str, $length, (int)$offset);
        }
        $ret = db_simple_getdata($sql_str, false, 101);
        return $ret;
    }

    /**
     * �ظ�����
     * @param int $id
     * @return array
     */
    public function restore_new_rank_event_by_id($id)
    {
        $return_data = array('code'=>0);
        $id = intval($id);
        if($id <1) return $return_data['err'] = 'ID����Ϊ��';
        $this->dell_new_rank();
        $ret = $this->get_rank_cms_log_info($id);
        if(is_array($ret) && !empty($ret))
        {
            $main_list = unserialize($ret['main_data_log']);
            if(!is_array($main_list)) $main_list = array();
            $table_name = 'pai_cms_db.pai_rank_main_tbl';
            foreach($main_list as $val)
            {
                $this->insert_sql($table_name, $val);
            }
            unset($main_list);
            $rank_info_list = unserialize($ret['rank_info_data_log']);
            if(!is_array($rank_info_list)) $rank_info_list = array();
            $table_name2 = 'pai_cms_db.pai_rank_info_tbl';
            foreach($rank_info_list as $vo)
            {
                $this->insert_sql($table_name2, $vo);
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    ############################################����¼���Ʒ����###########################################

    /**
     * ��Ҫ�¼ܵĲ�Ʒ�����̼�
     * @param array $data
     * @return bool
     */
    public function add_out_shelf_info($data)
    {
        if(!is_array($data)) return false;
        $table_name = "pai_cms_db.pai_rank_out_shelf_tbl";
        foreach($data as $rank_info)
        {
            $rank_info['add_time'] = date('Y-m-d H:i:s',time());
            $this->insert_sql($table_name, $rank_info,"REPLACE");
        }
        return true;
    }

    /**
     * �����¼ܱ�����
     * @param int $id
     * @param array $update_array
     *  @return bool
     */
    public function update_out_shelf($id, $update_array)
    {
        global $yue_login_id;
        if($id && is_array($update_array))
        {
            $update_array['audit_id'] = $yue_login_id;
            $update_array['audit_time'] = date('Y-m-d H:i:s',time());
            $update_str = '';
            $sql_str = "UPDATE pai_cms_db.pai_rank_out_shelf_tbl SET ";
            foreach($update_array AS $key=>$val)
            {
                $update_str .= "`" . $key ."`='" . $val ."',";
            }
            $update_str = trim($update_str, ',');
            $sql_str .=  $update_str . " WHERE id=$id";
            db_simple_getdata($sql_str, TRUE, 101);
            return true;
        }
    }

    /**
     * ��ѯ��Ҫ�¼ܵ�����
     * @param bool $b_select_count
     * @param int $status
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array
     */
    public  function get_rank_out_shelf_list($b_select_count = false,$status = -1,$where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $table_name = 'pai_cms_db.pai_rank_out_shelf_tbl';
        $status = intval($status);
        if($status >=0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "status={$status}";
        }
        $whereby = $where_str != '' ? "WHERE {$where_str}" : '';
        if($b_select_count == TRUE)//��ȡ����
        {
            $sql_str = "SELECT count($fields) AS c FROM {$table_name} {$whereby} LIMIT 0,1";
            $ret = db_simple_getdata($sql_str, true, 101);
            return intval($ret['c']);
        }
        // ��������
        $sortby = $order_by != '' ? " ORDER BY {$order_by}" : '';
        // ���� $limit
        if (!empty($limit) && preg_match("/^\d+,\d+$/i", $limit)) {
            list($length, $offset) = explode(',', $limit);
        }
        else {
            $length = 0;
            $offset = 1000;
        }
        $sql_str = "SELECT {$fields} FROM {$table_name} {$whereby} {$sortby}";
        if (null !== $length || null !== $offset)
        {
            $sql_str = sprintf('%s LIMIT %d,%d', $sql_str, $length, (int)$offset);
        }
        $ret = db_simple_getdata($sql_str, false, 101);
        return $ret;
    }


    /**
     * ���˿ո�
     * @param string $str
     * @return string
     */
    public function trimall($str)//ɾ���ո�
    {
        $qian=array(" ","��","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }
    ############################################�ӿ�����###########################################
}


