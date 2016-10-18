<?php
/**
 * Additional helper functions that the framework or themes may use.  The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
* @api{} Empty Array
* @apiName EmptyArray
* @apiGroup Array
* @apiVersion 1.0.0
* @apiDescription Checks if array is empty
*
* ---
*
* ## Return Values
* Loops inside the array and uses empty() to check value.
*
* Returns FALSE if var exists and has a non-empty, non-zero value. Otherwise returns TRUE.
*
* The following things are considered to be empty:
* - array() (an empty array)
* - array(0) (an empty array with empty value)
*
* ---
*
* @apiParam {Array} arr Array to be checked
*
* @apiExample {php} Example Usage
* 	if ( rojak_empty_array( $array ) ) {
* 		...
* 	}
*/
function rojak_empty_array( $arr ) {
	if ( is_array( $arr ) ) {
		foreach ( $arr as $elm ) {
			if( ! empty( $elm ) ) {
				return false;
			}
		}
	}
	return true;
}

/**
* @api{} Empty Object
* @apiName EmptyObject
* @apiGroup Array
* @apiVersion 1.0.0
* @apiDescription Check if object is empty
*
* ---
*
* ## Return Values
* Casts object to array then loops inside which uses empty() to check value.
*
* Returns FALSE if var exists and has a non-empty, non-zero value. Otherwise returns TRUE.
*
* The following things are considered to be empty:
* - new stdClass() (an empty object)
*
* ---
*
*
* @apiParam {Object} obj Object to be checked
*
* @apiExample {php} Example Usage
* 	if ( rojak_empty_object( $obj ) ) {
* 		...
* 	}
*/
function rojak_empty_object( $obj ) {
	$arr = (array) $obj;
	if ( ! rojak_empty_array( $arr ) ) {
		return false;
	}
	return true;
}

/**
 * Check if string contains $needle
 *
 * @since  0.9.0
 * @access public
 * @param  string   $haystack
 * @param  string   $needle
 * @return bool
 */
function rojak_str_contains( $haystack, $needle ) {
	if ( strpos( $haystack, $needle ) !== false ) {
		return true;
	}
	return false;
}

/**
 * Check if string starts with $needle
 *
 * @since  0.9.0
 * @access public
 * @param  string   $haystack
 * @param  string   $needle
 * @return bool
 */
function rojak_str_starts_with( $haystack, $needle ) {
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

/**
 * Check if string ends with $needle
 *
 * @since  0.9.0
 * @access public
 * @param  string   $haystack
 * @param  string   $needle
 * @return bool
 */
function rojak_str_ends_with( $haystack, $needle ) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}
	return (substr($haystack, -$length) === $needle);
}

/**
 * Get page id based on page template
 *
 * @since  0.9.0
 * @access public
 * @param  string   $tpl_name
 * @return bool
 */
function rojak_get_page_id_by_tpl( $tpl_name ) {
	$page_id = false;
	$args = array(
		'post_type'  => 'page',
		'meta_key'   => '_wp_page_template',
		'meta_value' => $tpl_name,
		'suppress_filters' => 0
	);

	if ( current_theme_supports( 'rojak-templates' ) ) {
		$args['meta_value'] = rojak_tpl_get_path( $tpl_name, 'php' );
	}

	$pages = get_posts( $args );

	if ( count( $pages ) == 1 ) {
		$first = true;
		foreach( $pages as $page ){
			if ( $first ) {
				$page_id = $page->ID;
				$first = false;
				break;
			}
		}
		if ( $page_id ) {
			return $page_id;
		}
	} else {
		return false;
	}
}

/**
 * Get the parent's page id based on give page id
 *
 * @since  0.9.0
 * @access public
 * @param  int   $page_id
 * @return bool
 */
function rojak_get_parent_page_id( $page_id ) {
	$page = get_post( $page_id );
	if ( $page->post_parent ) {
		$ancestors = get_post_ancestors( $page->ID );
		$root = count( $ancestors ) - 1;
		return $ancestors[$root];
	}

	return false;
}

/**
 * Get menu name based on menu's theme location
 *
 * @since  0.9.0
 * @access public
 * @param  string   $theme_location
 * @return string
 */
function rojak_get_menu_name( $theme_location ) {
	if ( ! $theme_location ) {
		return false;
	}

	$theme_locations = get_nav_menu_locations();
	if ( ! isset( $theme_locations[$theme_location] ) ) {
		return false;
	}

	$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
	if ( ! $menu_obj ) {
		$menu_obj = false;
	}
	if ( ! isset( $menu_obj->name ) ) {
		return false;
	}

	return $menu_obj->name;
}

/**
 * Print
 *
 * @since  0.9.0
 * @access public
 * @param  any   $print_this
 * @return void
 */
function rojak_print( $print_this )	{
	echo '<pre style="position:fixed; width:1000px; height:800px; background-color:#000; color:#fff; z-index:9999; top:20px; right:0; overflow:scroll; font-size:12px; ">' . print_r( $print_this, true ) . '</pre>';
}

/**
* @api{} Get Post Meta Object
* @apiName GetPostMetaObject
* @apiGroup Post
* @apiVersion 1.0.0
* @apiDescription Post meta as object instead of array
*
* Get value from pods post type, if result not in array and is single value,
* convert into array with key [value] so can return into object
*
* @apiParam {Integer} post_id Post ID
* @apiParam {String} field_name Custom field name
*
* @apiExample {php} Example Usage
*    rojak_get_post_meta_object( $post->ID, 'miles_page' );
*/
function rojak_get_post_meta_object( $post_id, $field_name ) {
	$data = get_post_meta( $post_id, $field_name );
	$data = $data[0];
	if(!is_array($data)) {
		$convert_data['value'] = $data;
		return (object) $convert_data;
	}
	else {
		return (object) $data;
	}
}

/**
* @api{} Print To Console
* @apiName PrintToConsole
* @apiGroup Print
* @apiVersion 1.0.0
* @apiDescription Prints data to window console
*
* Refer to http://php.net/manual/en/function.array-column.php
*
* @apiParam {Mixed} data The data to be shown on console
* @apiParam {Boolean} public If set to true, logs will not be shown for non-login users
*
* @apiExample {php} Example Usage
*      rojak_console( array( 'name' => 'John Doe' ) );
*/
function rojak_console( $data, $public = false ) {
	if ( $public == true || is_user_logged_in() ) {
		echo '<script>';
		echo 'console.log('. json_encode( $data ) .')';
		echo '</script>';
	}
}