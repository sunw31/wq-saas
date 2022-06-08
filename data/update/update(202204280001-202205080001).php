<?php
load()->model('setting');
load()->model('cloud');
cloud_api('we7/site/console/visible', array('visible' => 1), array('nocache' => true));
setting_upgrade_version(IMS_FAMILY, '2.7.57', '202205080001');
return true;