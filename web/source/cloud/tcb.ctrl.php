<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('extension');
load()->model('cloud');
load()->model('module');
load()->func('communication');
load()->func('db');
load()->model('system');

if (!$_W['isadmin']) {
	iajax(-1, '您没有权限！');
}
$cloud_ready = cloud_prepare();
if (is_error($cloud_ready)) {
	iajax(-1, $cloud_ready['message']);
}
$dos = array('list', 'step', 'prepared', 'system_upgrade');
$do = in_array($do, $dos) ? $do : 'process';

$tcb_status = array(
	'no_start' => 0,
	'preparing' => 10,
	'can_deploy' => 20,
	'finished' => 50,
);
if ('list' == $do) {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	if (1 == $pindex) {
		module_upgrade_info();
	}
	$module_list = pdo_getall('modules_cloud', array('show_in_tcb' => 1, 'tcb_status IN' => array($tcb_status['no_start'], $tcb_status['finished'])), array('name', 'title', 'version', 'logo', 'main_module_name', 'modified_time'), '', array('id DESC'), ($pindex - 1) * $psize . ',' . $psize);
	$count = pdo_getall('modules_cloud', array('show_in_tcb' => 1, 'tcb_status' => 0), array('id'));
	foreach ($module_list as &$item) {
		if (!empty($item['main_module_name'])) {
			$item['type'] = 'plugin';
		} else {
			$item['type'] = 'module';
		}
		$item['modified_time'] = date('Y-m-d', $item['modified_time']);
		$item['new_version'] = $item['version'];
		$item['version'] = $item['tcb_version'];
		unset($item['main_module_name']);
	}
	iajax(0, array(
		'total' => count($count),
		'page' => $pindex,
		'page_size' => $psize,
		'list' => $module_list,
	));
}
$module_tcb_info = module_query_tcb_info();
if ('system_upgrade' == $do) {
	$info = array();
	if (!empty($module_tcb_info['system']) && in_array($module_tcb_info['system']['status'], array($tcb_status['preparing'], $tcb_status['can_deploy']))) {
		iajax(0, $info);
	}
	$upgrade = cloud_build(true);
	if (!empty($upgrade['files']) && version_compare(IMS_VERSION, $upgrade['version']) == -1) {
		$info = array(
			'name' => 'w7_system',
			'title' => '微擎系统',
			'version' => IMS_VERSION,
			'new_version' => $upgrade['version'],
			'logo' => !empty($_W['setting']['copyright']['blogo']) ? to_global_media($_W['setting']['copyright']['blogo']) : ($_W['siteroot'] . 'web/resource/images/logo/logo.png'),
			'modified_time' => substr($upgrade['release'], 0, 4) . '-' . substr($upgrade['release'], 4, 2) . '-' . substr($upgrade['release'], 6, 2),
			'type' => 'system',
		);
	}
	iajax(0, $info);
}
if ('prepared' == $do) {
	$prepared_list = array();
	$have_system = STATUS_OFF;
	if (!empty($module_tcb_info)) {
		$prepared_modules = array();
		foreach ($module_tcb_info as $tcb_key => $tcb_info) {
			if ('system' == $tcb_key && in_array($tcb_info['status'], array($tcb_status['preparing'], $tcb_status['can_deploy']))) {
				$have_system = STATUS_ON;
				continue;
			}
			if (in_array($tcb_info['status'], array($tcb_status['no_start'], $tcb_status['preparing'], $tcb_status['can_deploy']))) {
				$prepared_modules[] = $tcb_info['name'];
			}
		}
		if (!empty($prepared_modules)) {
			$prepared_list = pdo_getall('modules_cloud', array('name IN' => $prepared_modules), array('name', 'title', 'version', 'logo', 'main_module_name', 'modified_time'));
		}
	}
	foreach ($prepared_list as &$item) {
		if (!empty($item['main_module_name'])) {
			$item['type'] = 'plugin';
		} else {
			$item['type'] = 'module';
		}
		$item['modified_time'] = date('Y-m-d', $item['modified_time']);
		$item['new_version'] = $item['version'];
		$item['version'] = $item['tcb_version'];
		$item['tcb_status'] = $module_tcb_info[$item['name']]['status'];
		unset($item['main_module_name']);
	}
	if ($have_system) {
		$upgrade = cloud_build();
		if (version_compare(IMS_VERSION, $upgrade['version']) == -1) {
			$prepared_list[] = array(
				'name' => 'w7_system',
				'title' => '微擎系统',
				'version' => IMS_VERSION,
				'new_version' => $upgrade['version'],
				'logo' => !empty($_W['setting']['copyright']['blogo']) ? to_global_media($_W['setting']['copyright']['blogo']) : ($_W['siteroot'] . 'web/resource/images/logo/logo.png'),
				'modified_time' => substr($upgrade['release'], 0, 4) . '-' . substr($upgrade['release'], 4, 2) . '-' . substr($upgrade['release'], 6, 2),
				'type' => 'system',
			);
		}
	}
	iajax(0, $prepared_list);
}
if ('step' == $do) {
	$preparing_num = $can_deploy_num = 0;
	if (!empty($module_tcb_info)) {
		foreach ($module_tcb_info as $item) {
			if ($tcb_status['preparing'] == $item['status']) {
				$preparing_num++;
			}
			if ($tcb_status['can_deploy'] == $item['status']) {
				$can_deploy_num++;
			}
		}
	}
	$step = !empty($preparing_num) ? 1 : (!empty($can_deploy_num) ? 2 : 1);
	iajax(0, array('step' => $step));
}
