<?php
/*
Plugin Name: In Post Ads
Plugin URI: http://premium.wpmudev.org/project/in-post-ads
Description: Adds custom ads post type and manages it on single post pages.
Version: 1.5.2
Author: WPMU DEV
Author URI: http://premium.wpmudev.org
WDP ID: 240

Copyright 2009-2011 Incsub (http://incsub.com)
Author - Ve Bailovity (Incsub)
Contributors - Victor Ivanov (Incsub)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


define ('WDCA_PLUGIN_SELF_DIRNAME', basename(dirname(__FILE__)), true);
define ('WDCA_PROTOCOL', (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'), true); // Protocol check

//Setup proper paths/URLs and load text domains
if (is_multisite() && defined('WPMU_PLUGIN_URL') && defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('WDCA_PLUGIN_LOCATION', 'mu-plugins', true);
	define ('WDCA_PLUGIN_BASE_DIR', WPMU_PLUGIN_DIR, true);
	define ('WDCA_PLUGIN_URL', str_replace('http://', WDCA_PROTOCOL, WPMU_PLUGIN_URL), true);
	$textdomain_handler = 'load_muplugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . WDCA_PLUGIN_SELF_DIRNAME . '/' . basename(__FILE__))) {
	define ('WDCA_PLUGIN_LOCATION', 'subfolder-plugins', true);
	define ('WDCA_PLUGIN_BASE_DIR', WP_PLUGIN_DIR . '/' . WDCA_PLUGIN_SELF_DIRNAME, true);
	define ('WDCA_PLUGIN_URL', str_replace('http://', WDCA_PROTOCOL, WP_PLUGIN_URL) . '/' . WDCA_PLUGIN_SELF_DIRNAME, true);
	$textdomain_handler = 'load_plugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('WDCA_PLUGIN_LOCATION', 'plugins', true);
	define ('WDCA_PLUGIN_BASE_DIR', WP_PLUGIN_DIR, true);
	define ('WDCA_PLUGIN_URL', str_replace('http://', WDCA_PROTOCOL, WP_PLUGIN_URL), true);
	$textdomain_handler = 'load_plugin_textdomain';
} else {
	// No textdomain is loaded because we can't determine the plugin location.
	// No point in trying to add textdomain to string and/or localizing it.
	wp_die(__('There was an issue determining where In Post Ads plugin is installed. Please reinstall.'));
}
$textdomain_handler('wdca', false, WDCA_PLUGIN_SELF_DIRNAME . '/languages/');


require_once WDCA_PLUGIN_BASE_DIR . '/lib/class_wdca_data.php';
require_once WDCA_PLUGIN_BASE_DIR . '/lib/class_wdca_codec.php';
require_once WDCA_PLUGIN_BASE_DIR . '/lib/class_wdca_custom_ad.php';

function wdca__init () {
	Wdca_CustomAd::init();

	if (is_admin()) {
		require_once WDCA_PLUGIN_BASE_DIR . '/lib/class_wdca_admin_form_renderer.php';
		require_once WDCA_PLUGIN_BASE_DIR . '/lib/class_wdca_admin_pages.php';
		Wdca_AdminPages::serve();
	} else {
		require_once WDCA_PLUGIN_BASE_DIR . '/lib/class_wdca_public_pages.php';
		Wdca_PublicPages::serve();
	}
}
add_action('init', 'wdca__init', 0); // Make sure we're as early with this as possible.
