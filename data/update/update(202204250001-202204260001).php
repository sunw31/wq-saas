<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.55', '202204260001');
return true;