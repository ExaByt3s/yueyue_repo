<?php
    //����������
    function getExcel($fileName, $headArr, $data,$title='tt')//����excel��
    {
        if (empty ( $data ) || ! is_array ( $data ))
        {
            die ( "data must be a array" );
        }
        if (empty ( $fileName ))
        {
            exit ();
        }
        $date = date ( "Y_m_d_H_i_s", time () );
        $fileName .= "_{$date}.xls";
        
        //�����µ�PHPExcel����
        $objPHPExcel = new PHPExcel ();
        $objProps = $objPHPExcel->getProperties ();
        //���ñ�ͷ
        $key = ord ( "A" );
        $objActSheet = $objPHPExcel->getActiveSheet ();
        $objActSheet->getRowDimension ( '1' )->setRowHeight ( 22 );
        foreach ( $headArr as $v )
        {
            $colum = chr ( $key );
            $objActSheet->getColumnDimension ( $colum )->setWidth ( 20 );
            $objActSheet->getStyle ( $colum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $objActSheet->getStyle ( $colum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::VERTICAL_CENTER );
            $v = iconv ( 'GBK', 'utf-8', $v );
            $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $colum . '1', $v );
            $key += 1;
        }
        //exit;
        $column = 2;
        //$objActSheet = $objPHPExcel->getActiveSheet();
        foreach ( $data as $key => $rows )
        { //��д��
            $span = ord ( "A" );
            foreach ( $rows as $keyName => $value )
            { // ��д��
                $j = chr ( $span );
                $objActSheet->getColumnDimension ( $j )->setWidth ( 20 );
                $value = iconv ( 'GBK', 'utf-8', $value );
                $objActSheet->setCellValue ( $j . $column, $value );
                $span ++;
            }
            $column ++;
        }
        
        //$fileName = iconv("utf-8", "gb2312", $fileName);
        //��������
        //$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //��������Ӧ
        $objPHPExcel->getActiveSheet ()->setTitle ( iconv ( 'GB2312', 'utf-8', $title ) );
        //���û��ָ������һ����,����Excel�����ǵ�һ����
        $objPHPExcel->setActiveSheetIndex ( 0 );
        //������ض���һ���ͻ���web�����(Excel2007)
        //ob_end_clean();//���������,��������
        header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
        header ( "Content-Disposition: attachment; filename=\"$fileName\"" );
        header ( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
        /*   if(!empty($_GET['excel'])){
                $objWriter->save('php://output'); //�ļ�ͨ�����������
            }else{
              $objWriter->save($fileName); //�ű���ʽ���У������ڵ�ǰĿ¼
            }*/
        $objWriter->save ( 'php://output' );
        exit ();
    }
    
    //�ж��̼�ϵͳ��¼Ȩ��	
    function mall_check_admin_permissions($yue_login_id)
    {
		$url_fileName=strtolower($_SERVER['PHP_SELF']);
        if(!in_array($url_fileName,array('/yue_admin/task/index.php')))
        {
			$_redis_cache_name_prefix = 'G_YUEUS_MALL_GOODS';
            if(empty($yue_login_id))
            {
                
                $r_url = urlencode($r_url);
                header("location:http://www.yueus.com/yue_admin/task/login.php?r_url=".$r_url);
                exit;
            }
			$acl = POCO::getCache($_redis_cache_name_prefix."_ACL");			
			$user_acl = POCO::getCache($_redis_cache_name_prefix."_USER_".(int)$yue_login_id);			
            return true;
        }
    }
    
    //�ж��̼�ϵͳ��¼Ȩ��	
    function mall_check_seller_permissions($yue_login_id)
    {
        $url_fileName=basename(strtolower($_SERVER['PHP_SELF']),".php");
        
        $login_info = urldecode($_GET['login_info']);
        $json_arr = json_decode($login_info,true);
        
        $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        
        if($json_arr['user_id'] && $json_arr['token'])
        {
            $login_status = mall_app_check_login();
            
            if($login_status['result'] == 1)
            {
                if($login_status['switch'] == 1)
                {
                    $cur_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
                    // Ҫ�л��˺ţ�����load
                    header("Location:{$cur_url}");
                    exit;
                }
            }
            else
            {
                //��֤ʧ��
                header("location:http://yp.yueus.com/mobile/auth_jump_page.php?r_url=".$r_url);
                exit;
            }
        }
        
        if(!in_array($url_fileName,array('index')))
        {
            if(empty($yue_login_id))
            {
                
                $r_url = urlencode($r_url);
                header("location:http://www.yueus.com/pc/login.php?r_url=".$r_url);
                exit;
            }
            $mall_obj = POCO::singleton('pai_mall_seller_class');
            $seller_info=$mall_obj->get_seller_info($yue_login_id,2);
            $seller_status=$seller_info['seller_data']['status'];
            if(!in_array($url_fileName,array('check_out','buy_bill_list','buy_goods_list','normal_certificate_basic','normal_certificate_check','normal_certificate_choose','normal_certificate_detail','service_certificate_detail','pai_mall_certificate_basic_op','pai_mall_certificate_service_op','normal_certificate_list','app_guide')))
            {
                if(!$seller_info or $seller_status == 2)
                {
                    header("location:./normal_certificate_choose.php");
                    eixt;
                }
            }
            return $seller_info;
        }
    }
    
    
    //�ж��û�ϵͳ��¼Ȩ��	
    function mall_check_user_permissions($yue_login_id)
    {

        $user_obj = POCO::singleton('pai_user_class');

        $url_fileName=basename(strtolower($_SERVER['PHP_SELF']),".php");
        
        $login_info = urldecode($_GET['login_info']);
        $json_arr = json_decode($login_info,true);

        $__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;
        $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $r_url = urlencode($r_url);

        $current_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $log_arr['result'] = $json_arr;
        pai_log_class::add_log($log_arr, 'check_start', 'check_fail');

        $parse_url = parse_url($current_url);
        $parse_url['query'];
        $__is_app_login_info = (preg_match('/login_info=/',$parse_url['query'])) ? true : false;

        //�ж�����login_info��ʶ
        if($__is_app_login_info) {
            if ($json_arr['user_id'] && $json_arr['token']) {
                
                //��¼��֤
                $login_status = mall_app_check_login();


                $log_arr['json_arr'] = $json_arr;
                $log_arr['login_status'] = $login_status;
                $log_arr['query'] = $parse_url['query'];
                pai_log_class::add_log($log_arr, 'app_check_login', 'app_check_login');

                if ($login_status['result'] == 1) {
                    $log_arr['result'] = $json_arr;

                    pai_log_class::add_log($log_arr, 'check_success', 'check_fail');
                    if ($login_status['switch'] == 1) {
                        // Ҫ�л��˺ţ�����load
                        header("Location:{$current_url}");
                        exit;
                    }
                } else {

                    $log_arr['result'] = $json_arr;

                    pai_log_class::add_log($log_arr, 'check_fail', 'check_fail');
                    //��֤ʧ��
                    header("location:http://yp.yueus.com/mobile/auth_jump_page.php?r_url=" . $r_url);
                    exit;
                }

            }
            else
            {
                //��login_info��ʶ������login_info jsonΪ�գ�ִ����ҳ�ǳ�
                $user_obj->logout();

                if(!defined('MALL_NOT_REDIRECT_LOGIN')) {
                    header("location:http://yp.yueus.com/mobile/auth_jump_page.php?r_url=" . $r_url);
                    exit;
                }
                return true;
            }
        }


        $log_arr['result'] = $yue_login_id;

        pai_log_class::add_log($log_arr, 'yue_login_id', 'check_fail');



        if(empty($yue_login_id))
        {

            if($__is_yueyue_app)
            {
                header("location:http://yp.yueus.com/mobile/auth_jump_page.php?r_url=".$r_url);
            }
            else
            {
                header("location:http://www.yueus.com/pc/register.php?r_url=".$r_url);
            }

            exit;
        }

        return true;
    }

    //�༭����ȫHTML����ĺ�������2015-7-6���
    function mall_closetags($html) 
    {	
    // ����Ҫ��ȫ�ı�ǩ	
        $arr_single_tags = array('meta', 'img', 'br', 'link', 'area' ,'embed');	
    // ƥ�俪ʼ��ǩ	
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);	
        $openedtags = $result[1];	
    // ƥ��رձ�ǩ	
        preg_match_all('#</([a-z]+)>#iU', $html, $result);	
        $closedtags = $result[1];	
    // ����رտ�����ǩ�����������ͬ�ͷ���html����	
        $len_opened = count($openedtags);	
        if (count($closedtags) == $len_opened)
        {	
            return $html;	
        }	
    // ���������飬�����һ�������ı�ǩ������ǰ��	
        $openedtags = array_reverse($openedtags);	
    // ����������ǩ����	
        for ($i = 0; $i < $len_opened; $i++) 
        {	
    // �����Ҫ��ȫ�ı�ǩ	
            if (!in_array($openedtags[$i], $arr_single_tags)) 
            {	
    // ��������ǩ���ڹرյı�ǩ��	
                if (!in_array($openedtags[$i], $closedtags)) 
                {	
    // ֱ�Ӳ�ȫ�պϱ�ǩ	
                    $html .= '</' . $openedtags[$i] . '>';
    
                } 
                else 
                {	
                    unset($closedtags[array_search($openedtags[$i], $closedtags)]);	
                }	
            }	
        }	
        return $html;	
    }

    /*
     * APP����Ƿ��¼
     */
    function mall_app_check_login()
    {
        global $yue_login_id;
    
        $login_info = urldecode($_GET['login_info']);
        //var_dump($login_info);exit;
        
//		include_once("/disk/data/htdocs232/poco/pai/mobile_app/protocol_common.inc.php");
//		$cp = new poco_communication_protocol_class ();
        require_once('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
        $cp = new yue_protocol_system();
        $user_obj = POCO::singleton('pai_user_class');
        
        //exit;
        $json_arr = json_decode($login_info,true);
        $user_id = $json_arr['user_id'];
        $token = $json_arr['token'];

        $agent   = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone  = (strpos($agent,'iphone')) ? true : false;
        $android = (strpos($agent,'android')) ? true : false;
        if( $android ) {

            $app_name = 'poco_yuepai_android';

        }
        elseif( $iphone ){

            $app_name = 'poco_yuepai_iphone';

        }
        else{

            $app_name = 'poco_yuepai_android';

        }

        $access_info = $cp->get_access_info($user_id, $app_name, false, false);
        
        
        if($access_info['access_token']==$token)
        {
            $user_obj->load_member($user_id);
            
            $result['switch'] = 0;
            
            if($user_id!=$yue_login_id)
            {
                $result['switch'] = 1;
            }
            
            $result['result'] = 1;
            return $result;
        }
        
        $result['result'] = 0;
        $result['url'] = "";
        return $result;
    }
    
    
    /*
     * ���鴮��get��ʽ����
     */
    function mall_query_str($params) 
    {
       if ( !is_array($params) || count($params) == 0 ) return false;
       $fga = func_get_args();
       $akey = ( !isset($fga[1]) ) ? false : $fga[1];        
       static $out = Array();
       
       foreach ( $params as $key=>$val ) {
           if ( is_array($val) ) {    
               mall_query_str($val,$key);
               continue;
           }
    
           $thekey = ( !$akey ) ? $key : $akey.'['.$key.']';
           $out[] = $thekey."=".$val;
       }
       
       return implode("&",$out);    
    }
    
    //����script ��ǩ
    function del_script($str)
    {
        $preg = "/<script[\s\S]*?<\/script>/i";

        $new_str = preg_replace($preg,"",$str);
        
        return $new_str;
    }
    
    //У��ͼ�ı༭����ͼƬ�����Ƿ�Ϊ��վ����
    //SRC����У��
    function mall_src_link_check($text)
    {
        //ƥ���SRC��ַ�Ƿ����Ԥ��

        //ƥ����ַ�����ͼƬ
        preg_match_all("/src=((&quot;)|(&#39;))(.*?(?:))((&quot;)|(&#39;))/is",$text,$src_match);
        $check = true;
        foreach($src_match[0] as $k => $v)
        {
            $check = preg_match('#http://(.*).(poco.cn|yueus.com)/#',$v);
            if(!$check)
            {
                break;

            }

        }

        return $check;

    }

    //У��ͼ�ı༭����ͼƬ��������
    function mall_src_link_len_check($text)
    {
        //ƥ����ַ�����ͼƬ
        preg_match_all("/src=((&quot;)|(&#39;))(.*?(?:))((&quot;)|(&#39;))/is",$text,$src_match);
        $img_link_len = count($src_match[0]);
        return $img_link_len;
    }


    function mall_echo_debug($data)
    {
        if($_COOKIE['debug'])
        {
            dump($data);
        }
    }
    
    function mall_wx_get_js_api_sign_package()
    {
        //$app_id = 'wx8a082d58347117f7';	//ԼԼ���Ժ�
        $app_id = 'wx25fbf6e62a52d11e';	//ԼԼ��ʽ��
        $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        
        $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
        $ret = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $url);
        
        //��ʱ��־
        $payment_obj = POCO::singleton('pai_payment_class');
        ecpay_log_class::add_log(array('url'=>$url), __FILE__ , 'pai_weixin_js_api');
        
        return $ret;
    }
    
    /**
     * ��ʽ����������ķ��� 
     * @param type $config_data
     * @param type $select_one
     * @param type $which_select
     */
    function config_data_format($config_data,$select_one,$which_select)
    {
         foreach($config_data as $key => $val)
        {
            if($which_select == 'key')
            {
                $selected = $select_one===$key ? true : false;
            }else if($which_select == 'val')
            {
                $selected = $select_one===$val ? true : false;
            }
            $config_list[] = array(
                'key'=>$key,
                'val'=>$val,
                'selected'=> $selected
            );
            unset($selected);
        }
        
        return $config_list;
    }
    
    /**
     * ����csv
     * @param type $data
     * @param type $title_arr
     * @param type $file_name
     */
    function export_csv(&$data, $title_arr, $file_name = '') 
    {
        ini_set("max_execution_time", "3600");
        $csv_data = '';
        /** ���� */
        $nums = count($title_arr);
        for ($i = 0; $i < $nums - 1; ++$i) {
            $csv_data .= '"' . $title_arr[$i] . '",';
        }
        if ($nums > 0) {
         $csv_data .= '"' . $title_arr[$nums - 1] . "\"\r\n";
        }
        foreach ($data as $k => $row) {
            for ($i = 0; $i < $nums - 1; ++$i) {
                $row[$i] = str_replace("\"", "\"\"", $row[$i]);
                $csv_data .= '"' . $row[$i] . '",';
            }
            $csv_data .= '"' . $row[$nums - 1] . "\"\r\n";
            unset($data[$k]);
        }
        $csv_data = mb_convert_encoding($csv_data, "utf-8", "gbk");
        $file_name = empty($file_name) ? date('Y-m-d-H-i-s', time()) : $file_name;
        if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) { // ���IE�������������������bug
         $file_name = urlencode($file_name);
         $file_name = str_replace('+', '%20', $file_name);
        }
        $file_name = $file_name . '.csv';
        header("Content-type:text/csv;");
        header("Content-Disposition:attachment;filename=" . $file_name);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $csv_data;
    }
    
    ////��÷ÿ����������
    function mall_get_browser()
    {
        if( ! empty($_SERVER['HTTP_USER_AGENT']) )
        {
           $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i',$br)) 
            {    
                $br = 'MSIE';
            }elseif (preg_match('/Firefox/i',$br)) 
            {
                $br = 'Firefox';
            }elseif (preg_match('/Chrome/i',$br)) 
            {
                $br = 'Chrome';
            }elseif (preg_match('/Safari/i',$br)) 
            {
                $br = 'Safari';
            }elseif (preg_match('/Opera/i',$br)) 
            {
                $br = 'Opera';
            }
            else 
            {
                $br = 'Other';
            }
            
            return $br;
        }else
        {
            return "��ȡ�������Ϣʧ�ܣ�";
        } 
    }


    ////��ȡ�ÿͲ���ϵͳ
    function mall_get_os()
    {
        if( ! empty($_SERVER['HTTP_USER_AGENT']) )
        {
            $OS = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i',$OS)) 
            {
               $OS = 'Windows';
            }elseif (preg_match('/mac/i',$OS)) 
            {
               $OS = 'MAC';
            }elseif (preg_match('/linux/i',$OS)) 
            {
               $OS = 'Linux';
            }elseif (preg_match('/unix/i',$OS)) 
            {
               $OS = 'Unix';
            }elseif (preg_match('/bsd/i',$OS)) 
            {
               $OS = 'BSD';
            }
            else 
            {
               $OS = 'Other';
            }
            return $OS;  
        }else
        {
            return "��ȡ�ÿͲ���ϵͳ��Ϣʧ�ܣ�";
        }   
   }
 
    //��ȡip
    function mall_get_ip()
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
          $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
          $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif(!empty($_SERVER["REMOTE_ADDR"]))
        {
          $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
          $cip = "�޷���ȡ��";
        }
        return $cip;
    }
    
    //ljl��ӡ����
    function ljl_dump($data,$is_exit=false)
    {
        global $yue_login_id;
        if($yue_login_id == 115203)
        {
            dump($data);
            dump("$####$");
            if($is_exit)
            {
                exit;
            }
        }
    }
    
    
    
?>