<?php
/**
 * �Ż�ȯ����
 * @author Henry
 * @copyright 2015-04-11
 */

class pai_coupon_give_class extends POCO_TDG
{
	private $give_code_list = array(
		
			/*
			//����
			'DEMO' => array(
					'coupon_package' => array(
							array('batch_id' => 1, 'quantity' => 1),
							array('batch_id' => 2, 'quantity' => 2),
					),
					'message_info' => array(
							'content' => '��ϲ���ɹ�ע�ᣬԼԼΪ��׼���˷ḻ���Ż�����������������Լ��5��Ů��',
							'to_url' => '/mobile/app?from_app=1#topic/112',
					),
			),
			*/
			
			//2015��5��������ף���ֵ���Żݣ�300Ԫ�Ż����
			'Y2015M05D01_RECHARGE_300' => array(
					'coupon_package' => array(
							array('batch_id' => 83, 'quantity' => 4, 'coupon_days'=>90 ), //����ȯ5Ԫ
							array('batch_id' => 84, 'quantity' => 4, 'coupon_days'=>90), //����ȯ10Ԫ
							array('batch_id' => 85, 'quantity' => 6, 'coupon_days'=>90), //Լ��ȯ10Ԫ
							array('batch_id' => 86, 'quantity' => 4, 'coupon_days'=>90), //Լ��ȯ20Ԫ
							array('batch_id' => 87, 'quantity' => 2, 'coupon_days'=>90), //Լ��ȯ50Ԫ
					),
					'message_info' => array(
						'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
						'to_url' => '',
					),
			),
			
			//2015��5��������ף���ֵ���Żݣ�500Ԫ�Ż����
			'Y2015M05D01_RECHARGE_500' => array(
					'coupon_package' => array(
							array('batch_id' => 88, 'quantity' => 4, 'coupon_days'=>90), //����ȯ5Ԫ
							array('batch_id' => 89, 'quantity' => 8, 'coupon_days'=>90), //����ȯ10Ԫ
							array('batch_id' => 90, 'quantity' => 8, 'coupon_days'=>90), //Լ��ȯ10Ԫ
							array('batch_id' => 91, 'quantity' => 6, 'coupon_days'=>90), //Լ��ȯ20Ԫ
							array('batch_id' => 92, 'quantity' => 4, 'coupon_days'=>90), //Լ��ȯ50Ԫ
					),
					'message_info' => array(
						'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
						'to_url' => '',
					),
			),
			
			/*
			//2015��5����Լ�ݿ�ר�����
			'Y2015M05D01_RECHARGE_CARD' => array(
				'coupon_package' => array(
					array('batch_id' => 93, 'quantity' => 1), //������Լ��ȯ100Ԫ
					array('batch_id' => 94, 'quantity' => 1), //������ѵ����ȯ100Ԫ
					array('batch_id' => 95, 'quantity' => 1), //������ѵ����ȯ200Ԫ
					array('batch_id' => 96, 'quantity' => 1), //��Ʒ��������ȯ100Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ��Լ�ݿ���������Ӱʷ��3�����⡣Լģ�ء�Լ���Լ��ѵ�������벻���ĳ�ֵ�������������Լ������',
					'to_url' => '/mobile/app?from_app=1#topic/170',
				),
			),
			*/
			
			//2015��8������2015��10�£���ֵ���Żݣ�500Ԫ�Ż����
			'Y2015M08D01_RECHARGE_500' => array(
				'coupon_package' => array(
					array('batch_id' => 155, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 156, 'quantity' => 6), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),
			
			//2015��8������2015��10�£���ֵ���Żݣ�300Ԫ�Ż����
			'Y2015M08D01_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 177, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 178, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),

			//2015��9������2015��12��31�գ���ֵ���Żݣ�300Ԫ�Ż����
			'Y2015M09D30_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 469, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 468, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),
			
			//2015��10����ע���û�
			'Y2015M10D01_USER_REG' => array(
				'coupon_package' => array(
					array('batch_id' => 506, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '��ϲ���ɹ�ע�ᣬԼԼΪ��׼���˷ḻ���Ż�������������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
			
			//2015��10��11����ע���û�
			'Y2015M10D11_USER_REG' => array(
				'coupon_package' => array(
					array('batch_id' => 529, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '��ϲ���ɹ�ע�ᣬԼԼΪ��׼���˷ḻ���Ż�������������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
			
			//2015��10�����ѻ�����30Ԫ���
			'Y2015M10D01_CONSUMPTION_BACK_30' => array(
				'coupon_package' => array(
					array('batch_id' => 507, 'quantity' => 1),
					array('batch_id' => 508, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������¶������ѽ����������Ӧ���Ż�ȯ���������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
			
			//2015��10�����ѻ�����50Ԫ���
			'Y2015M10D01_CONSUMPTION_BACK_50' => array(
				'coupon_package' => array(
					array('batch_id' => 509, 'quantity' => 1),
					array('batch_id' => 510, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������¶������ѽ����������Ӧ���Ż�ȯ���������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
		
			//2015��10�����ѻ�����100Ԫ���
			'Y2015M10D01_CONSUMPTION_BACK_100' => array(
				'coupon_package' => array(
					array('batch_id' => 511, 'quantity' => 1),
					array('batch_id' => 512, 'quantity' => 1),
					array('batch_id' => 513, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������¶������ѽ����������Ӧ���Ż�ȯ���������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
			
			//2015��10�����ѻ�����200Ԫ���
			'Y2015M10D01_CONSUMPTION_BACK_200' => array(
				'coupon_package' => array(
					array('batch_id' => 514, 'quantity' => 2),
					array('batch_id' => 515, 'quantity' => 2),
					array('batch_id' => 516, 'quantity' => 2),
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������¶������ѽ����������Ӧ���Ż�ȯ���������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
			
			//2015��9����ԼԼ��Ʒ����
			'Y2015M09D08_WAIPAI_YUEJP' => array(
				'coupon_package' => array(
					array('batch_id' => 292, 'quantity' => 1),
				),
				'message_info' => array(
					'content' => '�װ����û����ã���л���μ��˱��Ρ�ԼԼ��Ʒ���񡱻��ԼԼ��������25Ԫ���Ż�����������������ҵ� ���Ż�ȯ���ڲ鿴���ٴθ�л���ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
			
			//2015��8��28����2015��11�£���ֵ���Żݣ�300Ԫ�Ż����
			'Y2015M08D28_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 251, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 252, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��9��30���������Żݣ�����ȯ ������
			'Y2015M09D07_COMMENT_APP_1' => array(
				'coupon_package' => array(
					array('batch_id' => 123, 'quantity' => 1), //������ͨ��ȯ1Ԫ
				),
				'message_info' => array(
					'content' => '��л�����ۣ������֧��ԼԼ������Ŭ���������İ�����',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ��ģ�ط���
			'Y2015M09D08_CONSUMPTION_BACK_200-299' => array(
				'coupon_package' => array(
					array('batch_id' => 293, 'quantity' => 1), //����������ȯ20Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_300-499' => array(
				'coupon_package' => array(
					array('batch_id' => 294, 'quantity' => 1), //����������ȯ30Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_500-999' => array(
				'coupon_package' => array(
					array('batch_id' => 295, 'quantity' => 1), //����������ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_1000-1999' => array(
				'coupon_package' => array(
					array('batch_id' => 296, 'quantity' => 1), //����������ȯ100Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_2000' => array(
				'coupon_package' => array(
					array('batch_id' => 297, 'quantity' => 1), //����������ȯ200Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ����ױ����
			'Y2015M09D08_CONSUMPTION_BACK_200_299_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 458, 'quantity' => 1), //����������ȯ20Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_300_499_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 456, 'quantity' => 1), //����������ȯ30Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_500_999_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 454, 'quantity' => 1), //����������ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_1000_1999_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 452, 'quantity' => 1), //����������ȯ100Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_2000_cate_3' => array(
				'coupon_package' => array(
					array('batch_id' => 450, 'quantity' => 1), //����������ȯ200Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ��Ӱ������
			'Y2015M09D08_CONSUMPTION_BACK_200_299_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 459, 'quantity' => 1), //����������ȯ20Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_300_499_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 457, 'quantity' => 1), //����������ȯ30Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_500_999_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 455, 'quantity' => 1), //����������ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_1000_1999_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 453, 'quantity' => 1), //����������ȯ100Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),

			//2015��9��1����2015��10��12������������ȯ
			'Y2015M09D08_CONSUMPTION_BACK_2000_cate_12' => array(
				'coupon_package' => array(
					array('batch_id' => 451, 'quantity' => 1), //����������ȯ200Ԫ
				),
				'message_info' => array(
					'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
					'to_url' => '',
				),
			),
			
			//2015��9��18����2015��12�£���ֵ���Żݣ�300Ԫ�Ż����
			'Y2015M09D18_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 361, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 362, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),

			//2015��10������2016��1��31�գ���ֵ���Żݣ�300Ԫ�Ż����
			'Y2015M10D16_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 536, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 533, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),

			//2015��11������2016��3��31�գ���ֵ300Ԫ���Żݣ�300Ԫ�Ż����
			'Y2015M11D05_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 592, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 593, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),

			//2015��11������2016��3��31�գ���ֵ500Ԫ���Żݣ�300Ԫ�Ż����
			'Y2015M11D05_RECHARGE_500' => array(
				'coupon_package' => array(
					array('batch_id' => 594, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 595, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),

			//2015��11��09��~2015��12��10�գ�ע���û�
			'Y2015M11D09_USER_REG' => array(
				'coupon_package' => array(
					array('batch_id' => 599, 'quantity' => 1),
				),
				'message_info' => array(
                    'msg_type' => 2, 
					'content' => '��ϲ���ɹ�ע�ᣬԼԼΪ��׼���˷ḻ���Ż�������������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�',
					'to_url' => '',
                    'card_text1' => '���ڵȵ��㣡1Ԫ��ֵ��Ʒ����ϲ����ͣ',
                    'card_title' => '�Ż�ȯ����������ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
				),
			),

			//2015��11��09��~2015��12��10�գ�ע�����û���������
			'Y2015M11D09_USER_REG_TOMORROW' => array(
				'coupon_package' => array(
					array('batch_id' => 600, 'quantity' => 1),
				),
				'message_info' => array(
                    'msg_type' => 2, 
					'content' => '',
					'to_url' => '',
                    'card_text1' => '��ϲ�����ˣ�Լ���������ƣ��Ҷ�Ϊ���򵥣�',
                    'card_title' => '�Ż�ȯ����������ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
				),
			),

			//2015��11��09����2015��12��10�գ�100-8
			'Y2015M11D09_CONSUMPTION_BACK_8' => array(
				'coupon_package' => array(
					array('batch_id' => 601, 'quantity' => 1), //8Ԫͨ��ȯ
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
                    'to_url' => '',
                    'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                    'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015��11��09����2015��12��10�գ�200-18
			'Y2015M11D09_CONSUMPTION_BACK_18' => array(
				'coupon_package' => array(
					array('batch_id' => 602, 'quantity' => 1), //18Ԫͨ��ȯ
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
                    'to_url' => '',
                    'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                    'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015��11��09����2015��12��10�գ�300-28
			'Y2015M11D09_CONSUMPTION_BACK_28' => array(
				'coupon_package' => array(
					array('batch_id' => 603, 'quantity' => 1), //28Ԫͨ��ȯ
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
                    'to_url' => '',
                    'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                    'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015��11��09����2015��12��10�գ�500-38
			'Y2015M11D09_CONSUMPTION_BACK_38' => array(
				'coupon_package' => array(
					array('batch_id' => 604, 'quantity' => 1), //38Ԫͨ��ȯ
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
                    'to_url' => '',
                    'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                    'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015��11��09����2015��12��10�գ�600-48
			'Y2015M11D09_CONSUMPTION_BACK_48' => array(
				'coupon_package' => array(
					array('batch_id' => 605, 'quantity' => 1), //48Ԫͨ��ȯ
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
                    'to_url' => '',
                    'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                    'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015��11��09����2015��12��10�գ�1000-68
			'Y2015M11D09_CONSUMPTION_BACK_68' => array(
				'coupon_package' => array(
					array('batch_id' => 606, 'quantity' => 1), //68Ԫͨ��ȯ
				),
				'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
                    'to_url' => '',
                    'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                    'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                    'link_url' => '/mall/user/topic/index.php?topic_id=820&online=1',
                ),
			),

			//2015��11��16����2015��11��21�գ�������ȯ
            'Y2015M11D16_SHARE_EVENT_60559' => array(
                'coupon_package' => array(
                    array('batch_id' => 636, 'quantity' => 1), //10Ԫ100ͨ��
                ),
                'message_info' => array(
                    'msg_type' => 2, 
                    'content' => '���֮ҹ���Ż݄��ѷ��͵�����ԼԼ�˻�������ʹ�ü��������Ż�',
                    'to_url' => '',
                    'card_text1' => '�����֮ҹ���Ż݄��ѷ��͵�����ԼԼ�˻�������ʹ�ü��������Ż�',
                    'card_title' => '�Ż�ȯ����������ʹ�ã�',
                    'link_url' => '/mall/user/act/detail.php?event_id=60559',
                ),
            ),

			//2015��11��16����2016��3��31��
            'Y2015M11D16_RECHARGE_1000' => array(
                'coupon_package' => array(
                    array('batch_id' => 638, 'quantity' => 6), //Լ��ȯ50Ԫ
                    array('batch_id' => 637, 'quantity' => 10), //Լ��ȯ20Ԫ
                ),
                'message_info' => array(
                    'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
                    'to_url' => '',
                ),
            ),
			
			//2015��11������2016��3��31�գ���ֵ300Ԫ���Żݣ�300Ԫ�Ż����
			'Y2015M11D25_RECHARGE_300' => array(
				'coupon_package' => array(
					array('batch_id' => 643, 'quantity' => 10), //Լ��ȯ20Ԫ
					array('batch_id' => 644, 'quantity' => 2), //Լ��ȯ50Ԫ
				),
				'message_info' => array(
					'content' => '��ϲ��ɹ���ֵ�����͵��Ż�ȯ����ѷ��ŵ������˻����������⣡',
					'to_url' => '',
				),
			),

			//2015��11��16����2016��3��31��
            'Y2015M11D27_ONE_BUCK_1' => array(
                'coupon_package' => array(
                    array('batch_id' => 649, 'quantity' => 1), //Լ��ȯ50Ԫ
                ),
            ),

			//2015��11��16����2016��3��31��
            'Y2015M11D27_ONE_BUCK_2' => array(
                'coupon_package' => array(
                    array('batch_id' => 647, 'quantity' => 1), //Լ��ȯ50Ԫ
                ),
            ),

			//2015��11��16����2016��3��31��
            'Y2015M11D27_ONE_BUCK_3' => array(
                'coupon_package' => array(
                    array('batch_id' => 648, 'quantity' => 1), //Լ��ȯ50Ԫ
                ),
            ),

			//2015��11��16����2016��3��31��
            'Y2015M11D27_ONE_BUCK_4' => array(
                'coupon_package' => array(
                    array('batch_id' => 650, 'quantity' => 1), //Լ��ȯ50Ԫ
                ),
            ),

			//2015��11��16����2016��3��31��
            'Y2015M11D27_ONE_BUCK_5' => array(
                'coupon_package' => array(
                    array('batch_id' => 645, 'quantity' => 1), //Լ��ȯ50Ԫ
                ),
            ),

			//2015��11��16����2016��3��31��
            'Y2015M11D27_ONE_BUCK_6' => array(
                'coupon_package' => array(
                    array('batch_id' => 646, 'quantity' => 1), //Լ��ȯ50Ԫ
                ),
            ),
	);
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_coupon_db');
	}
	
	/**
	 * ָ����
	 */
	private function set_coupon_give_queue_tbl()
	{
		$this->setTableName('coupon_give_queue_tbl');
	}
	
	/**
	 * ��ȡ���ű�ʶ�����б�
	 * @return array
	 */
	public function get_give_code_list()
	{
		return $this->give_code_list;
	}
	
	/**
	 * ���
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
	 * �޸�
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
	 * �����ѷ���
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
	 * ���²�����
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
	 * ����ȡ��
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
	 * ��ȡ�б�
	 * @param int $status -1��ʾ������
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
		
		//�����ѯ����
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
		
		//��ѯ
		$this->set_coupon_give_queue_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * �ύ����
	 * �û�ID���ֻ����룬������дһ����
	 * ���߶���дʱ�������ֻ����롣
	 * @param string $give_code ���ű�ʶ
	 * @param string $cellphone �ֻ�����
	 * @param int $user_id �û�ID
	 * @param int $ref_id ����ID�����ڴ���һ���û����Է��Ŷ��
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
		
		//���ű�ʶδ����
		if( !array_key_exists($give_code, $this->give_code_list) )
		{
			return 0;
		}
		
		//������Ϣ
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
		
		//�������
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
	 * �����Ż�ȯ
	 * @param array $queue_info
	 * @return array
	 */
	public function give_coupon_by_queue_info($queue_info)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn_arr'=>array());
		
		//������
		if( !is_array($queue_info) || empty($queue_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$id = intval($queue_info['id']);
		$give_code = trim($queue_info['give_code']);
		$cellphone = trim($queue_info['cellphone']);
		$user_id = intval($queue_info['user_id']);
		$status = intval($queue_info['status']);
		$message_data = unserialize($queue_info['message_data']);
		
		//�ж�״̬
		if( $status!=0 )
		{
			$result['result'] = -2;
			$result['message'] = '״̬����';
			return $result;
		}
		
		//���ű�ʶδ����
		if( !array_key_exists($give_code, $this->give_code_list) )
		{
			$result['result'] = -3;
			$result['message'] = '���ű�ʶδ����';
			return $result;
		}
		
		//��ȡ������Ϣ
		$give_code_info = $this->give_code_list[$give_code];
		if( !is_array($give_code_info) ) $give_code_info = array();
		$coupon_package = $give_code_info['coupon_package'];
		if( !is_array($coupon_package) ) $coupon_package = array();
		$message_info = $give_code_info['message_info'];
		if( !is_array($message_info) ) $message_info = array();
		if( empty($give_code_info) || empty($coupon_package) )
		{
			$result['result'] = -4;
			$result['message'] = '���ű�ʶ���ô���';
			return $result;
		}
		
		//��ȡ�û���Ϣ
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
			$result['message'] = '�û�������';
			return $result;
		}
		
		$cur_time = time();
		
		/*
		//�����Ÿ�ģ��
		if( $user_info['role']=='model' )
		{
			$more_info = array('lately_time' => $cur_time);
			$this->update_queue_ungive($id, $more_info);
			
			$result['result'] = -6;
			$result['message'] = '�û���ģ��';
			return $result;
		}
		*/
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		$more_info = array('lately_time' => $cur_time);
		$ret = $this->update_queue_give($id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -7;
			$result['message'] = '״̬ʧ��';
			return $result;
		}
		
		//�����Ż�ȯ
		$coupon_is_give = true;
		$coupon_sn_arr = array();
		$coupon_obj = POCO::singleton('pai_coupon_class');
		foreach($coupon_package as $key=>$val)
		{
			$batch_id = intval($val['batch_id']);
			$quantity = intval($val['quantity']);
			$coupon_days = intval($val['coupon_days']);
			
			//��������Ƿ���Ч
			$batch_info = $coupon_obj->get_batch_info($batch_id);
			if( empty($batch_info) || $batch_info['check_status']!=1 || ($coupon_days<1 && $batch_info['coupon_end_time']<$cur_time) )
			{
				$coupon_is_give = false;
				break;
			}
			
			//������Ч��������Ȼ�죩����ȡ֮����1�졣
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
			
			//����
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
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -8;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//�����ύ
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
                //΢�Ź��ں�ģ����Ϣ
                $template_data = array(
                    'title' => '�Ż���Ϣ����',
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
                    'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                    'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
                    'card_text1' => $card_text1, // (����media_type=card�ģ����ϱ���)
                    'card_title' => $card_title, // (����media_type=card�ģ��ײ�)
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
		$result['message'] = '�ɹ�';
		$result['coupon_sn_arr'] = $coupon_sn_arr;
		return $result;
	}

	/**
	 * �Ƿ������۽�����ʾ��ȯ��ʾ
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
            $rst['tips'] = '�ף���ɴ��+���ۣ����ɻ�ó�ֵ�Ż�ȯ�ޡ�';
            return $rst;
        }
        return $rst;
	}
}
