<?php
/**
 * ��ά����תҳ
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 18 August, 2015
 * @package default
 */

/**
 * ��ʼ�������ļ�
 */
include_once 'config.php';

//================= ���ղ��� =================
$url = urldecode(trim($_INPUT['s_url']));
//================= ���ղ��� END =================


$go_to_url = $url;

echo "<script>window.location.href='".$go_to_url."'</script>";
die();
 
?>
