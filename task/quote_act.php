<?php
/** 
 * 
 * ����
 * 
 * 2015-4-11
 * 
 */
 
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);

/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

$request_id = (int)$_INPUT['request_id'];
$price = $_INPUT['price'];
$content = $_INPUT['content'];

if(empty($price))
{
	js_pop_msg("���۲���Ϊ��");
}

if((int)$price < 1 )
{
	js_pop_msg("���ۼ۸���С��1�����������룡");
}

if((int)$price >= 1000000 )
{
    js_pop_msg("���۲��ܳ���1000000�����������룡");
}


if(ceil($price) == $price )
{
	// echo "����"; 
}
else
{
	js_pop_msg("���۱���Ϊ����Ŷ��");
}



if(empty($content))
{
	js_pop_msg("������ԭ��");
}

if(empty($request_id))
{
	js_pop_msg("��������");
}

/**
 * �ύ����
 * @param int $request_id
 * @param int $user_id
 * @param double $price
 * @param string $content
 * @param array $more_info
 * @return array
 */
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$ret = $task_quotes_obj->submit_quotes($request_id, $yue_login_id, $price, $content, $more_info);

if($ret['result']==1)
{
	$pay_ret = $task_quotes_obj->pay_quotes_coins($ret['quotes_id']);
	if($pay_ret['result']==1)
	{
		js_pop_msg("�ύ�ɹ�",false,"./quote_success.php?quotes_id=".$ret['quotes_id']);
	}
	elseif($pay_ret['result']==-3 || $pay_ret['result']==-4)
	{
		js_pop_msg("������⿨���㣬����г�ֵ",false,"./pay.php?quotes_id=".$ret['quotes_id']);
	}
	else
	{
		js_pop_msg($pay_ret['message']);
	}
}
else
{
	js_pop_msg($ret['message']);
}


 ?>