<?php
/*
 * 美丽变现操作类
 */

class pai_beauty_cash_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_beauty_cash_tbl' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_info($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}
    
    public function del_info($id)
    {
        $id = (int)$id;

		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->delete($where_str);
        
    }
    
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_info($data, $id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_info_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_beauty_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		
		return $ret;
	}
	
	public function get_beauty_rank_percent($price)
	{
		$sql = "SELECT COUNT(*) AS rank FROM pai_db.pai_beauty_cash_tbl WHERE price>={$price};";
		$rank = db_simple_getdata($sql,true,101);
		
		$sql = "SELECT COUNT(*) AS count FROM pai_db.pai_beauty_cash_tbl";
		$count = db_simple_getdata($sql,true,101);
		
		$percent = (1-($rank['rank']/$count['count']))*100;
		
		return sprintf("%.2f", $percent);;
	}
	
	
	
	public function cal_price($height=0,$weight=0,$bust=0,$waist=0,$hip=0)
	{

		$top = 14000;
				
		$cal_bust = $height*0.53;
		$cal_waist = $height*0.375;
		$cal_hip = $height*0.55;
		
		if($cal_hip==0) 
		{
			$cal_hip=1;//保证分母不为0
		}
		
		$cal_waist_hip = $waist/$hip;
		$cal_bmi = $weight/pow(($height/100),2);
		
		$diff_bust = $cal_bust-$bust*2.54;
		$diff_waist = $cal_waist-$waist*2.54;
		$diff_hip = $cal_hip-$hip*2.54;
		$diff_waist_hip = 0.685-$cal_waist_hip;
		$diff_bmi = 17-$cal_bmi;
		
		$offset = abs($diff_bust)+abs($diff_waist)+abs($diff_hip)+abs($diff_waist_hip)+abs($diff_bmi);
		
		$price = $top/$offset;
		
		$price = (($price/35)/12)*10000;
		
		return $price;
		
	}
	

}

?>