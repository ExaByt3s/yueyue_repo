<?php
/**
 * �̳�����ͨ���ļ�
 * @copyright 2015-06-18
 */
include(dirname(__FILE__).'/api_rest.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); 
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
include_once(dirname(__FILE__).'/include/output_function.php');
include_once(dirname(__FILE__).'/no_copy_online_config.inc.php');
define('TASK_TEMPLATES_ROOT',MALL_USER_DIR_APP."templates/default/");

// ����UA����
$user_agent_arr = mall_get_user_agent_arr();
if($user_agent_arr['is_pc'] == 1 )
{
	define('MALL_UA_IS_PC',1);
}
else
{
	define('MALL_UA_IS_MOBILE',1);
	
	if($user_agent_arr['is_weixin'] == 1 )
	{
		define('MALL_UA_IS_WEIXIN',1);
	}
	else if($user_agent_arr['is_yueyue_app'] == 1 )
	{
		define('MALL_UA_IS_YUEYUE',1);
	}
}



// ��Ŀ�ؼ�������
$MALL_COLUMN_CONFIG = array
(
        // ��Ӱ���� Ʒ���б�
        '40' => array(
                "title_key" => 'ԼԼ - ��Ӱ���� ���ר����Ӱʦ',
                "keywords_key" => 'Լ��Ӱʦ��Լ��Ӱ������������Ӱ����ӰȦ��Լ�ģ����㣬��Ӱ����',
                "description_key" => '����ۼ��ڶ���Ӱ�󿧣�Ϊ���ṩ�������͸��Լ۱ȵľ�Ʒ��Ӱ�������ǲ�ֻ�����ռ�¼������Ҫ��ÿ����Ƭ��˵����Ĺ��£�Լ�㣬���ר����Ӱʦ��',
                "key_nav" => '��Ӱ����'
        ),

        // ģ�ط��� Ʒ���б�
        '31' => array(
                "title_key" => 'ԼԼ - ģ����Լ 100000+ģ������Լ',
                "keywords_key" => 'Լģ�أ�ģ�أ�Ů����Ӱģ�أ�ƽ��ģ�أ�ģ��Ȧ������ģ�أ���չģ��',
                "description_key" => '��ЧԼ��ȫ����ģ��ÿ�캣����ģ��פ��ÿ����춥����ģ�����ﲻ����Լ��ƽ��ģ�أ�����Լ����ҵ�ģ�أ���Ӱ�����㡢��չ���Ա�����ģ��Ӧ�о��У�ԼԼ���֣��������У�',
                "key_nav" => 'ģ����Լ'
        ),

        // ��Ӱ��ѵ Ʒ���б�
        '5' => array(
                "title_key" => 'ԼԼ - ��Ӱ��ѵ ����ѧ��Ӱ',
                "keywords_key" => '��Ӱ��ѵ����Ӱ���ţ�ѧ��Ӱ����Ӱ���ɣ���Ӱ���ã�������Ӱ��ѵ����Ӱ��ѧ����Ӱ��������Ӱ���ڣ���Ӱ�࣬��Ӱ˽�̰࣬��Ӱ��ʦ����Ӱ��ʦ����Ӱ��ѵ����',
                "description_key" => '�����Ӱ��ʦ���ǣ���ǧ�γ�������רҵ��Ӱ��ѵ�����Ի�����ѵ���������������ɡ���Ч����Ȥѧ��Ӱ��',
                "key_nav" => '��Ӱ��ѵ'
        ),

        // ��ױ���� Ʒ���б�
        '3' => array(
                "title_key" => 'ԼԼ - ��ױ���� ר���������',
                "keywords_key" => 'Լ��ױ����ױ���񣬲�ױ���񣬻�ױ�����ݣ�����',
                "description_key" => '���������������Ҫ��ԼԼ��Ϊ�����ɸ㶨��ԼԼƽ̨ӵ�д������ױ�����ṩ�̣��ó�����ױ�ݼ����͡���ԼԼ��һ˲�������������ô�򵥡�',
                "key_nav" => '��ױ����'
        ),

        // Ӱ������ Ʒ���б�
        '12' => array(
                "title_key" => 'ԼԼ - Ӱ������ ��ĵ���������',
                "keywords_key" => 'ԼӰ��ҳ��أ�Ӱ����ƾ����Ӱ�Ӱ����⣬��������',
                "description_key" => '����Ӱ���ɫ����������ѡ����ʮ����ǧƽ��Ӧ�о��У�����������������Ĵ�����������һվʽ������������ѡ��ԼԼ���֣����ز��',
                "key_nav" => 'Ӱ������'
        ),

        // Լ��ʳ Ʒ���б�
        '41' => array(
                "title_key" => 'ԼԼ - Լ��ʳ ����ĳԻ�����',
                "keywords_key" => 'Լ��ʳ����ʳ���ˣ��вͣ����ͣ��պ����������ͣ��۲ͣ�����������Ʒ�����⣬���',
                "description_key" => 'ԼԼЯ����ʳ���ˣ�Ϊ�û���������ɫ���ȡ�˽�˶��ƣ��ҿ����ƣ������λ������һ������ζ��֮�ðɣ�',
                "key_nav" => 'Լ��ʳ'
        ),
        // Լ��ʳ Ʒ���б�
        '43' => array(
                "title_key" => 'ԼԼ - Լ��Ȥ',
                "keywords_key" => 'Լ��Ȥ',
                "description_key" => 'Լ��Ȥ',
                "key_nav" => 'Լ��Ȥ'
        ),
        // Լ�
        '42' => array(
                "title_key" => 'ԼԼ - Լ�',
                "keywords_key" => 'Լ�',
                "description_key" => 'Լ�',
                "key_nav" => 'Լ�'
        )
);



