<?php
/**	
 * �ʾ���������
 */
$config ['data_model'] ['total_page'] = 8;

$config ['data_model'] ['title'] = '����Լ��';

$config ['data_model'] ['total_data'] [0] = array (
		'page' => 1,
		'next' => 2,
		'title' => '��Ԥ�������ʱ�䣿',
		'content' => array (
				'type' => 'time',
				'data' => array (
						0 => array (
								'text' => 'ѡ��һ��ʱ��' 
						) 
				)
				 
		),
		'btn' => '��һ��' 
);

$config ['data_model'] ['total_data'] [1] = array (
		'page' => 2,
		'next' => 3,
		'title' => '������ʲô����ģ�أ�',
		'content' => array (
				'type' => 'list-select',
				'data' => array (
						0 => array (
								'text' => '�Ը�',
								'son_txt' => array (
										0 => array (
												'text' => '���' 
										),
										1 => array (
												'text' => 'ȫ��' 
										),
										2 => array (
												'text' => '����/�Ȼ���' 
										)

								) 
						),
						1 => array (
								'text' => '����',
								'son_txt' => array (
										0 => array (
												'text' => '����' 
										),
										1 => array (
												'text' => '��ˮ' 
										),
										2 => array (
												'text' => '����' 
										)
								) 
						),
						2 => array (
								'text' => '����',
								'son_txt' => array (
										0 => array (
												'text' => '�պ�' 
										),
										1 => array (
												'text' => 'ŷ��' 
										)
								) 
						),
						3 => array (
								'text' => '����',
								'son_txt' => array (
										0 => array (
												'text' => '��װ' 
										),
										1 => array (
												'text' => '���ո���' 
										)
								) 
						),
						4 => array (
								'text' => '��ҵ',
								'son_txt' => array (
										0 => array (
												'text' => '����' 
										),
										1 => array (
												'text' => '��չ' 
										),
										2 => array (
												'text' => '����' 
										),
										3 => array (
												'text' => '�Ա�' 
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
		'title' => '���Ԥ���Ƕ���',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '100����' 
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
								'text' => '1000����' 
						),
						6 => array (
								'text' => '���������' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [3] = array (
		'page' => 4,
		'next' => 5,
		'branch' => true,
		'title' => '���ж������㳡��ô�� <br> ����У����������㳡�ط���ģ��ѡ��',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '��',
								'flag' => true,
								'next' => '4-1' 
						),
						1 => array (
								'text' => 'û��',
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
		'title' => '���ж������㳡��ô�� <br> ����У����������㳡�ط���ģ��ѡ��',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => '��������ϸ��ַ',
				'data' => array () ,
				'must_be_txt' => true
		),
		'btn' => '��һ��' 
);

$config ['data_model'] ['total_data'] [5] = array (
		'page' => 5,
		'next' => 6,
		'title' => '����ҪԼԼΪ���ṩ���׷���ô��',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '����/��ױ/��װ/����' 
						),
						1=> array (
								'text' => '����Ҫ��' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [6] = array (
		'page' => 6,
		'next' => 7,
		'title' => '��ϣ��������ҵ����ʵ�ģ�أ�',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'û��ν���Ҳ���' 
						),
						1 => array (
								'text' => '������' 
						),
						2 => array (
								'text' => 'һ����' 
						),
						
						3 => array (
								'text' => '���ڣ����Ͼ���Լ��' 
						) 
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [7] = array (
		'page' => 7,
		'next' => 8,
		'title' => '������������ģ��֪������',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => 'ѡ���������ġ���ò������Ҫ��',
				'data' => array () 
		),
		'btn' => '�����ҵ�����',
		'submit' => true 
);

$config ['data_model'] ['total_data'] [8] = array (
		'page' => 8,
		'next' => - 1,
		'title' => "<div><i class='icon icon-success-max'></i></div><p class='p1'>�����ɹ�</p><p class='p2'>�Ժ����ǻ�ͨ����Ϣ��������ģ�أ�<br>��������ϵͳ��Ϣ��</p>",
		'content' => array (
				'type' => 'false',
				'data' => array () 
		),
		'btn' => 'ȷ��',
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