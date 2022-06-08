<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.25', '202201250002');

return true;