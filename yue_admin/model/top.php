<?php
ob_start();
?>
		<li> <a href="#" class="nav-top-item">约约专题</a>
          <ul>
            <li><a href="topic_user_list.php">模特录入列表</a></li>
          </ul>
        </li>
  <?php
$_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER = ob_get_contents();
ob_end_clean();
?>