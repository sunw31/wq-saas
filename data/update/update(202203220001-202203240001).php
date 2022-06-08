<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.49', '202203240001');

return true;