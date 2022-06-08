<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('user');

$dos = array('display', 'validate_mobile', 'bind_mobile', 'bind_oauth', 'console', 'verify_mobile', 'bind_console', 'console_url');
$do = in_array($do, $dos) ? $do : 'display';

if (in_array($do, array('validate_mobile', 'bind_mobile', 'verify_mobile'))) {
	$user_profile = table('users_profile')->getByUid($_W['uid']);
	$mobile = safe_gpc_string($_GPC['mobile'], '', 'mobile');
	if (empty($mobile)) {
		iajax(-1, '手机号不能为空');
	}
	if (!preg_match(REGULAR_MOBILE, $mobile)) {
		iajax(-1, '手机号格式不正确');
	}
	$bind_type = in_array($do, array('verify_mobile')) ? USER_REGISTER_TYPE_CONSOLE : USER_REGISTER_TYPE_MOBILE;
	$mobile_exists = table('users_bind')->getByTypeAndBindsign($bind_type, $mobile);
	if (empty($type) && !empty($mobile_exists)) {
		iajax(-1, '手机号已存在');
	}
}

if ('validate_mobile' == $do) {
	iajax(0, '本地校验成功');
}

if ('bind_mobile' == $do) {
	if ($_W['isajax'] && $_W['ispost']) {
		$bind_info = OAuth2Client::create('mobile')->bind();
		if (is_error($bind_info)) {
			iajax(-1, $bind_info['message']);
		}
		iajax(0, '绑定成功', url('user/profile/bind'));
	} else {
		iajax(-1, '非法请求');
	}
}

if ('display' == $do) {
	$support_bind_urls = user_support_urls();
	$setting_sms_sign = setting_load('site_sms_sign');
	$bind_sign = !empty($setting_sms_sign['site_sms_sign']['register']) ? $setting_sms_sign['site_sms_sign']['register'] : '';
	if (!empty($_W['user']['type']) && $_W['user']['type'] == USER_TYPE_CLERK) {
		$_W['setting']['copyright']['bind'] = empty($_W['setting']['copyright']['clerk']['bind']) ? '' : $_W['setting']['copyright']['clerk']['bind'];
	}
	if (empty($_W['isw7_request'])) {
		$_W['setting']['copyright']['bind'] = 'console';
	}
}

if ('bind_oauth' == $do) {
	$uid = intval($_GPC['uid']);
	$openid = safe_gpc_string($_GPC['openid']);
	$register_type = intval($_GPC['register_type']);

	if (empty($uid) || empty($openid) || !in_array($register_type, array(USER_REGISTER_TYPE_QQ, USER_REGISTER_TYPE_WECHAT))) {
		itoast('参数错误!', url('user/login'), '');
	}
	$user_info = user_single($uid);
	if ($user_info['is_bind']) {
		itoast('账号已绑定!', url('user/login'), '');
	}

	if ($_W['ispost']) {
		$member['username'] = safe_gpc_string($_GPC['username']);
		$member['password'] = safe_check_password($_GPC['password']);
		$member['repassword'] = safe_check_password($_GPC['repassword']);
		$member['is_bind'] = 1;

		if (empty($member['username']) || empty($member['password']) || empty($member['repassword'])) {
			itoast('请填写完整信息！', referer(), '');
		}
		if (!preg_match(REGULAR_USERNAME, $member['username'])) {
			itoast('必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。', referer(), '');
		}
		if (user_check(array('username' => $member['username']))) {
			itoast('非常抱歉，此用户名已经被注册，你需要更换注册名称！', referer(), '');
		}
		if (is_error($member['password'])) {
			itoast($member['password']['message'], referer(), '');
		}
		if ($member['password'] != $member['repassword']) {
			itoast('两次秘密输入不一致');
		}
		unset($member['repassword']);
		if (user_check(array('username' => $member['username']))) {
			itoast('非常抱歉，此用户名已经被注册，你需要更换注册名称！', referer(), '');
		}

		$member['salt'] = random(8);
		$member['password'] = user_hash($member['password'], $member['salt']);
		$result = pdo_update('users', $member, array('uid' => $uid, 'openid' => $openid, 'register_type' => $register_type));
		if ($result) {
			itoast('注册绑定成功!', url('user/login'), '');
		} else {
			itoast('注册绑定失败, 请联系管理员解决!', url('user/login'), '');
		}
	} else {
		template('user/bind-oauth');
		exit;
	}
}

if ('console' == $do) {
	template('user/console');
	exit();
}

if (in_array($do, array('verify_mobile'))) {
	if (!$_W['isajax']) {
		iajax(-1, '非法请求');
	}
}

if ('verify_mobile' == $do) {
	$code = safe_gpc_string($_GPC['code']);
	if (empty($code)) {
		iajax(-1, '请输入验证码');
	}
	$data = array(
		'mobile' => $mobile,
		'sms_verify_code' => $code
	);
	$result = cloud_check_mobile($data);
	if (is_error($result)) {
		iajax(-1, $result['message']);
	}
	if (!empty($result['out_user_id'])) {
		if ($_W['uid'] != $result['out_user_id']) {
			$user = pdo_get('users', array('uid' => $result['out_user_id']), array('username'));
			$message = '该手机号已绑定用户 ' . empty($user['username']) ? '' : $user['username'] . ' ，请更换其他手机号';
			iajax(-1, $message);
		}
	}
	iajax(0, $result);
}

if ('bind_console' == $do) {
	$code = safe_gpc_string($_GPC['code']);
	if (empty($code)) {
		itoast('参数错误！', '', 'info');
	}

	$token = cloud_oauth_token($code);
	if (is_error($token)) {
		itoast($token['message'], '', 'info');
	}
	$result = cloud_bind_user_token(array('access_token' => $token['access_token'], 'out_user_id' => $_W['uid']));
	if (is_error($result)) {
		itoast($result['message'], '', 'info');
	}

	$user_bind = pdo_get('users_bind', array('uid' => $_W['uid'], 'third_type' => USER_REGISTER_TYPE_CONSOLE));
	if (empty($user_bind)) {
		pdo_insert('users_bind', array('uid' => $_W['uid'], 'third_type' => USER_REGISTER_TYPE_CONSOLE));
	} else {
		pdo_update('users_bind', array('bind_sign' => '', 'third_nickname' => ''), array('id' => $user_bind['id']));
	}

	header("Location: " . url('user/third-bind/console_url', array('type' => 'client')));
}

if ('console_url' == $do) {
	$type = safe_gpc_string($_GPC['type']);
	if (empty($type) || !in_array($type, array('we7', 'client', 'system'))) {
		itoast('参数错误！', url('user/third-bind/console'), 'info');
	}
	if ('system' == $type) {
		$type = 'we7';
	}
	$data = [
		'out_user_id' => empty($_W['isadmin']) ? $_W['uid'] : 0,
		'console_type' => $type
	];
	if (!empty($_GPC['out_user_id'])) {
		$data['out_user_id'] = safe_gpc_int($_GPC['out_user_id']);
	}
	$result = cloud_console_index_url($data);
	if (is_error($result)) {
		itoast($result['message'], url('user/third-bind/console'), 'info');
	}
	isetcookie('__direct_to_console', 1);
	$url = $result['data'];
	header('location: ' . $url);
	exit();
}
template('user/third-bind');