<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('payment');
load()->model('account');

$dos = array('display');
$do = in_array($do, $dos) ? $do : 'display';
permission_check_account_user('publish_setting');

if ('display' == $do) {
	$account = array(
		'serviceUrl' => $_W['siteroot'] . "api.php?id=" . $_W['account']['acid'],
		'token' => $_W['account']['token'],
		'encodingaeskey' => $_W['account']['encodingaeskey'],
		'isconnect' => $_W['account']['isconnect']
	);
}

template('profile/publish');
