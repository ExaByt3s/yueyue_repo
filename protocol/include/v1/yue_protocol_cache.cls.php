<?php

//defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * 重写缓存
 *
 * @author willike  <chenwb@yueus.com>
 * @since 2015-9-10
 * @version 1.0 Beta
 */
class yue_protocol_cache {

    /**
     * 判断poco类是否存在 ( 是否引入框架 )
     * 
     * @return boolean
     */
    static private function poco_exists() {
        if (!class_exists('POCO')) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 设置缓存
     * 
     * @param string $key 缓存键值
     * @param string|data $data 缓存内容
     * @param array $setting 附加设置 ( 如: array('life_time' => 120) )
     * @return boolean 
     */
    static public function set_cache($key, $data, $setting = array()) {
        if (!self::poco_exists()) {
            return FALSE;
        }
        if ($setting['life_time'] <= 0) {  // 有效期小于0, 不缓存
            return FALSE;
        }
        return POCO::setCache($key, $data, $setting);
    }

    /**
     * 获取 缓存
     * 
     * @param string $key 缓存键值
     * @return string 
     */
    static public function get_cache($key) {
        if (!self::poco_exists()) {
            return FALSE;
        }
        return POCO::getCache($key);
    }

    /**
     * 删除 缓存
     * 
     * @param string $key 缓存键值
     * @return boolean 
     */
    static public function delete_cache($key) {
        if (!self::poco_exists()) {
            return FALSE;
        }
        return POCO::deleteCache($key);
    }

}
