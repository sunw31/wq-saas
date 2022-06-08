<?php

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
load()->model('activity');
$op = !empty($_GPC['op']) ? safe_gpc_string($_GPC['op']) : 'consume';
$user_permission = permission_account_user('system');
if ('consume' == $op) {
	$type = intval($_GPC['type']);
	$qrcode = safe_gpc_string($_GPC['code']);
	if ($_W['isajax']) {
		$code = safe_gpc_string($_GPC['code']);
		$record = pdo_get('coupon_record', array('code' => $code));
		if (empty($record)) {
			message(error(-1, '卡券记录不存在'), '', 'ajax');
		}
		$status = activity_coupon_use($record['couponid'], $record['id'], 'paycenter');
		if (!is_error($status)) {
			message(error('0', ''), '', 'ajax');
		} else {
			message(error('-1', $status['message']), '', 'ajax');
		}
	}
}
include $this->template('cardconsume');