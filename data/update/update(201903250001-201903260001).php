<?php

$mobile_binds = pdo_fetchall("SELECT * FROM " . tablename('users_bind') . " WHERE third_type = " . USER_REGISTER_TYPE_MOBILE);
		if (!empty($mobile_binds)) {
			foreach ($mobile_binds as $bind_info) {
				$is_mobile = preg_match(REGULAR_MOBILE, $bind_info['third_nickname']);
				if (!$is_mobile) {
					pdo_delete('users_bind', array('id' => $bind_info['id']));
				}
			}
		}


load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.0.1', '201903260001');
return true;
