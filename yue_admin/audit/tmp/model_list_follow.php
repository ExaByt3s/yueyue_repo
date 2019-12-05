<?php 


	include_once 'common.inc.php';
    check_authority(array('model'));
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
    $uid    = $_INPUT['uid']     ? $_INPUT['uid'] : 0;
    $list = $model_add_obj->get_model_follow($uid, '3,10000');
    if (!empty($list) && is_array($list)) 
    {
        foreach ($list as $key => $vo) 
        {
            switch ($vo['result']) {
                case '0':
                    # code...
                    $list[$key]['result'] = iconv('GB2312','UTF-8','�ѽ��');
                    break;
                case '1':
                    $list[$key]['result'] = iconv('GB2312','UTF-8','δ���');
                    break;
                case '2':
                    $list[$key]['result'] = iconv('GB2312','UTF-8','������');
                    break;
                default:
                    $list[$key]['result'] = iconv('GB2312','UTF-8','�ѽ��');
                    break;
            }
            $list[$key]['problem_type'] = iconv('GB2312','UTF-8',$list[$key]['problem_type']);
            $list[$key]['problem_content'] = iconv('GB2312','UTF-8',$list[$key]['problem_content']);
            $list[$key]['follow_name'] = iconv('GB2312','UTF-8',$list[$key]['follow_name']);
        }
        # code...
    }
    $arr  = array
    (
    	'msg'  => 'success!',
    	'list' => $list 
    );
    echo json_encode($arr);
    
 ?>