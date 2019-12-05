<?php
/**
 * @desc:   ����ҳ������ҳ����v2��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/20
 * @Time:   11:54
 * version: 2.0
 */

class pai_rank_event_v2_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_rank_event_v2_tbl' );
        $this->versions_list = include("/disk/data/htdocs232/poco/pai/yue_admin_v2/rank/versions_config.inc.php");//�汾�����ļ�
	}

    /**
     * ���
     * @param array $insert_data
     * @return int  true|false
     * @throws App_Exception
     */
    public function add_info($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data );
	
	}

    /**
     * ��Ӱ����ݺ�log
     * @param array $insert_data ������
     * @param string $action     �񵥲���
     * @return array
     * @throws App_Exception     �����쳣
     */
    public function add_info_and_log($insert_data,$action='insert')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;
        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        $data_log = $this->get_serialize_rank_event_list();
        //��Ӱ�
        $status = $this->add_info($insert_data);
        $status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  '�����ʧ��';
            return $ret;
        }
        //���log��������
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $status;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '��Ӱ�logʧ��';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  '��Ӱ񵥺���Ӱ�log�ɹ�';
        $ret['success_log'] = $log_status;
        return $ret;
    }


    /**
     * ��������
     * @param array $data
     * @param int  $id
     * @return boo|int
     * @throws App_Exception
     */
    public function update_info($data, $id)
	{
		if (!is_array($data) || empty($data))
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		$id = (int)$id;
		if ($id <1)
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}

    /**
     * ���°����ݺ����log
     * @param array    $update_data   ������
     * @param int     $id
     * @param  string $action
     * @return array  ����ֵ
     * @throws App_Exception �����쳣
     */
    public function update_info_and_log($update_data,$id,$action='update')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;

        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        if (!is_array($update_data) || empty($update_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
        }
        $id = intval($id);
        if ($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
        }
        $data_log = $this->get_serialize_rank_event_list();
        $status = $this->update_info($update_data,$id);
        $status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  '�޸İ�ʧ��';
            return $ret;
        }
        //���log��������
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $id;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '��Ӱ�logʧ��';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  '�޸İ񵥺���Ӱ�log�ɹ�';
        $ret['success_log'] = $log_status;
        return $ret;
    }

    /**
     * ��ȡһ������
     * @param $id
     * @return array
     * @throws App_Exception
     */
    public function get_rank_event_info($id)
    {
        $id = (int) $id;
        if ($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
        }
        $ret = $this->find ( "id={$id}" );
        return $ret;
    }


    /**
     * ɾ��
     * @param  int $id
     * @return bool|int
     * @throws App_Exception
     */
    public function delete_info($id)
	{
        $id = (int)$id;
        if ($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
        }
        $where_str = "id = {$id}";
        return $this->delete($where_str);
	}

    /**
     * ɾ�������ݲ��Ҽ�log
     * @param int $id        ��ID
     * @param string $action �񵥲���
     * @return array         ����ֵ
     * @throws App_Exception ����쳣
     */
    public function delete_info_and_log($id,$action='del')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;
        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
        }
        $data_log = $this->get_serialize_rank_event_list();
        $status = $this->delete_info($id);
        $status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  'ɾ����ʧ��';
            return $ret;
        }
        //���log��������
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $id;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '��Ӱ�logʧ��';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  'ɾ���񵥺���Ӱ�log�ɹ�';
        $ret['success_log'] = $log_status;
        return $ret;
    }

    /**
     * ɾ������������
     * @return bool|int
     */
    public function dell_rank_event()
    {
        $where_str = '1';
        return $this->delete($where_str);
    }

    /**
     * �ָ�������
     * @param int    $id      log����Զ���ID
     * @param string $action  �񵥲�������
     * @return array
     */
    public function  restore_rank_event_by_id($id,$action='restore')
    {
        $rank_event_log_v2_obj = POCO::singleton( 'pai_rank_event_log_v2_class' ) ;
        global $yue_login_id;
        $ret = array('status'=>0,'msg'=>'','success_log'=>'');
        $action = trim($action);
        $id = intval($id);
        $data_log = $this->get_serialize_rank_event_list();//��ȡ����event_rank����
        $this->dell_rank_event();//ɾ��������
        /*$status = intval($status);
        if($status <1)
        {
            $ret['status'] = -1;
            $ret['msg'] =  'ɾ�����а�ʧ��';
            return $ret;
        }*/
        $list = $rank_event_log_v2_obj->get_unserialize_rank_event_info($id);
        if(!is_array($list)) $list = array();
        //�ָ�������
        foreach($list as $val)
        {
           $this->add_info($val);
        }
        //���log��������
        $data['act']      = $action;
        $data['data_log'] = $data_log;
        $data['rank_event_id'] = $id;
        $data['audit_id'] = $yue_login_id;
        $data['audit_time'] = time();
        $log_status = $rank_event_log_v2_obj->add_info($data);
        $log_status = intval($log_status);
        if($log_status <1)
        {
            $ret['status'] = -2;
            $ret['msg'] =  '��Ӱ�logʧ��';
            return $ret;
        }
        $ret['status'] = 1;
        $ret['msg'] =  '�ָ��񵥺���Ӱ�log�ɹ�';
        $ret['success_log'] = $log_status;
        return $ret;

    }
	
	/*
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param int $versions_id    �汾ID
	 * @param string $place       λ��index(��ҳ)|list(�б�)
	 * @param int    $location_id ����
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_rank_event_list($b_select_count = false,$versions_id,$place,$location_id,$where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
	{
        $versions_id = intval($versions_id);
		$place = trim($place);
        $location_id = intval($location_id);
        if($versions_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "versions_id={$versions_id}";
        }
        if(strlen($place)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "place=:x_place";
            sqlSetParam($where_str,"x_place",$place);
        }
        if($location_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "location_id={$location_id}";
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

	/*
	 * ��ȡ
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_serialize_rank_event_list($where_str='')
	{
		$total_count  = $this->findCount ( $where_str );
		$where_str = "1";
		$ret = $this->findAll($where_str, "0,{$total_count}", "sort_order DESC,id DESC");
		return serialize($ret);
	}

    /**
     * ��ȡ�汾����
     * @param $versions_id
     * @return mixed
     */
    public function versions_option($versions_id)
    {

        $versions_id = intval($versions_id);
        foreach($this->versions_list as $val)
        {
            if($val['versions_id'] == $versions_id) return $val['name'];
        }
    }

    /**
     * ͨ���汾����ȡ�汾ID
     * @param $version_name   �汾��
     * @return int      �汾ID
     */
    public function  get_version_id($version_name)
    {
        $version_name = trim($version_name);
        if(strlen($version_name) <1) return 0;
        foreach($this->versions_list as $val)
        {
            if($val['name'] == $version_name) return $val['versions_id'];
        }
        return 0;
    }

    /**
     * ��ȡ�б����� APP�ӿ�,��
     * @param $place  λ��  index(��ҳ),list(�б�ҳ)
     * @param int $type_id      ����ID  (��ҳ����Ϊ��,Ĭ��ѡ��Ϊ-1),����ģ����ԼΪ31
     * @param int $location_id  ����ID  (Ĭ��ѡ��Ϊ101029001����)������ݵ�location_id '101029001'
     * @param $version_name     �汾��(����Ϊ��) ���� '3.0.0'
     * @param string $limit     ѭ������,ʾ��:"0,15"
     * @param int $status       ״̬-1ȫ����0�¼ܣ�1Ϊ�ϼ�
     * @return array|int        ����ֵ
     */
    public function get_rank_event_by_location_id($place,$type_id = -1,$location_id = 101029001,$version_name,$limit="0,15",$status=1)
    {
        $arr = array('index','list');
        $place = trim($place);
        $type_id = intval($type_id);
        $location_id = intval($location_id);
        $version_name = trim($version_name);
        $status = intval($status);
        $versions_id = 0;
        $where_str = '';
        if(strlen($place) <1 || !in_array($place,$arr))
        {
            return false;
        }
        if($place == 'list')
        {
           if($type_id >=0)
           {
               if(strlen($where_str) >0) $where_str .= ' AND ';
               $where_str .= "cat_id ={$type_id}";
           }
        }
        if($location_id <1)
        {
            $location_id = 101029001;
        }
        if(strlen($version_name) >0)
        {
            $versions_id = $this->get_version_id($version_name);
        }
        else
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "versions_id !=2";
        }
        if($status >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "status={$status}";
        }
        $list = $this->get_rank_event_list(false,$versions_id,$place,$location_id,$where_str,'sort_order DESC,id DESC',$limit,'cat_id as type_id,type,rank_id,url,cover_url,headtile,subtitle,pid,app_sort,cms_type');
        if(!is_array($list) || empty($list)) return false;
        foreach($list as &$val)
        {
            //if($val['pid'] == 0) $val['pid'] = 1220101;
            $val['cover_url'] = yueyue_resize_act_img_url($val['cover_url']);
            //$val['headtile'] = urlencode(iconv('gbk', 'utf8', $val['headtile']));
            $curl = '';
            if($val['type'] ==1)//�񵥷�ʽ
            {
                 $cms_str = urlencode('yueyue_static_cms_id=' . $val['rank_id'].'&cms_type='.$val['cms_type']);
                 $curl = "yueyue://goto?type=inner_app&pid={$val['pid']}&return_query={$cms_str}&title=".urlencode(iconv('gbk', 'utf8', $val['headtile']));
            }
            elseif($val['type'] == 0)
            {
                $url_arr = parse_url($val['url']);
                $httts = trim($url_arr['scheme']);
                if($httts == 'http' || $httts == 'https')//�Ƿ�Ϊhttp||https
                {
                   $wifi_url = str_replace('yp.yueus.com','yp-wifi.yueus.com',$val['url']);
                   $curl = "yueyue://goto?type=inner_web&url=".urlencode(iconv('gbk', 'utf8',$val['url']))."&wifi_url=".urlencode(iconv('gbk', 'utf8',$wifi_url))."&title=".urlencode(iconv('gbk', 'utf8', $val['headtile']))."&showtitle=2";
                }
                elseif($httts == 'yueyue' || $httts == 'yueseller')
                {
                    $curl = $val['url'];
                }
                else
                {
                    $curl = "yueyue://goto?type=inner_app&pid={$val['pid']}&return_query=".urlencode(iconv('gbk', 'utf8',$val['url']))."&title=" . urlencode(iconv('gbk', 'utf8', $val['headtile']));
                }

            }
            $val['curl'] = $curl;
        }
        return $list;
    }

    /**
     * ��ȡ������
     * @param int $type ����
     * @param $headtile �����
     * @param int $pid      PID
     * @param string $url �Զ�ģʽ����
     * @param int $rank_id ��ģʽ,������
     * @param string $cms_type ��ģʽ,����Ʒ���̼�ģʽ(good,mall)
     * @return string
     */
    public function get_url_by_type($type =0,$headtile,$pid,$url='',$rank_id =0,$cms_type = '')
   {
       $type = intval($type);
       $headtile = trim($headtile);
       $pid = intval($pid);
       $url = trim($url);
       $rank_id = intval($rank_id);
       $cms_type = trim($cms_type);

       $curl = '';
       if($type ==1)//�񵥷�ʽ
       {
           $cms_str = urlencode('yueyue_static_cms_id=' .$rank_id.'&cms_type='.$cms_type);
           $curl = "yueyue://goto?type=inner_app&pid={$pid}&return_query={$cms_str}&title=".urlencode(iconv('gbk', 'utf8', $headtile));
       }
       elseif($type == 0)
       {
           $url_arr = parse_url($url);
           $httts = trim($url_arr['scheme']);
           if($httts == 'http' || $httts == 'https')//�Ƿ�Ϊhttp||https
           {
               $wifi_url = str_replace('yp.yueus.com','yp-wifi.yueus.com',$url);
               $curl = "yueyue://goto?type=inner_web&url=".urlencode(iconv('gbk', 'utf8',$url))."&wifi_url=".urlencode(iconv('gbk', 'utf8',$wifi_url))."&title=".urlencode(iconv('gbk', 'utf8', $headtile))."&showtitle=2";
           }
           elseif($httts == 'yueyue' || $httts == 'yueseller')
           {
               $curl = $url;
           }
           else
           {
               $curl = "yueyue://goto?type=inner_app&pid={$pid}&return_query=".urlencode(iconv('gbk', 'utf8',$url))."&title=" . urlencode(iconv('gbk', 'utf8', $headtile));
           }

       }
       return $curl;
   }
	
}

?>