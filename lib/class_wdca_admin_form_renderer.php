<?php
class Wdca_AdminFormRenderer {

	private $_mode_prefix;

	public function __construct ($mode) {
		if (Wdca_Data::AB_MODE_KEY == $mode) $this->_mode_prefix = Wdca_Data::AB_MODE_KEY;
		else $this->_mode_prefix = Wdca_Data::get_valid_key($mode);
	}

	function _get_option ($key=false) {
		$opts = get_option($this->_mode_prefix);
		if (!$key) return $opts;
		return @$opts[$key];
	}

	function _create_checkbox ($name) {
		$pfx = $this->_mode_prefix;
		$opt = $this->_get_option($name);
		$value = @$opt[$name];
		return
			"<input type='radio' name='{$pfx}[{$name}]' id='{$name}-yes' value='1' " . ((int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-yes'>" . __('Yes', 'wdca') . "</label>" .
			'&nbsp;' .
			"<input type='radio' name='{$pfx}[{$name}]' id='{$name}-no' value='0' " . (!(int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-no'>" . __('No', 'wdca') . "</label>" .
		"";
	}

	function _create_textbox ($name) {
		$pfx = $this->_mode_prefix;
		$value = (int)esc_attr($this->_get_option($name));
		return "<input type='text' size='2' maxsize='4' name='{$pfx}[{$name}]' id='{$pfx}-{$name}' value='{$value}' />";
	}

	function _create_text_inputbox ($name, $label, $help='', $pfx='wdca') {
		$pfx = $this->_mode_prefix;
		$value = esc_attr($this->_get_option($name));
		if ($help) $help = "<div><small>{$help}</small></div>";
		return
			"<label for='{$pfx}-{$name}'>{$label}</label> " .
			"<input type='text' class='widefat' name='{$pfx}[{$name}]' id='{$pfx}-{$name}' value='{$value}' />" .
		$help;
	}

	function _create_radiobox ($name, $value) {
		$pfx = $this->_mode_prefix;
		$opt = $this->_get_option($name);
		$checked = (@$opt == $value) ? true : false;
		return "<input type='radio' name='{$pfx}[{$name}]' id='{$name}-{$value}' value='{$value}' " . ($checked ? 'checked="checked" ' : '') . " /> ";
	}

	function create_enabled_box () {
		echo $this->_create_checkbox('enabled');
	}

	function create_live_mode_box () {
		echo $this->_create_checkbox('live_mode');
		echo '<div><small>' . __('Disabling this will only show your ads to logged in users', 'wdca') . '</small></div>';
		echo '<div>' . __('Do NOT turn this on until you are ready to go live', 'wdca') . '</div>';
	}

	function create_ad_count_box () {
		echo $this->_create_textbox('ad_count');
		echo '<div><small>' . __('This many Ads will be shown per post page', 'wdca') . '</small></div>';
	}

	function create_ad_order_box () {
		$orders = array(
			'rand' => __('Random', 'wdca'),
			'title' => __('Title', 'wdca'),
			'date' => __('Date', 'wdca'),
			'modified' => __('Modified', 'wdca'),
		);
		$bys = array('ASC', 'DESC');

		$opt_ord = $this->_get_option('ad_order');
		$opt_ord = $opt_ord ? $opt_ord : 'rand';
		$opt_ord_by = $this->_get_option('ad_order_by');
		$opt_ord_by = $opt_ord_by ? $opt_ord_by : 'ASC';

		echo '<select name="' . $this->_mode_prefix . '[ad_order]" id="wdca-ad_order">';
		foreach ($orders as $key=>$title) {
			$selected = ($opt_ord == $key) ? 'selected="selected"' : '';
			echo "<option value='{$key}' {$selected}>{$title}</option>";
		}
		echo '</select>';
		echo '<select name="wdca[ad_order_by]" id="wdca-ad_order_by">';
		foreach ($bys as $key) {
			$selected = ($opt_ord_by == $key) ? 'selected="selected"' : '';
			echo "<option value='{$key}' {$selected}>{$key}</option>";
		}
		echo '</select>';

		echo '<div><small>' . __('Your Ads will be ordered in the way you set up here', 'wdca') . '</small></div>';
	}

	function create_p_first_count_box () {
		echo $this->_create_textbox('p_first_count');
		echo '<div><small>' . __('Your first Ad will be injected in your post after this many paragraphs.', 'wdca') . '</small></div>';
	}
	function create_p_count_box () {
		echo $this->_create_textbox('p_count');
		echo '<div><small>' . __('Your subsequent Ads will be injected in your post every [number] of paragraphs.', 'wdca') . '</small></div>';
	}

	function create_ad_show_after_box () {
		$predefined_delays = array(1,5) + range(5, 30, 5);
		$delay = $this->_get_option('ad_delay');
		$select = '<select name="' . $this->_mode_prefix . '[ad_delay]">';
		$select .= '<option value="">' . __('immediately', 'wdca') . '</option>';
		foreach ($predefined_delays as $count) {
			$selected = $count == $delay ? 'selected="selected"' : '';
			$label = $count != 1 
				? sprintf(__('%d days', 'wdca'), $count)
				: sprintf(__('%d day', 'wdca'), $count)
			;
			$select .= "<option value='{$count}' {$selected}>{$label}</option>";
		}
		$select .= '</select>';
		echo '<label>' . sprintf(__('Show my Ads %s after the post gets published', 'wdca'), $select) . '</label>';
		echo '<div><small>' . __('Use this option to delay automatic Ads injection for a selected time period.', 'wdca') . '</small></div>';
	}

	function create_predefined_positions_box () {
		echo '' .
			__('Before first paragraph:', 'wdca') .
			'&nbsp;' .
			$this->_create_checkbox('predefined_before_first_p') .
			'<div><small>' . __('Enabling this option will insert your first Ad at the very begining of your post', 'wdca') . '</small></div>' .
		'<br />';
		echo '' .
			__('Halway through your post:', 'wdca') .
			'&nbsp;' .
			$this->_create_checkbox('predefined_halfway_through') .
			'<div><small>' . __('Enabling this option will insert an Ad halway through your post', 'wdca') . '</small></div>' .
		'<br />';
		echo '' .
			__('After last paragraph:', 'wdca') .
			'&nbsp;' .
			$this->_create_checkbox('predefined_after_last_p') .
			'<div><small>' . __('Enabling this option will insert your first Ad at the very end of your post', 'wdca') . '</small></div>' .
		'<br />';

		$ps = (int)$this->_get_option('predefined_ignore_other-paragraph_count');
		$paragraphs = "<input type='text' name='{$this->_mode_prefix}[predefined_ignore_other-paragraph_count]' size='2' value='{$ps}' />";
		echo '' .
			__('Ignore other injection settings:', 'wdca') .
			'&nbsp;' .
			$this->_create_checkbox('predefined_ignore_other') .
			'<div><small>' . __('Enabling this option will ignore other injection settings and insert your Ads at the selected predefined settings only', 'wdca') . '</small></div>' .
			sprintf(__('... but only on posts longer than %s paragraphs', 'wdca'), $paragraphs) .
			'<div><small>' . __('If your posts are shorter than the number of paragraphs entered here, the default behavior will take precedence over the predefined positions injection.', 'wdca') . '</small></div>' .
			'<div><small>' . __('Leave the value at <code>0</code> to disable this behavior.', 'wdca') . '</small></div>' .
		'<br />';
	}

	function create_theme_box () {
		$themes = array(
			'' => __('Default', 'wdca'),
			'wpmu' => __('WPMU.org', 'wdca'),
			'dark' => __('Dark', 'wdca'),
			'dotted' => __('Dotted', 'wdca'),
			'greenbutton' => __('Green Button', 'wdca'),
			'wpmu2013' => __('wpmu.org 2013', 'wdca'),
			'paper' => __('Paper (modern browsers only)', 'wdca'),
			//'alex' => __('Alex', 'wdca'),
		);
		$current = $this->_get_option('theme');

		echo '<select name="' . $this->_mode_prefix . '[theme]" id="wdca-theme">';
		foreach ($themes as $key => $lbl) {
			$selected = ($current == $key) ? 'selected="selected"' : '';
			echo "<option value='{$key}' {$selected}>{$lbl}</option>";
		}
		echo '</select>';
	}

	function create_messages_box () {
		echo $this->_create_text_inputbox('msg_header', __('Header text', 'wdca'), __('This text will appear in your Ad header, before the link', 'wdca'));
		echo $this->_create_text_inputbox('msg_footer', __('Footer text', 'wdca'), __('This text will appear below your Ad content, before the link', 'wdca'));
		echo $this->_create_text_inputbox('msg_link', __('Footer link text', 'wdca'), __('This text will appear as link text in your footer', 'wdca'));
	}

	function create_link_box () {
		echo $this->_create_radiobox('link_target', '') .
			'&nbsp;' .
			'<label for="link_target-">' . __('Opens in current window/tab', 'wdca') . '</label>' .
		'<br />';
		echo $this->_create_radiobox('link_target', 'blank') .
			'&nbsp;' .
			'<label for="link_target-blank">' . __('Opens in new window/tab', 'wdca') . '</label>' .
		'';
	}

	function create_ga_setup_box () {
		echo '<p><i>' .
			__('<b>Note:</b> your pages need to already be set up for Google Analytics tracking for this to work properly.', 'wdca') .
		'</i></p>';
	}

	function create_ga_integration_box () {
		echo $this->_create_checkbox('ga_integration');
	}

	function create_ga_category_box () {
		$value = $this->_get_option('ga_category');
		$value = esc_attr((
			$value
				? $value
				: 'In Post Ads'
		));
		echo "<input type='text' name='{$this->_mode_prefix}[ga_category]' value='{$value}' class='regular-text' />";
	}

	function create_ga_label_box () {
		$value = $this->_get_option('ga_label');
		if (!$value) $value = Wdca_Data::DEFAULT_KEY == $this->_mode_prefix ? 'Default' : 'Group B';
		$value = esc_attr($value);
		echo "<input type='text' name='{$this->_mode_prefix}[ga_label]' value='{$value}' class='regular-text' />";
	}

	function create_selector_box () {
		$selector = $this->_get_option('selector');
		$selector = $selector ? $selector : '>p';
		echo "<input type='text' name='{$this->_mode_prefix}[selector]' id='wdca-selector' value='{$selector}' class='widefat' />" .
			'<div><small>' . __('If you are experiencing problems with your theme, you may want to change the default selector to something more generic - e.g. <code>p</code>', 'wdca') . '</small></div>' .
			'<div><small>' . __('You can also use this box to allow Ad inserting after other elements too - e.g. <code>ul,ol,p</code>', 'wdca') . '</small></div>' .
		'';
	}

	function create_cpt_ads_box () {
		$raw_types = get_post_types(array('public'=>true), 'objects');
		$types = array();
		$_skip_types = array('attachment', 'post', Wdca_CustomAd::POST_TYPE);
		foreach ($raw_types as $type) {
			if (in_array($type->name, $_skip_types)) continue;
			$types[$type->name] = $type->label;
		}
		$selected_types = $this->_get_option('custom_post_types');
		$selected_types = $selected_types ? $selected_types : array();

		echo '<select name="' . $this->_mode_prefix . '[custom_post_types][]" multiple="multiple">';
		foreach ($types as $key => $label) {
			$selected = in_array($key, $selected_types) ? 'selected="selected"' : '';
			echo "<option value='{$key}' {$selected}>{$label}</option>";
		}
		echo '</select>';
		echo '<div><small>' . __('The plugin will auto-insert Ads into your Posts by default. Select additional post types here.', 'wdca') . '</small></div>';

		echo '' .
			'<label for="cpt_skip_posts-yes">' . __('Do not auto-inject into posts:', 'wdca') . '</label>&nbsp;' .
			$this->_create_checkbox('cpt_skip_posts') .
		'';

		echo '<div><small>' . __('These settings apply to auto-insertion only - you will still be able to insert the Ads using shortcodes.', 'wdca') . '</small></div>';
	}

	function create_post_metabox_box () {
		echo $this->_create_checkbox('post_metabox');
		echo '<div><small>' . __('Enabling this option will add a metabox to your post editor interface, which you can use to prevent Ad insertion per post.', 'wdca') . '</small></div>';
	}

	function create_categories_box () {
		$categories = apply_filters('wdca-settings-categories_list', get_terms('category', array('orderby'=>'term_group', 'hide_empty' => false)));
		$ad_terms = get_terms('wdca_ad_categories', array('orderby'=>'term_group', 'hide_empty' => false));

		$cats_to_ads = $this->_get_option('category_ads');
		$cats_to_ads = is_array($cats_to_ads) ? $cats_to_ads : array();

		$ad_str = $cat_str = '';

		if( ! empty( $categories ) ){

			foreach ($categories as $cat) {

				$cat_str .= '<tr id="wdca-category-options-' . $cat->term_id . '">';

					$cat_str .= '<td style="vertical-align:top;">';
						$cat_str .= '<span>' . $cat->name . '</span>';
					$cat_str .= '</td>';


					$cat_str .= '<td>';
					foreach( $ad_terms as $ad ){

						$ads_ids = isset( $cats_to_ads[ $cat->term_id ] ) ? $cats_to_ads[ $cat->term_id ] : array();
						$checked = ( in_array( $ad->term_id, $ads_ids ) ) ? 'checked="checked"' : '';
						$cat_str .= '<div>';
							$cat_str .= '<label>';								
								$cat_str .= '<input type="checkbox" name="' . $this->_mode_prefix . '[category_ads][' . $cat->term_id . '][' . $ad->term_id . ']" value="' . $ad->term_id . '" '. $checked .' />';
								$cat_str .= $ad->name;
							$cat_str .= '</label>';
						$cat_str .= '</div>';

					}
					$cat_str .= '</td>';

				$cat_str .= '</tr>';

			}

		}

		echo '<div style="display:block; max-height: 300px; overflow:hidden; overflow-y: scroll;">';

			echo '<table class="widefat">';
				echo '<thead>
						<tr>
							<th>' . __('My posts within this Category', 'wdca') . '&hellip;</th>
							<th>&hellip;' . __('will only show Ads from these Ad Categories', 'wdca') . '</th>
						</tr>
					</thead>';
				echo '<tfoot><tr><th></th><th></th></tr></tfoot>';
				echo "<tbody><tr><td>{$cat_str}</td><td>{$ad_str}</td></tr></tbody>";
			echo '</table>';

		echo '</div>';
		_e('If you do not set any mappings here, any Ad could appear in any of your posts.', 'wdca');
	}

	function create_tags_box () {
		$tags = apply_filters('wdca-settings-tags_list', get_terms('post_tag', array('orderby'=>'term_group', 'hide_empty' => false)));
		$ad_terms = get_terms('wdca_ad_categories', array('orderby'=>'term_group', 'hide_empty' => false));

		$cats_to_ads = $this->_get_option('category_ads');
		$cats_to_ads = is_array($cats_to_ads) ? $cats_to_ads : array();

		$ad_str = $cat_str = '';

		if( ! empty( $tags ) ){

			foreach ($tags as $term) {

				$cat_str .= '<tr id="wdca-category-options-' . $term->term_id . '">';

					$cat_str .= '<td style="vertical-align:top;">';
						$cat_str .= '<span>' . $term->name . '</span>';
					$cat_str .= '</td>';


					$cat_str .= '<td>';
					foreach( $ad_terms as $ad ){

						$ads_ids = isset( $cats_to_ads[ $term->term_id ] ) ? $cats_to_ads[ $term->term_id ] : array();
						$checked = ( in_array( $ad->term_id, $ads_ids ) ) ? 'checked="checked"' : '';
						$cat_str .= '<div>';
							$cat_str .= '<label>';								
								$cat_str .= '<input type="checkbox" name="' . $this->_mode_prefix . '[category_ads][' . $term->term_id . '][' . $ad->term_id . ']" value="' . $ad->term_id . '" '. $checked .' />';
								$cat_str .= $ad->name;
							$cat_str .= '</label>';
						$cat_str .= '</div>';

					}
					$cat_str .= '</td>';

				$cat_str .= '</tr>';

			}

		}


		//echo $cat_str . $ad_str;
		echo '<div style="display:block; max-height: 300px; overflow:hidden; overflow-y: scroll;">';

			echo '<table class="widefat">';
				echo '<thead>
						<tr>
							<th>' . __('My posts within this Tag', 'wdca') . '&hellip;</th>
							<th>&hellip;' . __('will only show Ads from these Ad Categories', 'wdca') . '</th>
						</tr>
					</thead>';
				echo '<tfoot><tr><th></th><th></th></tr></tfoot>';
				echo "<tbody>{$cat_str}</tbody>";
				echo '</table>';

		echo '</div>';

		_e('If you do not set any mappings here, any Ad could appear in any of your posts.', 'wdca');
	}

	function create_lazy_loading_box () {
		echo __('Enable lazy dependency loading:', 'wdca') .
			'&nbsp;' .
			$this->_create_checkbox('enable_late_binding') .
			'<div><small>' . __('Lazy dependency loading can improve your site load times by requiring resources as they are needed.', 'wdca') . '</small></div>'
		;

		$wdca = Wdca_CustomAd::get_instance();
		$hook = $wdca->get_late_binding_hook();
		echo '<br />' .
			'<label for="wdca-late_binding_hook">' . __('Lazy loading hook <small>(advanced)</small>:', 'wdca') . '</label>&nbsp;' .
			'<input type="text" name="' . $this->_mode_prefix . '[late_binding_hook]" id="wdca-late_binding_hook" value="' . $hook . '" />' .
			'<div><small>' . __('Lazy dependency loading relies on footer hook to deploy properly. If your theme does not implement the default hook, use this field to set your custom one.', 'wdca') . '</small></div>'
		;

		echo '<h4>' . __('Style inclusion type', 'wdca') . '</h4>' .
			$this->_create_radiobox('style_inclusion_type', '') . '&nbsp;<label for="style_inclusion_type-">' . __('Normal', 'wdca') . '</label><br />' .
			$this->_create_radiobox('style_inclusion_type', 'inline') . '&nbsp;<label for="style_inclusion_type-inline">' . __('Inline', 'wdca') . '</label><br />' .
			$this->_create_radiobox('style_inclusion_type', 'dynamic') . '&nbsp;<label for="style_inclusion_type-dynamic">' . __('Dynamic', 'wdca') . '</label><br />' .
		'';
	}


	function create_ab_mode_setup_box () {
		echo '<p><i>' .
			__('This is where you can set up your A/B testing, and settings group loading rules.', 'wdca') .
		'</i></p>';
	}

	function create_sessions_box () {
		echo $this->_create_checkbox('remember_in_session');
		echo '<div><small>' .
			__('By default, A/B mode distribution is random. Enabling this option will enforce initially selected mode for your users to persist accross requests (i.e. users that got A mode settings will keep seeing them, and vice versa).', 'wdca') .
		'</small></div>';
	}

	function create_b_group_for_admins_box () {
		echo $this->_create_checkbox('b_group_for_admins');
	}
	
	function create_b_group_for_users_box () {
		echo $this->_create_checkbox('b_group_for_users');
	}

	function create_get_key_override_box () {
		echo $this->_create_checkbox('allow_get_key_override');
		echo '<div><small>' .
			__('If A/B testing is enabled, allowing this option will let you test each group unconditionally, by passing this to your URL: <code>?wdca_mode=a</code> for A group settings, <code>?wdca_mode=b</code> for B group settings.', 'wdca') .
		'</small></div>';
	}


}