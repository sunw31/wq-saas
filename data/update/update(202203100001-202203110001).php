<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.46', '202203110001');

return true;