<?php
/**
 * Functions for handling Pods related custom post types or custom fields
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Return the ids from a pods custom field which is set as Relationship
 * with other custom post type
 *
 * @since  0.9.0
 * @access public
 * @param  int      $post_id
 * @param  string   $cpt
 * @param  string   $cpt_field
 * @return bool|array
 */
function rojak_get_post_ids_from_pods_field( $post_id, $cpt, $cpt_field ) {
	if ( ! empty( $post_id ) ) {
		$cpt_pod   = pods( $cpt, $post_id );
		$cpt_field = $cpt_pod->field( $cpt_field );
		$posts_by_ids = array();
		if ( !empty( $cpt_field ) && !rojak_empty_array( $cpt_field ) ) {
			foreach( $cpt_field as $apost ) {
				array_push($posts_by_ids, $apost['ID']);
			}
			return $posts_by_ids;
		}
	}
	return false;
}
