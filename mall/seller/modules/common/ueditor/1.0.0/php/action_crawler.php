<?php
/**
 * ץȡԶ��ͼƬ
 * User: Jinqn
 * Date: 14-04-14
 * Time: ����19:18
 */
set_time_limit(0);
include("Uploader.class.php");

/* �ϴ����� */
$config = array(
    "pathFormat" => $CONFIG['catcherPathFormat'],
    "maxSize" => $CONFIG['catcherMaxSize'],
    "allowFiles" => $CONFIG['catcherAllowFiles'],
    "oriName" => "remote.png"
);
$fieldName = $CONFIG['catcherFieldName'];

/* ץȡԶ��ͼƬ */
$list = array();
if (isset($_POST[$fieldName])) {
    $source = $_POST[$fieldName];
} else {
    $source = $_GET[$fieldName];
}
foreach ($source as $imgUrl) {
    $item = new Uploader($imgUrl, $config, "remote");
    $info = $item->getFileInfo();
    array_push($list, array(
        "state" => $info["state"],
        "url" => $info["url"],
        "size" => $info["size"],
        "title" => htmlspecialchars($info["title"]),
        "original" => htmlspecialchars($info["original"]),
        "source" => htmlspecialchars($imgUrl)
    ));
}

/* ����ץȡ���� */
return json_encode(array(
    'state'=> count($list) ? 'SUCCESS':'ERROR',
    'list'=> $list
));