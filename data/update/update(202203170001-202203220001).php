<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.48', '202203220001');

return true;