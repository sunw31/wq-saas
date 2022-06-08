<?php

if(pdo_fieldexists('uni_account', 'logo')) {
	$uni_accounts = table('uni_account')->select(array('uniacid', 'default_acid'))->getall();
	foreach ($uni_accounts as $uni_account) {
		table('uni_account')
			->fill(array(
				'logo' => to_global_media('headimg_' . $uni_account['default_acid'] . '.jpg'),
				'qrcode' => to_global_media('qrcode_' . $uni_account['default_acid'] . '.jpg')
			))
			->where('uniacid', $uni_account['uniacid'])
			->save();
	}
}
if (pdo_fieldexists('modules_cloud', 'module_status')) {
	pdo_query("ALTER TABLE " . tablename('modules_cloud') . " ALTER COLUMN `module_status` SET DEFAULT 1;");
}
$ext_uni_groups = table('uni_group')->where('uniacid >', 0)->getall();

if (!empty($ext_uni_groups)) {
	foreach($ext_uni_groups as $ext_uni_group) {
		$data = array(
			'uniacid' => $ext_uni_group['uniacid'],
			'modules' => $ext_uni_group['modules'],
		);

		pdo_insert('uni_account_extra_modules', $data);
		table('uni_group')->where('id', $ext_uni_group['id'])->delete();
	}
}
load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.5.7', '202002100001');
return true;