<?php
ob_start();
?>
		<li> <a href="#" class="nav-top-item">ԼԼר��</a>
          <ul>
            <li><a href="topic_user_list.php">ģ��¼���б�</a></li>
          </ul>
        </li>
  <?php
$_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER = ob_get_contents();
ob_end_clean();
?>