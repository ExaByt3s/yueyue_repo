<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/11/25
 * Time: 14:02
 */
class pai_gearman_base_class extends POCO_TDG
{
    public $retry_num = 5;
    public $timeout = 5000;

    public $err_log = array('code'=>0, 'msg'=>'正常');
    public $client_obj;
    public $errno;

    public function __construct()
    {
        //检查类是否存在
        if( !class_exists('GearmanClient') )
        {
            $this->set_err_log('2', 'GearmanClient不存在');
            return $this->err_log;
        }

        //实例化对象
        $this->client_obj = new GearmanClient();
    }

    /**
     * @param $server_ip
     * @param $server_port
     * 连接Gearman服务器
     */
    public function connect($server_ip, $server_port)
    {
        if($server_ip && $server_port)
        {
            if($this->client_obj->addServer($server_ip, $server_port))
            {
                $this->client_obj->setTimeout($this->timeout);
                $this->set_err_log('1', '连接成功');
            }else{
                $this->set_err_log('4', '连接失败');
            }


        }else{
            $this->set_err_log('3', '缺少参数');
        }
    }


    /**
     * @param $func_name
     * @param $func_array
     * 实时执行,用于执行后,需要获得返回的方法
     */
    public function _do($func_name, $func_array, $return_type = 'array'){
        $num = 0;
        $b_time = time();
        do
        {
            $result= $this->client_obj->do($func_name,json_encode($func_array) );
            $num ++;
            if($num >= $this->retry_num){
                $msg = '重试错误，func_name:' .$func_name;
                $this->set_err_log(10, $msg);
                $this->set_sql_log('do', $func_name, $func_array, $result, 0, 'Err');
                return FALSE;
            }
        }
        while( $this->client_obj->returnCode() != GEARMAN_SUCCESS);
        $s_timeout = time() - $b_time;
        if($s_timeout >= 0) $this->set_sql_log('do', $func_name, $func_array, $result, $s_timeout);

        if( $return_type == 'array')
        {
            return json_decode($result,true);
        }else{
            return $result;
        }

    }

    /**
     * @param $func_name
     * @param $func_array
     * 异步执行,用户不需要获得返回的方法
     */
    public function _doBackground($func_name, $func_array){
        $b_time = time();
        $result = $this->client_obj->dobackground($func_name, json_encode($func_array));
        $s_timeout = time() - $b_time;
        if($s_timeout >= 0) $this->set_sql_log('doBackground', $func_name, $func_array, $result, $s_timeout);
        return $result;
    }



  public function set_err_log($code, $msg)
    {
        if($code)
        {
            $this->err_log = array('code'=>$code, 'msg'=>$msg);
        }
    }

   public function get_gearman_sys_errno()
   {
       return $this->client_obj->getErrno();
   }

    public function get_err_log()
    {
        return $this->err_log;
    }

    public function set_sql_log($type, $func_name, $func_array, $return_array , $timeout, $e_level = 'warning'){
        $func_array     = serialize($func_array);
        $return_array   = serialize($return_array);

        $sql_str = "INSERT INTO pai_log_db.pai_gearman_log(g_type, g_func_name, g_func_array, s_return_array, s_timeout, e_level)
                    VALUES ('{$type}', '{$func_name}', :x_func_array, :x_return_array, '{$timeout}', '{$e_level}')";
        sqlSetParam($sql_str, 'x_func_array', $func_array);
        sqlSetParam($sql_str, 'x_return_array', $return_array);

        try{
            db_simple_getdata($sql_str, TRUE, 101);
        }catch (Exception $e) {

        }

    }

}