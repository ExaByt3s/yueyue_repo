<?php
/**
 * 优惠券发放
 * @author Henry
 * @copyright 2015-04-11
 */

class pai_coupon_give_class extends POCO_TDG
{
	private $give_code_list = array(
		
			/*
			//测试
			'DEMO' => array(
					'coupon_package' => array(
							array('batch_id' => 1, 'quantity' => 1),
							array('batch_id' => 2, 'quantity' => 2),
					),
					'message_info' => array(
							'content' => '恭喜您成功注册，约约为您准备了丰富的优惠礼包，点击链接立即约拍5折女神！',
							'to_url' => '/mobile/app?from_app=1#topic/112',
					),
			),
			*/
			
			//2015年5月起，至年底，充值送优惠，300元优惠礼包
			'Y2015M05D01_RECHARGE_300' => array(
					'coupon_package' => array(
							array('batch_id' => 83, 'quantity' => 4, 'coupon_days'=>90 ), //外拍券5元
							array('batch_id' => 84, 'quantity' => 4, 'coupon_days'=>90), //外拍券10元
							array('batch_id' => 85, 'quantity' => 6, 'coupon_days'=>90), //约拍券10元
							array('batch_id' => 86, 'quantity' => 4, 'coupon_days'=>90), //约拍券20元
							array('batch_id' => 87, 'quantity' => 2, 'coupon_days'=>90), //约拍券50元
					),
					'message_info' => array(
						'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
						'to_url' => '',
					),
			),
			
			//2015年5月起，至年底，充值送优惠，500元优惠礼包
			'Y2015M05D01_RECHARGE_500' => array(
					'coupon_package' => array(
							array('batch_id' => 88, 'quantity' => 4, 'coupon_days'=>90), //外拍券5元
							array('batch_id' => 89, 'quantity' => 8, 'coupon_days'=>90), //外拍券10元
							array('batch_id' => 90, 'quantity' => 8, 'coupon_days'=>90), //约拍券10元
							array('batch_id' => 91, 'quantity' => 6, 'coupon_days'=>90), //约拍券20元
							array('batch_id' => 92, 'quantity' => 4, 'coupon_days'=>90), //约拍券50元
					),
					'message_info' => array(
						'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
						'to_url' => '',
					),
			),
			
			/*
			//2015年5月起，约惠卡专属礼包
			'Y2015M05D01_RECHARGE_CARD' => array(
				'coupon_package' => array(
					array('batch_id' => 93, 'quantity' => 1), //火爆网红约惠券100元
					array('batch_id' => 94, 'quantity' => 1), //线上培训体验券100元
					array('batch_id' => 95, 'quantity' => 1), //线下培训体验券200元
					array('batch_id' => 96, 'quantity' => 1), //精品外拍体验券100元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值，约惠卡帮你解决摄影史上3大难题。约模特、约活动、约培训，你意想不到的超值！点击链接马上约起来！',
					'to_url' => '/mobile/app?from_app=1#topic/170',
				),
			),
			*/
			
			//2015年8月起，至2015年10月，充值送优惠，500元优惠礼包
			'Y2015M08D01_RECHARGE_500' => array(
				'coupon_package' => array(
					array('batch_id' => 155, 'quantity' => 10), //约拍券20元
					array('batch_id' => 156, 'quantity' => 6), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),
			
			//2015年8月起，至2015年10月，充值送优惠，300元优惠礼包
			'Y2015M08D01_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 177, 'quantity' => 10), //约拍券20元
					array('batch_id' => 178, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),

			//2015年9月起，至2015年12月31日，充值送优惠，300元优惠礼包
			'Y2015M09D30_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 469, 'quantity' => 10), //约拍券20元
					array('batch_id' => 468, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),
			
			//2015年10月起，注册用户
			'Y2015M10D01_USER_REG' => array(
				'coupon_package' => array(
					array('batch_id' => 506, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '恭喜您成功注册，约约为您准备了丰富的优惠礼包，详情请点击 我的 － 优惠券内查看。感谢你对约约的支持。',
					'to_url' => '',
				),
			),
			
			//2015年10月11日起，注册用户
			'Y2015M10D11_USER_REG' => array(
				'coupon_package' => array(
					array('batch_id' => 529, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '恭喜您成功注册，约约为您准备了丰富的优惠礼包，详情请点击 我的 － 优惠券内查看。感谢你对约约的支持。',
					'to_url' => '',
				),
			),
			
			//2015年10月消费回馈，30元礼包
			'Y2015M10D01_CONSUMPTION_BACK_30' => array(
				'coupon_package' => array(
					array('batch_id' => 507, 'quantity' => 1),
					array('batch_id' => 508, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您上月订单消费金额赠送您对应的优惠券，详情请点击 我的 － 优惠券内查看。感谢你对约约的支持。',
					'to_url' => '',
				),
			),
			
			//2015年10月消费回馈，50元礼包
			'Y2015M10D01_CONSUMPTION_BACK_50' => array(
				'coupon_package' => array(
					array('batch_id' => 509, 'quantity' => 1),
					array('batch_id' => 510, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您上月订单消费金额赠送您对应的优惠券，详情请点击 我的 － 优惠券内查看。感谢你对约约的支持。',
					'to_url' => '',
				),
			),
		
			//2015年10月消费回馈，100元礼包
			'Y2015M10D01_CONSUMPTION_BACK_100' => array(
				'coupon_package' => array(
					array('batch_id' => 511, 'quantity' => 1),
					array('batch_id' => 512, 'quantity' => 1),
					array('batch_id' => 513, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您上月订单消费金额赠送您对应的优惠券，详情请点击 我的 － 优惠券内查看。感谢你对约约的支持。',
					'to_url' => '',
				),
			),
			
			//2015年10月消费回馈，200元礼包
			'Y2015M10D01_CONSUMPTION_BACK_200' => array(
				'coupon_package' => array(
					array('batch_id' => 514, 'quantity' => 2),
					array('batch_id' => 515, 'quantity' => 2),
					array('batch_id' => 516, 'quantity' => 2),
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您上月订单消费金额赠送您对应的优惠券，详情请点击 我的 － 优惠券内查看。感谢你对约约的支持。',
					'to_url' => '',
				),
			),
			
			//2015年9月起，约约精品人像
			'Y2015M09D08_WAIPAI_YUEJP' => array(
				'coupon_package' => array(
					array('batch_id' => 292, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，感谢您参加了本次“约约精品人像”活动，约约现赠送您25元的优惠礼包，详情请点击【我的 －优惠券】内查看。再次感谢你对约约的支持。',
					'to_url' => '',
				),
			),
			
			//2015年8月28起，至2015年11月，充值送优惠，300元优惠礼包
			'Y2015M08D28_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 251, 'quantity' => 10), //约拍券20元
					array('batch_id' => 252, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年9月30，评价送优惠，测试券 ，测试
			'Y2015M09D07_COMMENT_APP_1' => array(
				'coupon_package' => array(
					array('batch_id' => 123, 'quantity' => 1), //测试用通用券1元
				),
				'message_info' => array(
					'content' => '感谢您评价，有你的支持约约将更加努力（测试文案）！',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券，模特服务
			'Y2015M09D08_CONSUMPTION_BACK_200-299' => array(
				'coupon_package' => array(
					array('batch_id' => 293, 'quantity' => 1), //消费满额赠券20元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_300-499' => array(
				'coupon_package' => array(
					array('batch_id' => 294, 'quantity' => 1), //消费满额赠券30元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_500-999' => array(
				'coupon_package' => array(
					array('batch_id' => 295, 'quantity' => 1), //消费满额赠券50元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_1000-1999' => array(
				'coupon_package' => array(
					array('batch_id' => 296, 'quantity' => 1), //消费满额赠券100元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_2000' => array(
				'coupon_package' => array(
					array('batch_id' => 297, 'quantity' => 1), //消费满额赠券200元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券，化妆服务
			'Y2015M09D08_CONSUMPTION_BACK_200_299_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 458, 'quantity' => 1), //消费满额赠券20元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_300_499_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 456, 'quantity' => 1), //消费满额赠券30元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_500_999_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 454, 'quantity' => 1), //消费满额赠券50元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_1000_1999_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 452, 'quantity' => 1), //消费满额赠券100元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_2000_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 450, 'quantity' => 1), //消费满额赠券200元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券，影棚租赁
			'Y2015M09D08_CONSUMPTION_BACK_200_299_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 459, 'quantity' => 1), //消费满额赠券20元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_300_499_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 457, 'quantity' => 1), //消费满额赠券30元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_500_999_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 455, 'quantity' => 1), //消费满额赠券50元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_1000_1999_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 453, 'quantity' => 1), //消费满额赠券100元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),

			//2015年9月1起，至2015年10月12，消费满额赠券
			'Y2015M09D08_CONSUMPTION_BACK_2000_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 451, 'quantity' => 1), //消费满额赠券200元
				),
				'message_info' => array(
					'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
					'to_url' => '',
				),
			),
			
			//2015年9月18起，至2015年12月，充值送优惠，300元优惠礼包
			'Y2015M09D18_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 361, 'quantity' => 10), //约拍券20元
					array('batch_id' => 362, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),

			//2015年10月起，至2016年1月31日，充值送优惠，300元优惠礼包
			'Y2015M10D16_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 536, 'quantity' => 10), //约拍券20元
					array('batch_id' => 533, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),

			//2015年11月起，至2016年3月31日，充值300元送优惠，300元优惠礼包
			'Y2015M11D05_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 592, 'quantity' => 10), //约拍券20元
					array('batch_id' => 593, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),

			//2015年11月起，至2016年3月31日，充值500元送优惠，300元优惠礼包
			'Y2015M11D05_RECHARGE_500' => array(
				'coupon_package' => array(
					array('batch_id' => 594, 'quantity' => 10), //约拍券20元
					array('batch_id' => 595, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),

			//2015年11月09日~2015年12月10日，注册用户
			'Y2015M11D09_USER_REG' => array(
				'coupon_package' => array(
					array('batch_id' => 599, 'quantity' => 1),
				),
				'message_info' => array(
                    'msg_type' => 2, 
					'content' => '恭喜您成功注册，约约为您准备了丰富的优惠礼包，详情请点击 我的 － 优惠券内查看。感谢你对约约的支持。',
					'to_url' => '',
                    'card_text1' => '终于等到你！1元超值爆品，惊喜不会停',
                    'card_title' => '优惠券已赠，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
				),
			),

			//2015年11月09日~2015年12月10日，注册新用户，次日送
			'Y2015M11D09_USER_REG_TOMORROW' => array(
				'coupon_package' => array(
					array('batch_id' => 600, 'quantity' => 1),
				),
				'message_info' => array(
                    'msg_type' => 2, 
					'content' => '',
					'to_url' => '',
                    'card_text1' => '惊喜又来了！约遍所有姿势，我都为你买单！',
                    'card_title' => '优惠券已赠，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
				),
			),

			//2015年11月09日至2015年12月10日，100-8
			'Y2015M11D09_CONSUMPTION_BACK_8' => array(
				'coupon_package' => array(
					array('batch_id' => 601, 'quantity' => 1), //8元通用券
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
                    'to_url' => '',
                    'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                    'card_title' => '优惠券已赠送，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015年11月09日至2015年12月10日，200-18
			'Y2015M11D09_CONSUMPTION_BACK_18' => array(
				'coupon_package' => array(
					array('batch_id' => 602, 'quantity' => 1), //18元通用券
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
                    'to_url' => '',
                    'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                    'card_title' => '优惠券已赠送，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015年11月09日至2015年12月10日，300-28
			'Y2015M11D09_CONSUMPTION_BACK_28' => array(
				'coupon_package' => array(
					array('batch_id' => 603, 'quantity' => 1), //28元通用券
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
                    'to_url' => '',
                    'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                    'card_title' => '优惠券已赠送，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015年11月09日至2015年12月10日，500-38
			'Y2015M11D09_CONSUMPTION_BACK_38' => array(
				'coupon_package' => array(
					array('batch_id' => 604, 'quantity' => 1), //38元通用券
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
                    'to_url' => '',
                    'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                    'card_title' => '优惠券已赠送，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015年11月09日至2015年12月10日，600-48
			'Y2015M11D09_CONSUMPTION_BACK_48' => array(
				'coupon_package' => array(
					array('batch_id' => 605, 'quantity' => 1), //48元通用券
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
                    'to_url' => '',
                    'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                    'card_title' => '优惠券已赠送，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015年11月09日至2015年12月10日，1000-68
			'Y2015M11D09_CONSUMPTION_BACK_68' => array(
				'coupon_package' => array(
					array('batch_id' => 606, 'quantity' => 1), //68元通用券
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '亲爱的用户您好，约约将根据您本次订单消费金额赠送您对应的优惠券，详情请点击【我的 － 优惠券】内查看。感谢您对约约的支持。',
                    'to_url' => '',
                    'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                    'card_title' => '优惠券已赠送，马上使用！',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015年11月16日至2015年11月21日，分享活动赠券
            'Y2015M11D16_SHARE_EVENT_60559' => array(
                'coupon_package' => array(
                    array('batch_id' => 636, 'quantity' => 1), //10元100通用
                ),
                'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '恒大之夜”优惠劵已发送到您的约约账户，马上使用即可享受优惠',
                    'to_url' => '',
                    'card_text1' => '“恒大之夜”优惠劵已发送到您的约约账户，马上使用即可享受优惠',
                    'card_title' => '优惠券已赠，马上使用！',
                    'link_url' => '/mall/user/act/detail.php?event_id=60559',
                ),
            ),

			//2015年11月16日至2016年3月31日
            'Y2015M11D16_RECHARGE_1000' => array(
                'coupon_package' => array(
                    array('batch_id' => 638, 'quantity' => 6), //约拍券50元
                    array('batch_id' => 637, 'quantity' => 10), //约拍券20元
                ),
                'message_info' => array(
                    'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
                    'to_url' => '',
                ),
            ),
			
			//2015年11月起，至2016年3月31日，充值300元送优惠，300元优惠礼包
			'Y2015M11D25_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 643, 'quantity' => 10), //约拍券20元
					array('batch_id' => 644, 'quantity' => 2), //约拍券50元
				),
				'message_info' => array(
					'content' => '恭喜你成功充值！赠送的优惠券礼包已发放到您的账户，敬请留意！',
					'to_url' => '',
				),
			),

			//2015年11月16日至2016年3月31日
            'Y2015M11D27_ONE_BUCK_1' => array(
                'coupon_package' => array(
                    array('batch_id' => 649, 'quantity' => 1), //约拍券50元
                ),
            ),

			//2015年11月16日至2016年3月31日
            'Y2015M11D27_ONE_BUCK_2' => array(
                'coupon_package' => array(
                    array('batch_id' => 647, 'quantity' => 1), //约拍券50元
                ),
            ),

			//2015年11月16日至2016年3月31日
            'Y2015M11D27_ONE_BUCK_3' => array(
                'coupon_package' => array(
                    array('batch_id' => 648, 'quantity' => 1), //约拍券50元
                ),
            ),

			//2015年11月16日至2016年3月31日
            'Y2015M11D27_ONE_BUCK_4' => array(
                'coupon_package' => array(
                    array('batch_id' => 650, 'quantity' => 1), //约拍券50元
                ),
            ),

			//2015年11月16日至2016年3月31日
            'Y2015M11D27_ONE_BUCK_5' => array(
                'coupon_package' => array(
                    array('batch_id' => 645, 'quantity' => 1), //约拍券50元
                ),
            ),

			//2015年11月16日至2016年3月31日
            'Y2015M11D27_ONE_BUCK_6' => array(
                'coupon_package' => array(
                    array('batch_id' => 646, 'quantity' => 1), //约拍券50元
                ),
            ),
	);
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_coupon_db');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_give_queue_tbl()
	{
		$this->setTableName('coupon_give_queue_tbl');
	}
	
	/**
	 * 获取发放标识配置列表
	 * @return array
	 */
	public function get_give_code_list()
	{
		return $this->give_code_list;
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_queue($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_give_queue_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 修改
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	private function update_queue($data, $id)
	{
		$id = intval($id);
		if( !is_array($data) || empty($data) || $id<1 )
		{
			return false;
		}
		$this->set_coupon_give_queue_tbl();
		$this->update($data, "id={$id}");
		return true;
	}
	
	/**
	 * 更新已发放
	 * @param int $id
	 * @param array $more_info array('lately_time'=>0)
	 * @return boolean
	 */
	public function update_queue_give($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_give_queue_tbl();
		$affected_rows = $this->update($data, "id={$id} AND status=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新不发放
	 * @param int $id
	 * @param array $more_info array('lately_time'=>0)
	 * @return boolean
	 */
	public function update_queue_ungive($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => 2,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_give_queue_tbl();
		$affected_rows = $this->update($data, "id={$id} AND status=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新取消
	 * @param int $id
	 * @param array $more_info array('lately_time'=>0)
	 * @return boolean
	 */
	public function update_queue_cancel($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => 7,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_give_queue_tbl();
		$affected_rows = $this->update($data, "id={$id} AND status=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取列表
	 * @param int $status -1表示不限制
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_queue_list($status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$status = intval($status);
		
		//整理查询条件
		$sql_where = '';
		
		if( $status>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "status={$status}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_coupon_give_queue_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 提交发放
	 * 用户ID或手机号码，必须填写一个；
	 * 两者都填写时，优先手机号码。
	 * @param string $give_code 发放标识
	 * @param string $cellphone 手机号码
	 * @param int $user_id 用户ID
	 * @param int $ref_id 关联ID，用于处理一个用户可以发放多次
	 * @param array $more_info array('date_id'=>0, 'event_id'=>0, 'enroll_id'=>0, 'url'=>'', 'remark'=>'', message_data => 'string')
	 * @return int
	 */
	public function submit_queue($give_code, $cellphone, $user_id, $ref_id, $more_info=array())
	{
		$give_code = trim($give_code);
		$cellphone = trim($cellphone);
		$user_id = intval($user_id);
		$ref_id = intval($ref_id);
		if( !is_array($more_info ) ) $more_info = array();
		if( strlen($give_code)<1 || (strlen($cellphone)<1 && $user_id<1) || $ref_id<0 )
		{
			return 0;
		}
		
		//发放标识未配置
		if( !array_key_exists($give_code, $this->give_code_list) )
		{
			return 0;
		}
		
		//补充信息
		$user_obj = POCO::singleton('pai_user_class');
		if( strlen($cellphone)>0 )
		{
			$user_info = $user_obj->get_user_by_phone($cellphone);
			$user_id = intval($user_info['user_id']);
		}
		else
		{
			$cellphone = $user_obj->get_phone_by_user_id($user_id);
			if( strlen($cellphone)<1 )
			{
				return 0;
			}
		}
		
		//保存入库
		$date_id = intval($more_info['date_id']);
		$event_id = intval($more_info['event_id']);
		$enroll_id = intval($more_info['enroll_id']);
		$url = trim($more_info['url']);
		$remark = trim($more_info['remark']);
		$message_data = serialize($more_info['message_data']);
		$data = array(
			'give_code' => $give_code,
			'cellphone' => $cellphone,
			'ref_id' => $ref_id,
			'user_id' => $user_id,
			'date_id' => $date_id,
			'event_id' => $event_id,
			'enroll_id' => $enroll_id,
			'url' => $url,
			'remark' => $remark,
			'add_time' => time(),
            'message_data' => $message_data, 
		);
		return $this->add_queue($data);
	}
	
	/**
	 * 发放优惠券
	 * @param array $queue_info
	 * @return array
	 */
	public function give_coupon_by_queue_info($queue_info)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn_arr'=>array());
		
		//检查参数
		if( !is_array($queue_info) || empty($queue_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$id = intval($queue_info['id']);
		$give_code = trim($queue_info['give_code']);
		$cellphone = trim($queue_info['cellphone']);
		$user_id = intval($queue_info['user_id']);
		$status = intval($queue_info['status']);
		$message_data = unserialize($queue_info['message_data']);
		
		//判断状态
		if( $status!=0 )
		{
			$result['result'] = -2;
			$result['message'] = '状态错误';
			return $result;
		}
		
		//发放标识未配置
		if( !array_key_exists($give_code, $this->give_code_list) )
		{
			$result['result'] = -3;
			$result['message'] = '发放标识未配置';
			return $result;
		}
		
		//获取配置信息
		$give_code_info = $this->give_code_list[$give_code];
		if( !is_array($give_code_info) ) $give_code_info = array();
		$coupon_package = $give_code_info['coupon_package'];
		if( !is_array($coupon_package) ) $coupon_package = array();
		$message_info = $give_code_info['message_info'];
		if( !is_array($message_info) ) $message_info = array();
		if( empty($give_code_info) || empty($coupon_package) )
		{
			$result['result'] = -4;
			$result['message'] = '发放标识配置错误';
			return $result;
		}
		
		//获取用户信息
		$user_obj = POCO::singleton('pai_user_class');
		if( $user_id>0 )
		{
			$user_info = $user_obj->get_user_info($user_id);
		}
		else
		{
			$user_info = $user_obj->get_user_by_phone($cellphone);
			$user_id = intval($user_info['user_id']);
		}
		if( empty($user_info) || $user_id<1 )
		{
			$result['result'] = -5;
			$result['message'] = '用户不存在';
			return $result;
		}
		
		$cur_time = time();
		
		/*
		//不发放给模特
		if( $user_info['role']=='model' )
		{
			$more_info = array('lately_time' => $cur_time);
			$this->update_queue_ungive($id, $more_info);
			
			$result['result'] = -6;
			$result['message'] = '用户是模特';
			return $result;
		}
		*/
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		$more_info = array('lately_time' => $cur_time);
		$ret = $this->update_queue_give($id, $more_info);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -7;
			$result['message'] = '状态失败';
			return $result;
		}
		
		//发放优惠券
		$coupon_is_give = true;
		$coupon_sn_arr = array();
		$coupon_obj = POCO::singleton('pai_coupon_class');
		foreach($coupon_package as $key=>$val)
		{
			$batch_id = intval($val['batch_id']);
			$quantity = intval($val['quantity']);
			$coupon_days = intval($val['coupon_days']);
			
			//检查批次是否有效
			$batch_info = $coupon_obj->get_batch_info($batch_id);
			if( empty($batch_info) || $batch_info['check_status']!=1 || ($coupon_days<1 && $batch_info['coupon_end_time']<$cur_time) )
			{
				$coupon_is_give = false;
				break;
			}
			
			//计算有效天数（自然天），领取之日算1天。
			if( $coupon_days>0 )
			{
				$start_time = strtotime( date('Y-m-d 00:00:00', $cur_time) );
				$end_time = strtotime( date('Y-m-d 23:59:59', $cur_time+($coupon_days-1)*24*3600) );
				$more_info = array('start_time'=>$start_time, 'end_time'=>$end_time);
			}
			else
			{
				$more_info = array();
			}
			
			//生成
			for($i=1; $i<=$quantity; $i++)
			{
				$ret = $coupon_obj->give_coupon_by_create($user_id, $batch_id, $more_info);
				if( $ret['result']!=1 )
				{
					$coupon_is_give = false;
					break;
				}
				$coupon_sn_arr[] = $ret['coupon_sn'];
			}
			if( !$coupon_is_give ) break;
		}
		if( !$coupon_is_give || empty($coupon_sn_arr) )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -8;
			$result['message'] = '发放失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
        if( !empty($message_data) )
        {
            POCO::singleton('pai_information_push')->message_sending_for_system($user_id, $message_data, 10002, 'yuebuyer');

            $card_text1 = $message_data['card_text1'];
            $card_title = $message_data['card_title'];
            $content = $message_data['content'];

            $msg_content = trim("{$card_text1}\r\n{$card_title}\r\n{$content}");
            if( strlen($msg_content)>0 )
            {
                //微信公众号模板消息
                $template_data = array(
                    'title' => '优惠消息提醒',
                    'content' => $msg_content,
                );

                // $message_data['link_url'] = 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131784_82';
                preg_match('/goods_id=([^&]+)/i', $message_data['link_url'] , $match);
                $goods_id = $match[1];
                $template_to_url = '';
                if( strlen($goods_id)>0 ) $template_to_url = 'http://yp.yueus.com/mall/user/goods/service_detail.php?goods_id=' . $goods_id;

                POCO::singleton('pai_weixin_pub_class')->message_template_send_by_user_id($user_id, 'G_PAI_WEIXIN_SYSTEM_NOTICE', $template_data, $template_to_url);
            }
        }
        elseif( !empty($message_info) )
		{
            $content = trim($message_info['content']);
            $to_url = trim($message_info['to_url']);
            $msg_type = intval($message_info['msg_type']);
            $link_url = 'http://yp.yueus.com'.trim($message_info['link_url']);
            $wifi_link_url = 'http://yp-wifi.yueus.com'.trim($message_info['link_url']);
            $card_text1 = trim($message_info['card_text1']);
            $card_title = trim($message_info['card_title']);
            if( $msg_type==2 )
            {
                $send_data = array(
                    'media_type' => 'card', // (text:文字; notify:系统通知; card:卡片)
                    'card_style' => 2, // (1:中间有金额那个模板; 2:只有一条文字那个模板)
                    'card_text1' => $card_text1, // (服务media_type=card的，最上标题)
                    'card_title' => $card_title, // (服务media_type=card的，底部)
                    'link_url' => 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url),
                );
                POCO::singleton('pai_information_push')->message_sending_for_system($user_id, $send_data, 10002, 'yuebuyer');
            }
            elseif( strlen($content)>0 )
            {
                send_message_for_10002($user_id, $content, $to_url, 'yuebuyer');
            }
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['coupon_sn_arr'] = $coupon_sn_arr;
		return $result;
	}

	/**
	 * 是否在评价界面显示赠券提示
	 * @param array $order_sn
	 * @return array
	 */
	public function show_tips_for_comment_interface($order_sn)
	{
        $rst = array('is_show' => false, 'tips' => '', );
        $order_sn = trim($order_sn);
        if( strlen($order_sn)<1 )
        {
            return $rst;
        }
        $order_obj = POCO::singleton('pai_mall_order_class');

        $order_info = $order_obj->get_order_full_info($order_sn);
        if( empty($order_info) )
        {
            return $rst;
        }
        $cur_time = time();
        $order_pending_amount = $order_info['pending_amount'];
        if($order_pending_amount >= 100 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59'))
        {
            $rst['is_show'] = true;
            $rst['tips'] = '亲，完成打分+评论，即可获得超值优惠券噢～';
            return $rst;
        }
        return $rst;
	}
}
