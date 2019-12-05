<?php
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

$gmclient= new GearmanClient();
$gmclient->addServers("172.18.5.211:9830");
$gmclient->setTimeout(5000); // ÉèÖÃ³¬Ê±
echo "Sending job\n";
do
{
	$req_param['pocoid'] = 100293;
    echo json_encode($req_param);
    $result= $gmclient->doBackground("add_blacklist",json_encode($req_param) );

}
while(false);
//while($gmclient->returnCode() != GEARMAN_SUCCESS);
echo "Success: $result\n";

?>
