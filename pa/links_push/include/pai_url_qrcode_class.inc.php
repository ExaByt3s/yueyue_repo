<?php
/**
 * @desc:   �ƹ���������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/17
 * @Time:   16:27
 * version: 1.0
 */
class pai_url_qrcode_class extends POCO_TDG
{

    /**
     * @var string
     */
    private $domain_name = 'http://pa.yueus.com';
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_admin_db' );
        $this->setTableName( 'pai_pa_dt_url_qrcode_tbl' );
    }

    /**
     * ��������
     * @param $url
     * @param string $remark Ϊ˭����
     * @return bool
     */
    public function create_qrcode_img($url,$remark)
    {
        $return_data = array();
        $url = trim($url);
        $remark = trim($remark);
        if(strlen($url) <1 || !filter_var($url, FILTER_VALIDATE_URL)) return false;
        //����ʼ
        POCO_TRAN::begin($this->getServerId());
        $ret = $this->add_info_index($url,$remark);
        $retID = (int)$ret['code'];
        if($retID<1)
        {
            //����ع�
            POCO_TRAN::rollback($this->getServerId());
            return false;
        }//����ʧ��
        $puid = $this->getStr(10)."{$retID}";//��ȡ�ƹ�ID
        $curl = "{$this->domain_name}?url=".urlencode($url)."&puid={$puid}";
        $img_url = pai_activity_code_class::get_qrcode_img($curl);
        if(strlen($img_url)>0)
        {
            POCO_TRAN::commmit($this->getServerId()); //�����ύ
            $this->update_info($retID,array('qrcode_img_url'=>$img_url,'curl'=>$curl,'puid'=>$puid));//��������
            $return_data['curl'] = $curl;
            $return_data['url'] = $url;
            $return_data['img_url'] = $img_url;
            $return_data['remark'] = $remark;
            return $return_data;
        }
        //����ع�
        POCO_TRAN::rollback($this->getServerId());
        return false;
    }

    /**
     * ��ӵ�����
     * @param array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_info($insert_data)
    {
        if (empty($insert_data) || !is_array($insert_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
        }
        return $this->insert($insert_data);
    }

    /**
     * @param $id
     * @param $update_data
     * @return mixed
     * @throws App_Exception
     */
    public function update_info($id,$update_data)
    {
        $id = (int)$id;
        if($id <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
        }
        if (empty($update_data) || !is_array($update_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
        }
        return $this->update($update_data,"id={$id}");
    }

    /**
     * ͨ��IDɾ������
     * @param int $id
     * @return bool
     * @throws App_Exception
     */
    public function del_info_by_id($id)
    {
        $id = (int)$id;
        if($id <1) return false;
        return $this->delete("id={$id}");
    }

    /**
     * �������ݲ��Ұ����ݲ��뵽���ݱ���
     * @param string $url
     * @param string $remark
     * @return array
     * @throws App_Exception
     */
    public function add_info_index($url,$remark)
    {
        global $yue_login_id;
        $return_data = array();
        $url = trim($url);
        $remark = trim($remark);
        if(strlen($url) <1) return $return_data['err'] = '����Ϊ��';
        $data = array();
        $data['url'] = $url;
        $data['remark'] = $remark;
        $data['user_id'] = $yue_login_id;
        $data['add_time'] = time();
        $retID = $this->add_info($data);
        if($retID >0)
        {
            $return_data['code'] = $retID;
            return $return_data;
        }
        return $return_data;
    }

    /**
     * ��ȡ�б�
     * @param bool $b_select_count
     * @param $user_id
     * @param $url
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_url_qrcode_list($b_select_count = false,$user_id,$url,$where_str = '', $order_by = 'add_time DESC,id DESC', $limit = '0,10', $fields = '*')
    {
        $user_id = (int)$user_id;
        $url = trim($url);
        if($user_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id={$user_id}";
        }
        if(strlen($url)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "url=:x_url";
            sqlSetParam($where_str,"x_url",$url);
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * @param int $len
     * @return string
     */
    private function getStr($len =8)
    {
        $arr = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z"
        );
        $charslen = count($arr) - 1;
        $outputstr = "";
        for ($i = 0 ; $i< $len; $i++)
        {
            $outputstr .= $arr[mt_rand(0, $charslen)];
        }
        return $outputstr;
    }
}