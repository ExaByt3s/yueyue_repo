<?php 
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$id = (int)$_INPUT['id'];

if(!$_COOKIE['BEAUTY_CASH'] || $id==0)
{
	header("Location: http://www.yueus.com/topic/beautiful_cash/start.php");
}

$tpl = new SmartTemplate ( "result.tpl.htm" );



$beauty_cash_obj = POCO::singleton ( 'pai_beauty_cash_class' );

$info = $beauty_cash_obj->get_beauty_info($id);



// var_dump($info);

$info['price'] = (int)$info['price'];

$price_split = str_split($info['price'],1);
$info['price_split'] = $price_split;

if($info['percent']>=80)
{
	$level = "�ǻ�Ů��";
	$text = "�ҵ���ѽ���������Ȼ����ȹǻҼ���Ů�񣬡�ԼԼ�����������ˣ�����";
}
elseif($info['percent']>=70 && $info['percent']<80)
{
	$level = "��Ů��";
	$text = "Ů���������������һ���Ĵ�����ô��������ԼԼ��һ���أ�";
}
elseif($info['percent']>=60 && $info['percent']<70)
{
	$level = "��׼Ů��";
	$text = "�á�ԼԼ���ߺ���һ����Ů�񡰣�Ů��Լ��";
}elseif($info['percent']>=50 && $info['percent']<60)
{
	$level = "�ٽ�Ů��";
	$text = "ֻ��Ҫ�ټ���Ŭ��һ�����£�Ů��ĳƺž�������ˣ���ԼԼ��������Ŭ����Ŀ�꣡";
}
elseif($info['percent']>=40 && $info['percent']<50)
{
	$level = "����Ů��";
	$text = "��ĵ����ʹ����Ѿ���һ��Ů��Ϊ��ݷ����ڼ��������ǹǻҼ����ˣ���ԼԼ�����������۱����ʵ��~";
}
else
{
	$level = "��Ů��";
	$text = "Ů�񡭡����������⼶���Ů����������ǹǻҼ��ģ����ţ�����ԼԼ�����ƾ�֪����";
}

$info['text'] = $text;
$info['level'] = $level;

$tpl->assign($info);


$tpl->assign("open_id",$_COOKIE['yueus_openid']);

$tpl->output();
?>