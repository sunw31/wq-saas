<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.54', '202204250001');
return true;