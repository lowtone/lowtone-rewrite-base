<?php
/*
 * Plugin Name: Rewrite Base
 * Plugin URI: http://wordpress.lowtone.nl/plugins/rewrite-base/
 * Description: Define custom base slug for authors, feeds, search, comments and pagination.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\rewrite\base
 */

namespace lowtone\rewrite\base {

	// Overwrite base values

	/**
	 * Base values are overwritten using the setup_theme action because it is 
	 * the first action after $wp_rewrite is set.
	 */

	add_action("setup_theme", function() {
		global $wp_rewrite;

		foreach (keys() as $key) {
			$key = $key . "_base";

			if (!($base = get_option($key)))
				continue;

			$wp_rewrite->{$key} = apply_filters("rewrite_" . $key, $base);
		}
	});

	// Add base settings
	
	add_action("admin_init", function() {
		global $wp_rewrite, $wp_settings_sections;

		add_settings_section(
				"lowtone_rewrite_base", 
				__("Rewrite base", "lowtone_rewrite_base"),
				function() {
					echo '<p>' . __("These values are used in rewrite rules, for example to define pagination.", "lowtone_rewrite_base") . '</p>';
				},
				"permalink"
			);

		foreach (keys() as $key) {

			$title = __(ucfirst($key)  . " base", "lowtone_rewrite_base");

			$key = $key . "_base";

			// Handle event base option setting
					
			if (isset($_POST[$key]))
				update_option($key, sanitize_title(untrailingslashit($_POST[$key])));

			// Add base option settings fields
			
			add_settings_field(
				$key,
				$title,
				function() use ($wp_rewrite, $key) {
					echo apply_filters("rewrite_input_" . $key, sprintf('<input name="%s" type="text" class="regular-text code" value="%s" />', esc_attr($key), esc_attr(get_option($key) ?: $wp_rewrite->{$key})));
				},
				"permalink",
				"lowtone_rewrite_base"
			);

		}
	});

	// Register textdomain
	
	add_action("plugins_loaded", function() {
		load_plugin_textdomain("lowtone_rewrite_base", false, basename(__DIR__) . "/assets/languages");
	});

	// Functions

	function keys() {
		return apply_filters("lowtone_rewrite_base_keys", array(
				"author",
				"search",
				"comments",
				"pagination",
				"feed"
			));
	}

}