<?php
/**	
 * �ʾ���������
 */


$config ['data_model'] ['total_page'] = 18;

$config ['data_model'] ['title'] = '��������';

$config ['data_model'] ['total_data'] [0] = array (
		'page' => 1,
		'next' => 2,
		'title' => '����Ҫ��Ӱ�����ʱ�䣿',
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
		'branch' => true,
		'title' => '<p>��Ҫѡ�����Ӱ�����ǣ�</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'Լ�ķ���',
								'flag' => true,
								'next' => '3' 
						),
						1 => array (
								'text' => '��Ӱ��ѵ����',
								'flag' => false,
								'next' => 10
						) 
				)
				 
		),
		'btn' => '' 
);



/****************/
// �����￪ʼ���з�֧·��
// Լ�ķ���Ĳ���
$config ['data_model'] ['total_data'] [2] = array (
		'page' => 3,
		'next' => 4,
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

$config ['data_model'] ['total_data'] [3] = array (
		'page' => 4,
		'next' => 5,
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

$config ['data_model'] ['total_data'] [4] = array (
		'page' => 5,
		'next' => 6,
		'branch' => true,
		'title' => '���ж������㳡��ô�� <br> ����У����������㳡�ط���ģ��ѡ��',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '��',
								'flag' => true,
								'next' => "51" 
						),
						1 => array (
								'text' => 'û��',
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
		'title' => '���ж������㳡��ô�� <br> ����У����������㳡�ط���ģ��ѡ��',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => '��������ϸ��ַ',
				'data' => array () ,
				'must_be_txt' => true
		),
		'btn' => '��һ��' 
);

$config ['data_model'] ['total_data'] [6] = array (
		'page' => 6,
		'next' => 7,
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

$config ['data_model'] ['total_data'] [7] = array (
		'page' => 7,
		'next' => 8,
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

$config ['data_model'] ['total_data'] [8] = array (
		'page' => 8,
		'next' => 9,
		'title' => '������������ģ��֪������',
		'content' => array (
				'type' => 'textarea',
				'placeholder' => 'ѡ���������ġ���ò������Ҫ��',
				'data' => array () 
		),
		'btn' => '�����ҵ�����',
		'submit' => true 
);



/****************/
// �����￪ʼ���з�֧·��
// ��Ӱ����Ĳ���
$config ['data_model'] ['total_data'] [9] = array (
		'page' => 9,
		'next' => 10,
		'title' => '<p>�õ�һ��רҵ����ѵ�γ�</p><p>��ѡ�����ĵ�ַ</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => '����' 
						),
						1 => array (
								'text' => '�人' 
						),
						2 => array (
								'text' => '����' 
						),
						3 => array (
								'text' => '�Ϻ�' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [10] = array (
		'page' => 10,
		'next' => 11,
		'title' => '<p>���μ���Ӱ��ѵ��ѧϰĿ����</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A��רҵ��������רҵ��Ӱʦ' 
						),
						1 => array (
								'text' => 'B����Ȥ���ã�ҵ��ʱ��������Ƭ' 
						),
						2 => array (
								'text' => 'C������������Ҫ�õ���Ӱ����' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [11] = array (
		'page' => 11,
		'next' => 12,
		'title' => '<p>���Ӵ���Ӱ�೤ʱ���ˣ�</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A��1������' 
						),
						1 => array (
								'text' => 'B��1��-3��' 
						),
						2 => array (
								'text' => 'C��3��-5��' 
						),
						3 => array (
								'text' => 'D��5������' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [12] = array (
		'page' => 12,
		'next' => 13,
		'title' => '<p>������Ӱ�У�����������ѧϰ��һ�����棿</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A��������Ӱ' 
						),
						1 => array (
								'text' => 'B����ҵ��Ӱ' 
						),
						2 => array (
								'text' => 'C�������Ӱ' 
						),
						3 => array (
								'text' => 'D�����ļ�ʵ' 
						),
						4 => array (
								'text' => 'E��������Ӱ'
						),
						5 => array (
								'text' => 'F����̬��Ӱ'
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [13] = array (
		'page' => 13,
		'next' => 14,
		'title' => '<p>ͨ���μӿγ��������ﵽ��ô��ˮƽ��</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A������ˮƽ' 
						),
						1 => array (
								'text' => 'B���м�ˮƽ' 
						),
						2 => array (
								'text' => 'C���߼�ˮƽ' 
						),
						3 => array (
								'text' => 'D����ʦ��ˮƽ' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [14] = array (
		'page' => 14,
		'next' => 15,
		'title' => '<p>��ϣ�����ڿη�ʽ</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A�������ڿ�' 
						),
						1 => array (
								'text' => 'B�������ڿ�' 
						),
						2 => array (
								'text' => 'C��1��1' 
						),
						3 => array (
								'text' => 'D��������' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [15] = array (
		'page' => 15,
		'next' => 16,
		'title' => '<p>���ܽ��ܵĿγ������λ�ǣ�</p>',
		'content' => array (
				'type' => 'list-radio',
				'data' => array (
						0 => array (
								'text' => 'A��1000-2000 ' 
						),
						1 => array (
								'text' => 'B��2000-3000' 
						),
						2 => array (
								'text' => 'C��3000-4000' 
						),
						3 => array (
								'text' => 'D��4000������' 
						)
				)
				 
		),
		'btn' => '' 
);

$config ['data_model'] ['total_data'] [16] = array (
		'page' => 16,
		'next' => 17,
		'title' => '<p>����һ����ɣ�����д�����ֻ����룿</p>',
		'btn' => 'ȷ��',
		'submit' => true,
		'content' => array (
				'type' => 'input',
				'placeholder' => '����д�����ֻ�����',
				'data' => array () ,
				'input_type' => 'tel',
				'must_be_txt' => true
				
				 
		),
		
);

$config ['data_model'] ['total_data'] [17] = array (
		'page' => 18,
		'next' => -1,
		'title' => "<div><i class='icon icon-success-max'></i></div><p class='p1'>�����ɹ�</p><p class='p2'>�Ժ����ǻ�ͨ����Ϣ����������Ӱ����<br>��������ϵͳ��Ϣ��</p>",
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