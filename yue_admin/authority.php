<?php

/*
 *�û�Ȩ�޿��ƺ���
 *@param string $ret_type ���ظ�ʽ ���ظ�ʽ,�˳���ʽ
 *@param $authority_list ��ѯ����Ȩ��
 *@param Ȩ��ģ������
 *Ȩ�޲�ѯֵ
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
         echo "<script type='text/javascript'>window.alert('��û��Ȩ��');history.back();</script>";
         exit;
       }
    }
    //��ʾ���ָ�ʽ
    if ($ret_type == 'msg' ) 
    {
       if ($info == 0) 
       {
         $info = "��û��Ȩ��!";
       }
    }
    return $info;
  }
?>