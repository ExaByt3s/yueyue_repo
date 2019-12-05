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
	$level = "骨灰女神";
	$text = "我滴妈呀，这世间居然有这等骨灰级的女神，【约约】都不蛋定了！！！";
}
elseif($info['percent']>=70 && $info['percent']<80)
{
	$level = "神级女神";
	$text = "女神，你造吗？你这般神一样的存在肿么都不来【约约】一下呢？";
}
elseif($info['percent']>=60 && $info['percent']<70)
{
	$level = "标准女神";
	$text = "让【约约】高呼你一声”女神“！女神，约吗？";
}elseif($info['percent']>=50 && $info['percent']<60)
{
	$level = "临界女神";
	$text = "只需要再继续努力一咪咪下，女神的称号就是你的了！【约约】就是你努力的目标！";
}
elseif($info['percent']>=40 && $info['percent']<50)
{
	$level = "蛋定女神";
	$text = "你的淡定和从容已经让一众女神为你拜服，勤加修炼就是骨灰级的了，【约约】能让你的身价变成现实噢~";
}
else
{
	$level = "神经女神";
	$text = "女神……经！你在这级别的女神里面绝对是骨灰级的，不信？来【约约】瞧瞧就知道！";
}

$info['text'] = $text;
$info['level'] = $level;

$tpl->assign($info);


$tpl->assign("open_id",$_COOKIE['yueus_openid']);

$tpl->output();
?>