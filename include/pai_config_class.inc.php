<?php
/*
 * 配置操作类
 */

class pai_config_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
	
	}
	
	/*
	 * 大外拍活动ID配置
	 * 
	 * @param string  $type 
	 * 
	 * return array 
	 */
	public function big_waipai_event_id_arr($type='')
	{
		
		$waipai_config = include '/disk/data/htdocs232/poco/pai/config/waipai_event_config.php';
		
		if($type)
		{
			$__event_id_str = $waipai_config[$type];
			$event_id_arr = explode(",",$event_id_str);
		}
		else
		{
			foreach($waipai_config as $k=>$val)
			{
				$__event_id_str .= $val.',';
				
			}
			$trim_str = rtrim($__event_id_str,',');
			$event_id_arr = explode(",",$trim_str);		
		}
		
		return $event_id_arr;
	}
	
	
	/*
	 * 问卷小分类获取大分类
	 */
	public function question_big_style($style='')
	{
		$config = include ('/disk/data/htdocs232/poco/pai/mobile/config/demand.conf.php');
	
		$arr = $config['data_model']['total_data'][1]['content']['data'];
		
		foreach($arr as $val)
		{
			foreach($val['son_txt'] as $bval)
			{
				if($bval['text']==$style)
				{
					return $val['text'];
				}
			}
		}
	}

}

?>