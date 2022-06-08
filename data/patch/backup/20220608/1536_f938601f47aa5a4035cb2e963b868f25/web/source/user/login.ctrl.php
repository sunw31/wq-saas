<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
define('IN_GW', true);

load()->model('message');
load()->model('utility');

if (!empty($_W['uid']) && 'bind' != $_GPC['handle_type'] && empty($_W['isw7_sign'])) {
	if ($_W['isajax']) {
		iajax(-1, '请先退出再登录！');
	}
	itoast('', $_W['siteroot'] . 'web/home.php');
}
$support_login_types = OAuth2Client::supportThirdLoginType();
if (checksubmit() || $_W['isajax'] || in_array($_GPC['login_type'], $support_login_types)) {
	if ($_W['isajax'] && STATUS_OFF == $_W['ishttps']) {
		iajax(-1, '该站点没有配置https，该功能无法正常使用，请联系站点管理员处理。');
	}
	_login($_GPC['referer']);
}

$setting = $_W['setting'];
$_GPC['login_type'] = !empty($_GPC['login_type']) ? $_GPC['login_type'] : 'system';

$login_urls = user_support_urls();
$login_template = !empty($_W['setting']['basic']['login_template']) ? $_W['setting']['basic']['login_template'] : 'base';
template('user/login-' . $login_template);

