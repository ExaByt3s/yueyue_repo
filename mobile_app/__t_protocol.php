<?php

$query['tag'] = '日系';
$query['price'] = '10-50';
$query['hour'] = '2';
$query['order'] = 'num DESC';
$query['key'] = '美女';
// 请求参数
$param = array('location_id' =>101029001, 'query'=>$query, 'limit'=>'0,10');

echo json_encode($param);
exit();
include_once("protocol_commin.inc.php");

$cp = new poco_communication_protocol_class();
$client_data = $cp->input();
var_dump($client_data);


if($client_data['code'] != 100)
{
    
}

$json_arr = array(
                'width' => '11111',
                'height' => 2222,
                'link' => 'http://www.poco.cn', 
                'title' => 3333333
                );
                
var_dump($json_arr);

$format = array(
        'integer' => 'width,height',
        'string' => 'link,title, url');
$json_arr = $cp->data_format($json_arr, $format);
var_dump($json_arr);

$options['data'] = $json_arr;
$cp->output($options);
?>