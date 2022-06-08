<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.31', '202202240001');

return true;