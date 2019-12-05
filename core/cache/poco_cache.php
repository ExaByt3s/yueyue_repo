<?php
/**
 * poco_cache POCOĬ�ϻ���ϵͳ
 *
 *
 * @author erldy
 * @package cache
 */
class POCO_Cache extends poco_cache_class
{
	/**
	 * Ĭ�ϵĻ������
	 *
	 * @var array
	 */
	protected $_default_policy = array(
		/**
		 * ������Чʱ��
		 *
		 * �������Ϊ 0 ��ʾ��������ʧЧ������Ϊ null ���ʾ����黺����Ч�ڡ�
		 */
		'life_time'         => 900,
		'force_use_cache'   => false,//�Ƿ����ҳ���_no_cache����ǿ����cache
		'no_page_cache'		=> false,//�Ƿ����ͬҳ�е�$_PAGE_CACHE
	);

	/**
	 * ���캯��
	 *
	 * @param Ĭ�ϵĻ������ $default_policy
	 */
	function __construct(array $default_policy = null)
	{
		parent::poco_cache_class();
		if (isset($default_policy['life_time']))
        {
			$this->_default_policy['life_time'] = (int)$default_policy['life_time'];
		}		
	}

	/**
	 * д�뻺��
	 *
	 * @param string $id
	 * @param mixed $data
	 * @param array $policy
	 */
	function set($id, $data, array $policy = null)
	{
		$life_time = isset($policy['life_time']) ? (int)$policy['life_time'] : $this->_default_policy['life_time'];
		return $this->save_cache($id, $data, $life_time);
	}

	/**
	 * ��ȡ���棬ʧ�ܻ򻺴���ʧЧʱ���� false
	 *
	 * @param string $id
	 * @param array $policy
	 *
	 * @return mixed
	 */
	function get($id, array $policy = null)
	{
		$force_use_cache = isset($policy['force_use_cache']) ? $policy['force_use_cache'] : $this->_default_policy['force_use_cache'];
		$no_page_cache =  isset($policy['no_page_cache']) ? $policy['no_page_cache'] : $this->_default_policy['no_page_cache'];
		return $this->get_cache($id, $force_use_cache,$no_page_cache);
	}

	/**
	 * ɾ��ָ���Ļ���
	 *
	 * @param string $id
	 */
	function delete($id)
	{
		return $this->delete_cache($id);
	}
}

