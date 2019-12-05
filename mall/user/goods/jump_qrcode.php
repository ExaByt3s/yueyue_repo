<?php
/**
 * 二维码中转页
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 18 August, 2015
 * @package default
 */

/**
 * 初始化配置文件
 */
include_once 'config.php';

//================= 接收参数 =================
$url = urldecode(trim($_INPUT['s_url']));
//================= 接收参数 END =================


$go_to_url = $url;

echo "<script>window.location.href='".$go_to_url."'</script>";
die();
 
?>
