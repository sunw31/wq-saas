<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.28', '202202220001');

return true;