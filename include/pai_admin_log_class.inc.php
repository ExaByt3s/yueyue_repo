<?php
/**
 * @desc:   ����Ա������¼��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/30
 * @Time:   17:05
 * version: 1.0
 */

class pai_admin_log_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_log_db' );
        $this->setTableName ( 'pai_admin_log_tbl' );
    }

    /**
     * ��ȡlog�б�
     * @param bool $b_select_count  �Ƿ��ѯ����(true��ʾ��ѯ����,false ��ѯ����)
     * @param string $module    ģ����
     * @param $action           ����,ʾ��:insert(��ʾ����),update(����),del(��ʾɾ��)
     * @param $operate_id       ������ID,ʾ��:100000
     * @param string $where_str ��ѯ����,ʾ��:$where_str ="add_time=1438249981";
     * @param string $order_by  ����,ʾ��:'add_time DESC,id DESC'
     * @param string $limit     ��ѯ����,ʾ��:'0,10'�����ѯ10����
     * @param string $fields    ��ѯ�ֶ�,ʾ��:'module,action,add_time'
     * @return array|int       ����ֵ
     */
    public function get_admin_log_list($b_select_count = false,$module,$action,$operate_id,$where_str = '', $order_by = 'add_time DESC,id DESC', $limit = '0,20', $fields = '*')
    {
        $module = trim($module);
        $action = trim($action);
        $operate_id = intval($operate_id);
        if(strlen($module)>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "module=:x_module";
            sqlSetParam($where_str,"x_module",$module);
        }
        if(strlen($action) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "action=:x_action";
            sqlSetParam($where_str,'x_action',$action);
        }
        if($operate_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "operate_id={$operate_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount ( $where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;
    }

    /**
     * �������
     * @param array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_info($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        return $this->insert ( $insert_data );
    }

    /**
     * ���log����
     * @param string $module ģ������
     * @param string $action ����
     * @return bool
     * @throws App_Exception
     */
    public function add_admin_log($module,$action)
    {
        global $yue_login_id;
        global $_INPUT;
        $action_arr = array('insert','update','del');
        if(!in_array($action,$action_arr)) return false; //������ӵ���Ϣ

        $operate_id = intval($yue_login_id);
        if($operate_id <1) return false;
        $module = trim($module);
        $action = trim($action);
        $data['module'] = $module;
        $data['action'] = $action;
        $data['log'] = serialize($_INPUT);
        $data['add_time'] = time();
        $data['operate_id'] = $operate_id;
        $this->add_info($data);
        return true;
    }

}