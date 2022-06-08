<?php

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

setting_upgrade_version(IMS_FAMILY, '2.0.5', '201905080001');
return true;