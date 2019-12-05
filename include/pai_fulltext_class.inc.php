<?php
class pai_fulltext_class extends POCO_TDG
{
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_fulltext_tbl' );
	}
    
    
    public function add_data($insert_data)  
    {
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}  
    
    public function cp_data()
    {
        return true;
        $sql_str = "TRUNCATE pai_db.pai_fulltext_tbl";
        db_simple_getdata($sql_str, TRUE, 101);
        
        $sql_str = "SELECT user_id, nickname, role, location_id FROM pai_db.pai_user_tbl WHERE role ='model'";
        $result = db_simple_getdata($sql_str, FALSE, 101);
        foreach($result AS $key=>$val)
        {
            $user_id    = $val['user_id'];
            $nickname   = $val['nickname'];
            $role       = $val['role'];
            $date_num   = $this->get_date_rank($user_id);
            $score_num  = $this->get_score_rank($user_id);
            $fulltext   = $user_id . ";" . $nickname . ";" . $this->get_full_text($user_id);
            $show       = $this->fulltext_check_id($user_id);
            $location_id= $val['location_id'];
            
            $sql_str = "INSERT IGNORE INTO pai_db.pai_fulltext_tbl(`user_id`, `nickname`, `date_num`, `score_num`, `role`, `fulltext`, `is_show`, `location_id`) 
                        VALUES ('{$user_id}', '{$nickname}', '{$date_num}', '{$score_num}', '{$role}', '{$fulltext}', '{$show}', '{$location_id}')";
            echo $sql_str . "<BR>";
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }
    
    public function get_full_text($user_id)
    {
        $sql_str = "SELECT style FROM pai_db.pai_model_style_v2_tbl WHERE user_id=$user_id";
        
        $fulltext = '';
        $result = db_simple_getdata($sql_str, FALSE, 101);
        foreach($result AS $key=>$val)
        {
            $fulltext .= $val['style'];
        }
        
        $model_add_obj  = POCO::singleton('pai_model_add_class');

        $label_array['A1'] = '真空';
        $label_array['A2'] = '全裸';
        $label_array['A3'] = '内衣/比坚尼';
        $label_array['B1'] = '甜美';
        $label_array['B2'] = '糖水';
        $label_array['B3'] = '情绪';
        $label_array['C1'] = '日韩';
        $label_array['C2'] = '欧美';
        $label_array['D1'] = '古装';
        $label_array['D2'] = '文艺复古';
        $label_array['E1'] = '礼仪';
        $label_array['E2'] = '车展';
        $label_array['E3'] = '走秀';
        $label_array['E4'] = '淘宝';

        $list = $model_add_obj->get_label_info($user_id);
        if($list)
        {
            foreach($list AS $key=>$val)
            {
                $label_key = substr($val['label'], 0, 2);
                $label_str = $label_array[$label_key]?$label_array[$label_key]:$val['label'];

                $fulltext .= $label_str;

                unset($label_key);
                unset($label_str);
            }
        }
        
        return $fulltext;
    }
    
    
    public function cp_data_by_user_id($user_id)
    {
        $sql_str = "DELETE FROM pai_db.pai_fulltext_tbl WHERE user_id=$user_id";
        db_simple_getdata($sql_str, TRUE, 101);
        
        $sql_str = "SELECT user_id, nickname, role, location_id FROM pai_db.pai_user_tbl WHERE user_id=$user_id";
        $result = db_simple_getdata($sql_str, FALSE, 101);
        foreach($result AS $key=>$val)
        {
            $user_id    = $val['user_id'];
            $nickname   = $val['nickname'];
            $role       = $val['role'];
            $date_num   = $this->get_date_rank($user_id);
            $score_num  = $this->get_score_rank($user_id);
            $fulltext   = $user_id . ";" . $nickname . ";" . $this->get_full_text($user_id);
            $show       = $this->fulltext_check_id($user_id);
            $location_id= $val['location_id'];
            
            $sql_str = "INSERT IGNORE INTO pai_db.pai_fulltext_tbl(user_id, nickname, date_num, score_num, role, `fulltext`, is_show, location_id) 
                        VALUES ('{$user_id}', :x_nickname, '{$date_num}', '{$score_num}', '{$role}', :x_fulltext, '{$show}', '{$location_id}')";
            sqlSetParam($sql_str, 'x_nickname', $nickname);
            sqlSetParam($sql_str, 'x_fulltext', $fulltext);
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }
    
    public function get_score_rank($user_id)
    {
        $sql_str = "SELECT num FROM pai_db.pai_comment_score_rank_tbl WHERE user_id=$user_id";
        $result = db_simple_getdata($sql_str, TRUE, 101);
        if($result)
        {
           return $result['num']; 
        }else{
           return 3;
        }
        
        return FALSE;     
    }
    
    public function get_date_rank($user_id)
    {
        $sql_str = "SELECT num FROM pai_db.pai_date_rank_tbl WHERE user_id=$user_id";
        $result = db_simple_getdata($sql_str, TRUE, 101);
        if($result)
        {
            return $result['num'];   
        }
        
        return FALSE;
    }
    
    /**
     * 全文表处理队列
     * @param $user_id 用户的yue_id
     * @param $act     操作；'add'/'update'/'del'
     * 
     * */
    public function add_fulltext_act($user_id, $act = 'update')
    {
        $add_time = date('Y-m-d H:i:s');
        $sql_str = "INSERT INTO pai_db.pai_fulltext_act_tbl(user_id, act, add_time) 
                    VALUES ($user_id, '{$act}', '{$add_time}')";
        db_simple_getdata($sql_str, TRUE, 101);
    }
    
    public function get_fulltext_act()
    {
        $sql_str = "SELECT * FROM pai_db.pai_fulltext_act_tbl WHERE status=0";
        $result = db_simple_getdata($sql_str, FALSE, 101);
        foreach($result AS $key=>$val)
        {
            $this->cp_data_by_user_id($val['user_id']);
        }
    }
    
    public function fulltext_check_id($user_id)
    {
        $sql_str = "SELECT is_approval FROM pai_db.pai_model_audit_tbl WHERE user_id=$user_id";
        $result = db_simple_getdata($sql_str, TRUE, 101);
        return $result['is_approval'];
    }
}
?>