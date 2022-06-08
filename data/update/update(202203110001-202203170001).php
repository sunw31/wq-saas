<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.47', '202203170001');

return true;