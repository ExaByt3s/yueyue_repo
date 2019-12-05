<?php

/**
 * ��֤������
 * @author ����
 * @copyright 2015-10-20
 */
class validation_code_class extends POCO_TDG
{
    /**
     * ����һ��hashֵ
     * @return string
     */
    public function get_hash()
    {
        global $_INPUT;
        //$str = $_COOKIE['yue_session_id'] . '_' . $_INPUT['IP_ADDRESS'] . '_' . date('Y-m-d 00:00:00');
        $str = date('Y-m-d 00:00:00');
        return md5($str);
    }

    /**
     * ����ȼ���ֵ
     * @return int 0����Ҫ��֤��1��֤hash��2ͼ����֤
     */
    public function get_level()
    {
        # �ж��ǲ���΢�ŷ���
       /* if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'yue_pai') !== false) {
            return 0;
        }*/
        include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
        $device_arr = mall_get_user_agent_arr();
        if( $device_arr["is_pc"] == 1 )
        {
            return 2;
        }
        return 0;
    }

    /**
     * @var
     * �ж��Ƿ�����ٴλ�ȡ��֤��
     * @return bool
     */
    public function check_code($code)
    {
        $rst = false;
        $code = trim($code);
        if (strlen($code) < 1) {
            return $rst;
        }
        $level = $this->get_level();
        //���ݵȼ�ֵ�������ķ���
        switch ($level) {
            case 0:
                $rst = true;
                break;
            case 1: //hash��֤
                $hash = $this->get_hash();
                if ($hash == $code) {
                    $rst = true;
                }
                break;
            case 2: //ͼ����֤

                break;
            default:
                $rst = false;
                break;
        }
        return $rst;
    }
}
