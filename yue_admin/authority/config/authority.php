<?php

/*
 *用户权限控制函数
*/
  //print_r($authority_list);exit;
  function check_authority($ret_type = '',$authority_list, $action_val, $val = null)
  {
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
         echo "您没有权限";
         exit;
       }
    }
    return $info;
  }
?>