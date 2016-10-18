<?php
/**
* @api{} Get Default Options
* @apiName FgGetOptions
* @apiGroup FileGallery
* @apiVersion 1.0.0
* @apiDescription Set the default options
*
* @apiExample {php} Example Usage
* 	$fg_options = rojak_fg_get_options();
*
* @apiSuccessExample {php} Return value:
* 	array(
* 		'media_tag' => 'slideshow',
* 		'post_type' => 'page',
* 		'post_id'   => $post->ID,
* 	);
*/
function rojak_fg_get_options() {
	global $post;
	return array(
		'media_tag' => 'slideshow',
		'post_type' => 'page',
		'post_id'   => $post->ID,
	);
}

/**
* @api{} Get ML Slideshow
* @apiName FgMultiLangGetSlideshow
* @apiGroup FileGallery
* @apiVersion 1.0.0
* @apiDescription Get attachments that have 'slideshow' as media tag
*
* If there is no attachments with 'slideshow' media tag
* It will look to its primary language. See below conditions in order:
*
* 1. Current post of current language
* 2. Alternate post of primary language
* 3. Homapage of primary language
*
* @apiParam {Array} [options] List of options to get attachments.
* 	Refer to [Get Default Options](#api-FileGallery-FgGetOptions)
*
* @apiExample {php} Example Usage
* 	$fg_attachments = rojak_fg_multilang_get_slideshow();
* 	if ( $fg_attachments ) {
* 		foreach ( $fg_attachments as $attachment ) {
* 			...
* 		}
* 	}
*/
function rojak_fg_multilang_get_slideshow( $options = array() ) {
	$default_options = rojak_fg_get_options();
	$options = wp_parse_args( $options, $default_options );

	$attachments = rojak_fg_multilang_get_post_attachments( $options );
	if ( false == $attachments ) {
		$attachments = rojak_fg_multilang_get_home_attachments( $options );
	}

	if ( rojak_empty_array( $attachments ) ) {
		return false;
	}

	return $attachments;
}

/**
* @api{} Get ML Post Attachments
* @apiName FgMultiLangGetPostAttachments
* @apiGroup FileGallery
* @apiVersion 1.0.0
* @apiDescription Get attachments of a post
*
* Get attachments of a post in either current post
* or from the primary language of post
* See below conditions in order:
*
* 1. Current post of current language
* 2. Alternate post of primary language
*
* @apiParam {Array} [options] List of options to get attachments.
* 	Refer to [Get Default Options](#api-FileGallery-FgGetOptions)
*
* @apiExample {php} Example Usage
* 	$fg_attachments = rojak_fg_multilang_get_post_attachments();
* 	if ( $fg_attachments ) {
* 		foreach ( $fg_attachments as $attachment ) {
* 			...
* 		}
* 	}
*/
function rojak_fg_multilang_get_post_attachments( $options = array() ) {
	$default_options = rojak_fg_get_options();
	$options = wp_parse_args( $options, $default_options );

	// get from page of current language
	if ( rojak_fg_has_attachments( $options ) ) {
		$attachments = rojak_fg_get_attachments( $options );
	} else {
		$options['post_id'] = rojak_get_primary_lang_post_id(
			$options['post_id'],
			$options['post_type']
		);
		// get from page of primary language
		if ( rojak_fg_has_attachments( $options ) ) {
			$attachments = rojak_fg_get_attachments( $options );
		}
	}

	if ( rojak_empty_array( $attachments ) ) {
		return false;
	}

	return $attachments;
}

/**
* @api{} Get ML Home Attachments
* @apiName FgMultiLangGetHomeAttachments
* @apiGroup FileGallery
* @apiVersion 1.0.0
* @apiDescription Get attachments of homepage or from the primary language homepage
*
* @apiParam {Array} [options] List of options to get attachments.
* 	Refer to [Get Default Options](#api-FileGallery-FgGetOptions)
*
* @apiExample {php} Example Usage
* 	$fg_attachments = rojak_fg_multilang_get_home_attachments();
*/
function rojak_fg_multilang_get_home_attachments( $options = array() ) {
	$default_options = rojak_fg_get_options();
	$options = wp_parse_args( $options, $default_options );

	// get from homepage of primary language
	$options['post_id'] = rojak_get_primary_lang_post_id(
		get_option('page_on_front'),
		$options['post_type']
	);
	if ( rojak_fg_has_attachments( $options ) ) {
		$attachments = rojak_fg_get_attachments( $options );
	}

	if ( rojak_empty_array( $attachments ) ) {
		return false;
	}

	return $attachments;
}

/**
* @api{} Get Post Attachments
* @apiName FgGetPostAttachments
* @apiGroup FileGallery
* @apiVersion 1.0.0
* @apiDescription Get post attachments
*
* @apiParam {Array} [options] List of options to get attachments.
* 	Refer to [Get Default Options](#api-FileGallery-FgGetOptions)
*
* @apiExample {php} Example Usage
* 	$fg_attachments = rojak_fg_get_attachments();
*/
function rojak_fg_get_attachments( $options = array() ) {
	$default_options = rojak_fg_get_options();
	$options = wp_parse_args( $options, $default_options );

	$args = array(
		'posts_per_page' => -1,
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'post_type'      => 'attachment',
		'post_parent'    => $options['post_id'],
		'post_mime_type' => 'image',
		'post_status'    => null
	);
	$attachments = get_posts( $args );

	return $attachments;
}

/**
* @api{} Has Attachments
* @apiName FgHasAttachments
* @apiGroup FileGallery
* @apiVersion 1.0.0
* @apiDescription Whether post has attachments with the specified media
*
* @apiParam {Array} [options] List of options to get attachments.
* 	Refer to [Get Default Options](#api-FileGallery-FgGetOptions)
*
* @apiExample {php} Example Usage
* 	if ( rojak_fg_has_attachments( $options ) ) {
* 		...
* 	}
*/
function rojak_fg_has_attachments( $options = array() ) {
	$default_options = rojak_fg_get_options();
	$options = wp_parse_args( $options, $default_options );

	$args = array(
		'posts_per_page' => -1,
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'post_type'      => 'attachment',
		'post_parent'    => $options['post_id'],
		'post_mime_type' => 'image',
		'post_status'    => null
	);
	$attachments = get_posts( $args );

	if ( ! empty( $options['media_tag'] ) && ! rojak_empty_array( $attachments ) ) {
		foreach ( $attachments as $attachment ) {
			$attachment_id = $attachment->ID;
			$term_list     = wp_get_post_terms( $attachment_id, 'media_tag', array( 'fields' => 'all' ) );
			$terms         = array();
			foreach ( $term_list as $term ) {
				array_push( $terms, $term->slug );
			}
			if ( in_array( $options['media_tag'], $terms ) ) {
				return true;
				break;
			}
		}
	} else if ( ! rojak_empty_array( $attachments ) ) {
		return true;
	}

	return false;
}