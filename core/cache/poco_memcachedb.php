<?php
/**
 * poco_cache POCO默认缓存系统
 *
 *
 * @author erldy
 * @package cache
 */

class POCO_Memcachedb
{
	/**
	 * 构造函数
	 *
	 * @param 默认的缓存策略 $default_policy
	 */
	function __construct(array $default_policy = null)
	{
	}

	/**
	 * 写入缓存
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
	 * 读取缓存，失败或缓存撒失效时返回 false
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
	 * 删除指定的缓存
	 *
	 * @param string $id
	 */
	function delete($id, array $policy = null)
	{
		return false;
	}
}

