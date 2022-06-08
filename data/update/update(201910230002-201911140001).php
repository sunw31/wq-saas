<?php

// function user_save_operate_star2($uid, $type, $uniacid, $module_name) {
//         global $_W;
// 	load()->model('module');
// 	if (!in_array($type, array(1, 2)) || empty($uniacid)) {
// 		return error(-1, '参数不合法！');
// 	}
// 	if (2 == $type) {
// 		if (!empty($module_name) && !pdo_get('uni_modules', array('uniacid' => $uniacid, 'module_name' => $module_name))) {
// 			return error(-1, '平台账号无该模块权限，请更新缓存后重试！');
// 		}
// 	}
// 	$data = array('uid' => $uid, 'uniacid' => $uniacid, 'module_name' => $module_name, 'type' => $type);
// 	if (1 == $type) {
// 		unset($data['module_name']);
// 	}
// 	$if_exists = pdo_get('users_operate_star', $data);
// 	if ($if_exists) {
// 		$result = pdo_delete('users_operate_star', $data);
// 	} else {
// 		$data['createtime'] = TIMESTAMP;
// 		$maxrank = pdo_fetchcolumn("SELECT max(`rank`) FROM " . tablename('users_operate_star') . " WHERE uid=" . $_W['uid']);
// 		$data['rank'] = intval($maxrank) + 1;
// 		$result = pdo_insert('users_operate_star', $data);
// 	}
// 	if ($result) {
// 		return error(0, '');
// 	} else {
// 		return error(-1, '设置失败！');
// 	}
// }
// $shortcult_list = pdo_getall('core_menu_shortcut', array('position' => 'home_welcome_system_common'));
// if (!empty($shortcult_list)) {
// 	$account_info = pdo_getall('account', array('uniacid IN' => array_column($shortcult_list, 'uniacid')), array(),'uniacid');
// 	foreach ($shortcult_list as $info) {
// 		if (empty($info['uniacid'])) {
// 			continue;
// 		}
// 		if (empty($account_info[$info['uniacid']]) || 1 == $account_info[$info['uniacid']]['isdeleted']) {
// 			continue;
// 		}
// 		$uni_modules_table = table('uni_modules');
// 		$uni_modules_table->searchGroupbyModuleName();
// 		$own_account_modules_all = $uni_modules_table->getModulesByUid($info['uid']);
// 		if (!empty($info['modulename']) && !in_array($info['modulename'], array_column($own_account_modules_all['modules'], 'module_name'))) {
// 			continue;
// 		}
// 		if (!empty($info['modulename'])) {
// 			$data[] = array('module_name' => $info['modulename'], 'uniacid' => $info['uniacid'], 'uid' => $info['uid']);
// 		} else {
// 			$data[] = array('uniacid' => $info['uniacid'], 'uid' => $info['uid']);
// 		}
// 	}
// 	foreach ($data as $item) {
// 		if (!empty($item['module_name'])) {
// 			$result = user_save_operate_star2($item['uid'], 2, $item['uniacid'], $item['module_name']);
// 		} else {
// 			$result = user_save_operate_star2($item['uid'], 1, $item['uniacid'], '');
// 		}
// 	}
// }

// $menu_array = array('help', 'custom_help');
// foreach ($menu_array as $permission_name) {
// 	$menu_db = pdo_get('core_menu', array('permission_name' => $permission_name));

// 	if (!empty($menu_db)) {
// 		pdo_update('core_menu', array('is_display' => 0), array('permission_name' => $permission_name));
// 	} else {
// 		$menu_data = array('is_display' => 0, 'permission_name' => $permission_name);
// 		$menu_data['is_system'] = 1;
// 		$menu_data['group_name'] = 'frame';
// 		pdo_insert('core_menu', $menu_data);
// 	}
// }

load()->model('setting');
setting_save(array('template' => '2.0'), 'basic');

setting_upgrade_version(IMS_FAMILY, '2.5.0', '201911140001');
return true;