<?php
/**
 * @desc:   paĿ¼�µ��ƣ�����ע���ࡾ��Ҫ�����ƹ��ж����û�ͨ�����ƽ���ע�᡿
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/19
 * @Time:   11:17
 * version: 1.0
 */
class pai_pa_dt_register_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
        $this->setTableName( 'pai_pa_dt_register_log' );
    }

    /**
     * ������ݽ�log��
     * @param array $insert_data
     * @return bool|int
     */
    private function add_pa_register_info($insert_data)
    {
        if (empty ($insert_data) || !is_array($insert_data)) return false;//�����log����ֱ��ʹ��false
        return $this->insert ( $insert_data,'IGNORE' );
    }

    /**
     * ͨ��ID���log����
     * @param int $user_id  ˵��:ע��ID
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
	 * ��ȡ �б�
	 * @param bool $b_select_count
	 * @param string $puid �ƹ���
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit
	 * @param string $fields ��ѯ�ֶ�
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