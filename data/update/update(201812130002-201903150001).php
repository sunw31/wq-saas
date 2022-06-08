<?php

$all_wxapp_settings = pdo_fetchall("SELECT wx.uniacid, s.uniacid as id, s.creditnames, s.creditbehaviors FROM " . tablename('account_wxapp') . " AS wx LEFT JOIN " . tablename('uni_settings') . " AS s ON wx.uniacid = s.uniacid");

if (!empty($all_wxapp_settings)) {
	foreach ($all_wxapp_settings as $wxapp) {
		$unisettings = array();
		if (empty($wxapp['creditnames'])) {
			$unisettings['creditnames'] = array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1));
			$unisettings['creditnames'] = iserializer($unisettings['creditnames']);
		}
		if (empty($wxapp['creditbehaviors'])) {
			$unisettings['creditbehaviors'] = array('activity' => 'credit1', 'currency' => 'credit2');
			$unisettings['creditbehaviors'] = iserializer($unisettings['creditbehaviors']);
		}
		if (empty($unisettings)) {
			continue;
		}
		if (empty($wxapp['id'])) {
			$unisettings['uniacid'] = $wxapp['uniacid'];
			pdo_insert('uni_settings', $unisettings);
		} else {
			pdo_update('uni_settings', $unisettings, array('uniacid' => $wxapp['uniacid']));
		}
	}
}

$modules = pdo_fetchall("SELECT name FROM " . tablename('modules') . " WHERE issystem!=1");
foreach ($modules as $key => $val) {
	if (file_exists (IA_ROOT . '/addons/' . $val['name'] . '/icon-custom.jpg')) {
		$val['logo'] = 'addons/' . $val['name'] . '/icon-custom.jpg';
	} else {
		$val['logo'] = 'addons/' . $val['name'] . '/icon.jpg';
	}

	$module_info = module_fetch($val['name']);
	if (!empty($module_info) && empty($module_info['main_module'])) {
		pdo_update('modules', array('logo' => $val['logo']), array('name' => $val['name']));
	}
}



if (IMS_FAMILY != 'l') {
			setting_save(array('template' => '2.0'), 'basic');
}



$founder_own_users = tablename('users_founder_own_users');
$users = tablename('users');
$sql = <<<EOF
INSERT INTO $founder_own_users(`uid`, `founder_uid`) select `uid`, `owner_uid` from $users where `owner_uid` != 0;
EOF;
pdo_query($sql);


$founder_own_uni_groups = tablename('users_founder_own_uni_groups');
$uni_group = tablename('uni_group');
$sql = <<<EOF
INSERT INTO $founder_own_uni_groups(`founder_uid`, `uni_group_id`) select `owner_uid`, `id` from $uni_group where `owner_uid` != 0;
EOF;
pdo_query($sql);


$founder_own_users_groups = tablename('users_founder_own_users_groups');
$users_group = tablename('users_group');
$sql = <<<EOF
INSERT INTO $founder_own_users_groups(`founder_uid`, `users_group_id`) select `owner_uid`, `id` from $users_group where `owner_uid` != 0;
EOF;
pdo_query($sql);


load()->model('module');
$modules = pdo_fetchall("SELECT name FROM " . tablename('modules') . " WHERE issystem!=1");
foreach ($modules as $key => $val) {
	if (file_exists (IA_ROOT . '/addons/' . $val['name'] . '/icon-custom.jpg')) {
		$val['logo'] = 'addons/' . $val['name'] . '/icon-custom.jpg';
	} else {
		$val['logo'] = 'addons/' . $val['name'] . '/icon.jpg';
	}

	$module_info = module_fetch($val['name']);
	if (!empty($module_info)) {
		pdo_update('modules', array('logo' => $val['logo']), array('name' => $val['name']));
	}
}


$all_version = pdo_getall('wxapp_versions', array(), array('id', 'tominiprogram'));
if (!empty($all_version)) {
	foreach($all_version as $version) {
		$tominiprogram = iunserializer($version['tominiprogram']);
		if (empty($tominiprogram) || !is_array($tominiprogram)) {
			continue;
		}
		$data = array();
		foreach($tominiprogram as $key => $item) {
			if (!is_array($item) && !empty($item)) {
				$data[$item]['appid'] = $data[$item]['app_name'] = $item;
			}
		}
		if (!empty($data)) {
			pdo_update('wxapp_versions', array('tominiprogram' => iserializer($data)), array('id' => $version['id']));
		}
	}
}


$condition = array('title' => '', 'group_name' => 'frame', 'icon' => '', 'url' => '', 'type' => 'url', 'is_system' => 1);
pdo_delete('core_menu', $condition);



