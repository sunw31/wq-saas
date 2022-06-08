<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
define('IN_SYS', true);
require __DIR__ . '/../framework/bootstrap.inc.php';
require IA_ROOT . '/web/common/bootstrap.sys.inc.php';
if (igetcookie('__toUrl')) {
	$to_url = igetcookie('__toUrl');
	isetcookie('__toUrl', '', -10);
	itoast('', $to_url);
}
if (empty($_W['isfounder']) && !empty($_W['user']) && ($_W['user']['status'] == USER_STATUS_CHECK || $_W['user']['status'] == USER_STATUS_BAN)) {
	isetcookie('__session', '', -10000);
	message('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！', '', 'expired', '', array());
}
if (!empty($_W['setting']['copyright']['status']) && $_W['setting']['copyright']['status'] == 1 && empty($_W['isfounder'])) {
	isetcookie('__session', '', -10000);
	itoast('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason'], url('user/login'), 'info');
}
if (!empty($_GPC['getmenu'])) {
	if (STATUS_OFF == $_W['ishttps']) {
		iajax(-1, '该站点没有配置https，该功能无法正常使用，请联系站点管理员处理。');
	}
}
module_permission_check('message');
$_W['page'] = array();
$_W['page']['copyright'] = $_W['setting']['copyright'];
checklogin();
if (empty($_W['isadmin']) && 'document' == $_SERVER['HTTP_SEC_FETCH_DEST']) {
	if (strpos(referer(), 'web/index.php?c=site&a=entry') !== false) {
		isetcookie('__toUrl', $_W['siteurl'], 15);
		header('Location: ' . 'https://console.w7.cc/console/' . $_W['setting']['site']['key'] . '/client');
		exit;
	}
	if (1 == igetcookie('__direct_to_console')) {
		header('Location: ' . 'https://console.w7.cc/console/' . $_W['setting']['site']['key'] . '/client');
		exit;
	}
	if (ACCOUNT_MANAGE_NAME_UNBIND_USER == $_W['highest_role']) {
		header('Location: ' . 'https://console.w7.cc/bind?appid=' . $_W['setting']['site']['key'] . '&redirect_url=' . urlencode(url('user/third-bind/bind_console', array(), true)));
		exit;
	}
	itoast('', url('user/third-bind/console'));
}
if (ACCOUNT_MANAGE_NAME_UNBIND_USER == $_W['highest_role']) {
	itoast('', url('user/third-bind'));
}
isetcookie('__iscontroller', 0);
$_W['iscontroller'] = 0;
function _calc_current_frames() {
	global $_W;
	$_W['page']['title'] = '';
	return true;
}
if (!empty($_GPC['getmenu'])) {
	$home_menu = system_star_menu();
	iajax(0, $home_menu);
}
template('home/home');