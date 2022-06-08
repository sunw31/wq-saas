<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

isetcookie('__session', '', -10000);
isetcookie('__iscontroller', '', -10000);
isetcookie('__uniacid', '', -10000);
isetcookie('__w7sign', '', -10000);
isetcookie('__console_username', '', -10000);
isetcookie('__direct_to_console', 0, -10000);
$forward = safe_gpc_url($_GPC['forward'], false);
if (empty($forward)) {
	$forward = $_W['siteroot'];
}
if ($_W['isajax']) {
	iajax(0, '', $forward);
}
header('Location:' . $forward);
