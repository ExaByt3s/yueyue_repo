<?php
/**
 * ��ȡ��Ʒ�б��첽
 *
 * @author ����
 * @version $Id$
 * @copyright , 2015-11-23
 * @package default
 */

/**
 * ��ʼ�������ļ�
 */
include_once 'config.php';

$p = (int)$_INPUT["p"];
$goods_id = (int)$_INPUT["goods_id"];
$user_id = $yue_login_id;
$ajax_status = 1;

if($p=="" || empty($goods_id))
{
    $ajax_status = 0;
    $msg = iconv('gbk//IGNORE','utf-8',"��������");

}

if($ajax_status == 1)
{
    if(empty($p))
    {
        $p = 1;
    }
    $limit_count = 3;//���ԣ�����������
    $limit_start = ($p-1)*$limit_count;

    $limit_str = $limit_start.",".$limit_count;



    $article_arr_count = POCO::singleton('pai_mall_relate_opus_class')->get_opus_full_list(true,$goods_id,$limit_str,$order_by='add_time desc');
    $article_arr = POCO::singleton('pai_mall_relate_opus_class')->get_opus_full_list(false,$goods_id,$limit_str,$order_by='add_time desc');
    //���ݵ�ǰҳ�������������ж��Ƿ�����һҳ
    $now_search_count = $p*$limit_count;
    if($now_search_count>=$article_arr_count)
    {
        $next_p = 0;
    }
    else
    {
        $next_p = $p+1;
    }




    foreach($article_arr as $key => $value)
    {
        $article_arr[$key]["title"] = iconv('gbk//IGNORE','utf-8', $value["title"]);
        $article_arr[$key]["nickname"] = iconv('gbk//IGNORE','utf-8', $value["nickname"]);
        if(!empty($user_id))
        {
            //�ж��Ƿ���ɾ����Ȩ��
            if($value["user_id"]==$user_id)
            {
                $article_arr[$key]["del_op"] = 1;//ɾ�����ܲ�����ʾ
            }
            else
            {
                $article_arr[$key]["del_op"] = 0;//ɾ�����ܲ�����ʾ
            }
        }
        else
        {
            $article_arr[$key]["del_op"] = 0;//ɾ�����ܲ�����ʾ
        }

    }



}

//$ret�����飬����Ϊresult��msg

$arr["ajax_status"] = $ajax_status;
$arr["article_arr"] = $article_arr;
$arr["next_p"] = $next_p;//�ж��Ƿ�����һҳ��ť
$arr["msg"] = $msg;
echo json_encode($arr);
exit;


?>