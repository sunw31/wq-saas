<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.14', '202112020001');

return true;