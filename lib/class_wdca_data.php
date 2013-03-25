<?php

class Wdca_Data {

	const DEFAULT_KEY = 'wdca';
	const B_GROUP_KEY = 'wdca-b';
	const AB_MODE_KEY = 'wdca-ab';

	public static function get_ab_options () {
		/*
		return array(
			'enabled' => true,
			'remember_in_session' => false,
			'allow_get_key_override' => true,
			'b_group_for_admins' => false,
			'b_group_for_users' => true,
		);
		*/
		$data = get_option(self::AB_MODE_KEY);
		return $data
			? $data
			: array()
		;
	}

	public static function get_ab_option ($opt) {
		$opts = self::get_ab_options();
		return !empty($opts[$opt])
			? $opts[$opt]
			: false
		;
	}

	public static function get_options ($key=false) {
		if (is_admin()) return self::get_options_by_key($key);
		else return self::get_options_by_context();
	}

	/**
	 * Determines context in which the data call originated 
	 * and spawns proper keys.
	 * @return array Options data
	 */
	public static function get_options_by_context () {
		$key = self::get_active_context_key();
		$data = get_option($key);
		return $data
			? $data
			: array()
		;
	}

	/**
	 * Access data by key, directly.
	 * @param string $key Options key
	 * @return array Options data
	 */
	public static function get_options_by_key ($key) {
		$key = self::get_valid_key($key, self::DEFAULT_KEY);
		$data = get_option($key);
		return $data
			? $data
			: array()
		;
	}

	/**
	 * Makes sure a valid options key is returned
	 * @param  string $key Options key to check
	 * @param  string $default Optional fallback key
	 * @return string Valid options key
	 */
	public static function get_valid_key ($key, $default=false) {
		$default = self::is_valid_key($default) ? $default : self::DEFAULT_KEY;
		return self::is_valid_key($key) ? $key : $default;
	}

	/**
	 * Checks to see if we deal with a valid options key
	 * @param  string $key Options key to check
	 * @return boolean True if valid, false otherwise
	 */
	public static function is_valid_key ($key) {
		$valid = array(self::DEFAULT_KEY, self::B_GROUP_KEY);
		return (bool)($key && in_array($key, $valid));
	}

	public static function get_active_context_key () {
		if (!self::get_ab_option('enabled')) return self::DEFAULT_KEY;
		if (self::get_ab_option('allow_get_key_override') && !empty($_GET['wdca_mode'])) {
			$mode = 'b' == strtolower($_GET['wdca_mode'])
				? self::B_GROUP_KEY
				: self::DEFAULT_KEY
			;
			return $mode;
		}
		if (self::get_ab_option('remember_in_session') && !empty($_SESSION) && !empty($_SESSION['wdca_ab_mode'])) return $_SESSION['wdca_ab_mode']; // We established a context
		
		$key = self::determine_active_context_key();
		if (self::get_ab_option('remember_in_session')) $_SESSION['wdca_ab_mode'] = $key;
		return $key;
	}

	public static function determine_active_context_key () {
		$key = false;
		if (self::get_ab_option('b_group_for_admins')) $key = current_user_can('manage_options') ? self::B_GROUP_KEY : self::DEFAULT_KEY;
		if (self::get_ab_option('b_group_for_users')) $key = is_user_logged_in() ? self::B_GROUP_KEY : self::DEFAULT_KEY;
		if (!$key) {
			// Randomize key getting - throw dice
			$key = (rand(1,11) > 5) ? self::B_GROUP_KEY : self::DEFAULT_KEY;
		}
		return self::get_valid_key($key);
	}
}