<?php

/*
 *�û�Ȩ�޿��ƺ���
*/
  //print_r($authority_list);exit;
  function check_authority($ret_type = '',$authority_list, $action_val, $val = null)
  {
  	if (empty($authority_list)) 
    {
      echo "��û��Ȩ��";
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
    //����
    if ($ret_type == 'display') 
    {
      $info = $info == 0 ? "style='display:none';" : '';
      # code...
    }
    //�˳���ʽ
    if ($ret_type == 'exit_type') 
    {
       if ($info == 0) 
       {
         echo "��û��Ȩ��";
         exit;
       }
    }
    return $info;
  }
?>