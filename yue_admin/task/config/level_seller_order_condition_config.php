<?php
//条件 => 等级  如 0,4=>1 意思是0到4笔的交易就是1级
return array(
			 '0,4'=>0,
			 '5,9'=>1,
			 '10,19'=>2,
			 '21,49'=>3,
			 '50,99'=>4,
			 '100,199'=>5,
			 '200,499'=>6,
			 '500,999'=>7,
			 '1000,1999'=>8,
			 '2000,4999'=>9,
			 '5000,1000000'=>10,
);
?>