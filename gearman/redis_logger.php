<?php
date_default_timezone_set("PRC");

Class RedisLogger
{
    private $r;
    private $expire_days = 7;
    private $expire_time;

    function __construct($host, $port)
    {
        $this->r = new redis();
        if (!$this->r->connect($host, $port))
        {
            echo "Connect to redis [$host:$port] fail.\n";
            exit(1);
        }

        $this->expire_time = $this->expire_days * 86400;
    }

    // Return value: -1 if fail, otherwise return the new task_id.
    function addTask($cmd)
    {
        $task_id = $this->r->incr('TaskID');
        if(false === $task_id)
        {
            echo "Incr fail.\n";
            return -1;
        }

        $task_key = "Task:$task_id";
        $date_time = date('Ymd');
        $task_set_key = "Task:Set:$date_time";

        $rc = $this->r->multi()
                      ->hMset($task_key, array('task_id' => $task_id, 'command' => $cmd))
                      ->zadd($task_set_key, time(), $task_key)
                      ->expire($task_set_key, $this->expire_time)
                      ->exec();
        if(false === $rc[0])
        {
            echo "hset fail.\n";
            return -1;
        }

        return $task_id;
    }

    // Return value: success 0, fail -1.
    function successTask($task_id, $code, $errmsg)
    {
        $task_key = "Task:$task_id";

        $rc = $this->r->multi()
                      ->hMset($task_key, array('status' => $code, 'message' => $errmsg))
                      ->expire($task_key, $this->expire_time)
                      ->exec();
        if(false === $rc[0])
        {
            echo "hMset fail.\n";
            return -1;
        }

        return 0;
    }

    // Return value: success 0, fail -1.
    function failTask($task_id, $code, $errmsg)
    {
        $task_key = "Task:$task_id";
        $date_time = date('Ymd');
        $task_failset_key = "Task:FailSet:$date_time";

        $rc = $this->r->multi()
                      ->hMset($task_key, array('status' => $code, 'message' => $errmsg))
                      ->expire($task_key, $this->expire_time)
                      ->zadd($task_failset_key, time(), $task_key)
                      ->expire($task_failset_key, $this->expire_time)
                      ->exec();
        if(false === $rc[0])
        {
            echo "hMset fail.\n";
            return -1;
        }

        return 0;
    }

    function getAllTask()
    {
        $tm = time();
        $start_tm = $tm - $this->expire_days * 86400;

        $r_mul = $this->r->multi();
        for($i = $this->expire_days; $i >= 0; --$i)
        {
            $date_time = date('Ymd', $tm - $i * 86400);
            $task_set_key = "Task:Set:$date_time";
            $r_mul->zRangeByScore($task_set_key, $start_tm, '+inf');
        }
        $rc = $r_mul->exec();
        //var_dump($rc);

        $r_mul = $this->r->multi();
        foreach($rc as $key_set_arr)
        {
            foreach( $key_set_arr as $key )
            {
                $r_mul->hgetall($key);
            }
        }
        $rc = $r_mul->exec();
        //var_dump($rc);

        $res = array();
        foreach( $rc as $task )
        {
            array_push($res, json_encode($task));
        }
        return $res;
    }

    function getFailTask()
    {
        $tm = time();
        $start_tm = $tm - $this->expire_days * 86400;

        $r_mul = $this->r->multi();
        for($i = $this->expire_days; $i >= 0; --$i)
        {
            $date_time = date('Ymd', $tm - $i * 86400);
            $task_set_key = "Task:FailSet:$date_time";
            $r_mul->zRangeByScore($task_set_key, $start_tm, '+inf');
        }
        $rc = $r_mul->exec();
        //var_dump($rc);

        $r_mul = $this->r->multi();
        foreach( $rc as $key_set_arr )
        {
            foreach ( $key_set_arr as $key )
            {
                $r_mul->hgetall($key);
            }
        }
        $rc = $r_mul->exec();
        //var_dump($rc);

        $res = array();
        foreach ($rc as $task)
        {
            array_push($res, json_encode($task));
        }
        return $res;
    }
}

?>
