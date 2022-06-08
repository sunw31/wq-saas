<?php
if (pdo_fieldexists('modules_cloud', 'module_status')) {
	table('modules_cloud')->searchWithUninstall(MODULE_CLOUD_UNINSTALL)->where('module_status', 0)->fill('module_status', 1)->save();
}

		$settings = $_W['setting']['copyright'];
		if (!empty($settings['icp'])) {
			$icp = array('id' => 1, 'domain' => $_SERVER['HTTP_HOST'], 'icp' => $settings['icp']);
			$icps[1] = $icp;
			$settings['icps'] = iserializer($icps);
			unset($settings['icp']);
			setting_save($settings, 'copyright');
		}

load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.5.4', '202001100001');
return true;