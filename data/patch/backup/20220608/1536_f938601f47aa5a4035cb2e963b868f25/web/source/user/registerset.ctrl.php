<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('setting');

$dos = array('display');
$do = in_array($do, $dos) ? $do : 'display';
check_w7_request($action);
$copyright = $_W['setting']['copyright'];
$settings = $_W['setting']['register'];

if ($_W['ispost']) {
	$is_copyright = false;
	switch ($_GPC['key']) {
		case 'open':
			$settings['open'] = intval($_GPC['value']);
			break;
		case 'verify':
			$settings['verify'] = intval($_GPC['value']);
			break;
		case 'groupid':
			$settings['groupid'] = intval($_GPC['value']);
			break;
		case 'safe':
			$settings['safe'] = intval($_GPC['value']);
			break;
		case 'verifycode':
			$copyright['verifycode'] = intval($_GPC['value']);
			$is_copyright = true;
			break;
		case 'refused_login_limit':
			$copyright['refused_login_limit'] = intval($_GPC['value']);
			$is_copyright = true;
			break;
	}
	if ($is_copyright) {
		setting_save($copyright, 'copyright');
	} else {
		setting_save($settings, 'register');
		cache_delete(cache_system_key('defaultgroupid', array('uniacid' => $_W['uniacid'])));
	}
	iajax(0, '更新设置成功！', referer());
}

if ('display' == $do) {
	$groups = user_group();
	if (empty($groups)) {
		$groups = array(array('id' => "0", 'name' => '请选择所属用户组'));
	} else {
		array_unshift($groups, array('id' => "0", 'name' => '请选择所属用户组'));
	}

	$group = array();
	foreach ($groups as $item) {
		if ($item['id'] == $settings['groupid']) {
			$group = $item;
			break;
		}
	}
	if ($_W['isajax']) {
		$message = array(
			'settings' => $settings,
			'default_group' => $group,
			'groups' => $groups,
			'verifycode' => $copyright['verifycode'],
			'refused_login_limit' => $copyright['refused_login_limit'],
			'copyright' => $copyright
		);
		iajax(0, $message);
	}
}

template('user/registerset');
