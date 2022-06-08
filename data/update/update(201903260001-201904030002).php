<?php

pdo_update('users', array('founder_groupid' => 1), array('uid' => 1));

load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.0.2', '201904030002');
return true;
