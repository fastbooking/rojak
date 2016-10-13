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
* @api{} Array IsEmpty
* @apiName ArrayIsEmpty
* @apiGroup Array
* @apiVersion 1.0.0
* @apiDescription Checks if array fields are empty
*
* Refer to http://php.net/manual/en/function.array-column.php
*
* @apiParam {Array} array Array to be checked
*
* @apiExample {php} Example Usage
*    rojak_console( array( 'name' => 'John Doe' ) );
* @apiSuccessExample {php} Response:
*    false
*/
function rojak_empty_array( $arr ) {
	if ( is_array( $arr ) ) {
		foreach ( $arr as $elm ) {
			if( !empty( $elm ) ) {
				return false;
			}
		}
	}
	return true;
}

/**
 * Check if object is empty
 *
 * @since  0.9.0
 * @access public
 * @param  object $obj
 * @return bool
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
 * Get value from pods post type, if result not in array and is single value,
 * convert into array with key [value] so can return into object
 *
 * @since  0.9.0
 * @access public
 * @param  int     $post_id
 * @param  string  $field_name
 * @return object
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

/**
* @api{} Array Column
* @apiName ArrayColumn
* @apiGroup Array
* @apiVersion 1.0.0
* @apiDescription Alternate to array_column since it does not work with < PHP5.5
*
* Refer to http://php.net/manual/en/function.array-column.php
*
* @apiParam {Array} array A multi-dimensional array or an array of objects from which to pull a column of values from.
* @apiParam {Mixed} column_name The column of values to return.
*
* @apiExample {php} Example Usage
*      $first_names = array_column($records, 'first_name');
*/
function rojak_array_column( $array,$column_name ) {
	return array_map( function( $element ) use( $column_name ) { 
		return $element->{$column_name}; 
	}, $array);
}