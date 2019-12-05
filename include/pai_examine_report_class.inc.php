<?php
/*
 * 图片操作类
 */

class pai_examine_report_class extends POCO_TDG
{
    
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_pic_tbl' );
	}
    
    
    /**
     * 添加举报信息
     * $to_informer 举报人ID
     * $by_informer 被举报人ID
     * $data        举报的内容，数组，例如链接举报 $data['url']=XXXXXX, 内容：$data['text']=XXXXX
     * $come_from   举报来源  'web'/'app'
     * 
     * */
    public function add_report_content($to_informer, $by_informer, $data ,$come_from = 'web')
    {
        return TRUE;
    }
    
}

?>