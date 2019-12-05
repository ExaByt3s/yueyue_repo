<?php

$gmclient= new GearmanClient();
$gmclient->addServers("113.107.204.233:9830");
do
{
	
	$req_param['pocoid']='103231';
    //echo json_encode($req_param);	
    $result= $gmclient->do("get_pushtoken",json_encode($req_param) );
    print_r($result);
}
while($gmclient->returnCode() != GEARMAN_SUCCESS);

exit();
/*
 * Gearman PHP Extension
 *
 * Copyright (C) 2008 James M. Luedke <contact@jamesluedke.com>,
 *                    Eric Day <eday@oddments.org>
 * All rights reserved.
 *
 * Use and distribution licensed under the PHP license.  See
 * the LICENSE file in this directory for full text.
 */

echo "Starting\n";

# Create our client object.
$gmclient= new GearmanClient();

# Add default server (localhost).
$gmclient->addServers("113.107.204.233:9830");

echo "Sending job\n";
# Send reverse job
do
{
	
	$req_param['pocoid']='6468095';
	$req_param['os']='ios';
	$req_param['osver']='7.0.0';
	$req_param['appver']='1.0.0';
	$req_param['machinecode']='e1d276f71ede916a5d09f3c893facf0fb5406e706ecc7dc4c40fa503c7ab4968';
	$req_param['ios_app_type']='dev'; //'dev','pro'
  echo json_encode($req_param);	
  $result= $gmclient->doBackground("save_pushtoken",json_encode($req_param) );
  # Check for various return packets and errors.
  /*
  switch($gmclient->returnCode())
  {
    case GEARMAN_WORK_DATA:
      echo "Data: $result\n";
      break;
    case GEARMAN_WORK_STATUS:
      list($numerator, $denominator)= $gmclient->doStatus();
      echo "Status: $numerator/$denominator complete\n";
      break;
    case GEARMAN_SUCCESS:
      break;
    default:
      echo "RET: " . $gmclient->returnCode() . "\n";
      exit;
  }*/
}
while($gmclient->returnCode() != GEARMAN_SUCCESS);
echo "Success: $result\n";

?>
