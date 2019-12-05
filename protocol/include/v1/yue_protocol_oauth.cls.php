<?php

//defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');
// 引入数据库操作类文件
require(dirname(YUE_PROTOCOL_ROOT) . '/core/include/poco_tdg.inc.php');

/**
 * 授权类
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-30
 */
class yue_protocol_oauth extends POCO_TDG {

    /**
     * @var string 授权来源(客户端)名称
     */
    private $_app_name = null;

    /**
     * @var string 错误信息
     */
    private $_errmsg = null;

    /**
     * @var boolean 是否调试
     */
    private $_debug = FALSE;

    /**
     * @var string 授权表
     */
    private $_db_oauth_tbl = 'yue_communication_oauth_tbl';

    /**
     * @var array 表字段
     */
    protected $fields = array(
        'id', // 主键（自增）（int(10)）
        'user_id', // 用户ID（索引）（int(10)）
        'access_token', // 授权Token（char(25)）
        'expire_time', // 过期时间（int(10)）
        'refresh_token', // 更新Token的Token（char(25)）
        'app_id', // 所对应的APP ID（smallint(5)）
        'add_time', // 添加时间（int(10)）
        'update_time', // 更新时间（int(10)）
    );

    /**
     * @var string 表主键
     */
    protected $primary_key = 'id';

    /**
     * 初始化
     * 
     * @param string $app_name 请求
     * @param boolean $debug 是否调试
     */
    public function __construct($app_name = null, $debug = FALSE) {
        $this->_app_name = $app_name;
        $this->_debug = ($debug === TRUE) ? TRUE : FALSE;
        $this->config_db_server();
    }

    /**
     * 配置 数据库 服务器
     * 
     * @return void 
     */
    public function config_db_server() {
        $db_name = conf('DB_NAME', 'config');  // 数据库名称
        $server_id = conf('SERVER_ID', 'config');  // 数据库服务器序列
        $this->setServerId($server_id);
        $this->setDBName($db_name);
        $this->setTableName($this->_db_oauth_tbl);
    }

    /**
     * 获取 错误信息
     * 
     * @return string
     */
    public function get_errmsg() {
        return $this->_errmsg;
    }

    /**
     * 获取缓存Key
     * 
     * @access private
     * @param  int  $user_id  用户ID
     * @param string $app_name 请求客户端名
     * @return string
     */
    private function _get_cache_key($user_id, $app_name = null) {
        $app_id = 0;
        if (!empty($app_name)) {
            $config = conf($app_name, 'access');
            if (!empty($config)) {
                $app_id = $config['app_id'];
            }
        }
        return 'yue_protocol_token_' . $user_id . '_' . $app_id . '_v1';
    }

    /**
     * 生成 授权信息
     *
     * @access public
     * @param int  $user_id   用户ID
     * @param string  $app_name  APP名称
     * @return array
     */
    public function generate_access_info($user_id, $app_name = null) {
        $user_id = intval($user_id);
        $app_name = empty($app_name) ? $this->_app_name : $app_name;
        // 判断参数是否合法
        if ($user_id < 1 || empty($app_name)) {
            $this->_errmsg = 'This param is invalid!';
            return array();
        }
        $app_config = conf($app_name, 'access');  // 获取 token 配置
        if (empty($app_config)) {
            $this->_errmsg = 'This app name is invalid!';
            return array();
        }
        // 创建Token
        $base_token_str = $user_id . date('YmdHis') . protocol_api_get_rand_string(6);
        $access_token = protocol_api_hash_code($base_token_str);  // 生成token
        $refresh_token = protocol_api_hash_code($base_token_str . '_refresh');
        if (empty($access_token) || empty($refresh_token)) {
            $this->_errmsg = 'Generate token failed!';
            return array();
        }
        $cur_time = time();  // 当前时间
        $expire_time = $cur_time + ($app_config['token_expire_time'] * 24 * 3600);  // 授权过期时间
        // 授权数据
        $access_info = array(
            'user_id' => $user_id, // 用户ID
            'app_name' => $app_name, // 客户端名称
            'access_token' => $access_token, // 授权Token
            'expire_time' => $expire_time, // 过期时间（时间戳）
            'token_expire_time' => $app_config['token_expire_time'], // token有效期
            'refresh_token' => $refresh_token, // 刷新用的Token
            'app_id' => $app_config['app_id'], // APP ID
            'add_time' => $cur_time, // 添加时间
            'update_time' => $cur_time, // 更新时间
        );
        return $access_info;
    }

