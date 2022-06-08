<?php

/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('module');

$modulename = safe_gpc_string($_GPC['modulename']);
$callname = safe_gpc_string($_GPC['callname']);
$uniacid = intval($_GPC['uniacid']);
$_W['uniacid'] = intval($_GPC['uniacid']);

$args = is_array($_GPC['args']) ? safe_gpc_array($_GPC['args']) : safe_gpc_string($_GPC['args']);
$module_info = module_fetch($modulename);
if (empty($module_info)) {
	iajax(0, array());
}
$site = WeUtility::createModuleSite($modulename);
if (empty($site)) {
	iajax(0, array());
}
if (!method_exists($site, $callname)) {
	iajax(0, array());
}
$ret = @$site->$callname($args);
iajax(0, $ret);