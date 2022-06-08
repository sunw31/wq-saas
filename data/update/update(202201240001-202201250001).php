<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.24', '202201250001');

return true;