    /**
     * 数据写入 处理
     * 
     * @param array $data
     * @return int 
     */
    private function data_process($data) {
        if (empty($data)) {
            return 0;
        }
        $fileds = $this->fields;
        $new_data = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fileds)) {
                // TODO:: 数据处理
                $new_data[$key] = $value;
            }
        }
        return $new_data;
    }

    /**
     * 获取 token 缓存有效期
     * 
     * @return int 
     */
    private function get_token_cache_expire() {
        $expire = intval(conf('TOKEN_EXPIRE', 'config'));
        if ($expire < 1) {
            return 0;
        }
        return $expire * 24 * 60 * 60;
    }

    /**
     * 创建一个 授权
     *
     * @access public
     * @param int $user_id  用户ID
     * @param string $app_name 授权来源名称
     * @return array
     */
    public function create_access_info($user_id, $app_name = null) {
        $app_name = empty($app_name) ? $this->_app_name : $app_name;
        $user_id = intval($user_id);
        // 判断参数是否合法
        if ($user_id < 1 || empty($app_name)) {
            $this->_errmsg = 'The token param is invalid!';
            return array();
        }
        // 生成授权信息
        $oauth_info = $this->generate_access_info($user_id, $app_name);
        if (empty($oauth_info)) {
            return array();
        }
        // 写入数据库
        $this->insert($this->data_process($oauth_info), 'REPLACE');
        yue_protocol_log::token_log(__FUNCTION__, $oauth_info, $oauth_info['app_name']);
        // 判断是否更新
        if ($this->get_affected_rows() > 0) {
            // 更新成功 缓存 授权信息
            $cache_key = $this->_get_cache_key($user_id, $app_name);
            $life_time = $this->get_token_cache_expire();
            yue_protocol_cache::set_cache($cache_key, $oauth_info, array('life_time' => $life_time));
        }
        $this->_errmsg = 'No more data is available for upate!';
        return $oauth_info;
    }

    /**
     * 根据 refresh_token 刷新用户的授权信息
     * 
     * @access public
     * @param int $user_id  用户ID
     * @param string $refresh_token  刷新Token
     * @return array
     */
    public function update_by_refresh_token($user_id, $refresh_token, $app_name = null) {
        $user_id = intval($user_id);
        $refresh_token = trim($refresh_token);
        $app_name = empty($app_name) ? $this->_app_name : $app_name;
        if ($user_id < 1 || empty($refresh_token) || empty($app_name)) {
            $this->_errmsg = 'The update param is invalid!';
            return array();
        }
        // 获取用户的授权
        $oauth_info = $this->get_access_info($user_id, $app_name, FALSE);
        if (empty($oauth_info)) {
            return array();
        }
        // 判断refresh_token是否一致
        if ($oauth_info['refresh_token'] != $refresh_token) {
            $this->_errmsg = 'The refresh token is invalid!';
            return array();
        }
        // 清除掉用户的授权信息 缓存
        $cache_key = $this->_get_cache_key($user_id, $app_name);  // 缓存KEY
        yue_protocol_cache::delete_cache($cache_key);
        // 生成 新的授权信息
        $oauth_info_new = $this->generate_access_info($user_id, $app_name);
        $oauth_info_new['refresh_token'] = $oauth_info['refresh_token']; // refresh_token不变 
        $oauth_info_new['add_time'] = $oauth_info['add_time'];  // 添加时间 不变
        // 更新用户信息
        $affected_rows = $this->update($this->data_process($oauth_info_new), 'id =' . intval($oauth_info['id']));
        // 写日志
        yue_protocol_log::token_log(__FUNCTION__, $oauth_info_new, $oauth_info_new['app_name']);
        if ($affected_rows < 1) {
            $this->_errmsg = 'Failed to update oauth info!';
            return array();
        }
        // 更新缓存中授权
        $life_time = $this->get_token_cache_expire();
        yue_protocol_cache::set_cache($cache_key, $oauth_info_new, array('life_time' => $life_time));
        return $oauth_info_new;
    }

    /**
     * 检测授权是否过期
     *
     * @access public
     * @param int $user_id 用户ID
     * @param int $access_token  授权Token
     * @param string  $app_name  APP名称
     * @return boolean - true 已过期
     */
    public function is_access_expire($user_id, $access_token, $app_name = null) {
        $access_info = $this->get_access_info($user_id, $app_name);  // 获取授权信息
        if (empty($access_info)) {
            return TRUE;
        }
        // 令牌不一致则表示授权过期
        if ($access_info['access_token'] != $access_token) {
            return TRUE;
        }
        // 判断时间是否过期
        if (time() > $access_info['expire_time']) {
            return TRUE;
        }
        return FALSE;  // 令牌没有过期
    }

    /**
     * 获取 用户的授权信息
     *
     * @access public
     * @param int $user_id      用户ID
     * @param string $app_name 授权来源
     * @param boolean  $b_use_cache  是否使用缓存，默认：true
     * @return array
     */
    public function get_access_info($user_id, $app_name = null, $b_use_cache = TRUE) {
        $user_id = intval($user_id);
        if ($user_id < 1) {
            $this->_errmsg = 'Unknown user!';
            return array();
        }
        $app_name = empty($app_name) ? $this->_app_name : $app_name;  // 请求来源
        if (empty($app_name)) {
            $this->_errmsg = 'Undefined app name!';
            return array();
        }
        $config = conf($app_name, 'access');
        if (empty($config)) {
            $this->_errmsg = 'App name invalid!';
            return array();
        }
        $cache_key = $this->_get_cache_key($user_id, $app_name);  // 缓存Key
        // 获取 缓存 授权信息
        $cache_data = $b_use_cache ? yue_protocol_cache::get_cache($cache_key) : array();
        if (!empty($cache_data)) {
            return $cache_data;
        }
        // 获取 数据库 授权信息
        $where_str = 'user_id = ' . $user_id . ' AND app_id = ' . intval($config['app_id']);
        $oauth_info = $this->find($where_str, 'id DESC');  // 获取 数据库中用户授权信息
        if (empty($oauth_info)) {
            $this->_errmsg = 'The oauth info of this user is empty!';
            return array();
        }
        // 授权信息 缓存
        $life_time = $this->get_token_cache_expire();
        yue_protocol_cache::set_cache($cache_key, $oauth_info, array('life_time' => $life_time));
        return $oauth_info;
    }

    /**
     * 清除用户 某个 授权(缓存)信息
     * 
     * @param int $user_id 
     * @return boolean 
     */
    public function clear_an_access_cache($user_id, $app_name) {
        if (empty($user_id) || empty($app_name)) {
            return FALSE;
        }
        $cache_key = $this->_get_cache_key($user_id, $app_name);
        return yue_protocol_cache::delete_cache($cache_key);
    }

    /**
     * 清除用户 所有授权(缓存)信息
     * 
     * @param int $user_id 用户ID
     * @return boolean 
     */
    public function clear_user_access_cache($user_id) {
        $user_id = intval($user_id);
        if ($user_id < 1) {
            return FALSE;
        }
        $access = conf('', 'access');
        foreach ($access as $value) {
            $app_name = $value['app_name'];
            $cache_key = $this->_get_cache_key($user_id, $app_name);
            yue_protocol_cache::delete_cache($cache_key);
        }
        return TRUE;
    }

    /**
     * 获取用户 所有授权(缓存)信息
     * 
     * @param int $user_id 用户ID
     * @return array 
     */
    public function get_user_access_cache($user_id) {
        $user_id = intval($user_id);
        if ($user_id < 1) {
            return FALSE;
        }
        $access = conf('', 'access');
        $access_list = array();
        foreach ($access as $value) {
            $app_name = $value['app_name'];
            $cache_key = $this->_get_cache_key($user_id, $app_name);
            $access_list[$app_name] = yue_protocol_cache::get_cache($cache_key);
        }
        return $access_list;
    }

    /**
     * 获取用户的授权列表
     * 
     * @access public
     * @param int $user_id 用户ID
     * @param boolean $b_select_count  是否获取数量，默认：FALSE
     * @param string $order_by 排序方式，默认：'id DESC'
     * @param string $limit 分页，默认：'0,20'
     * @param string $fields 字段，默认：'*'
     * @return array | int
     */
    public function get_access_list($user_id, $b_select_count = FALSE, $order_by = 'id DESC', $limit = '0,20', $fields = '*') {
        $user_id = intval($user_id);
        if ($user_id < 1) {
            $this->_errmsg = 'The query param is invalid!';
            return $b_select_count ? 0 : array();
        }
        // 获取数据
        if ($b_select_count) {
            return $this->findCount("user_id = {$user_id}");
        }
        $list = $this->findAll("user_id = {$user_id}", $limit, $order_by, $fields);
        foreach ($list as $k => $v) {
            $list[$k]['format_add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $list[$k]['format_update_time'] = date('Y-m-d H:i:s', $v['update_time']);
            $list[$k]['format_expire_time'] = date('Y-m-d H:i:s', $v['expire_time']);
        }
        return $list;
    }

}
