<?php

/*
 *用户权限控制函数
 *@param string $ret_type 返回格式 隐藏格式,退出格式
 *@param $authority_list 查询所得权限
 *@param 权限模型名称
 *权限查询值
*/
  function check_authority_by_list($ret_type = '',$authority_list, $action_val, $val = null)
  {
    global $yue_login_id,$authority_obj;
    $is_root = $authority_obj->user_id_is_root();
    if($is_root)
    {
        return true;
    }
    /*if($yue_login_id == 100293)
    {
      var_dump($authority_list);exit();
    }*/
  	if (empty($authority_list)) 
    {
      echo "你没有权限";
      exit;    
    }
    $info = false;
    foreach ($authority_list as $key => $vo) 
    {
      if ($vo['action'] == $action_val) 
      {
          if($val != '')
          {
            if($authority_list[$key][$val] == 1)
                $info = true;
          }
          else
          {
            $info = true;
          }
      }
    }
    //隐藏
    if ($ret_type == 'display') 
    {
      $info = $info == 0 ? "style='display:none';" : '';
      # code...
    }
    //退出格式
    if ($ret_type == 'exit_type') 
    {
       if ($info == 0) 
       {
         echo "<script type='text/javascript'>window.alert('您没有权限');history.back();</script>";
         exit;
       }
    }
    //提示文字格式
    if ($ret_type == 'msg' ) 
    {
       if ($info == 0) 
       {
         $info = "您没有权限!";
       }
    }
    return $info;
  }
?>