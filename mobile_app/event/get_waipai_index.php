<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];

//ģ��ID
$data['mid'] = '122LT02004';

$result['dmid']             = 'w1';
$result['text']             = 'ȫ���';
$result['text1']            = '�����ϼ������츣��';
$result['title']            = 'ȫ���';
$result['text_col']         = '0xff52a2e7';
$result['text1_col']        = '0xff7eb5e4';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&title='. urlencode(iconv('gbk', 'utf8', 'ȫ���'));
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w2';
$result['text']             = '���ڴ���';
$result['text1']            = '';
$result['title']            = '���ڴ���';
$result['text_col']         = '0xfffc8622';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '����')) . '&title=' . urlencode(iconv('gbk', 'utf8', '���ڴ���')) ;
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '����')) . '&title=' . urlencode(iconv('gbk', 'utf8', '���ڴ���')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w3';
$result['text']             = '���ⴴ��';
$result['text1']            = '';
$result['title']            = '���ⴴ��';
$result['text_col']         = '0xff4cbfad';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '����')) . '&title=' . urlencode(iconv('gbk', 'utf8', '���ⴴ��'));
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w4';
$result['text']             = '���Ĵ���';
$result['text1']            = '';
$result['title']            = '���Ĵ���';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '����')) . '&title=' . urlencode(iconv('gbk', 'utf8', '���Ĵ���'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '��̨')) . '&title=' . urlencode(iconv('gbk', 'utf8', '���Ĵ���')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w5';
$result['text']             = '��Ʒ�';
$result['text1']            = '';
$result['title']            = '��Ʒ�';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&remarks_querys=is_top&title=' . urlencode(iconv('gbk', 'utf8', '��Ʒ�'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '�')) . '&title=' . urlencode(iconv('gbk', 'utf8', '��Ʒ�')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w6';
$result['text']             = '�ٷ��';
$result['text1']            = '';
$result['title']            = '�ٷ��';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&start_querys=start_by_authority&title=' . urlencode(iconv('gbk', 'utf8', '�ٷ��'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', 'POCO')) . '&title=' . urlencode(iconv('gbk', 'utf8', '�ٷ��')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);

$result['dmid']             = 'w7';
$result['text']             = '���ѻ';
$result['text1']            = '';
$result['title']            = '���ѻ';
$result['text_col']         = '0xff000000';
$result['text1_col']        = '';
$result['bg_col']           = '0xffffffff';
$result['bg_col_over']      = '0xfffafafa';
$result['url']              = 'yueyue://goto?type=inner_app&pid=1220076&start_querys=start_by_net_friends&title=' . urlencode(iconv('gbk', 'utf8', '���ѻ'));
//if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk', 'utf8', '�����')) . '&title=' . urlencode(iconv('gbk', 'utf8', '���ѻ')) ;
$result['recom_num']        = '';
$result['icon']             = '';
$data['layout'][]           = $result;
unset($result);


$options['data'] = $data;

$cp->output($options);
?>