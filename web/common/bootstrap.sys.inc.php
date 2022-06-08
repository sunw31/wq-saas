<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->web('common');
load()->web('template');
load()->func('file');
load()->func('tpl');
load()->model('cloud');
load()->model('user');
load()->model('permission');
load()->model('attachment');
load()->classs('oauth2/oauth2client');
load()->model('switch');
load()->model('system');

$_W['token'] = token();

$_W['isw7_request'] = isset($_GPC['w7_request_secret']) && isset($_GPC['w7_accesstoken']);
$_W['isw7_sign'] = (isset($_GPC['w7_sign']) || igetcookie('__w7sign')) && ($_SERVER['HTTP_SEC_FETCH_DEST'] != 'document');
$w7_sign = empty($_GPC['w7_sign']) ? igetcookie('__w7sign') : safe_gpc_string($_GPC['w7_sign']);
$verify_user_token = true;
if ('cloud' == $controller && 'newprocess' == $action && in_array($do, array('process', 'files', 'scripts', 'schemas', 'get_error_file_list', 'get_upgrade_info'))) {
	$verify_user_token = false;
}
if ($_W['isw7_request']) {
	$request_token = cloud_w7_request_token(safe_gpc_string($_GPC['w7_request_secret']));
	if (isset($_GPC['w7_accesstoken'])) {
		if ($_GPC['w7_accesstoken'] != $request_token) {
			$request_token = cloud_w7_request_token(safe_gpc_string($_GPC['w7_request_secret']), true);
		}
		if (is_error($request_token)) {
			$onlineSecret = file_get_contents('https://cdn.w7.cc/ued/we7/release/js/getSecret.js?r=' . TIMESTAMP);
			WeUtility::logging('cloud-api-error', array('secret' => $_GPC['w7_request_secret'], 'remote_secret' => $onlineSecret, 'accesstoken' => $_GPC['w7_accesstoken'], 'request_token' => $request_token), true);
			iajax(403, '错误详情:' . $request_token['message']);
		}
		if (!empty($request_token) && $_GPC['w7_accesstoken'] == $request_token) {
			$cloud_user = current(explode(',', $_W['config']['setting']['founder']));
		}
	} else {
		iajax(403, '错误详情:请求异常,缺少w7_accesstoken参数');
	}
} elseif ($_W['isw7_sign']) {
	$cloud_user = cloud_verify_user_by_token($w7_sign);
}
if ($_W['isw7_request'] || $_W['isw7_sign']) {
	if (empty($cloud_user)) {
		echo '<script>top.location.href = "https://console.w7.cc"</script>';
		exit;
	}
	if (is_error($cloud_user) && $_W['isw7_sign']) {
		WeUtility::logging('cloud-api-error', array('w7_sign' => $_GPC['w7_sign']), true);
		message('错误详情:' . $cloud_user['message'], 'https://console.w7.cc', 'error');
	}
	$cloud_user = intval($cloud_user);
	if (-1 === $cloud_user) {
		$cloud_user = current(explode(',', $_W['config']['setting']['founder']));
	}
	$_W['user'] = user_single($cloud_user);
	if (!empty($_W['user'])) {
		if ('iframe' == $_SERVER['HTTP_SEC_FETCH_DEST'] && !empty($_W['user']['bind_domain_id'])) {
			$cannon_fodder_domain = pdo_get('cannon_fodder', array('id' => $_W['user']['bind_domain_id']), array('domain'));
			if (!empty($cannon_fodder_domain['domain']) && strpos($_W['siteroot'], $cannon_fodder_domain['domain']) === false) {
				$tourl = $cannon_fodder_domain['domain'] . '/web/home.php?w7_sign=' . $_GPC['w7_sign'] . '&console_username=' . $_GPC['console_username'] . '&cannon_fodder_domain=' . $cannon_fodder_domain['domain'];
				header('Location: ' . $tourl);
				exit();
			}
		}
		$cookie = array('uid' => $_W['user']['uid'], 'hash' => $_W['user']['hash'], 'lastvisit' => $_W['user']['lastvisit'], 'lastip' => $_W['user']['lastip']);
		$session = authcode(json_encode($cookie), 'encode');
		$_W['isadmin'] = user_is_founder($_W['user']['uid'], true);
		if (igetcookie('__w7sign') && !empty($w7_sign) || $w7_sign != igetcookie('__w7sign')) {
			isetcookie('__w7sign', $w7_sign, 1800);
		}
		isetcookie('__session', $session, 0, true);
		$_GPC['__session'] = $session;
		unset($session);
	} else {
		isetcookie('__session', '', -10000);
		isetcookie('__w7sign', '', -10000);
		isetcookie('__console_username', '', -10000);
		isetcookie('__direct_to_console', 0, -10000);
		message('用户不存在，请联系管理员处理！', 'https://console.w7.cc', 'error');
	}
}

$session = !empty($_GPC['__session']) ? json_decode(authcode($_GPC['__session']), true) : '';
if (is_array($session)) {
	$user = user_single(array('uid' => $session['uid']));
	if (is_array($user) && $session['hash'] === $user['hash']) {
		$_W['uid'] = $user['uid'];
		$_W['username'] = $user['username'];
		if (!empty($_GET['console_username'])) {
			isetcookie('__console_username', safe_gpc_string($_GET['console_username']), 1800);
		}
		$user['currentvisit'] = $user['lastvisit'];
		$user['currentip'] = $user['lastip'];
		$user['lastvisit'] = $session['lastvisit'];
		$user['lastip'] = $session['lastip'];
		$_W['user'] = $user;
		$_W['isfounder'] = user_is_founder($_W['uid']);
		$_W['isadmin'] = user_is_founder($_W['uid'], true);
	} else {
		isetcookie('__session', '', -100);
	}
	unset($user);
}
unset($w7_sign, $cloud_user, $session);

if (empty($_GPC['w7i']) && !empty($_GPC['uniacid']) && 0 < $_GPC['uniacid']) {
	$_GPC['w7i'] = $_GPC['uniacid'];
}
$_W['uniacid'] = !empty($_GPC['w7i']) ? $_GPC['w7i'] : igetcookie('__uniacid');
if (empty($_W['uniacid'])) {
	$_W['uniacid'] = switch_get_account_display();
}
$_W['uniacid'] = $_GPC['w7i'] = intval($_W['uniacid']);
if (!empty($_GPC['w7i']) && !empty(igetcookie('__uniacid')) && $_GPC['w7i'] != igetcookie('__uniacid')) {
	isetcookie('__uniacid', $_W['uniacid'], 7 * 86400);
}

if (!empty($_W['uid'])) {
	$_W['highest_role'] = permission_account_user_role($_W['uid']);
	$_W['role'] = permission_account_user_role($_W['uid'], $_W['uniacid']);
}

$_W['template'] = '2.0';


$_W['attachurl'] = attachment_set_attach_url();
