<?php
/**	
 * 问卷内容配置
 */
$config ['data_model'] ['total_page'] = 8;

$config ['data_model'] ['title'] = '发起约拍';

$config ['data_model'] ['total_data'] [0] = array (
		'page' => 1,
		'next' => 2,
		'title' => '你预想的拍摄时间？',
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

$config ['data_model'] ['total_data'] [2] = array (
		'page' => 3,
		'next' => 4,
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

$config ['data_model'] ['total_data'] [3] = array (
		'page' => 4,
		'next' => 5,
		'branch' => true,
		'title' => '你有定下拍摄场地么？ <br> 如果有，请输入拍摄场地方便模特选择。',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '有',
								'flag' => true,
								'next' => '4-1' 
						),
						1 => array (
								'text' => '没有',
								'flag' => false,
								'next' => '5' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [4] = array (
		'page' => '4-1',
		'next' => 5,
		'title' => '你有定下拍摄场地么？ <br> 如果有，请输入拍摄场地方便模特选择。',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => '请输入详细地址',
				'data' => array () ,
				'must_be_txt' => true
		),
		'btn' => '下一步' 
);

$config ['data_model'] ['total_data'] [5] = array (
		'page' => 5,
		'next' => 6,
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

$config ['data_model'] ['total_data'] [6] = array (
		'page' => 6,
		'next' => 7,
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

$config ['data_model'] ['total_data'] [7] = array (
		'page' => 7,
		'next' => 8,
		'title' => '还有其他想让模特知道的吗？',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => '选填项，比如身材、样貌等其他要求',
				'data' => array () 
		),
		'btn' => '发布我的需求',
		'submit' => true 
);

$config ['data_model'] ['total_data'] [8] = array (
		'page' => 8,
		'next' => - 1,
		'title' => "<div><i class='icon icon-success-max'></i></div><p class='p1'>发布成功</p><p class='p2'>稍后我们会通过消息给你推送模特，<br>请你留意系统消息。</p>",
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