<?php
/**
 * poco_cache POCO默认缓存系统
 *
 *
 * @author erldy
 * @package cache
 */
class POCO_Cache extends poco_cache_class
{
	/**
	 * 默认的缓存策略
	 *
	 * @var array
	 */
	protected $_default_policy = array(
		/**
		 * 缓存有效时间
		 *
		 * 如果设置为 0 表示缓存总是失效，设置为 null 则表示不检查缓存有效期。
		 */
		'life_time'         => 900,
		'force_use_cache'   => false,//是否忽略页面的_no_cache参数强制用cache
		'no_page_cache'		=> false,//是否忽略同页中的$_PAGE_CACHE
	);

	/**
	 * 构造函数
	 *
	 * @param 默认的缓存策略 $default_policy
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
	 * 写入缓存
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
	 * 读取缓存，失败或缓存撒失效时返回 false
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
	 * 删除指定的缓存
	 *
	 * @param string $id
	 */
	function delete($id)
	{
		return $this->delete_cache($id);
	}
}

