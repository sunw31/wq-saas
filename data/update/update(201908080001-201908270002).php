<?php

$is_exist = pdo_get('core_settings', array('key'=> 'system_module_expire'));
if (empty($is_exist)) {
	pdo_insert('core_settings', array('key'=> 'system_module_expire', 'value' => '您访问的功能模块不存在，请重新进入'), TRUE);
}
load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.0.9', '201908270002');
return true;