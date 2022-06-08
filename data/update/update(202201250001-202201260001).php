<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.26', '202201260001');

return true;