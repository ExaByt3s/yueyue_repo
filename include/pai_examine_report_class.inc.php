<?php
/*
 * ͼƬ������
 */

class pai_examine_report_class extends POCO_TDG
{
    
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_pic_tbl' );
	}
    
    
    /**
     * ��Ӿٱ���Ϣ
     * $to_informer �ٱ���ID
     * $by_informer ���ٱ���ID
     * $data        �ٱ������ݣ����飬�������Ӿٱ� $data['url']=XXXXXX, ���ݣ�$data['text']=XXXXX
     * $come_from   �ٱ���Դ  'web'/'app'
     * 
     * */
    public function add_report_content($to_informer, $by_informer, $data ,$come_from = 'web')
    {
        return TRUE;
    }
    
}

?>