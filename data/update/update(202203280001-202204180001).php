<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.51', '202204180001');
return true;