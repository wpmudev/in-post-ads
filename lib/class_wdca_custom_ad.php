<?php
/**
 * Handles custom ads post types.
 */
class Wdca_CustomAd {

	const POST_TYPE = 'wdca_custom_ad';

	private static $_cache;
	private static $_cache_ids = array();

	private $_data = array();

	private static $_instance;

	private function __construct () {
		$this->_data = Wdca_Data::get_options();//get_option('wdca');
	}

	public static function init () {
		$me = self::get_instance();
		add_action('init', array($me, 'register_post_type'));
		add_action('admin_init', array($me, 'add_meta_boxes'));
		add_action('save_post', array($me, 'save_ad_meta'));

		add_filter("manage_edit-" . self::POST_TYPE . "_columns", array($me, "add_custom_columns"));
		add_action("manage_posts_custom_column",  array($me, "fill_custom_columns"));
	}

	public static function get_instance () {
		if (!self::$_instance) self::$_instance = new Wdca_CustomAd;
		return self::$_instance;
	}

	public function register_post_type () {
		register_post_type(self::POST_TYPE, array(
			'labels' => array(
				'name' => __('In Post Ads', 'wdca'),
				'singular_name' => __('In Post Ad', 'wdca'),
				'add_new_item' => __('Add new In Post Ad', 'wdca'),
				'edit_item' => __('Edit In Post Ad', 'wdca'),
			),
			'public' => true,
			'supports' => array(
				'title', 'editor', 'thumbnail'
			),
			'rewrite' => false,
			'capabilities' => array(
				'publish_posts' => 'manage_options',
				'edit_posts' => 'manage_options',
				'edit_others_posts' => 'manage_options',
				'delete_posts' => 'manage_options',
				'delete_others_posts' => 'manage_options',
				'read_private_posts' => 'manage_options',
				'edit_post' => 'manage_options',
				'delete_post' => 'manage_options',
				'read_post' => 'read_post',
			),
		));

		register_taxonomy('wdca_ad_categories', self::POST_TYPE, array(
			'labels' => array(
				'name' => __('Ad Categories', 'wdca'),
				'singular_name' => __('Ad Category', 'wdca'),
				'add_new_item' => __('Add new Ad Category', 'wdca'),
				'edit_item' => __('Edit Ad Category', 'wdca'),
				'search_items' => __('Search Ad Categories', 'wdca'),
				'popular_items' => __('Popular Ad Categories', 'wdca'),
				'all_items' => __('All Ad Categories', 'wdca'),
				'separate_items_with_commas' => __('Separate Ad Categories with commas', 'wdca'),
				'add_or_remove_items' => __('Add or remove Ad Categories', 'wdca'),
				'choose_from_most_used' => __('Choose from most used Ad Categories', 'wdca'),
			),
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'hierarchical' => false,
			'rewrite' => false,
		));
	}

	public function add_custom_columns ($cols) {
		return array_merge($cols, array(
			'ad_categories' => __('Ad Categories', 'wdca'),
		));
	}

	public function fill_custom_columns ($col) {
		global $post;
		if ('ad_categories' != $col) return $col;
		echo get_the_term_list($post->ID, 'wdca_ad_categories', '', ', ', '');
	}

	public function add_meta_boxes () {
		add_meta_box(
			'wdca_plugin_link',
			__('Ad link', 'wdca'),
			array($this, 'render_link_box'),
			self::POST_TYPE,
			'side',
			'high'
		);
		add_meta_box(
			'wdca_ad_appearance',
			__('Ad appearance', 'wdca'),
			array($this, 'render_appearance_box'),
			self::POST_TYPE,
			'side',
			'low'
		);
	}

	public function render_link_box () {
		global $post;
		$link = get_post_meta($post->ID, 'wdca_plugin_url', true);
		echo '<p><label for="wdca_plugin_url">' . __('Link URL', 'wdca') . '</label>';
		echo "<input type='text' name='wdca_plugin_url' id='wdca_plugin_url' class='widefat' value='{$link}' /></p>";
	}

