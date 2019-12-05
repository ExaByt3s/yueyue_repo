<?php
    /**
     * ios ������ҳ����
     * ÿ�η���Ӧ��Ҫ�޸�
     * $lowest_version,$web_package_version_dir�������汾��
     */
    header('Content-Type: application/json');
    
    include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
    
    $version = require_once('/disk/data/htdocs232/poco/pai/mobile/config/version_control.conf.php');
    
    
    $osver = $_INPUT['osver'];
    $appver = $_INPUT['appver'];
    $modelver = $_INPUT['modelver'];
    $reactver = $_INPUT['reactver'];
    $bs_version = $_INPUT['bs_version'];
    
    /*************************�˴���������********************************/
//    $lowest_version = '1.0.1'; // ��Ͱ汾�ţ��˴����������޸�
//    $web_package_version_dir = '1.0.1';//���°汾�ŵ�·���ļ��� ��ҳ������Դ��
//    
//    $web_package_arr =array(
//                            '1.0.0' => array(
//                                             'dir' =>'1.0.0',
//                                             'package_ver' => '1.0.003'
//                                             ),
//                            '1.0.1' => array(
//                                             'dir' =>'1.0.1',
//                                             'package_ver' => '1.0.005'
//                                             )
//                            );
    
    $lowest_version = '1.2.0'; // ��Ͱ汾�ţ��˴����������޸�
    $web_package_version_dir = '1.2.0';//���°汾�ŵ�·���ļ��� ��ҳ������Դ��
    
    $web_package_arr =array(
                            '1.0.0' => array(
                                             'dir' =>'1.0.0',
                                             'package_ver' => '1.0.005'
                                             ),
                            '1.0.1' => array(
                                             'dir' =>'1.0.1',
                                             'package_ver' => '1.0.005'
                                             ),
                            '1.1.1' => array(
                                             'dir' =>'1.1.1',
                                             'package_ver' => '1.0.007'
                                             ),
                            '1.2.0' => array(
                                             'dir' =>'1.2.0',
                                             'package_ver' => '1.0.009'
                                             )
                            );
    /*************************�˴���������********************************/
    
    if($bs_version == '88.8.8') {
        $lowest_version = '1.3.0'; // ��Ͱ汾�ţ��˴����������޸�
        $web_package_version_dir = '1.3.0';//���°汾�ŵ�·���ļ��� ��ҳ������Դ��
        
        $web_package_arr =array(
                                '1.0.0' => array(
                                             'dir' =>'1.0.0',
                                             'package_ver' => '1.0.005'
                                                 ),
                                '1.0.1' => array(
                                             'dir' =>'1.0.1',
                                             'package_ver' => '1.0.005'
                                             ),
                                '1.1.1' => array(
                                             'dir' =>'1.1.1',
                                             'package_ver' => '1.0.007'
                                             ),
                                '1.2.0' => array(
                                                 'dir' =>'1.2.0',
                                                 'package_ver' => '1.0.010'
                                                 ),
                                '1.3.0' => array(
                                                 'dir' =>'1.3.0',
                                                 'package_ver' => '1.0.020'
                                                 )
                                );
        
        foreach ($web_package_arr as $key => $value)
        {
            if(version_compare($appver,$lowest_version,">="))
            {
                $web_package_arr[$key]['dir'] = $web_package_version_dir;
            }
        }
        
        if (version_compare($reactver,$web_package_arr[$appver]['package_ver'],"<")) {
            $output['needUpdate']='YES';
        }else{
            $output['needUpdate']='NO';
        }
        
        $output['app_version']=$appver;
        $output['packageUrl'] = 'http://yp-wifi.yueus.com/mobile/react_native_yuesell/'.$web_package_arr[$appver]['dir'].'/app_test.zip';
        $output['no_wifi_packageUrl'] = 'http://yp.yueus.com/mobile/react_native_yuesell/'.$web_package_arr[$appver]['dir'].'/app_test.zip';
    }else {
        foreach ($web_package_arr as $key => $value)
        {
            if(version_compare($appver,$lowest_version,">="))
            {
                $web_package_arr[$key]['dir'] = $web_package_version_dir;
            }
        }
        
        if (version_compare($reactver,$web_package_arr[$appver]['package_ver'],"<")) {
            $output['needUpdate']='YES';
        }else{
            $output['needUpdate']='NO';
        }
        
        $output['packageUrl'] = 'http://yp-wifi.yueus.com/mobile/react_native_yuesell/'.$web_package_arr[$appver]['dir'].'/app.zip';
        $output['no_wifi_packageUrl'] = 'http://yp.yueus.com/mobile/react_native_yuesell/'.$web_package_arr[$appver]['dir'].'/app.zip';
    }
    
    $output['packageVer'] = $web_package_arr[$appver]['package_ver'];
    $output['packageForceUpdate'] = 'http://yp-wifi.yueus.com/mobile/app';
    $output['enterUrl'] = 'http://yp-wifi.yueus.com/mobile/app';
    $output['no_wifi_enterUrl'] = 'http://yp.yueus.com/mobile/app';
    $output['appForceUpdate'] = 0;
    $output['isShowAppTips'] = 0;
    $output['appUpdateTips'] = mb_convert_encoding("�ף����������ޣ���ˢ�£�" , 'utf-8','gbk');
    
    echo json_encode($output);
    
    if($_REQUEST['print']==1)
    {
        var_dump($output);
        exit;
    }
    
    ?>