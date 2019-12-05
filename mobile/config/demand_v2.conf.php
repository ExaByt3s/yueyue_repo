<?php
/**	
 * 问卷内容配置
 */


$config ['data_model'] ['total_page'] = 18;

$config ['data_model'] ['title'] = '发布需求';

$config ['data_model'] ['total_data'] [0] = array (
		'page' => 1,
		'next' => 2,
		'title' => '你需要摄影服务的时间？',
		'content' => array (
				'type' => 'time',
				'data' => array (
						0 => array (
								'text' => '选择一个时间' 
						) 
				)
				 
		),
		'btn' => '下一步' 
);

$config ['data_model'] ['total_data'] [1] = array (
		'page' => 2,
		'next' => 3,
		'branch' => true,
		'title' => '<p>你要选择的摄影服务是？</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '约拍服务',
								'flag' => true,
								'next' => '3' 
						),
						1 => array (
								'text' => '摄影培训服务',
								'flag' => false,
								'next' => 10
						) 
				)
				 
		),
		'btn' => '' 
);



/****************/
// 从这里开始进行分支路线
// 约拍服务的操作
$config ['data_model'] ['total_data'] [2] = array (
		'page' => 3,
		'next' => 4,
		'title' => '你想拍什么风格的模特？',
		'content' => array (
				'type' => 'list-select',
				'data' => array (
						0 => array (
								'text' => '性感',
								'son_txt' => array (
										0 => array (
												'text' => '真空' 
										),
										1 => array (
												'text' => '全裸' 
										),
										2 => array (
												'text' => '内衣/比基尼' 
										)

								) 
						),
						1 => array (
								'text' => '清新',
								'son_txt' => array (
										0 => array (
												'text' => '甜美' 
										),
										1 => array (
												'text' => '糖水' 
										),
										2 => array (
												'text' => '情绪' 
										)
								) 
						),
						2 => array (
								'text' => '街拍',
								'son_txt' => array (
										0 => array (
												'text' => '日韩' 
										),
										1 => array (
												'text' => '欧美' 
										)
								) 
						),
						3 => array (
								'text' => '复古',
								'son_txt' => array (
										0 => array (
												'text' => '古装' 
										),
										1 => array (
												'text' => '文艺复古' 
										)
								) 
						),
						4 => array (
								'text' => '商业',
								'son_txt' => array (
										0 => array (
												'text' => '礼仪' 
										),
										1 => array (
												'text' => '车展' 
										),
										2 => array (
												'text' => '走秀' 
										),
										3 => array (
												'text' => '淘宝' 
										) 
								) 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [3] = array (
		'page' => 4,
		'next' => 5,
		'title' => '你的预算是多少',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '100以下' 
						),
						1 => array (
								'text' => '101-300' 
						),
						2 => array (
								'text' => '301-500' 
						),
						3 => array (
								'text' => '501-800' 
						),
						4 => array (
								'text' => '801-1000' 
						),
						5 => array (
								'text' => '1000以上' 
						),
						6 => array (
								'text' => '视情况而定' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [4] = array (
		'page' => 5,
		'next' => 6,
		'branch' => true,
		'title' => '你有定下拍摄场地么？ <br> 如果有，请输入拍摄场地方便模特选择。',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '有',
								'flag' => true,
								'next' => "51" 
						),
						1 => array (
								'text' => '没有',
								'flag' => false,
								'next' => '6' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [5] = array (
		'page' => "51",
		'next' => 6,
		'title' => '你有定下拍摄场地么？ <br> 如果有，请输入拍摄场地方便模特选择。',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => '请输入详细地址',
				'data' => array () ,
				'must_be_txt' => true
		),
		'btn' => '下一步' 
);

$config ['data_model'] ['total_data'] [6] = array (
		'page' => 6,
		'next' => 7,
		'title' => '你需要约约为你提供配套服务么？',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '场地/化妆/服装/服务' 
						),
						1=> array (
								'text' => '不需要了' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [7] = array (
		'page' => 7,
		'next' => 8,
		'title' => '你希望多久内找到合适的模特？',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '没所谓，我不急' 
						),
						1 => array (
								'text' => '两天内' 
						),
						2 => array (
								'text' => '一天内' 
						),
						
						3 => array (
								'text' => '现在，马上就想约拍' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [8] = array (
		'page' => 8,
		'next' => 9,
		'title' => '还有其他想让模特知道的吗？',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => '选填项，比如身材、样貌等其他要求',
				'data' => array () 
		),
		'btn' => '发布我的需求',
		'submit' => true 
);



/****************/
// 从这里开始进行分支路线
// 摄影服务的操作
$config ['data_model'] ['total_data'] [9] = array (
		'page' => 9,
		'next' => 10,
		'title' => '<p>得到一个专业的培训课程</p><p>请选择您的地址</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '广州' 
						),
						1 => array (
								'text' => '武汉' 
						),
						2 => array (
								'text' => '北京' 
						),
						3 => array (
								'text' => '上海' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [10] = array (
		'page' => 10,
		'next' => 11,
		'title' => '<p>您参加摄影培训的学习目的是</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A、专业需求，想做专业摄影师' 
						),
						1 => array (
								'text' => 'B、兴趣爱好，业余时间拍摄照片' 
						),
						2 => array (
								'text' => 'C、工作需求，需要用到摄影技能' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [11] = array (
		'page' => 11,
		'next' => 12,
		'title' => '<p>您接触摄影多长时间了？</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A、1年以下' 
						),
						1 => array (
								'text' => 'B、1年-3年' 
						),
						2 => array (
								'text' => 'C、3年-5年' 
						),
						3 => array (
								'text' => 'D、5年以上' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [12] = array (
		'page' => 12,
		'next' => 13,
		'title' => '<p>分类摄影中，你期望着重学习哪一个方面？</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A、人像摄影' 
						),
						1 => array (
								'text' => 'B、商业摄影' 
						),
						2 => array (
								'text' => 'C、风光摄影' 
						),
						3 => array (
								'text' => 'D、人文纪实' 
						),
						4 => array (
								'text' => 'E、新闻摄影'
						),
						5 => array (
								'text' => 'F、生态摄影'
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [13] = array (
		'page' => 13,
		'next' => 14,
		'title' => '<p>通过参加课程您期望达到怎么样水平？</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A、初级水平' 
						),
						1 => array (
								'text' => 'B、中级水平' 
						),
						2 => array (
								'text' => 'C、高级水平' 
						),
						3 => array (
								'text' => 'D、大师级水平' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [14] = array (
		'page' => 14,
		'next' => 15,
		'title' => '<p>您希望的授课方式</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A、线上授课' 
						),
						1 => array (
								'text' => 'B、线下授课' 
						),
						2 => array (
								'text' => 'C、1对1' 
						),
						3 => array (
								'text' => 'D、都可以' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [15] = array (
		'page' => 15,
		'next' => 16,
		'title' => '<p>您能接受的课程心理价位是？</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A、1000-2000 ' 
						),
						1 => array (
								'text' => 'B、2000-3000' 
						),
						2 => array (
								'text' => 'C、3000-4000' 
						),
						3 => array (
								'text' => 'D、4000及以上' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [16] = array (
		'page' => 16,
		'next' => 17,
		'title' => '<p>还差一步完成，请填写您的手机号码？</p>',
		'btn' => '确定',
		'submit' => true,
		'content' => array (
				'type' => 'input',
				'placeholder' => '请填写您的手机号码',
				'data' => array () ,
				'input_type' => 'tel',
				'must_be_txt' => true
				
				 
		),
		
);

$config ['data_model'] ['total_data'] [17] = array (
		'page' => 18,
		'next' => -1,
		'title' => "<div><i class='icon icon-success-max'></i></div><p class='p1'>发布成功</p><p class='p2'>稍后我们会通过消息给你推送摄影服务，<br>请你留意系统消息。</p>",
		'content' => array (
				'type' => 'false',
				'data' => array () 
		),
		'btn' => '确定',
		'jump_page' => true,
		'jump_info' => array (
				'url' => '',
				'type' => '' 
		) 
);

if (isset ( $_GET ['json'] )) {
	header ( 'Content-Type: application/json' );
	
	echo json_encode ( $config );
} else {
	return $config;
}


?>