<?php
class Wdca_Codec {

	var $shortcodes = array(
		'ad' => 'wdca_ad',
	);
	private $_data;
	private $_wdca;

	function __construct () {
		$this->_data = Wdca_Data::get_options();//get_option('wdca');
		$this->_wdca = Wdca_CustomAd::get_instance();
	}

	function process_ad_code ($args=array(), $content='') {
		$args = shortcode_atts(array(
			'id' => false,
			'appearance' => false,
			'forced' => false,
		), $args);
		$forced = ($args['forced'] && in_array($args['forced'], array('on', 'forced', 'yes', 'true')));
		if (!is_singular() && !$forced) return $content;
		if (is_singular() && !$forced && !defined('WDCA_ADS_DONE')) define('WDCA_ADS_DONE', true); // Auto-injection flag

		// Check published against delayed publishing
		if (!$forced) {
			global $post;
			$published = strtotime($post->post_date);
			$delay = $this->_data['ad_delay'];
			$ad_time = strtotime(sprintf('+%d days', $delay), $published);
			if ($ad_time > current_time('timestamp')) return $content;
		}

		$appearance_classes = $this->_parse_appearance($args['appearance']);

		$msg_header = @$this->_data['msg_header'];
		$msg_footer = @$this->_data['msg_footer'];
		$msg_link = @$this->_data['msg_link'];

		$ad = Wdca_CustomAd::get_ad($args['id']);
		if (!$ad) return '';
		$appearance = get_post_meta($ad->ID, 'wdca_appearance', true);
		if (!@$appearance['strip_class']) {
			$appearance_classes = $appearance_classes ? $appearance_classes : 'wdca_default';
		}
		$system_classes = '';

		ob_start();
			include $this->_wdca->get_ad_template();
			$markup = ob_get_contents();
		ob_end_clean();

		$this->_wdca->late_bind_frontend_dependencies();

		return $markup;
	}

	/**
	 * Registers shortcode handlers.
	 */
	function register () {
		foreach ($this->shortcodes as $key=>$shortcode) {
			add_shortcode($shortcode, array($this, "process_{$key}_code"));
		}
	}

	private function _parse_appearance ($arg) {
		if (!$arg) return false;
		$tmp = explode(' ', $arg);
		if (!$tmp) return false;

		$ret = array();
		foreach ($tmp as $class) {
			$ret[] = 'wdca_' . strtolower(preg_replace('/[^a-z0-9]/', '', $class));
		}

		if (!$ret) return false;
		return join(' ', $ret);
	}

}