<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.34', '202202250003');

return true;