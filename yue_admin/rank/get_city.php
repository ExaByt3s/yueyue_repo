<?php 

   //获取城市数据
   include_once 'common.inc.php';
   include_once 'include/locate_file.php';
   include_once 'include/common_function.php';
   include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
   $act = $_INPUT['act'] ? $_INPUT['act'] : 'city_list';
   if ($act == 'city_list') 
   {
      $prov_id = $_INPUT['prov_id'] ? intval($_INPUT['prov_id']) : 0;
      $city_list = ${'arr_locate_b'.$prov_id};
      $city_list = change_assoc_arr($city_list , true);
      $arr  = array(
        'msg' => 'success' ,
        'ret' => $city_list
    );
     echo json_encode($arr);
   }
   elseif ($act == 'city_sel') 
   {
       $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
       $location_info = get_poco_location_name_by_location_id ($location_id, false, true);
       if (isset($location_info['level_1']['id']) && $location_info['level_1']['id'] != '') 
       {
         $prov_id = $location_info['level_1']['id'];
         $city_list = ${'arr_locate_b'.$prov_id};
         $city_list = change_assoc_arr($city_list , true);
         foreach ($city_list as $key => $vo) 
         {
           if ($vo['c_id'] == $location_id) 
           {
             $city_list[$key]['city_sel'] = "selected='true'";
           }
         }
         $arr  = array(
           'msg' => 'success' ,
           'prov_id' => $prov_id,
           'ret' => $city_list
         );
         echo json_encode($arr);
       }

   }
   
 ?>