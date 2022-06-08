<?php

load()->model('cloud');
load()->model('module');
if (!pdo_fieldexists('modules','from')) {
	pdo_query("ALTER TABLE " . tablename('modules') . " ADD `from` varchar(50) NOT NULL DEFAULT 'cloud';");

	$module_list = module_installed_list();
	$cloud_m_query_module_pageinfo = cloud_m_query(array(), 1);
	$cloud_m_query_module = $cloud_m_query_module_pageinfo['data'];
	if ($cloud_m_query_module_pageinfo['page'] > 1) {
		for($i = 2;$i <= $cloud_m_query_module['page']; $i++) {
			$cloud_m_query_module_i = cloud_m_query(array(), $i);
			$cloud_m_query_module = array_merge($cloud_m_query_module, $cloud_m_query_module_i['data']);
		}
	}

	if (!empty($cloud_m_query_module) && !empty($module_list)) {
		$cloud_m_query_module = array_keys($cloud_m_query_module);
		foreach ($module_list as $module_name => $module_info) {
			$where = array('name' => $module_name);
			$module_exist = pdo_get('modules', $where);
			if (!empty($module_exist)) {
				if (in_array($module_name, $cloud_m_query_module)) {
					pdo_update('modules', array('from' => 'cloud'), $where);
				} else {
					pdo_update('modules', array('from' => 'local'), $where);
				}
			}
		}
	}
}
if (!pdo_fieldexists('uni_settings', 'remote')) {
	pdo_query('ALTER TABLE ' . tablename('uni_settings') . " ADD `remote` varchar(2000) NOT NULL DEFAULT '';");

	load()->model('cache');
	$remote = pdo_getcolumn('core_settings', array('key' => 'remote'), 'value');
	$remote = iunserializer($remote);
	if (!empty($remote) && is_array($remote)) {
		foreach ($remote as $uniacid => $item) {
			if (is_numeric($uniacid)) {
				$unisetting = pdo_get('uni_settings', array('uniacid' => $uniacid), array('uniacid'));
				if (!empty($unisetting)) {
					pdo_update('uni_settings', array('remote' => iserializer($item)), array('uniacid' => $uniacid));
				} else {
					pdo_insert('uni_settings', array('remote' => iserializer($item), 'uniacid' => $uniacid));
				}
				cache_delete(cache_system_key('uniaccount', array('uniacid' => $uniacid)));
				unset($remote[$uniacid]);
			}
		}
		pdo_update('core_settings', array('value' => iserializer($remote)), array('key' => 'remote'));
	}
}

load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.0.7', '201905180004');
return true;