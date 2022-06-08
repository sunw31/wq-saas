<?php

$templates = pdo_getall('site_templates');
$oldid_to_name = array();
foreach ($templates as $template) {
	$data = array(
		'name' => $template['name'],
		'version' => $template['version'] ? $template['version'] : '1.0.0',
		'title' => $template['title'],
		'title_initial' => get_first_pinyin($template['title']),
		'description' => $template['description'],
		'author' => $template['author'],
		'url' => $template['url'],
		'type' => $template['type'],
		'logo' => 'app/themes/' . $template['name'] . '/preview.jpg',
		'account_support' => 2,
		'sections' => $template['sections'],
		'application_type' => '2',
		'from' => 'cloud',
	);
	$insert_result = pdo_insert('modules', $data);
	if ($insert_result) {
		$insertid = pdo_insertid();
		$oldid_to_name[$template['id']] = $template['name'];
		pdo_update('site_styles', array('templateid' => $insertid), array('templateid' => $template['id']));
		pdo_update('site_styles_vars', array('templateid' => $insertid), array('templateid' => $template['id']));
	}
}
$uni_groups = pdo_getall('uni_group', array('templates !=' => ''), array('id', 'modules', 'templates'), 'id');
if (!empty($uni_groups)) {
	foreach ($uni_groups as $group_key => &$group) {
		$group['templates'] = iunserializer($group['templates']);
		if (empty($group['templates'])) {
			unset($uni_groups[$group_key]);
			continue;
		}
		$modules_data = iunserializer($group['modules']);
                if(!is_array($modules_data)){
                   unset($uni_groups[$group_key]);
                   continue;
                }
		foreach ($group['templates'] as $templateid) {
			array_push($modules_data['modules'], $oldid_to_name[$templateid]);
		}
		pdo_update('uni_group', array('modules' => iserializer($modules_data)), array('id' => $group['id']));
		unset($group);
	}
}
$user_extra_templates = pdo_getall('users_extra_templates');
if (!empty($user_extra_templates)) {
	foreach ($user_extra_templates as $extra_template) {
		$data = array('uid' => $extra_template['uid'], 'module_name' => $oldid_to_name[$extra_template['template_id']], 'support' => 'account_support');
		pdo_insert('users_extra_modules', $data);
	}
}
load()->model('setting');

setting_upgrade_version(IMS_FAMILY, '2.5.6', '202001200001');
return true;