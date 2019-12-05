<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_id = $_INPUT['user_id'];
$url = urlencode("http://www.yueus.com/".$user_id);

?>

  <div class="code-img"><img width='180' height='180' src="http://qr.liantu.com/api.php?text=http://www.yueus.com/share_card/<?php echo $user_id;?>" /></div>
  <p>…®“ª…®£¨Œ¢–≈∑÷œÌ</p>
  <div class="share-way">
  <ul>
  	<li><a href="http://www.yueus.com/module/share.php?user_id=<?php echo $user_id;?>&sign=sina&url=<?php echo $url;?>" target="_blank"><i class="icon wb-icon"></i></a></li>
    <li><a href="http://www.yueus.com/module/share.php?user_id=<?php echo $user_id;?>&sign=qzone&url=<?php echo $url;?>" target="_blank" ><i class="icon qzone-icon"></i></a></li>
    <li><a href="http://www.yueus.com/module/share.php?user_id=<?php echo $user_id;?>&sign=qq&url=<?php echo $url;?>" target="_blank"><i class="icon qq-icon"></i></a></li>
  </ul>