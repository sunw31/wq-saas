<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.44', '202203090001');

return true;