<?php
/**
 * Functions for handling WPML related features
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
* @api{} Get PrimaryLang PostID
* @apiName GetPrimaryLangPostID
* @apiGroup Post
* @apiVersion 1.0.0
* @apiDescription Returns the primary language post ID
*
* ---
*
* ## Return Values
*
* - Returns the primary language $post_id if found.
* - Returns current $post_id if not found.
*
* ---
*
* @apiParam {Integer} post_id Post ID
* @apiParam {String} [post_type] If custom post type then need to set the correct post type naem. Default is `'page'`.
*
* @apiExample {php} Example Usage
* $primary_post_id = rojak_get_primary_lang_post_id( $offer->post->ID, 'offer' );
*/
function rojak_get_primary_lang_post_id( $post_id, $post_type = 'page' ) {

	global $sitepress;
	$default_language = $sitepress->get_default_language();

	if ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE != $default_language ) {
		return apply_filters( 'wpml_object_id', $post_id, $post_type, true, $default_language );
	} else {
		return $post_id;
	}

}
