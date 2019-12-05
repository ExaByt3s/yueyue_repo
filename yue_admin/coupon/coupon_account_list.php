<?php

/**
 *�����ϸ
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-30 17:00:45
 * @version 1
 */
 include_once ('common.inc.php');
 //�Ż�ȯ��
 $coupon_obj = POCO::singleton('pai_coupon_class');
 $tpl = new SmartTemplate('coupon_account_list.tpl.htm');

 $act = trim($_INPUT['act']);
 //Ĭ��Ϊ����µ���ϸ
 $begin_time = $_INPUT['begin_time'] ? trim($_INPUT['begin_time']): date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
 $end_time   = $_INPUT['end_time']  ? trim($_INPUT['end_time']) : date('Y-m-d', mktime(0,0,0,date('m'),date('t'),date('Y')));

 //ʣ����
 $coupon_account_info = $coupon_obj->get_coupon_account_info();
 //ÿ������ӽ����ٽ��
 $list = $coupon_obj->get_stat_coupon_list(strtotime($begin_time)
, strtotime($end_time)+24*3600);

 if(!is_array($list)) $list = array();

 $tpl->assign($coupon_account_info);
 $tpl->assign('begin_time', $begin_time);
 $tpl->assign('end_time', $end_time);
 $tpl->assign('list', $list);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();

 ?>