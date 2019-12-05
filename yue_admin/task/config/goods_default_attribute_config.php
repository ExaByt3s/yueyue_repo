<?php
return array(
			 array(
				   'name'  =>'商品名称',
				   'key'   =>'titles',
				   'input'  =>3,
				   'message'  =>'',
				   ),
				   /*
			 array(
				   'name'  =>'简介',
				   'key'   =>'introduction',
				   'input'  =>4,
				   'message'  =>$data['introduction'],
				   ),
				   */
			 array(
				   'name'  =>'价格',
				   'key'   =>'prices',
				   'input'  =>3,
				   'message'  =>'',
				   ),
			 array(
				   'name'  =>'单位',
				   'key'   =>'unit',
				   'input'  =>3,
				   'message'  =>'',
				   ),
			 array(
				   'name'  =>'库存',
				   'key'   =>'stock_num',
				   'input'  =>3,
				   'message'  =>'',
				   ),
				   /*
			 array(
				   'name'  =>'提示信息',
				   'key'   =>'prompt',
				   'input'  =>3,
				   'message'  =>$data['prompt'],
				   ),
				   */
			 array(
				   'name'  =>'服务地区',
				   'key'   =>'location_id',
				   'input'  =>8,
				   'message'  =>'',
				   ),
			 array(
				   'name'  =>'经纬度',
				   'key'   =>'lng_lat',
				   'input'  =>3,
				   'message'  =>'',
				   ),				   
			 array(
				   'name'  =>'自动接受',
				   'key'   =>'is_auto_accept',
				   'input'  =>2,
				   'message'  =>'',
				   'child_data' => array(
										   array('val'=>1,'name'=>'订单支付后，自动接受'),
										   ),
				   ),				   
			 array(
				   'name'  =>'自动签到',
				   'key'   =>'is_auto_sign',
				   'input'  =>2,
				   'message'  =>'',
				   'child_data' => array(
										   array('val'=>1,'name'=>'订单接受后，自动签到'),
										   ),
				   ),				   
				   /*
			 array(
				   'name'  =>'视频',
				   'key'   =>'video',
				   'input'  =>4,
				   'message'  =>$data['video'],
				   ),
			 array(
				   'name'  =>'案例信息',
				   'key'   =>'demo',
				   'input'  =>4,
				   'message'  =>$data['demo'],
				   ),
				   */
			 array(
				   'name'  =>'图文详情',
				   'key'   =>'content',
				   'input'  =>5,
				   'message'  =>'',
				   ),
			 );
?>