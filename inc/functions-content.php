<?php
/**
 * Functions for handling content related information
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Limit the number of words returned in a given string
 *
 * @since  0.9.0
 * @access public
 * @param  array   $args
 * @return void
 */
function rojak_limit_words( $string, $word_limit = 40, $suffix = ' &hellip;' ){
	$words = explode( ' ', wp_strip_all_tags( do_shortcode( $string ) ), ( $word_limit + 1 ) );
	if( count( $words ) > $word_limit ) {
		array_pop( $words );

		$s = implode( ' ', $words );
		return ( strlen( $string ) == strlen( $s ) ) ? $string : $s.$suffix;
	}
}

/**
 * Returns the excerpt with given number of words.
 *
 * @since  0.9.0
 * @access public
 * @param  int   $post_id
 * @param  int   $length
 * @return string|html
 */
function rojak_get_excerpt( $post_id, $length = 22 ) {
	$entry_post    = get_post( $post_id );
	$entry_excerpt = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $post_id ) );
	if ( empty( $entry_excerpt ) ) {
		$entry_excerpt = apply_filters( 'the_content', $entry_post->post_content );
	}
	$entry_excerpt_cut = rojak_limit_words( $entry_excerpt, $length );
	if ( !empty( $entry_excerpt_cut ) ) {
		$entry_excerpt = apply_filters( 'the_content', $entry_excerpt_cut );
	}
	return $entry_excerpt;
}
