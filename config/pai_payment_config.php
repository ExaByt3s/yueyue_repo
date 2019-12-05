<?php
/**
 * 支付配置
 * @author Henry
 * @copyright 2014-06-23
 */
return array(

	//支付系统所在目录
	'ecpay_app_dir' => '/disk/data/htdocs232/poco/ecpay',

	//支付系统所在目录
	'ecpay_app_dev_dir' => '/disk/data/htdocs232/poco/paytest',

	//充值，支付通知URL
	'recharge_notify_url' => 'http://yp.yueus.com/mobile/action2.2.0/pay_recharge_notify.php',
	
	//活动报名，支付通知URL
	'activity_notify_url' => 'http://yp.yueus.com/mobile/action2.2.0/pay_activity_notify.php',
	
	//约拍邀请，支付通知URL
	'date_notify_url' => 'http://yp.yueus.com/mobile/action2.2.0/pay_date_notify.php',
	
	//活动报名，支付通知URL
	'activity_pc_return_url' => 'http://event.poco.cn/activity_join_return_new.php',
	'activity_pc_notify_url' => 'http://event.poco.cn/activity_join_notify_new.php',
);
