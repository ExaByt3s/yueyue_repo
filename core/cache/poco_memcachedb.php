<?php
/**
 * poco_cache POCOĬ�ϻ���ϵͳ
 *
 *
 * @author erldy
 * @package cache
 */

class POCO_Memcachedb
{
	/**
	 * ���캯��
	 *
	 * @param Ĭ�ϵĻ������ $default_policy
	 */
	function __construct(array $default_policy = null)
	{
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
		return false;
	}

	/**
	 * ��ȡ���棬ʧ�ܻ򻺴���ʧЧʱ���� false
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	function get($id, array $policy = null)
	{
		return false;
	}

	/**
	 * ɾ��ָ���Ļ���
	 *
	 * @param string $id
	 */
	function delete($id, array $policy = null)
	{
		return false;
	}
}

