<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/11/26
 * Time: 11:15
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$gmclient= new GearmanClient();
$gmclient->addServers("172.18.5.211:9830");
$gmclient->setTimeout(5000); // ÉèÖÃ³¬Ê±
$req_param['uid']=100008;
$result= $gmclient->do("get_shield_user",json_encode($req_param) );
var_dump($result);

unset($gmclient);

$gmclient = POCO::singleton('pai_gearman_base_class');
$gmclient->connect('172.18.5.211', '9830');

$req_param['uid']=100008;
$result = $gmclient->_do("get_shield_user", $req_param, 'json');
var_dump($result);


exit();
$greaman_obj = POCO::singleton('pai_gearman_base_class');
$greaman_obj->connect('172.18.5.216', '9870');

$req_param['string_encode'] = 'http://www.yueus.com';
$result = $greaman_obj->_do('qrencode_string', $req_param);
var_dump($result);

$gmclient= new GearmanClient();
$gmclient->addServers("172.18.5.216:9870");
do
{
    $req_param['string_encode'] = 'http://www.yueus.com';
    $result= $gmclient->do("qrencode_string",json_encode($req_param) );
}
while($gmclient->returnCode() != GEARMAN_SUCCESS);
$ret = json_decode($result,true);
var_dump($ret);