<?php
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
ob_start();
?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation"> 
  
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand" href="">订单报表</a> </div>
  
    <ul class="nav navbar-right top-nav">
      <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo get_user_nickname_by_user_id($yue_login_id); ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li> <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> 退出</a> </li>
        </ul>
      </li>
    </ul>

    <div class="collapse navbar-collapse navbar-ex1-collapse">
      <ul class="nav navbar-nav side-nav">
        <li> <a href="overall.php"><i class="fa fa-fw fa-dashboard"></i> 订单总览</a> </li>
		<li> <a href="overall.php?fenbu=1"><i class="fa fa-fw fa-map-marker"></i> 订单分布</a> </li>
        <li> <a href="order_list.php"><i class="fa fa-fw fa-edit"></i> 订单列表</a> </li>
		<li> <a href="reg_list.php"><i class="fa fa-fw fa-adjust"></i> 用户统计</a> </li>
		
      </ul>
    </div>

  </nav>


<?php
$_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER = ob_get_contents();
ob_end_clean();
?>