	public function render_appearance_box () {
		global $post;
		$appearance = get_post_meta($post->ID, 'wdca_appearance', true);
		$title = @$appearance['hide_title'] ? 'checked="checked"' : '';
		$body = @$appearance['hide_body'] ? 'checked="checked"' : '';
		$footer = @$appearance['hide_footer'] ? 'checked="checked"' : '';
		$strip_class = @$appearance['strip_class'] ? 'checked="checked"' : '';
		echo '<p>' .
			'<input type="hidden" name="wdca_appearance[hide_title]" value="0" />' .
			"<input type='checkbox' name='wdca_appearance[hide_title]' id='wdca_appearance-hide_title' value='1' {$title} /> " .
			'<label for="wdca_appearance-hide_title">' . __('Do not show title', 'wdca') . '</label>' .
		'</p>';
		echo '<p>' .
			'<input type="hidden" name="wdca_appearance[hide_body]" value="0" />' .
			"<input type='checkbox' name='wdca_appearance[hide_body]' id='wdca_appearance-hide_body' value='1' {$body} /> " .
			'<label for="wdca_appearance-hide_body">' . __('Do not show content', 'wdca') . '</label>' .
		'</p>';
		echo '<p>'.
			'<input type="hidden" name="wdca_appearance[hide_footer]" value="0" />' .
			"<input type='checkbox' name='wdca_appearance[hide_footer]' id='wdca_appearance-hide_footer' value='1' {$footer} /> " .
			'<label for="wdca_appearance-hide_footer">' . __('Do not show footer', 'wdca') . '</label>' .
		'</p>';
		echo '<p>' .
			'<input type="hidden" name="wdca_appearance[strip_class]" value="0" />' .
			"<input type='checkbox' name='wdca_appearance[strip_class]' id='wdca_appearance-strip_class' value='1' {$strip_class} /> " .
			'<label for="wdca_appearance-strip_class">' . __('Strip default style', 'wdca') . '</label>' .
		'</p>';
	}

	public function save_ad_meta () {
		global $post;
		if (@$_POST['wdca_plugin_url']) {
			update_post_meta($post->ID, "wdca_plugin_url", $_POST["wdca_plugin_url"]);
		}
		if (@$_POST['wdca_appearance']) {
			update_post_meta($post->ID, "wdca_appearance", $_POST["wdca_appearance"]);
		}
	}

	public static function get_all_ads () {
		$q = new Wp_Query(array(
			'post_type' => self::POST_TYPE,
			'posts_per_page' => -1,
			'orderby' => 'title',
		));
		return $q->posts;
	}

	public static function get_ads () {
		if (!self::$_cache) self::populate_cache();
		return self::$_cache;
	}

	public static function get_ad ($id) {
		if (!$id) return self::pull_add_from_cache();
		$ad = get_post($id);
		self::$_cache_ids[] = $ad->ID;
		return $ad;
	}

	private static function populate_cache () {
		$opts = Wdca_Data::get_options();//get_option('wdca');

		if (!current_user_can('manage_options') && !@$opts['live_mode']) return false;

		$limit = (int)@$opts['ad_count'];
		if (!$limit) return false;

		$orders = array('rand', 'title', 'date', 'modified');
		$bys = array('ASC', 'DESC');

		$order = @$opts['ad_order'];
		$order = in_array($order, $orders) ? $order : 'rand';

		$by = @$opts['ad_order_by'];
		$by = in_array($by, $bys) ? $by : 'ASC';

		// Handle Ad2Post categories
		global $post;
		$ad_cat_ids = array();
		$cats_to_ads = !empty($opts['category_ads']) ? $opts['category_ads'] : array();
		$cats_to_ads = is_array($cats_to_ads) ? $cats_to_ads : array();
		$categories = get_the_category($post->ID);
		foreach ($categories as $cat) {
			if (isset($cats_to_ads[$cat->term_id])) foreach ($cats_to_ads[$cat->term_id] as $ad_id) $ad_cat_ids[] = $ad_id;
		}

		// Ad2Post tags
		$tags_to_ads = !empty($opts['tag_ads']) ? $opts['tag_ads'] : array();
		$tags_to_ads = is_array($tags_to_ads) ? $tags_to_ads : array();
		$tags = wp_get_post_tags($post->ID);
		foreach ($tags as $tag) {
			if (isset($tags_to_ads[$tag->term_id])) foreach ($tags_to_ads[$tag->term_id] as $ad_id) $ad_cat_ids[] = $ad_id;
		}
		$ad_cat_ids = array_unique($ad_cat_ids);

		$query_args = array(
			'post__not_in' => self::$_cache_ids,
			'post_type' => self::POST_TYPE,
			'showposts' => $limit,
			'orderby' => $order,
			'order' => $by,
		);
		if ($ad_cat_ids) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'wdca_ad_categories',
				'field' => 'id',
				'terms' => $ad_cat_ids,
			);
		}
		$q = new Wp_Query($query_args);
		self::$_cache = $q->posts;

		foreach ($q->posts as $ad) {
			self::$_cache_ids[] = $ad->ID;
		}
	}

	private static function pull_add_from_cache () {
		if (!self::$_cache) self::populate_cache();
		$ad = array_pop(self::$_cache);
		return $ad;
	}

	public function get_ad_template () {
		$default_template = 'ad_single.php';
		$potential_template = !empty($this->_data['theme']) ? sprintf('ads_single-%s.php', $this->_data['theme']) : $default_template;
		return 
			file_exists(WDCA_PLUGIN_BASE_DIR . "/lib/forms/{$potential_template}")
				? WDCA_PLUGIN_BASE_DIR . "/lib/forms/{$potential_template}"
				: WDCA_PLUGIN_BASE_DIR . "/lib/forms/{$default_template}"
		;
	}

