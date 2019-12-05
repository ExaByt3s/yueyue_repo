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
    439=>array('location_id'=>101029001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    442=>array('location_id'=>101029001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    445=>array('location_id'=>101029001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    448=>array('location_id'=>101029001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    454=>array('location_id'=>101029001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    457=>array('location_id'=>101029001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    460=>array('location_id'=>101001001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    463=>array('location_id'=>101001001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    466=>array('location_id'=>101001001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    469=>array('location_id'=>101001001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    475=>array('location_id'=>101001001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    478=>array('location_id'=>101001001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    481=>array('location_id'=>101003001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    484=>array('location_id'=>101003001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    487=>array('location_id'=>101003001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    490=>array('location_id'=>101003001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    496=>array('location_id'=>101003001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    499=>array('location_id'=>101003001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    502=>array('location_id'=>101022001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    505=>array('location_id'=>101022001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    508=>array('location_id'=>101022001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    511=>array('location_id'=>101022001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    517=>array('location_id'=>101022001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    520=>array('location_id'=>101022001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    523=>array('location_id'=>101004001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    526=>array('location_id'=>101004001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    529=>array('location_id'=>101004001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    532=>array('location_id'=>101004001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    538=>array('location_id'=>101004001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    541=>array('location_id'=>101004001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    544=>array('location_id'=>101015001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    547=>array('location_id'=>101015001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    550=>array('location_id'=>101015001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    553=>array('location_id'=>101015001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    559=>array('location_id'=>101015001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    562=>array('location_id'=>101015001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    1057=>array('location_id'=>101029001, 'type_id'=>43, 'type_name'=>'Լ��Ȥ'),
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
    440=>array('location_id'=>101029001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    443=>array('location_id'=>101029001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    446=>array('location_id'=>101029001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    449=>array('location_id'=>101029001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    455=>array('location_id'=>101029001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    458=>array('location_id'=>101029001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    461=>array('location_id'=>101001001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    464=>array('location_id'=>101001001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    467=>array('location_id'=>101001001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    470=>array('location_id'=>101001001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    476=>array('location_id'=>101001001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    479=>array('location_id'=>101001001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    482=>array('location_id'=>101003001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    485=>array('location_id'=>101003001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    488=>array('location_id'=>101003001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    491=>array('location_id'=>101003001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    497=>array('location_id'=>101003001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    500=>array('location_id'=>101003001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    503=>array('location_id'=>101022001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    506=>array('location_id'=>101022001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    509=>array('location_id'=>101022001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    512=>array('location_id'=>101022001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    518=>array('location_id'=>101022001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    521=>array('location_id'=>101022001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    524=>array('location_id'=>101004001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    527=>array('location_id'=>101004001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    530=>array('location_id'=>101004001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    533=>array('location_id'=>101004001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    539=>array('location_id'=>101004001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    542=>array('location_id'=>101004001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    545=>array('location_id'=>101015001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    548=>array('location_id'=>101015001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    551=>array('location_id'=>101015001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    554=>array('location_id'=>101015001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    560=>array('location_id'=>101015001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    563=>array('location_id'=>101015001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    1058=>array('location_id'=>101029001, 'type_id'=>43, 'type_name'=>'Լ��Ȥ'),
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
    441=>array('location_id'=>101029001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    444=>array('location_id'=>101029001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    447=>array('location_id'=>101029001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    450=>array('location_id'=>101029001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    456=>array('location_id'=>101029001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    459=>array('location_id'=>101029001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    462=>array('location_id'=>101001001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    465=>array('location_id'=>101001001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    468=>array('location_id'=>101001001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    471=>array('location_id'=>101001001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    477=>array('location_id'=>101001001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    480=>array('location_id'=>101001001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    483=>array('location_id'=>101003001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    486=>array('location_id'=>101003001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    489=>array('location_id'=>101003001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    492=>array('location_id'=>101003001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    498=>array('location_id'=>101003001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    501=>array('location_id'=>101003001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    504=>array('location_id'=>101022001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    507=>array('location_id'=>101022001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    510=>array('location_id'=>101022001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    513=>array('location_id'=>101022001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    519=>array('location_id'=>101022001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    522=>array('location_id'=>101022001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    525=>array('location_id'=>101004001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    528=>array('location_id'=>101004001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    531=>array('location_id'=>101004001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    534=>array('location_id'=>101004001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    540=>array('location_id'=>101004001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    543=>array('location_id'=>101004001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    546=>array('location_id'=>101015001, 'type_id'=>31, 'type_name'=>'ģ����Լ'),
    549=>array('location_id'=>101015001, 'type_id'=>5, 'type_name'=>'��Ӱ��ѵ'),
    552=>array('location_id'=>101015001, 'type_id'=>12, 'type_name'=>'Ӱ������'),
    555=>array('location_id'=>101015001, 'type_id'=>3, 'type_name'=>'��ױ����'),
    561=>array('location_id'=>101015001, 'type_id'=>40, 'type_name'=>'��Ӱ����'),
    564=>array('location_id'=>101015001, 'type_id'=>41, 'type_name'=>'Լ��ʳ'),
    1059=>array('location_id'=>101029001, 'type_id'=>43, 'type_name'=>'Լ��Ȥ'),
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