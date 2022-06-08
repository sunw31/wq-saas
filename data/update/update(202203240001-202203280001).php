<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.50', '202203280001');
return true;