<?php

$wxapps = pdo_getall('account_wxapp', array(), array('uniacid'), 'uniacid');
if (!empty($wxapps)) {
	$wxapp_uniacids = array_keys($wxapps);
	foreach ($wxapp_uniacids as $uniacid) {
		$setting = uni_setting_load('payment', $uniacid);
		$payment = $setting['payment'];
		if (empty($payment) || !empty($payment['pay_type'])) {
			continue;
		}
		$new_payment = array(
			'pay_type' => 'wechat',
			'wechat' => !empty($payment['wechat']) ? $payment['wechat'] : array(),
			'wechat_facilitator' => !empty($payment['wechat_facilitator']) ? $payment['wechat_facilitator'] : array()
		);
		$new_payment['wechat']['status'] = 1;
		pdo_update('uni_settings', array('payment' => iserializer($new_payment)), array('uniacid' => $uniacid));
	}
}
load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.6.5', '202008170002');
return true;