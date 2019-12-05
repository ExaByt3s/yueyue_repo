<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/8/4
 * Time: 13:44
 */

$post_data ['send_user_id'] = ( string ) 10002;
$post_data ['to_user_id'] = ( string ) 100008;
$post_data ['content'] = iconv ( 'gbk', 'utf-8', 'ฒโสิฒโสิฃกฃก' );
$post_data ['media_type'] = 'text';
$post_data = json_encode ( $post_data );
$data ['data'] = $post_data;

yueyue_message_base_service($data);

function yueyue_message_base_service($data)
{
    $url = 'http://113.107.204.233:8080/sendserver.cgi';

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 10);
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch );
    curl_close ( $ch );
    //var_dump($result);

    $url = 'http://113.107.204.233:8080/sendmessage.cgi';
    $post_data = json_decode($data['data'], true);
    $post_data['send_user_role'] = 'yuebuyer';
    $data['data'] = json_encode($post_data);
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 10);
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch );
    curl_close ( $ch );
    //var_dump($result);

    $url = 'http://113.107.204.233:8080/sendmessage.cgi';
    $post_data = json_decode($data['data'], true);
    $post_data['send_user_role'] = 'yueseller';
    $data['data'] = json_encode($post_data);
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 10);
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch );
    curl_close ( $ch );
    //var_dump($result);
}