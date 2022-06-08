<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

if (!('material' == $action && 'delete' == $do) && empty($_GPC['version_id'])) {
	$account_api = WeAccount::createByUniacid();
	if (is_error($account_api)) {
		itoast('', $_W['siteroot'] . 'web/home.php');
	}
	$check_manange = $account_api->checkIntoManage();
	if (is_error($check_manange)) {
		itoast('', $account_api->displayUrl);
	}
	if ('detail' == $do) {
		define('FRAME', '');
	} else {
		define('FRAME', 'account');
	}
}

$_GPC['uniacid'] = empty($_GPC['uniacid']) ? 0 : $_GPC['uniacid'];
if ('material-post' != $action && FILE_NO_UNIACID != $_GPC['uniacid']) {
	!defined('FRAME') && define('FRAME', 'account');
} else {
	define('FRAME', '');
}
