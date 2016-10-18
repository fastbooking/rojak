<?php
/**
* @api{} Asset Async
* @apiName AssetAsync
* @apiGroup AddThemeSupport
* @apiVersion 1.0.0
* @apiDescription Make all assets (css and js) enqueue async
*
* - functions can be overridden in the theme by redeclaring the function
*
* ---
*
* ## CSS
* - Code inspired mainly from from __WP Async CSS__ https://wordpress.org/plugins/wp-async-css/
* - This feature uses https://github.com/filamentgroup/loadCSS
* - Install in bower, `bower install loadcss --save`
* - Include in js `#=include ../../bower/loadcss/src/loadCSS.js`
*
* ---
*
* ## Javascript
* - Code inspired mainly from from __Async JavaScript__ https://wordpress.org/plugins/async-javascript
* - Adds _"defer"_ on all script using wp_enqueue_script()
*
* ---
*
* @apiExample {php} Example Usage
* // functions.php
* add_theme_support( 'rojak-assets-async' );
*
*/

// Add the actions only if NOT Admin
if( false === is_admin() ) {
	add_action('wp_head', 'rojak_async_loadcss_js', 7);
	add_filter('style_loader_tag', 'rojak_async_loadcss', 9999, 3);
}

add_filter( 'clean_url', 'rojak_defer_js', 11 );


if ( ! function_exists( 'rojak_async_loadcss_js' ) ) {
	function rojak_async_loadcss_js() {
		// Get loadCSS-file
		$loadcss_file = ROJAK_PARENT . 'js/external-loadcss' . $GLOBALS['rojak_templates_minify'] . '.js';

		// Fetch content
		$content = file_get_contents($loadcss_file);

		// Print out in head
		echo '<script>' . $content . '</script>' . "\n";
	}
}

if ( ! function_exists( 'rojak_async_loadcss' ) ) {
	function rojak_async_loadcss( $html, $handle, $href ) {
		// Try to catch media-attribute in HTML-tag
		preg_match('/media=\'(.*)\'/', $html, $match);

		// Extract media-attribute, default all
		$media = (isset($match[1]) ? $match[1] : 'all');

		// Return new markup
		return "<script>loadCSS('$href',0,'$media');</script><!-- $handle -->\n";
	}
}

if ( ! function_exists( 'rojak_defer_js' ) ) {
	function rojak_defer_js( $url ) {
		$aj_enabled       = true;
		$aj_method        = 'defer';
		$aj_exclusions    = '';
		$array_exclusions = !empty($aj_exclusions) ? explode(',',$aj_exclusions) : array();

		if (false !== $aj_enabled && false === is_admin()) {
			if (false === strpos($url,'.js')) {
				return $url;
			}
			if (is_array($array_exclusions) && !empty($array_exclusions)) {
				foreach ($array_exclusions as $exclusion) {
					if ( $exclusion != '' ) {
						if (false !== strpos(strtolower($url),strtolower($exclusion))) {
							return $url;
						}
					}
				}
			}
			return $url . "' " . $aj_method . "='" . $aj_method;
		}
		return $url;
	}
}