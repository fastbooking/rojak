<?php

add_filter('style_loader_src', 'rojak_asset_timestamp', 10, 2);
add_filter('script_loader_src', 'rojak_asset_timestamp', 10, 2);

function rojak_asset_timestamp( $src, $handle ) {
	$parse_url  = parse_url($src);
	$server     = get_template_directory();

	// [mon] this part is consistent with our themes
	//       the textdomain has always been same with
	//       the folder name of theme
	$theme        = wp_get_theme();
	$theme_name   = $theme->get( 'TextDomain' );
	$server       = str_replace( "/wp-content/themes/$theme_name", '', $server );
	$file         = $server . $parse_url['path'];
	$file_modtime = '';
	if( file_exists( $file ) ) {
		$file_modtime = "&amp;mod=" . filemtime($file);
	}
	return $src . $file_modtime;
}