<?php

/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

function cache_memcached() {
	global $_W;
	static $memcacheobj;
	if (!extension_loaded('memcached')) {
		return error(1, 'Class Memcached is not found');
	}
	if (empty($memcacheobj)) {
		$config = $_W['config']['setting']['memcached'];
		$memcacheobj = new Memcached();
		$connect = $memcacheobj->addServer(
			$config['server'],
			!empty($config['port']) ? $config['port'] : 11211,
			!empty($config['weight']) ? $config['weight'] : 1
		);
		if (isset($config['username']) && isset($config['password'])) {
			$memcacheobj->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
			$memcacheobj->setSaslAuthData($config['username'], $config['password']);
		}
		if (!$connect) {
			return error(-1, 'Memcached is not in work');
		}
	}

	return $memcacheobj;
}


function cache_read($key) {
	$memcache = cache_memcached();
	if (is_error($memcache)) {
		return $memcache;
	}
	$result = $memcache->get(cache_prefix($key));
	return $result;
}


function cache_search($key) {
	return cache_read(cache_prefix($key));
}


function cache_write($key, $value, $ttl = 0) {
	$memcache = cache_memcached();
	if (is_error($memcache)) {
		return $memcache;
	}
	if ($memcache->set(cache_prefix($key), $value, $ttl)) {
		return true;
	} else {
		return false;
	}
}


function cache_delete($key) {
	$memcache = cache_memcached();
	if (is_error($memcache)) {
		return $memcache;
	}
	$cache_relation_keys = cache_relation_keys($key);
	if (is_error($cache_relation_keys)) {
		return $cache_relation_keys;
	}
	if (is_array($cache_relation_keys) && !empty($cache_relation_keys)) {
		foreach ($cache_relation_keys as $key) {
			$cache_info = cache_load($key);
			if (!empty($cache_info)) {
				$origins_cache_key = $key;
				$result = $memcache->delete(cache_prefix($key));
				unset($GLOBALS['_W']['cache'][$origins_cache_key]);
				if (!$result) {
					return error(1, '缓存: ' . $key . ' 删除失败！');
				}
			}
		}
	}

	return true;
}


function cache_clean() {
	$memcache = cache_memcached();
	if (is_error($memcache)) {
		return $memcache;
	}
	if ($memcache->flush()) {
		unset($GLOBALS['_W']['cache']);
		return true;
	} else {
		return false;
	}
}

function cache_prefix($key) {
	return $GLOBALS['_W']['config']['setting']['authkey'] . $key;
}
