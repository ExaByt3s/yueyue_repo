<?php

/** 
�����������





*/

set_time_limit(0);
ini_set("memory_limit","256M");
ignore_user_abort(true);
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('./config/phone_meeting_config.php');//���÷���Ӧ���ε�ID����Ǯ
$summit_meeting_supplier_obj   = POCO::singleton('pai_summit_meeting_supplier_class');
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$match_login_id_array = array(65804368,64484705,177721807);
/*if(empty($login_id))
{
    //û��¼ȥ��¼ҳ
    //ȡ��ǰҳ��url    
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
        alert('û��Ȩ�޵���');window.location.href='http://www.yueus.com/topic/meeting/'
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
        //�����μ�������
        $where_array = array();
        $where_array['pay_status'] = 1;
        $enroll_list = $summit_meeting_obj->get_summit_meeting_list_by_array($where_array,false,"add_time asc","0,10000");
        
    }
    else
    {
        //������Ӧ������
        $supplier_list = $summit_meeting_supplier_obj->get_summit_meeting_supplier_list_by_array("", false,'add_time asc', "0,1000");
    }


    Header("Content-type: text/html; charset=gb2312");
    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Content-type:application/vnd.ms-excel");
    if($type=="enroll")
    {
        Header("Content-Disposition:attachment;filename= ������Ա����_".date("Ymd").".xls");
        $enroll_list_count = count($enroll_list);
        if(empty($enroll_list_count))
        {
            $content  	       = "û���ҵ�����������Ҫ��ļ�¼\n";
        }
        else
        {
            $content 		  = " ���� \t ����ID \t �û��� \t �û��绰 \t �û����� \t �������� \t �ܼ� \t ����״̬ \t ����ʱ�� \t ��¼���ʱ�� ";

            $content 		 .= " \n";
            
            foreach($enroll_list as $key => $value)
            {
                
                if($value['pay_status']>0)
                {
                    $pay_status = "�Ѹ���";
                }
                else
                {
                    $pay_status = "δ����";
                }
                
                $content .= "��".$value['meeting_id']."��\t".$value['meeting_id']."\t".$value['name']." \t".$value['phone']." \t".$value['email']." \t".$value['enroll_num']."\t'".$value['sum_price']."\t".$pay_status."\t".date('Y-m-d H:i:s',$value['pay_time'])."\t".date('Y-m-d H:i:s',$value['add_time'])."\t";


            
                $content .=" \n ";
            }
            
        }
        
        
    }
    else
    {
        Header("Content-Disposition:attachment;filename= ��Ӧ������_".date("Ymd").".xls");
        $supplier_list_count = count($supplier_list);
        if(empty($supplier_list_count))
        {
            $content  	       = "û���ҵ�����������Ҫ��ļ�¼\n";
        }
        else
        {
            $content 		  = " ��Ӧ������ \t ��˾���� \t ��Ӧ�̵绰 \t ��Ӧ�̽��� \t ��¼���ʱ�� ";

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