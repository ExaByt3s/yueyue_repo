<?php
/**
 * 测试类
 * 
 * 
 */
exit;
class pai_mall_test_song_class extends POCO_TDG
{
    
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
	
	/**
	 *
	 */
	private function set_mall_goods_tbl()
	{
		$this->setTableName('mall_goods_tbl');
	}
    
    private function set_mall_goods_prices_tbl()
    {
        $this->setTableName('mall_goods_prices_tbl');
    }
    
    //2124194
    public function do_update($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $this->set_mall_goods_prices_tbl();
        $org_data = $this->findAll("goods_id='$goods_id'");
        
        if( ! empty($org_data) )
        {
            $price_ary = $price_list_ary = array();
            $sum_stock_num = $sum_stock_num_total = '';
            foreach($org_data as $k => $v)
            {
                $unit_price_list = array();
                $price_ary[] = $v['prices'];
                $sum_stock_num += $v['stock_num'];
                $sum_stock_num_total+= $v['stock_num_total'];
                if( ! empty($v['prices_list']) )
                {
                    $unit_price_list = unserialize($v['prices_list']);
                    
                    if( ! empty($unit_price_list) )
                    {
                        foreach($unit_price_list as $kp => $vp)
                        {
                            $price_list_ary[$kp] = $vp;
                        }
                    }
                    
                }else
                {
                    if( ! empty($v['type_id']))
                    {
                         $price_list_ary[$v['type_id']] = array(
                            'prices'=>$v['prices'],
                         );
                    }
                }
                
                
            }
            
            if( ! empty($price_ary) )
            {
                $price_min = min(array_filter($price_ary));
            }
            
            $update_data = array(
              'stock_num'=>$sum_stock_num,
              'stock_num_total'=>$sum_stock_num_total,
              'prices'=>$price_min,
              'prices_list'=>  serialize($price_list_ary),  
                
            );
            $this->set_mall_goods_tbl();
            $rs = $this->update($update_data, "goods_id='$goods_id'");
            
            //放出变量
            unset($update_data);
            unset($sum_stock_num);
            unset($sum_stock_num_total);
            unset($price_ary);
            unset($price_min);
            unset($price_list_ary);
            
            return $rs;
            
            
            
        }
    }
    
    public function excel_upload()
    {
        
    }
    
    public function read_excel()
    {
        
    }
    
    
    
    
    
}
