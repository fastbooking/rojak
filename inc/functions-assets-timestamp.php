<?php

add_filter('style_loader_src', 'rojak_asset_timestamp', 10, 2);
add_filter('script_loader_src', 'rojak_asset_timestamp', 10, 2);

function rojak_asset_timestamp( $src, $handle ) {
	$parse_url = parse_url($src);
	$server = get_template_directory();
	$server = str_replace('/wp-content/themes/santika', '', $server);
	$file = $server . $parse_url['path'];
	$file_modify_time = '';
	if( file_exists( $file ) ) {
		$file_modify_time = "&amp;mod=" . filemtime($file);
	}
	return $src . $file_modify_time;
}

