<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //����http://www.baidu.com����������
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //��������Ŀ���header
date_default_timezone_set("Asia/chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=gbk");

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* �ϴ�ͼƬ */
    case 'uploadimage':
    /* �ϴ�Ϳѻ */
    case 'uploadscrawl':
    /* �ϴ���Ƶ */
    case 'uploadvideo':
    /* �ϴ��ļ� */
    case 'uploadfile':
        $result = include("action_upload.php");
        break;

    /* �г�ͼƬ */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* �г��ļ� */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* ץȡԶ���ļ� */
    case 'catchimage':
        $result = include("action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '�����ַ����'
        ));
        break;
}

/* ������ */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback�������Ϸ�'
        ));
    }
} else {
    echo $result;
}