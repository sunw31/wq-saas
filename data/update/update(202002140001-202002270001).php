<?php

pdo_update('modules', array('issystem' => 1), array('name' => 'default', 'application_type' => 2));

load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.5.8', '202002270001');
return true;