/* ----- Dependency loading ----- */

	public function include_frontend_javascript () {
		if (defined('WDCA_FLAG_JAVASCRIPT_LOADED')) return false;

		wp_enqueue_script('jquery');
		wp_enqueue_script('wdca', WDCA_PLUGIN_URL . '/js/wdca.js');

		$wdca_data = array(
			"first_ad" => (!empty($this->_data['p_first_count']) ? (int)$this->_data['p_first_count'] : 0),
			"count" => (!empty($this->_data['p_count']) ? (int)$this->_data['p_count'] : 0),
			"selector" => (!empty($this->_data['selector']) ? $this->_data['selector'] : '>p'),
			"predefined" => array(
				"before" => (int)(!empty($this->_data['predefined_before_first_p'])),
				"middle" => (int)(!empty($this->_data['predefined_halfway_through'])),
				"after" => (int)(!empty($this->_data['predefined_after_last_p'])),
				"ignore_other" => (int)(!empty($this->_data['predefined_ignore_other'])),
				'ignore_requirement' => (int)(!empty($this->_data['predefined_ignore_other-paragraph_count']) ? $this->_data['predefined_ignore_other-paragraph_count'] : 0),
			),
			"ga" => array(
				"enabled" => !empty($this->_data['ga_integration']),
				"category" => (!empty($this->_data['ga_category']) ? esc_js($this->_data['ga_category']) : ''),
				"label" => (!empty($this->_data['ga_label']) ? esc_js($this->_data['ga_label']) : ''),
			),
		);
		echo '<script type="text/javascript">var _wdca=' . json_encode($wdca_data) . ';</script>';

		define('WDCA_FLAG_JAVASCRIPT_LOADED', true, true);
	}
	
	public function include_frontend_stylesheet () {
		if (defined('WDCA_FLAG_STYLESHEET_LOADED')) return false;

		$theme = @$this->_data['theme'];
		$theme = $theme ? $theme : 'default';
		if (!current_theme_supports('wdca')) {
			wp_enqueue_style('wdca', WDCA_PLUGIN_URL . "/css/wdca.css");
			if (!file_exists(WDCA_PLUGIN_BASE_DIR . "/css/wdca-{$theme}.css")) return false;
			wp_enqueue_style('wdca-theme', WDCA_PLUGIN_URL . "/css/wdca-{$theme}.css");
		}
		define('WDCA_FLAG_STYLESHEET_LOADED', true, true);
	}

	public function get_late_binding_hook () {
		$hook = @$this->_data['late_binding_hook'];
		$hook = $hook ? $hook : 'wp_footer';
		$hook = defined('WDCA_FOOTER_HOOK') && WDCA_FOOTER_HOOK
			? WDCA_FOOTER_HOOK
			: $hook
		;
		return apply_filters('wdca-core-footer_hook', $hook);
	}

	/**
	 * Used for late binding dependencies.
	 */
	public function late_bind_frontend_dependencies () {
		if (defined('WDCA_FLAG_LATE_INCLUSION_BOUND')) return false;
		if (defined('WDCA_FLAG_JAVASCRIPT_LOADED') && defined('WDCA_FLAG_STYLESHEET_LOADED')) return false;
		
		$hook = $this->get_late_binding_hook();
		if (!$hook) return false;

		add_action($hook, array($this, 'include_frontend_stylesheet'), 18);
		add_action($hook, array($this, 'include_frontend_javascript'), 19);

		define('WDCA_FLAG_LATE_INCLUSION_BOUND', true, true);
	}
}