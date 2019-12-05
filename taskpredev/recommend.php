<?php

 /** 
  * 
  * tt
  * 汤圆
  * 2015-4-11
  * 
  */
  
 include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
  
 /**
  * 引用资源文件定位，注意！确保引用路径争取
  */
 $file_dir = dirname(__FILE__);

 include_once($file_dir.'/./task_common.inc.php');
 include_once($file_dir.'/./task_for_normal_auth_common.inc.php');

 include_once($file_dir. '/./webcontrol/head.php');


 
 include_once($file_dir. '/./webcontrol/footer.php');
  
 $tpl = $my_app_pai->getView('recommend.tpl.htm');

 // 消费者 必须引入
 include_once($file_dir.'/./consumers_require.php');
 

 $tpl->assign('time', time());  //随机数

 // 公共样式和js引入
 $pc_global_top = _get_wbc_head();
 $tpl->assign('pc_task_top', $pc_global_top);

 $pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'lead_list'));
 $tpl->assign('pc_global_nav', $pc_global_nav);


 // 底部
 $footer_html = $my_app_pai->webControl('pc_task_footer', array(), true);
 $tpl->assign('footer_html', $footer_html);



$service_id = 4 ;
 
$task_questionnaire_obj = POCO::singleton('pai_task_questionnaire_class');
$json_arr = $task_questionnaire_obj -> get_questionnaire_version_list($service_id);

$json_arr_data =  $json_arr['data'];



//重组问答标题数据字端
// foreach ($json_arr_data as $k => $val) 
// {

//     if ($json_arr_data[$k]['type'] == 1) 
//     {

//         foreach ($json_arr_data[$k]['data'] as $key => $value) 
//         {

//             if ($value['link'] == '' || $value['link'] == '0') 
//             {
//                 $json_arr_data[$k]['data'][$key]['top_question_id'] = $json_arr_data[$k]['id'];
//             }

//             if ($value['link'] == '1') 
//             {

//                 foreach ($json_arr_data[$k]['data'][$key]['data'] as $key2 => $value2) 
//                 {
//                     $json_arr_data[$k]['data'][$key]['data'][$key2]['top_question_id'] = $json_arr_data[$k]['id'];
//                 }


//             }
            

//         }
//     }

// }


// print_r($json_arr);
// print_r($json_arr_data);


//  24小时时间重组
$hours_time_arr = array();
for ($i=0; $i < 24 ; $i++) { 
    array_push( $hours_time_arr,array('hours' => $i));
}


//  60分时间重组
$minutes_time_arr = array();
for ($i=0; $i < 60 ; $i++) { 
    if ($i > 0 && $i <= 9 ) 
    {
        array_push( $minutes_time_arr,array('minutes' => '0'.$i));
    }
    else
    {
        array_push( $minutes_time_arr,array('minutes' => $i));
    }
    
}


//  时段时间重组
$period_time_arr = array();
for ($i=0; $i < 24 ; $i++) { 
    array_push( $period_time_arr,array('period' => $i));
}



if (isset($_INPUT['print'])) 
{
    print_r($json_arr_data);
}






$tpl->assign('json_arr', $json_arr);  //原来最顶端数组

$tpl->assign('json_arr_data', $json_arr_data); //下面问答题目数组

$tpl->assign('hours_time_arr', $hours_time_arr);  

$tpl->assign('minutes_time_arr', $minutes_time_arr); 

$tpl->assign('period_time_arr', $period_time_arr);  




 $tpl->output();

?>