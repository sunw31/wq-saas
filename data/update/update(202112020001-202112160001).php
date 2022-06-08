<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.16', '202112160001');

return true;