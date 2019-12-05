<?php
/**
 * @desc:   �����Ƿ����¼ܵĲ�Ʒ
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/29
 * @Time:   9:20
 * version: 1.0
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin_v2/new_rank/pai_cms_rank_class.inc.php');

$task_goods_obj = POCO::singleton('pai_mall_goods_class');  //��Ʒ��
$mall_seller_obj = POCO::singleton('pai_mall_seller_class'); //�̼���

$cms_rank_obj = new pai_cms_rank_class();//����
$where_str ="versions_id !=2";
$list = $cms_rank_obj->get_main_rank_list(false,'',0,0,'on',0,$where_str,'switch ASC,`order` DESC,`id` DESC,`add_time` DESC', "0,99999999");
if(!is_array($list)) $list = array();

$empty_arr = array();

foreach($list as $v){
    $rank_info_list = $cms_rank_obj->get_rank_info_list(false,$v['id'],'','on',0,'','switch ASC,`order` DESC,`id` DESC,`add_time` DESC','0,99999999');
    if(!is_array($rank_info_list)) $rank_info_list = array();
    foreach($rank_info_list as $val)
    {
        //��Ʒ���ӳ���
        preg_match('/goods_id=(\d)+/', $val['link_url'],$ret);
        if(is_array($ret) && !empty($ret))//���ڲ�Ʒ���ӳ���
        {
            $data = array();
            $reason = '��Ʒ������˻�����Ʒ��˲�ͨ��';
            $uss_arr = explode("=",$ret[0]);
            $data['goods_id'] = $uss_arr[1];
            $goods_info = $task_goods_obj->user_search_goods_list($data, '0,1');
            if(!is_array($goods_info)) $goods_info = array();
            $total = intval($goods_info['total']);
            if($total <1) $empty_arr[] = array('main_id'=> $v['id'],'rank_id'=>$val['id'],'reason'=> $reason,'location_id'=>$v['location_id']);
            unset($goods_info);
            unset($uss_arr);
            unset($ret);
        }
        //�̼��ж�
        preg_match('/seller_user_id=(\d)+/', $val['link_url'],$user_info);
        if(is_array($user_info) && !empty($user_info)) //�̼Ҵ�����������
        {
            $data = array();
            $reason = '�̼��¼ܻ����̼�δͨ�����';
            $uss_arr = explode("=",$user_info[0]);
            $data['keywords'] = $uss_arr[1];
            $seller_info = $mall_seller_obj->user_search_seller_list($data, '0,1');
            $total = intval($seller_info['total']);
            if($total <1) $empty_arr[] = array('main_id'=> $v['id'],'rank_id'=>$val['id'],'reason'=> $reason,'location_id'=>$v['location_id']);
            unset($seller_info);
            unset($uss_arr);
            unset($user_info);
        }

    }
    //exit;
}
$code = $cms_rank_obj->add_out_shelf_info($empty_arr);

echo $code;