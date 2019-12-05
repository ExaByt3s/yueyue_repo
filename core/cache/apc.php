<?php
/**
 * Cache_APC ��ʹ�� APC ��չ����������
 *
 * @package cache
 */
class Cache_APC
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
	);

	/**
	 * ���캯��
	 *
	 * @param Ĭ�ϵĻ������ $default_policy
	 */
	function __construct(array $default_policy = null)
	{
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
		apc_store($id, $data, $life_time);
	}

	/**
	 * ��ȡ���棬ʧ�ܻ򻺴���ʧЧʱ���� false
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	function get($id)
	{
		return apc_fetch($id);
	}

	/**
	 * ɾ��ָ���Ļ���
	 *
	 * @param string $id
	 */
	function delete($id)
	{
		apc_delete($id);
	}
}
