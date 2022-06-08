<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.35', '202202260001');

return true;