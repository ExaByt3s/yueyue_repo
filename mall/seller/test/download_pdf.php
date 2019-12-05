<?php

/*
 * ÏÂÔØPDFÒ³Ãæ
 *
 *
 * authorÐÇÐÇ
 *
 * 2015-7-16
 */

$type = trim($_GET['type']);
$type_array = array("model","seller");

if(!in_array($type,$type_array))
{
    $type = "seller";
}

if($type=="seller")
{
    forceDownload("bangzhu.pdf");
}
else
{
    forceDownload("bangzhu_model.pdf");
}

function forceDownload($filename) {

    if (false == file_exists($filename)) {
        return false;
    }

// http headers
    header('Content-Type: application-x/force-download');
    header('Content-Disposition: attachment; filename="' . basename($filename) .'"');
    header('Content-length: ' . filesize($filename));

// for IE6
    if (false === strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) {
        header('Cache-Control: no-cache, must-revalidate');
    }
    header('Pragma: no-cache');

// read file content and output
    return readfile($filename);;
}










?>