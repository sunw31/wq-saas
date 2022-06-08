<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function setting_save($data = '', $key = '') {
	if (empty($data) && empty($key)) {
		return false;
	}
	if (is_array($data) && empty($key)) {
		foreach ($data as $key => $value) {
			$record[] = "('$key', '" . iserializer($value) . "')";
		}
		if ($record) {
			$return = pdo_query("REPLACE INTO " . tablename('core_settings') . " (`key`, `value`) VALUES " . implode(',', $record));
		}
	} else {
		$return = pdo_insert('core_settings', array('key' => $key, 'value' => iserializer($data)), true);
	}
	$cachekey = cache_system_key('setting');
	cache_write($cachekey, '');
	return $return;
}


function setting_load($key = '') {
	global $_W;

	$cachekey = cache_system_key('setting');
	$settings = cache_load($cachekey);
	if (empty($settings)) {
		$settings = pdo_getall('core_settings', array(), array(), 'key');
		if (is_array($settings)) {
			foreach ($settings as $k => &$v) {
				$settings[$k] = iunserializer($v['value']);
			}
		}
		cache_write($cachekey, $settings);
	}
	$_W['setting'] = empty($_W['setting']) ? array() : $_W['setting'];
	$_W['setting'] = array_merge($settings, (array)$_W['setting']);
	if (!empty($key)) {
		return array($key => $settings[$key]);
	} else {
		return $settings;
	}
}

function setting_upgrade_version($family, $version, $release) {
	if (version_compare(IMS_FAMILY, $version, '>=')) {
		return true;
	}
	$vaild_family = array('s', 'v', 'x', 'l');
	$family = in_array($family, $vaild_family) ? $family : 'v';
	$settings = [
		'version' => $version,
		'family' => $family,
		'release_date' => $release,
	];
	$result = setting_save($settings, 'local_version_info');
	return $result;
}
