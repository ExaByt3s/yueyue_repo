<?php


/**
 * Define ��������
 */

include_once 'config.php';

/**
 * ҳ����ղ���
 */
$goods_id = intval($_INPUT['goods_id']);
$stage_id = intval($_INPUT['stage_id']);







// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{
    //****************** pc�� ******************
    header("location: http://event.poco.cn/event_browse.php?event_id=".$event_id) ;
}
else
{

    //****************** wap�� ******************
    include_once './detail-wap.php';

} 
// ========================= ����pc��wapģ�������ݸ�ʽ���� end  =======================











$tpl->assign('goods_id', $goods_id); 

$tpl->output();

?>