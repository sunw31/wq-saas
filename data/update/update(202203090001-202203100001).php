<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.45', '202203100001');

return true;