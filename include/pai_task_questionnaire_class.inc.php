<?php
/**
 * 问卷调查
 * @author KOKO
 * @copyright 2015-04-09
 */

class pai_task_questionnaire_class extends POCO_TDG
{
	/**
	 * 初始化
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_task_db');
	}
	
	/**
	 * 获取问卷列表
	 * @param int $service_id 问卷类型ID
	 * @param int $type 返回数据方式,1 数组 2 格式化 3 json
	 * 
	 * @return json
	 */	
	public function get_questionnaire_list($service_id,$type=1)
	{
		$service_id=(int)$service_id;
		if($service_id)
		{
			$this->setTableName('pai_questionnaire_tbl');
			$questionnaire = $this->find('type="'.$service_id.'" and status="Y"');
			$this->setTableName('task_service_tbl');
			$service_name = $this->find('service_id="'.$service_id.'"');
			$questionnaire['service_name'] = $service_name['service_name'];
			unset($questionnaire['status']);
			$this->setTableName('pai_questionnaire_titles_tbl');
			$q_t = $this->findAll('questionnaire_id="'.$questionnaire['id'].'"','','step asc,id asc');
			$this->setTableName('pai_questionnaire_detail_tbl');
			foreach ($q_t  as $key => $val)
			{
				unset($q_t[$key]['questionnaire_titles_id']);
				unset($q_t[$key]['questionnaire_id']);
				unset($q_t[$key]['step']);
				$q_t_d = $this->findAll('questionnaire_titles_id="'.$val['id'].'"','','step asc,id asc');
				foreach ($q_t_d as $key_d => $val_d)
				{
					if($val_d['show'] == 1)
					{
						if($val_d['questionnaire_detail_id'])
						{
							$tt_detail_2 = $this->findAll('id in ('.$val_d['questionnaire_detail_id'].')','','step asc,id asc','id,is_input,titles,default_titles');
							foreach($tt_detail_2 as $key_d_2 => $val_d_2)
							{
								$tt_detail_2[$key_d_2]['link'] = 0;
								$tt_detail_2[$key_d_2]['type'] = $val_d['type'];
								$tt_detail_2[$key_d_2]['jump_id'] = 0;
							}
							$val_d['data'] = $tt_detail_2;
						}
						unset($val_d['questionnaire_titles_id']);
						unset($val_d['step']);
						unset($val_d['show']);
						unset($val_d['questionnaire_detail_id']);
						$q_t[$key]['data'][]=$val_d;
					}
				}
			}
		}
		$questionnaire['data'] = $q_t;
		if($type != 1)
		{
			$questionnaire = $type == 2?serialize($questionnaire):json_encode($questionnaire);
		}
		return $questionnaire;
	}

	/**
	 * 获取不同版本问卷列表
	 * @param int $service_id 问卷类型ID
	 * @param int $version 版本号,如果版本号没有就获取最新的
	 * 
	 * @return array
	 */	
	public function get_questionnaire_version_list($service_id,$version='')
	{
		$sql = 'select qv.* from `'.$this->_db_name.'`.pai_questionnaire_tbl as qt,`'.$this->_db_name.'`.pai_questionnaire_version_log_tbl as qv where qt.type="'.$service_id.'" and qt.status="Y" and qt.id=qv.questionnaire_id';
		if($version)
		{
			$sql .= ' and qv.version="'.$version.'"';
		}
		else
		{
			$sql .= ' order by qv.id desc';
		}
		//echo $sql;
		$re = $this->query($sql);
		//print_r($re);
		$re[0]['content'] = iconv('utf-8','gbk',$re[0]['content']);
		return unserialize($re[0]['content']);
	}
	
	/**
	 * 展示问卷答案
	 * @param int $request_id 订单ID
	 * 
	 * @return array
	 */	
	public function show_questionnaire_data($request_id)
	{
		$id = (int)$request_id;
		
		$this->setTableName('task_request_tbl');
		$re_data = $this->find('request_id="'.$id.'"');
		$questionnaire_id = $re_data['questionnaire_id'];
		$version = $re_data['version'];
		$this->setTableName('task_request_question_tbl');
		$data = $this->findAll('request_id="'.$re_data['request_id'].'"');
		$this->setTableName('pai_questionnaire_version_log_tbl');
		$questionnaire = $this->find('questionnaire_id="'.$questionnaire_id.'" and version="'.$version.'"');
		$questionnaire['content'] = iconv('utf-8','gbk',$questionnaire['content']);		
		$questionnaire_data = unserialize($questionnaire['content']);
		$anwser_data = array();
		foreach($data as $val)
		{
			$anwser_data[$val['question_detail_id']] = $val;
		}
		foreach($questionnaire_data['data'] as $key => $val)
		{
			foreach($val['data'] as $key_de => $val_de)
			{
				if($anwser_data[$val_de['id']])
				{
					if($val_de['is_input'] or in_array($val_de['type'],array(4,6,7,8,9)))
					{
						if($val_de['type']==6)
						{
							$questionnaire_data['data'][$key]['data'][$key_de]['titles'] = (int)$anwser_data[$val_de['id']]['message']?get_poco_location_name_by_location_id($anwser_data[$val_de['id']]['message']):$anwser_data[$val_de['id']]['message'];
						}
						else
						{
							$questionnaire_data['data'][$key]['data'][$key_de]['titles'] = $anwser_data[$val_de['id']]['message'];
						}						
					}
					if($val_de['data'])
					{
						foreach($val_de['data'] as $key_de_2 => $val_de_2)
						{
							if($anwser_data[$val_de_2['id']])
							{
								if($val_de_2['is_input'] or in_array($val_de_2['type'],array(4,6,7,8,9)))
								{
									if($val_de_2['type'] == 6)
									{
										$questionnaire_data['data'][$key]['data'][$key_de]['data'][$key_de_2]['titles'] = (int)$anwser_data[$val_de_2['id']]['message']?get_poco_location_name_by_location_id($anwser_data[$val_de_2['id']]['message']):$anwser_data[$val_de_2['id']]['message'];
									}
									else
									{
										$questionnaire_data['data'][$key]['data'][$key_de]['data'][$key_de_2]['titles'] = $anwser_data[$val_de_2['id']]['message'];
									}									
								}
							}
							else
							{
								unset($questionnaire_data['data'][$key]['data'][$key_de]['data'][$key_de_2]);
							}
						}
						$questionnaire_data['data'][$key]['data'][$key_de]['data'] = array_values($questionnaire_data['data'][$key]['data'][$key_de]['data']);
					}
				}
				else
				{
					unset($questionnaire_data['data'][$key]['data'][$key_de]);
				}				
			}
			$questionnaire_data['data'][$key]['data'] = array_values($questionnaire_data['data'][$key]['data']);
		}
		return $questionnaire_data;
	}
}
