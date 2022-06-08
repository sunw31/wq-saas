<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('user');
load()->model('module');
load()->model('setting');
load()->model('utility');

$dos = array('check_code', 'register_url', 'console_register');
$do = in_array($do, $dos) ? $do : 'register_url';

if (empty($_W['setting']['register']['open'])) {
	itoast('本站暂未开启注册功能，请联系管理员！', '', '');
}

if ('check_code' == $do) {
	if (!checkcaptcha(intval($_GPC['code']))) {
		iajax(-1, '你输入的验证码不正确, 请重新输入.');
	} else {
		iajax(0, '验证码正确');
	}
}

if ('register_url' == $do) {
	$params = empty($_GPC['owner_uid']) ? array() : array('owner_uid' => safe_gpc_int($_GPC['owner_uid']));
	$register_url = cloud_oauth_register_url(array('redirect_url' => url('user/register/console_register', $params, true), 'choose_account' => 1));
	if (is_error($register_url)) {
		itoast($register_url['message'], '', 'info');
	}

	header("Location: " . $register_url['url']);
}

if ('console_register' == $do) {
	$code = safe_gpc_string($_GPC['code']);
	$vice_founder_id = empty($_GPC['owner_uid']) ? 0 : safe_gpc_int($_GPC['owner_uid']);
	if (empty($code)) {
		itoast('参数错误！', '', 'info');
	}
	$token = cloud_oauth_token($code);
	if (is_error($token)) {
		itoast($token['message'], '', 'info');
	}
	$console_user = cloud_oauth_user($token['access_token']);
	if (is_error($console_user)) {
		itoast($console_user['message'], '', 'info');
	}
	if (!empty($vice_founder_id)) {
		$vice_founder_info = user_single($vice_founder_id);
		if (empty($vice_founder_info) || !user_is_vice_founder($vice_founder_info['uid'])) {
			itoast('副创始人不存在！', '', 'info');
		}
		$user_modules_info = user_modules($vice_founder_info['uid']);
		$user_modules = array_keys($user_modules_info);
		if (!empty($user_modules)) {
			$module_expired_list = module_expired_list();
			if (is_error($module_expired_list)) {
				itoast($module_expired_list['message'], '', 'info');
			}
			$expired_modules_name = module_expired_diff($module_expired_list, $user_modules);
			if (!empty($expired_modules_name)) {
				itoast('副创始人 ' .$vice_founder_info['username']. ' 的应用：' . $expired_modules_name . '，服务费到期，无法添加！', '', 'info');
			}
		}
	}

	$str = random(8);
	if (user_check(array('username' => $console_user['username']))) {
		$console_user['username'] = $console_user['username'] . $str;
	}
	$user_info = array(
		'username' => $console_user['username'],
		'password' => $str,
		'repassword' => $str,
		'remark' => '',
		'starttime' => TIMESTAMP,
		'groupid' => empty($_W['setting']['register']['groupid']) ? 0 : safe_gpc_int($_W['setting']['register']['groupid']),
		'owner_uid' => $vice_founder_id
	);
	$user = user_info_save($user_info);
	if (is_error($user)) {
		itoast($user['message'], '', 'info');
	}

	$result = cloud_bind_user_token(array('access_token' => $token['access_token'], 'out_user_id' => $user['uid']));
	if (is_error($result)) {
		itoast($result['message'], '', 'info');
	}

	$user_bind = pdo_get('users_bind', array('uid' => $user['uid'], 'third_type' => USER_REGISTER_TYPE_CONSOLE));
	if (empty($user_bind)) {
		pdo_insert('users_bind', array('uid' => $user['uid'], 'third_type' => USER_REGISTER_TYPE_CONSOLE));
	} else {
		pdo_update('users_bind', array('bind_sign' => '', 'third_nickname' => ''), array('id' => $user_bind['id']));
	}

	header("Location: " . url('user/third-bind/console_url', array('type' => 'client', 'out_user_id' => $user['uid'])));
}