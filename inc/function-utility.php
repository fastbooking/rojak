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
 * Check if array is empty
 *
 * @since  0.9.0
 * @access public
 * @param  array   $arr
 * @return bool
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
 * Check if string contains $keyword
 *
 * @since  0.9.0
 * @access public
 * @param  string   $str
 * @param  string   $keyword
 * @return bool
 */
function rojak_str_contains( $str, $keyword ) {
	if ( strpos( $str, $keyword ) !== false ) {
		return true;
	} else {
		return false;
	}
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
	$pages = get_posts(array(
		'post_type'  => 'page',
		'meta_key'   => '_wp_page_template',
		'meta_value' => $tpl_name,
		// 'meta_value' => rojak_tpl_path( $tpl_name, 'php' ),
		'suppress_filters' => 0
	));

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
	} else {
		return $page->ID;
	}
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


