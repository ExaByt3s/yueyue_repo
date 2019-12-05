<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];

//模板ID
$data['mid'] = '122LT02004';

$result['dmid']             = 'w1';
$result['text']             = '全部活动';
$result['text1']            = '最新上架抢先领福利';
$result['title']            = '全部活动';
$result['text_col']         = '0xff52a2e7';
$result['text1_col']        = '0xff7eb5e4';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&title='. urlencode(iconv('gbk', 'utf8', '全部活动'));
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w2';
$result['text']             = '室内创作';
$result['text1']            = '';
$result['title']            = '室内创作';
$result['text_col']         = '0xfffc8622';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '室内')) . '&title=' . urlencode(iconv('gbk', 'utf8', '室内创作')) ;
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '人像')) . '&title=' . urlencode(iconv('gbk', 'utf8', '室内创作')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w3';
$result['text']             = '室外创作';
$result['text1']            = '';
$result['title']            = '室外创作';
$result['text_col']         = '0xff4cbfad';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '室外')) . '&title=' . urlencode(iconv('gbk', 'utf8', '室外创作'));
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w4';
$result['text']             = '旅拍创作';
$result['text1']            = '';
$result['title']            = '旅拍创作';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '旅拍')) . '&title=' . urlencode(iconv('gbk', 'utf8', '旅拍创作'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '天台')) . '&title=' . urlencode(iconv('gbk', 'utf8', '旅拍创作')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w5';
$result['text']             = '精品活动';
$result['text1']            = '';
$result['title']            = '精品活动';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&remarks_querys=is_top&title=' . urlencode(iconv('gbk', 'utf8', '精品活动'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '活动')) . '&title=' . urlencode(iconv('gbk', 'utf8', '精品活动')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w6';
$result['text']             = '官方活动';
$result['text1']            = '';
$result['title']            = '官方活动';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&start_querys=start_by_authority&title=' . urlencode(iconv('gbk', 'utf8', '官方活动'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', 'POCO')) . '&title=' . urlencode(iconv('gbk', 'utf8', '官方活动')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w7';
$result['text']             = '网友活动';
$result['text1']            = '';
$result['title']            = '网友活动';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&start_querys=start_by_net_friends&title=' . urlencode(iconv('gbk', 'utf8', '网友活动'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '春天的')) . '&title=' . urlencode(iconv('gbk', 'utf8', '网友活动')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);


$options['data'] = $data;

$cp->output($options);
?>