function _login($forward = '') {
	global $_GPC, $_W;
	if (empty($_GPC['login_type'])) {
		$_GPC['login_type'] = 'system';
	}

	if (empty($_GPC['handle_type'])) {
		$_GPC['handle_type'] = 'login';
	}

	if ('login' == $_GPC['handle_type']) {
		$member = OAuth2Client::create($_GPC['login_type'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appid'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appsecret'])->login();
	} else {
		$member = OAuth2Client::create($_GPC['login_type'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appid'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appsecret'])->bind();
	}

	if (!empty($_W['user']) && '' != $_GPC['handle_type'] && 'bind' == $_GPC['handle_type']) {
		if (is_error($member)) {
			if ($_W['isajax']) {
				iajax(-1, $member['message'], url('user/profile/bind'));
			}
			itoast($member['message'], url('user/profile/bind'), '');
		} else {
			if ($_W['isajax']) {
				iajax(1, '绑定成功', url('user/profile/bind'));
			}
			itoast('绑定成功', url('user/profile/bind'), '');
		}
	}

	if (is_error($member)) {
		if ($_W['isajax']) {
			iajax(-1, $member['message'], url('user/login'));
		}
		itoast($member['message'], url('user/login'), '');
	}

	$record = user_single($member);
	$failed = pdo_get('users_failed_login', array('username' => safe_gpc_string($_GPC['username'])));
	if (!empty($record)) {
		if (USER_STATUS_CHECK == $record['status'] || USER_STATUS_BAN == $record['status']) {
			if ($_W['isajax']) {
				iajax(-1, '您的账号正在审核或是已经被系统禁止，请联系网站管理员解决', url('user/login'));
			}
			itoast('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决', url('user/login'), '');
		}
		$_W['uid'] = $record['uid'];
		$_W['isfounder'] = user_is_founder($record['uid']);
		$_W['isadmin'] = user_is_founder($_W['uid'], true);
		$_W['user'] = $record;

		if (!empty($_W['isadmin']) && empty($_W['setting']['copyright']['local_install'])) {
			$redirect = url('user/third-bind/console_url', array('type' => 'we7'));
			if (empty($_W['setting']['founder_access_console'])) {
				setting_save(1, 'founder_access_console');
				$extend_buttons = array();
				$extend_buttons['status_console_button'] = array(
					'url' => $redirect,
					'class' => 'btn btn-default',
					'title' => '我已知晓，立即跳转',
				);
				$message = '创始人功能操作已迁移至微擎控制台，请登录微擎控制台操作<br/>如需进入平台内管理，请在首页内点击进入客户端';
				if ($_W['isajax']) {
					$data = array(
						'status' => -1,
						'message' => $message,
						'extend_buttons' => $extend_buttons,
						'redirect' => $redirect,
					);
					iajax(-4, $data);
				}
				message($message, $redirect, 'expired', '', $extend_buttons);
			} else {
				if ($_W['isajax']) {
					iajax(-4, '', $redirect);
				}
				itoast('', $redirect);
			}
		}

		if (empty($record['console']) && empty($_W['isadmin'])) {
			$cloud_bind_info = cloud_bind_user_info($record['uid']);
			if (is_error($cloud_bind_info)) {
				iajax(-1, $cloud_bind_info['message']);
			}
			if (!empty($cloud_bind_info['bind_status']) && !empty($cloud_bind_info['bind_mobile'])) {
				$user_bind = pdo_get('users_bind', array('uid' => $record['uid'], 'third_type' => USER_REGISTER_TYPE_CONSOLE));
				if (empty($user_bind)) {
					pdo_insert('users_bind', array('uid' => $record['uid'], 'bind_sign' => $cloud_bind_info['bind_mobile'], 'third_type' => USER_REGISTER_TYPE_CONSOLE, 'third_nickname' => $cloud_bind_info['bind_mobile']));
				} else {
					pdo_update('users_bind', array('bind_sign' => $cloud_bind_info['bind_mobile'], 'third_nickname' => $cloud_bind_info['bind_mobile']), array('id' => $user_bind['id']));
				}
			}
		}

		$support_login_bind_types = Oauth2CLient::supportThirdLoginBindType();
		if (in_array($_GPC['login_type'], $support_login_bind_types) && !empty($_W['setting']['copyright']['oauth_bind']) && !$record['is_bind'] && empty($_W['isfounder']) && (USER_REGISTER_TYPE_QQ == $record['register_type'] || USER_REGISTER_TYPE_WECHAT == $record['register_type'])) {
			if ($_W['isajax']) {
				iajax(-1, '您还没有注册账号，请前往注册');
			}
			message('您还没有注册账号，请前往注册', url('user/third-bind/bind_oauth', array('uid' => $record['uid'], 'openid' => $record['openid'], 'register_type' => $record['register_type'])));
			exit;
		}

		if (!empty($_W['siteclose']) && empty($_W['isfounder'])) {
			if ($_W['isajax']) {
				iajax(-1, '站点已关闭，关闭原因:' . $_W['setting']['copyright']['reason']);
			}
			itoast('站点已关闭，关闭原因:' . $_W['setting']['copyright']['reason'], '', '');
		}

		$login_log = array(
			'uid' => $_W['uid'],
			'ip' => $_W['clientip'],
			'city' => isset($local['data']['city']) ? $local['data']['city'] : '',
			'createtime' => TIMESTAMP
		);
		table('users_login_logs')->fill($login_log)->save();

		if ((empty($_W['isfounder']) || user_is_vice_founder()) && $_W['user']['is_expired']) {
			$user_expire = setting_load('user_expire');
			$user_expire = !empty($user_expire['user_expire']) ? $user_expire['user_expire'] : array();
			$notice = !empty($user_expire['notice']) ? $user_expire['notice'] : '您的账号已到期，请联系管理员!';
			$redirect = '';
			$extend_buttons = array();
			$extend_buttons['cancel'] = array(
				'url' => '',
				'class' => 'btn btn-default',
				'title' => '取消',
			);
			if ($_W['isajax']) {
				$message = array(
					'status' => -1,
					'message' => $notice,
					'extend_buttons' => $extend_buttons,
					'redirect' => $redirect,
				);
				iajax(0, $message);
			}
			message($notice, $redirect, 'expired', '', $extend_buttons);
		}

		$cookie = array();
		$cookie['uid'] = $record['uid'];
		$cookie['lastvisit'] = $record['lastvisit'];
		$cookie['lastip'] = $record['lastip'];
		$cookie['hash'] = !empty($record['hash']) ? $record['hash'] : md5($record['password'] . $record['salt']);
		$cookie['rember'] = safe_gpc_int($_GPC['rember']);
		$session = authcode(json_encode($cookie), 'encode');
		isetcookie('__session', $session, !empty($_GPC['rember']) ? 7 * 86400 : 0, true);
		pdo_update('users', array('lastvisit' => TIMESTAMP, 'lastip' => $_W['clientip']), array('uid' => $record['uid']));

		if (empty($forward)) {
			$forward = user_login_forward($_GPC['forward']);
		}
		
		$forward = safe_gpc_url($forward);

		if ($record['uid'] != $_GPC['__uid']) {
			isetcookie('__uniacid', '', -7 * 86400);
			isetcookie('__uid', '', -7 * 86400);
		}
		if (!empty($failed)) {
			pdo_delete('users_failed_login', array('id' => $failed['id']));
		}
		cache_build_frame_menu();
		if ($_W['isajax']) {
			iajax(0, "欢迎回来，{$record['username']}", $forward);
		}
		itoast("欢迎回来，{$record['username']}", $forward, 'success');
	} else {
		if (empty($failed)) {
			pdo_insert('users_failed_login', array('ip' => $_W['clientip'], 'username' => safe_gpc_string($_GPC['username']), 'count' => '1', 'lastupdate' => TIMESTAMP));
		} else {
			pdo_update('users_failed_login', array('count' => $failed['count'] + 1, 'lastupdate' => TIMESTAMP), array('id' => $failed['id']));
		}
		if ($_W['isajax']) {
			iajax(-1, '登录失败，请检查您输入的账号和密码');
		}
		itoast('登录失败，请检查您输入的账号和密码', '', '');
	}
}