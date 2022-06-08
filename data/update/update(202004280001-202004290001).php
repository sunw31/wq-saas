<?php

$default = pdo_get('modules', array('name' => 'default', 'application_type' => 2), array('mid'));
if (empty($default)) {
	pdo_insert('modules', array('name' => 'default', 'application_type' => 2, 'type' => 0, 'title' => '微站默认模板', 'version' => '1.0.0', 'description' => '由微擎提供默认微站模板套系', 'author' => '微擎团队', 'url' => 'https://www.w7.cc', 'settings' => 0, 'isrulefields' => 0, 'issystem' => 1, 'target' => 0, 'iscard' => 0, 'wxapp_support' => 1, 'account_support' => 2, 'welcome_support' => 0, 'webapp_support' => 0, 'oauth_type' => 1, 'phoneapp_support' => 1, 'xzapp_support' => 1, 'aliapp_support' => 1, 'logo' => 'app/themes/default/preview.jpg', 'baiduapp_support' => 1, 'toutiaoapp_support' => 1, 'from' => 'cloud', 'cloud_record' => 0, 'sections' => 0));
}
$default_info = pdo_get('modules', array('name' => 'default', 'application_type' => 2), array('mid', 'title'));
$site_data = pdo_getall('site_styles', array('templateid' => 0), array('id', 'name'));
if (!empty($site_data)) {
	foreach ($site_data as $site_val) {
		pdo_update('site_styles', array('templateid' => $default_info['mid'], 'name' => '微站默认模板'), array('id' => $site_val['id']));
	}
}

$settings = $_W['setting']['copyright'];
if (!empty($settings['policeicp'])) {
	$icps = iunserializer($settings['icps']);
	foreach ($icps as $key => $setting) {
		if (strpos($_W['setting']['site']['url'], $setting['domain'])) {
			$icps[$key]['policeicp_location'] = $settings['policeicp']['policeicp_location'];
			$icps[$key]['policeicp_code'] = $settings['policeicp']['policeicp_code'];
		}
	}
	$settings['icps'] = iserializer($icps);
	setting_save($settings, 'copyright');
}
load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.6.2', '202004290001');
return true;