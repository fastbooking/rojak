<?php

define('MINIFY', false);

add_action( 'wp_enqueue_scripts', 'rojak_tpl_core_assets' );
add_action( 'wp_enqueue_scripts', 'rojak_tpl_enqueue_assets' );


function rojak_tpl_core_assets() {
	rojak_tpl_core_css();
	rojak_tpl_core_js();
	rojak_tpl_custom_css_js();
}

/**
 * Base scripts
 */
function rojak_tpl_core_js() {
	$minifiy = MINIFY ? '.min':'';

	wp_dequeue_script(    'jquery' );
	wp_deregister_script( 'jquery' );
	wp_register_script(   'jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery' .$minifiy. '.js', array(), '1.11.1', true );
	wp_enqueue_script(    'jquery' );

	wp_register_script( 'okuranikko', ROJAK_PARENT_URI . 'js/core' .$minifiy. '.js', array(), '', true );
	wp_enqueue_script(  'okuranikko' );
}

/**
 * Base stylesheet
 */
function rojak_tpl_core_css() {
	$minifiy = MINIFY ? '.min':'';

	$style_name = 'core' . $minifiy . '.css';
	wp_enqueue_style( 'okuranikko', ROJAK_PARENT_URI . $style_name );
}


/**
 * Custom stylesheet for child themes
 */
function rojak_tpl_custom_css_js() {

	// TODO: Add custom-en.css, custom css that depends on language

	$custom_css      = 'custom.css';
	$custom_css_path = ROJAK_CHILD . $custom_css;
	if ( file_exists( $custom_css_path ) && is_child_theme() ) {
		wp_enqueue_style( 'custom', ROJAK_CHILD_URI . $custom_css );
	}

	$custom_js      = 'custom.js';
	$custom_js_path = ROJAK_CHILD . $custom_js;
	if ( file_exists( $custom_js_path ) && is_child_theme() ) {
		wp_enqueue_script( 'custom', ROJAK_CHILD_URI . $custom_js , array(), '', true  );
	}

}


function rojak_tpl_enqueue_assets() {

	global $post;
	$minifiy = MINIFY ? '.min':'';

	if ( is_page() ) {

		if ( !get_page_template_slug( $post->ID ) ) {
			// do nothing :(
		} else {
			$current_tpl = get_post_meta( $post->ID, '_wp_page_template', true );
			$current_tpl_parts = pathinfo( $current_tpl );
			$current_tpl_name = $current_tpl_parts['dirname'];
			$current_tpl_path = ROJAK_PARENT . $current_tpl;

			if ( is_file( $current_tpl_path ) ) {

				$parts = pathinfo( $current_tpl_path );
				$curret_base_path = $parts['dirname'] . '/' . $parts['filename'];

				rojak_tpl_enqueue_style(  $current_tpl_name, $curret_base_path );
				rojak_tpl_enqueue_script( $current_tpl_name, $curret_base_path );
				rojak_tpl_require_tpl_fn( $current_tpl_name );

			}
		}
	}
}


function rojak_tpl_enqueue_style( $name, $path ) {
	$minifiy = MINIFY ? '.min':'';
	if ( is_file( $path . $minifiy . '.css' ) ) {
		wp_enqueue_style( $name, ROJAK_PARENT_URI . rojak_tpl_get_path( $name, $minifiy . '.css' ) );
	}
}

function rojak_tpl_enqueue_script( $name, $path ) {
	$minifiy = MINIFY ? '.min':'';
	if ( is_file( $path . $minifiy . '.js' ) ) {
		wp_enqueue_script( $name, ROJAK_PARENT_URI . rojak_tpl_get_path( $name, $minifiy . '.js' ), array(), '', true  );
	}
}

function rojak_tpl_require_tpl_fn( $name ) {

	$tpl_fn_file = ROJAK_PARENT . rojak_tpl_get_path( $name, '-fn.php' );
	if ( file_exists( $tpl_fn_file ) ) {
		require_once( $tpl_fn_file );
	}

}

function rojak_tpl_get_path( $name, $ext ) {

	if ( substr( $ext, 0, 1 ) === '-' ||
			 substr( $ext, 0, 1 ) === '.' )
		$path = trailingslashit( $name ) . $name . $ext;
	else
		$path = trailingslashit( $name ) . $name . '.' . $ext;

	return $path;

}

