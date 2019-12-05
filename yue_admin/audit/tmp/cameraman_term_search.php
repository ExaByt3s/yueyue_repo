<?php 

	include_once 'common.inc.php';
  check_authority(array('cameraman'));
	function select_sort($sort)
    {
        $sort_tmp;
        switch ($sort) {
            case 'uid_desc':
                $sort_tmp = "uid DESC"; 
                break;
            case 'ptime_desc':
                $sort_tmp = "inputer_time DESC";
                break;
            case 'ptime_asc':
                $sort_tmp = "inputer_time ASC";
                break;
            default:
                $sort_tmp = "uid DESC"; 
                break;
        }
        return $sort_tmp;
    }
    $page_obj = new show_page ();
	$show_count = 20;
	$cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
    $tpl = new SmartTemplate("cameraman_term_search.tpl.htm");
    $act      = $_INPUT['act'] ? $_INPUT['act'] : '';
    $name     = $_INPUT['name']  ? $_INPUT['name'] : '';
    $phone    = $_INPUT['phone'] ? $_INPUT['phone'] : '';
    $app_name = $_INPUT['app_name'] ? $_INPUT['app_name'] : '';
    $uid      = $_INPUT['uid'] ? $_INPUT['uid'] : '';
    $sort     = $_INPUT['sort'] ? $_INPUT['sort'] : 'uid_desc';
     //�ж�
    $where_str = "";
    switch ($act) {
    	 case 'search':
    	 	   if (!empty($name)) 
    	 	   {
    	 	   	 $where_str .= "name = '{$name}'";
    	 	   }
    	 	   if (!empty($phone)) 
    	 	   {
    	 	   	if (!empty($where_str)) 
    	 	   	{
    	 	   		$where_str .= " AND ";
    	 	   	}
    	 	   	$where_str .= " phone = {$phone} ";
    	 	   	# code...
    	 	   }
    	 	   if (!empty($app_name)) 
    	 	   {
    	 	   	 if (!empty($where_str)) 
    	 	   	 {
    	 	   	 	$where_str .= " AND ";
    	 	   	 }
    	 	   	 $where_str .= " app_name = '{$app_name}' ";
    	 	   }
    	 	   if (!empty($uid)) 
    	 	   {
    	 	   	   $uid = (int)$uid;
    	 	   	   if (!empty($where_str)) 
    	 	   	   {
    	 	   	   	   $where_str .= " AND ";
    	 	   	   }
    	 	   	   $where_str .= " uid = {$uid} ";
    	 	   }
    	 	    $total_count = $cameraman_add_obj->get_cameraman_list(true, $where_str);
    	 	    $page_obj->set ( $show_count, $total_count );
                $list = $cameraman_add_obj->get_cameraman_list(false, $where_str, select_sort($sort), $page_obj->limit(), $fields = '*');
    	       break;
    	 default:
             $total_count = $cameraman_add_obj->get_cameraman_list(true, $where_str);
              //print_r($total_count);exit;
            $page_obj->setvar ();
            $page_obj->set ( $show_count, $total_count );
            $list = $cameraman_add_obj->get_cameraman_list(false, $where_str, select_sort($sort), $page_obj->limit(), $fields = '*');
            break;
    }

    /*�б���ʾ����*/
    if (!empty($list) && is_array($list)) 
    {
       foreach ($list as $key_city => $vo) 
       {
          //��פ�������ƻ�ȡ
          $mycity = '';
          //echo $vo_city['city'];
          switch ($vo['city']) 
          {
            case 0:
                  $mycity = '����';
                  break;
            case 1:
                  $mycity = '�Ϻ�';
                  break;
            case 2:
                  $mycity = '����';
                  break;
            case 3:
                  $mycity = '�ɶ�';
                  break;
            case 4:
                  $mycity = '�人';
                  break;
            case 5:
                  $mycity = '����';
                  break;
          }
           $list[$key_city]['city'] = $mycity;
           //��������
           $style_list = $cameraman_add_obj->cameraman_list_style($vo['uid']);
           $style ="";
           foreach ($style_list as $style_key => $vstyle) 
           {
               switch ($vstyle['style']) 
               {
                   case 0:
                       $mystyle = 'ŷ��';
                       break;
                   case 1:
                       $mystyle = '����';
                       break;
                   case 2:
                       $mystyle = '����';
                       break;
                   case 3:
                       $mystyle = '����';
                       break;
                  case 4:
                       $mystyle = '��ϵ';
                       break;
                  case 5:
                       $mystyle = '��ϵ';
                       break;
                  case 6:
                       $mystyle = '�Ը�';
                       break;
                  case 7:
                       $mystyle = '����';
                       break;
                  case 8:
                       $mystyle = '��Ƭ';
                       break;
                  case 9:
                       $mystyle = '��ҵ';
                       break;
               }
               if ($style_key != 0) 
               {
                   $style .=',';
               }
               $style .=$mystyle;
           }
           $list[$key_city]['style'] = $style;
       }
      
    }
    //����
    switch ($sort) {
        case 'uid_desc':
            $sort_1 = "selected='true'";
            $tpl->assign('sort_1', $sort_1);
            break;
        case 'ptime_asc':
            $sort_2 = "selected='true'";
            $tpl->assign('sort_2', $sort_2);
            break;
        case 'ptime_desc':
            $sort_3 = "selected='true'";
            $tpl->assign('sort_3', $sort_3);
            break;
        default:
            # code...
            break;
    }
    $tpl->assign('list', $list);
    $tpl->assign('name', $name);
    $tpl->assign('phone', $phone);
    $tpl->assign('app_name', $app_name);
    $tpl->assign('uid', $uid);
    $tpl->assign('total_count', $total_count);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>