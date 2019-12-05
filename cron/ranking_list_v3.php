<?php
set_time_limit(0);
ignore_user_abort(true);
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/8/31
 * Time: 18:56
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$cms_obj        = new cms_system_class();
$mall_goods_obj = POCO::singleton('pai_mall_goods_class');

$new_list = array(
    439=>array('location_id'=>101029001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    442=>array('location_id'=>101029001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    445=>array('location_id'=>101029001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    448=>array('location_id'=>101029001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    454=>array('location_id'=>101029001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    457=>array('location_id'=>101029001, 'type_id'=>41, 'type_name'=>'约美食'),
    460=>array('location_id'=>101001001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    463=>array('location_id'=>101001001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    466=>array('location_id'=>101001001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    469=>array('location_id'=>101001001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    475=>array('location_id'=>101001001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    478=>array('location_id'=>101001001, 'type_id'=>41, 'type_name'=>'约美食'),
    481=>array('location_id'=>101003001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    484=>array('location_id'=>101003001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    487=>array('location_id'=>101003001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    490=>array('location_id'=>101003001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    496=>array('location_id'=>101003001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    499=>array('location_id'=>101003001, 'type_id'=>41, 'type_name'=>'约美食'),
    502=>array('location_id'=>101022001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    505=>array('location_id'=>101022001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    508=>array('location_id'=>101022001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    511=>array('location_id'=>101022001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    517=>array('location_id'=>101022001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    520=>array('location_id'=>101022001, 'type_id'=>41, 'type_name'=>'约美食'),
    523=>array('location_id'=>101004001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    526=>array('location_id'=>101004001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    529=>array('location_id'=>101004001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    532=>array('location_id'=>101004001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    538=>array('location_id'=>101004001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    541=>array('location_id'=>101004001, 'type_id'=>41, 'type_name'=>'约美食'),
    544=>array('location_id'=>101015001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    547=>array('location_id'=>101015001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    550=>array('location_id'=>101015001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    553=>array('location_id'=>101015001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    559=>array('location_id'=>101015001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    562=>array('location_id'=>101015001, 'type_id'=>41, 'type_name'=>'约美食'),
    1057=>array('location_id'=>101029001, 'type_id'=>43, 'type_name'=>'约有趣'),
);

foreach($new_list AS $key=>$val)
{
    $data['location_id'] = $val['location_id'];
    $data['type_id']     = $val['type_id'];
    $result = $mall_goods_obj->newgoods_list($data,'0,10');
    if(count($result['data']) > 0)
    {
        $rank_id = $key;
        $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);
        if($issue_id)
        {
            foreach($result['data'] AS $k=>$v)
            {
                $cms_obj->add_record_by_issue_id($issue_id, $v['goods_id'], '', $v['add_time']);
            }
            unset($issue_id);
            unset($rank_id);
        }
    }
}


$hot_list = array(
    440=>array('location_id'=>101029001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    443=>array('location_id'=>101029001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    446=>array('location_id'=>101029001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    449=>array('location_id'=>101029001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    455=>array('location_id'=>101029001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    458=>array('location_id'=>101029001, 'type_id'=>41, 'type_name'=>'约美食'),
    461=>array('location_id'=>101001001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    464=>array('location_id'=>101001001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    467=>array('location_id'=>101001001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    470=>array('location_id'=>101001001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    476=>array('location_id'=>101001001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    479=>array('location_id'=>101001001, 'type_id'=>41, 'type_name'=>'约美食'),
    482=>array('location_id'=>101003001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    485=>array('location_id'=>101003001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    488=>array('location_id'=>101003001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    491=>array('location_id'=>101003001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    497=>array('location_id'=>101003001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    500=>array('location_id'=>101003001, 'type_id'=>41, 'type_name'=>'约美食'),
    503=>array('location_id'=>101022001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    506=>array('location_id'=>101022001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    509=>array('location_id'=>101022001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    512=>array('location_id'=>101022001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    518=>array('location_id'=>101022001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    521=>array('location_id'=>101022001, 'type_id'=>41, 'type_name'=>'约美食'),
    524=>array('location_id'=>101004001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    527=>array('location_id'=>101004001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    530=>array('location_id'=>101004001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    533=>array('location_id'=>101004001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    539=>array('location_id'=>101004001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    542=>array('location_id'=>101004001, 'type_id'=>41, 'type_name'=>'约美食'),
    545=>array('location_id'=>101015001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    548=>array('location_id'=>101015001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    551=>array('location_id'=>101015001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    554=>array('location_id'=>101015001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    560=>array('location_id'=>101015001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    563=>array('location_id'=>101015001, 'type_id'=>41, 'type_name'=>'约美食'),
    1058=>array('location_id'=>101029001, 'type_id'=>43, 'type_name'=>'约有趣'),
);

foreach($hot_list AS $key=>$val)
{
    $data['location_id'] = $val['location_id'];
    $data['type_id']     = $val['type_id'];
    $data['start_time']  = date('Ymd', time()-3600*24*7);
    $data['end_time']    = date('Ymd', time());

    $result = $mall_goods_obj->hotgoods_list($data, '0,10');
    if(count($result['data']) > 0)
    {
        $rank_id = $key;
        $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);
        if($issue_id)
        {
            foreach($result['data'] AS $k=>$v)
            {
                $cms_obj->add_record_by_issue_id($issue_id, $v['goods_id'], '', 0);
            }
            unset($issue_id);
            unset($rank_id);
        }
    }
}

$mall_list = array(
    441=>array('location_id'=>101029001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    444=>array('location_id'=>101029001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    447=>array('location_id'=>101029001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    450=>array('location_id'=>101029001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    456=>array('location_id'=>101029001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    459=>array('location_id'=>101029001, 'type_id'=>41, 'type_name'=>'约美食'),
    462=>array('location_id'=>101001001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    465=>array('location_id'=>101001001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    468=>array('location_id'=>101001001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    471=>array('location_id'=>101001001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    477=>array('location_id'=>101001001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    480=>array('location_id'=>101001001, 'type_id'=>41, 'type_name'=>'约美食'),
    483=>array('location_id'=>101003001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    486=>array('location_id'=>101003001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    489=>array('location_id'=>101003001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    492=>array('location_id'=>101003001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    498=>array('location_id'=>101003001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    501=>array('location_id'=>101003001, 'type_id'=>41, 'type_name'=>'约美食'),
    504=>array('location_id'=>101022001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    507=>array('location_id'=>101022001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    510=>array('location_id'=>101022001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    513=>array('location_id'=>101022001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    519=>array('location_id'=>101022001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    522=>array('location_id'=>101022001, 'type_id'=>41, 'type_name'=>'约美食'),
    525=>array('location_id'=>101004001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    528=>array('location_id'=>101004001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    531=>array('location_id'=>101004001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    534=>array('location_id'=>101004001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    540=>array('location_id'=>101004001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    543=>array('location_id'=>101004001, 'type_id'=>41, 'type_name'=>'约美食'),
    546=>array('location_id'=>101015001, 'type_id'=>31, 'type_name'=>'模特邀约'),
    549=>array('location_id'=>101015001, 'type_id'=>5, 'type_name'=>'摄影培训'),
    552=>array('location_id'=>101015001, 'type_id'=>12, 'type_name'=>'影棚租赁'),
    555=>array('location_id'=>101015001, 'type_id'=>3, 'type_name'=>'化妆服务'),
    561=>array('location_id'=>101015001, 'type_id'=>40, 'type_name'=>'摄影服务'),
    564=>array('location_id'=>101015001, 'type_id'=>41, 'type_name'=>'约美食'),
    1059=>array('location_id'=>101029001, 'type_id'=>43, 'type_name'=>'约有趣'),
);

$seller_obj = POCO::singleton('pai_mall_seller_class');

foreach($mall_list AS $key=>$val)
{
    $data['location_id'] = $val['location_id'];
    $data['type_id']     = $val['type_id'];
    $data['start_time']  = date('Ymd', time()-3600*24*7);
    $data['end_time']    = date('Ymd', time());

    $result = $seller_obj->seller_commenttop_list(7,$data['location_id'],$data['type_id'],10);
    if(count($result) > 0)
    {
        $rank_id = $key;
        $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);
        if($issue_id)
        {
            foreach($result AS $k=>$v)
            {
                $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], '', 0);
            }
            unset($issue_id);
            unset($rank_id);
        }
    }
}