<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.19', '202201190002');

return true;