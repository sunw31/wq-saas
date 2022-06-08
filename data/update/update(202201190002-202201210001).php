<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.20', '202201210001');

return true;