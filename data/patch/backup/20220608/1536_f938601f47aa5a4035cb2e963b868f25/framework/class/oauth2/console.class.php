<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
class Console extends OAuth2Client {
	private $calback_url;

	public function __construct($ak, $sk) {
		parent::__construct($ak, $sk);
		$this->stateParam['from'] = 'console';
	}

	public function showLoginUrl($calback_url = '') {
		return '';
	}

	public function user() {
	}

	public function register() {
	}

	public function systemFields() {
	}

	public function login() {
		return $this->user();
	}

	public function bind() {
		return true;
	}

	public function unbind() {
		return true;
	}

	public function isbind() {
		global $_W;
		$bind_info = table('users_bind')->getByTypeAndUid(USER_REGISTER_TYPE_CONSOLE, $_W['uid']);

		return !empty($bind_info);
	}
}
