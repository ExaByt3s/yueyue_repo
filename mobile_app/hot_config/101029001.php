<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );

$ranking_array = array('12'=>array('ÿ����ģ', 'new_model', '����', ''),
                       '14'=>array('˽�ı����ĸ�����', 'spbwpgpy', 'Сʱ', ''),
                       '11'=>array('�Ȼ����ջ�', 'xiong_model', '����', ''),
                       '3'=>array('�������а�', 'score_list', '����', 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/charm_list'),
                       '5'=>array('�������а�', 'comment_list', '��', 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/date_list'),
                       '10'=>array('�Ը�Ů�� ˽��ר��', 'hot_model', 'Сʱ', ''),
                       '13'=>array('�����ӵ���Լ', 'recommend_model', 'Сʱ', ''),
                       '27'=>array('����Ů�� ��������', 'ctns_wmbl', '����', ''),
                       '28'=>array('Ů��ѧ�ñ�ҵ��', 'nsxtbyl', '����', ''),
                       '29'=>array('΢Ц��ʹ ��Ц����', 'wxts_axax', '����', ''),
                       '31'=>array('ԼԼ�Ƽ� ��Ƭ��֤', 'yytj_cpbz', 'Сʱ', ''),
                       '30'=>array('����ģ��', 'gdmt', '����', ''),
                       );
                       
if($yue_login_id)
{
   if($pai_user_obj->check_role($yue_login_id) == 'model')
   {
        $ranking_array = array('15'=>array('', 'pay_cameraman', '', ''),
                            '17'=>array('������ģ��', 'search_cameraman', '', ''),
                            '21'=>array('��Ӱʦ Լ�����а�', 'date_cameraman', '', ''),
                            '20'=>array('��Ӱʦ �������а�', 'comment_cameraman', '', '')
                            );
   }
}

return $ranking_array;
?>