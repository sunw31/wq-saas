<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.27', '202202170001');

return true;