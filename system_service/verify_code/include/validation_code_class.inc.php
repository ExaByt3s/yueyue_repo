<?php

/**
 * 验证操作类
 * @author 星星
 * @copyright 2015-10-20
 */
class validation_code_class extends POCO_TDG
{
    /**
     * 构造一个hash值
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
     * 计算等级的值
     * @return int 0不需要验证，1验证hash，2图形验证
     */
    public function get_level()
    {
        # 判断是不是微信访问
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
     * 判断是否可以再次获取验证码
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
        //根据等级值做乡音的返回
        switch ($level) {
            case 0:
                $rst = true;
                break;
            case 1: //hash验证
                $hash = $this->get_hash();
                if ($hash == $code) {
                    $rst = true;
                }
                break;
            case 2: //图形验证

                break;
            default:
                $rst = false;
                break;
        }
        return $rst;
    }
}
