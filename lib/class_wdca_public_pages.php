<?php
/**
 * Handles all Admin access functionality.
 */
class Wdca_PublicPages {

	private $_data;
	private $_codec;
	private $_wdca;

	function __construct () {
		$this->_data = Wdca_Data::get_options();//get_option('wdca');
		$this->_codec = new Wdca_Codec;
		$this->_wdca = Wdca_CustomAd::get_instance();
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	public static function serve () {
		$me = new Wdca_PublicPages;
		$me->add_hooks();
	}


	/**
	 * Loads javascript dependencies.
	 */
	function js_load_scripts () {
		if (!is_singular()) return false;
		$this->_wdca->include_frontend_javascript();
	}

	/**
	 * Loads css dependencies.
	 */
	function css_load_styles () {
		if (!is_singular()) return false;
		$this->_wdca->include_frontend_stylesheet();
	}

	function inject_ads_markup ($body) {
		if (defined('WDCA_ADS_DONE')) return $body;

		global $post, $wp_current_filter;
		if (!is_singular()) return $body;

		$selected_types = !empty($this->_data['custom_post_types']) ? $this->_data['custom_post_types'] : array();
		if (empty($this->_data['cpt_skip_posts'])) $selected_types[] = 'post';
		if (!in_array($post->post_type, $selected_types)) return $body;

		if (
			@in_array('get_the_excerpt', $wp_current_filter)
			||
			@in_array('wp_title', $wp_current_filter)
			||
			@in_array('wp_head', $wp_current_filter)
		) return $body;

		// Check published against delayed publishing
		$published = strtotime($post->post_date);
		$delay = $this->_data['ad_delay'];
		$ad_time = strtotime(sprintf('+%d days', $delay), $published);
		if ($ad_time > current_time('timestamp')) return $body;

		$opts = get_option('wdca');
		$prevent_items = !empty($opts['prevent_items']) ? $opts['prevent_items'] : array();
		$prevent_items = is_array($prevent_items) ? $prevent_items : array();
		if (in_array($post->ID, $prevent_items)) return $body;

		$data = Wdca_CustomAd::get_ads();
		if (!$data) return $body;

		$msg_header = @$this->_data['msg_header'];
		$msg_footer = @$this->_data['msg_footer'];
		$msg_link = @$this->_data['msg_link'];

		$link_target = !empty($this->_data['link_target'])
			? ('blank' == $this->_data['link_target'] ? 'target="_blank"' : '')
			: ''
		;

		ob_start();
			include WDCA_PLUGIN_BASE_DIR . '/lib/forms/ads_loop.php';
			$markup = ob_get_contents();
		ob_end_clean();

		$this->_wdca->late_bind_frontend_dependencies();

		return $body . $markup;
	}


	function add_hooks () {
		// Step0: Register options and menu
		if (!@$this->_data['enable_late_binding']) {
			add_action('wp_print_scripts', array($this, 'js_load_scripts'));
			add_action('wp_print_styles', array($this, 'css_load_styles'));
		}

		if (@$this->_data['enabled']) {
			add_filter('the_content', array($this, 'inject_ads_markup'), 20);
			$this->_codec->register();
		}
	}
}
