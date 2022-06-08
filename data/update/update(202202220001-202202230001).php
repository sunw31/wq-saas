<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.30', '202202230002');

return true;