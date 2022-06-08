<?php
load()->model('setting');
setting_upgrade_version(IMS_FAMILY, '2.7.22', '202201240001');

return true;