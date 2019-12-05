<?php

/** 
导出峰会数据





*/

set_time_limit(0);
ini_set("memory_limit","256M");
ignore_user_abort(true);
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱
$summit_meeting_supplier_obj   = POCO::singleton('pai_summit_meeting_supplier_class');
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$match_login_id_array = array(65804368,64484705,177721807);
/*if(empty($login_id))
{
    //没登录去登录页
    //取当前页面url    
    $REFERERURL = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI'];     
    //echo $REFERERURL;
    //exit();
    $redirect_url="http://www1.poco.cn/login.php?locate=".urlencode($REFERERURL);
    header("location:$redirect_url");
}
else
{
    if(!in_array($login_id,$match_login_id_array))
    {
        echo "<script>
        alert('没有权限导出');window.location.href='http://www.yueus.com/topic/meeting/'
        </script>";
    }
}*/




    $type = trim($_INPUT['type']);
    $match_array = array("enroll","supplier");
    if(!in_array($type,$match_array))
    {
        $type="enroll";
    }
    
    if($type=="enroll")
    {
        //导出参加者数据
        $where_array = array();
        $where_array['pay_status'] = 1;
        $enroll_list = $summit_meeting_obj->get_summit_meeting_list_by_array($where_array,false,"add_time asc","0,10000");
        
    }
    else
    {
        //导出供应商数据
        $supplier_list = $summit_meeting_supplier_obj->get_summit_meeting_supplier_list_by_array("", false,'add_time asc', "0,1000");
    }


    Header("Content-type: text/html; charset=gb2312");
    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Content-type:application/vnd.ms-excel");
    if($type=="enroll")
    {
        Header("Content-Disposition:attachment;filename= 报名人员内容_".date("Ymd").".xls");
        $enroll_list_count = count($enroll_list);
        if(empty($enroll_list_count))
        {
            $content  	       = "没有找到你符合你查找要求的记录\n";
        }
        else
        {
            $content 		  = " 场次 \t 场次ID \t 用户名 \t 用户电话 \t 用户邮箱 \t 报名人数 \t 总价 \t 付款状态 \t 付款时间 \t 记录添加时间 ";

            $content 		 .= " \n";
            
            foreach($enroll_list as $key => $value)
            {
                
                if($value['pay_status']>0)
                {
                    $pay_status = "已付款";
                }
                else
                {
                    $pay_status = "未付款";
                }
                
                $content .= "第".$value['meeting_id']."场\t".$value['meeting_id']."\t".$value['name']." \t".$value['phone']." \t".$value['email']." \t".$value['enroll_num']."\t'".$value['sum_price']."\t".$pay_status."\t".date('Y-m-d H:i:s',$value['pay_time'])."\t".date('Y-m-d H:i:s',$value['add_time'])."\t";


            
                $content .=" \n ";
            }
            
        }
        
        
    }
    else
    {
        Header("Content-Disposition:attachment;filename= 供应商内容_".date("Ymd").".xls");
        $supplier_list_count = count($supplier_list);
        if(empty($supplier_list_count))
        {
            $content  	       = "没有找到你符合你查找要求的记录\n";
        }
        else
        {
            $content 		  = " 供应商名字 \t 公司名称 \t 供应商电话 \t 供应商介绍 \t 记录添加时间 ";

            $content 		 .= " \n";
            
            foreach($supplier_list as $key => $value)
            {
                

                $content .= $value['name']."\t".$value['company']."\t".$value['phone']." \t".$value['intro'].date('Y-m-d H:i:s',$value['add_time'])."\t";


            
                $content .=" \n ";
            }
        }
    }
    


    
echo $content;
echo "\n";


?>