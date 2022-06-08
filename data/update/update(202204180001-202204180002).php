<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.53', '202204180002');
return true;