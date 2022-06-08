<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.21', '202201210002');

return true;