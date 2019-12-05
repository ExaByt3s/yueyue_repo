<?php

include_once 'common.inc.php';

$base_path = '/disk/data/htdocs232/mon/';
if(!is_file($base_path.$_GET['file']))
{
	die;
}

?>

<style>
table{border-collapse:collapse;margin:50px 0px 0px 50px}
table .red{border:1px solid red;padding:3px 5px;background-color:red;color:#fff;}
table td{border:1px solid #aaa;padding:3px 5px}
</style>
<table>
	<tbody>
		<tr><th class="red">修改时间</th><th class="red">文件路径</th>	</tr>
<?php

$list = array();
foreach(file($base_path.$_GET['file']) as $line)
{
	$line = trim($line);
	if(preg_match('#data/htdocs.+#', $line, $m))
	{
		$line = $m[0];
	}
	$line = '/disk/'.$line;
	$ctime = @filectime($line);
	if(empty($ctime))
	{
		$n ++;
		$list[$ctime+$n] = "		<tr><td class=\"red\">".date('Y-m-d H:i:s', (int)$ctime)."</td><td class=\"red\">{$line}</td></tr>\r\n";
	}
	else
	{
		$list[$ctime] = "		<tr><td>".date('Y-m-d H:i:s', (int)$ctime)."</td><td>{$line}</td></tr>\r\n";
	}
	
}
krsort($list);
foreach($list as $row)
{
	echo $row;
}


?>
	</tbody>
</table>
