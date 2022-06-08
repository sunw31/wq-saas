<?php

global $_W;
$settings = $_W['setting']['basic'];
if (empty($settings['login_template'])) {
	$settings['login_template'] = 'base';
	setting_save($settings, 'basic');
}
load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.5.1', '201912090001');
return true;