$fields = array('account_support', 'wxapp_support', 'welcome_support', 'webapp_support', 'phoneapp_support', 'xzapp_support', 'aliapp_support', 'baiduapp_support', 'toutiaoapp_support');
$delete_modules = pdo_getall('modules_recycle', array('type' => 2), array_merge($fields, array('name')), 'name');
if (!empty($delete_modules)) {
	foreach ($delete_modules as $name => $delete) {
		foreach ($fields as $field) {
			if ($delete[$field] == 1) {
				unset($delete_modules[$name]);
				break;
			}
		}
	}
	if (!empty($delete_modules)) {
		$count_modules_cloud = pdo_getcolumn('modules_cloud', array(), 'count(*)');
		if ($count_modules_cloud < count($delete_modules)) {
			load()->model('module');
			module_upgrade_info();
		}
		$cloud_modules = pdo_getall('modules_cloud', array('name' => array_keys($delete_modules)), array_merge($fields, array('name')), 'name');
		if (!empty($cloud_modules)) {
			foreach ($cloud_modules as $module) {
				if (!empty($module['name'])) {
					pdo_update('modules_recycle', array(
						'account_support' => $module['account_support'] == 2 ? 1: 0,
						'wxapp_support' => $module['wxapp_support'] == 2 ? 1: 0,
						'welcome_support' => $module['welcome_support'] == 2 ? 1: 0,
						'webapp_support' => $module['webapp_support'] == 2 ? 1: 0,
						'phoneapp_support' => $module['phoneapp_support'] == 2 ? 1: 0,
						'xzapp_support' => $module['xzapp_support'] == 2 ? 1: 0,
						'aliapp_support' => $module['aliapp_support'] == 2 ? 1: 0,
						'baiduapp_support' => $module['baiduapp_support'] == 2 ? 1: 0,
						'toutiaoapp_support' => $module['toutiaoapp_support'] == 2 ? 1: 0
					), array(
						'name' => $module['name'],
						'type' => 2
					));
				}
			}
		}
	}
}

$recycle_modules = pdo_getall('modules_recycle', array('type' => 1), array_merge($fields, array('name')), 'name');
if (!empty($recycle_modules)) {
	foreach ($recycle_modules as $name => $recycle) {
		foreach ($fields as $field) {
			if ($recycle[$field] == 1) {
				unset($recycle_modules[$name]);
				break;
			}
		}
	}
	if (!empty($recycle_modules)) {
		$modules = pdo_getall('modules', array('name' => array_keys($recycle_modules)), array_merge($fields, array('name')), 'name');
		if (!empty($modules)) {
			foreach ($modules as $module) {
				if (!empty($module['name'])) {
					pdo_update('modules_recycle', array(
						'account_support' => $module['account_support'] == 2 ? 1 : 0,
						'wxapp_support' => $module['wxapp_support'] == 2 ? 1 : 0,
						'welcome_support' => $module['welcome_support'] == 2 ? 1 : 0,
						'webapp_support' => $module['webapp_support'] == 2 ? 1 : 0,
						'phoneapp_support' => $module['phoneapp_support'] == 2 ? 1 : 0,
						'xzapp_support' => $module['xzapp_support'] == 2 ? 1 : 0,
						'aliapp_support' => $module['aliapp_support'] == 2 ? 1 : 0,
						'baiduapp_support' => $module['baiduapp_support'] == 2 ? 1 : 0,
						'toutiaoapp_support' => $module['toutiaoapp_support'] == 2 ? 1 : 0
					), array(
						'name' => $module['name'],
						'type' => 1
					));
				}
			}
		}
	}
}


$user_accounts = pdo_getall('uni_account_users', array('role' => 'clerk'));
if (!empty($user_accounts)) {
	foreach ($user_accounts as $user_account) {
		$data = array('uid' => $user_account['uid'], 'uniacid' => $user_account['uniacid']);
		$user_permission = pdo_get('users_permission', $data);
		if (!$user_permission) {
			pdo_delete('uni_account_users', $data);
			pdo_delete('users_lastuse', $data);
		}
	}
}


$files = array(
	IA_ROOT . '/web/themes/2.0/common/footer-base.html',
	IA_ROOT . '/web/themes/black/common/footer-base.html',
	IA_ROOT . '/web/themes/black/common/footer.html',
	IA_ROOT . '/web/themes/classical/common/footer-base.html',
);
foreach ($files as $file) {
	if (file_exists($file)) {
		@unlink($file);
	}
}


if (pdo_fieldexists('wxapp_versions', 'tominiprogram')) {
	$all_version = pdo_getall('wxapp_versions', array(), array('id', 'tominiprogram'));
	if (!empty($all_version)) {
		foreach($all_version as $version) {
			$tominiprogram = iunserializer($version['tominiprogram']);
			if (empty($tominiprogram) || !is_array($tominiprogram)) {
				continue;
			}
			if (is_array(current($tominiprogram)) && is_numeric(key($tominiprogram))) {
				$data = array();
				foreach($tominiprogram as $item) {
					if (!empty($item['appid'])) {
						$data[$item['appid']] = array(
							'appid' => $item['appid'],
							'app_name' => $item['app_name']
						);
					}
				}
				if (!empty($data)) {
					pdo_update('wxapp_versions', array('tominiprogram' => iserializer($data)), array('id' => $version['id']));
				}
			}
		}
		load()->model('cache');
		cache_clean(cache_system_key('miniapp_version'));
	}
}

load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.0.0', '201903150001');
return true;