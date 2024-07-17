<?php
	/*
	Plugin Name: 1 Trick Pony - Webpack for WordPress
	Plugin URI: 1 Trick Pony
	description: Handles HMR in WordPress.
	Author: 1 Trick Pony
	Version: 1.0.0
	Author URI: https://1trickpony.com
	*/

	function getJsModuleTags(){
		$webpack_hmr = getenv('WEBPACK_HMR');
		$app_env = getenv('APP_ENV');

		if($webpack_hmr && $app_env ==='local'){
			$manifestJSON = file_get_contents($webpack_hmr.'/manifest.json');

			if($manifestJSON){
				$modern = json_decode( $manifestJSON );
				echo "\n".'<script async src="'.$modern->{'style.js'}.'"></script>'."\n";
				echo "\n".'<script async src="'.$modern->{'whatsock.js'}.'"></script>'."\n";
				echo "\n".'<script async src="'.$modern->{'modules.js'}.'"></script>'."\n";
			}
		}
		else{
			$manifestJSON = file_get_contents(get_template_directory_uri().'/assets/manifest.json');
			$manifestJSONLegacy = file_get_contents(get_template_directory_uri().'/assets/manifest-legacy.json');

			if($manifestJSON && $manifestJSONLegacy){
				$modern = json_decode( $manifestJSON );
				$legacy = json_decode( $manifestJSONLegacy );

				// Core
				echo "\n".'<script async type="text/javascript" type="module" src="'.$modern->{'style.js'}.'"></script>'."\n";
				echo '<script async type="text/javascript" nomodule src="'.$legacy->{'style.js'}.'"></script>'."\n";

				// Modules
				echo "\n".'<script async type="text/javascript" type="module" src="'.$modern->{'whatsock.js'}.'"></script>'."\n";
				echo "\n".'<script async type="text/javascript" type="module" src="'.$modern->{'modules.js'}.'"></script>'."\n";
				echo '<script async type="text/javascript" nomodule src="'.$legacy->{'style.js'}.'"></script>'."\n";
			}
		}
	}

	 function getCssModuleTags(){
			$app_env = getenv('APP_ENV');

			if($app_env !=='local'){
				$manifestJSONLegacy = file_get_contents(get_template_directory_uri().'/assets/manifest-legacy.json');
				$version = (getenv('APP_VERSION')) ? '?v='.getenv('APP_VERSION') : '';

				if($manifestJSONLegacy){
					$legacy = json_decode( $manifestJSONLegacy );

					// This method doesn't work yet in Firefox: https://bugzilla.mozilla.org/show_bug.cgi?id=1405761
					// echo "\n".'<link rel="preload" href="'.$legacy->{'style.css'}.'" as="style" onload="this.onload=null;this.rel=\'stylesheet\'" />'."\n";
					echo "\n".'<link rel="stylesheet" href="'.$legacy->{'style.css'}.$version.'" />'."\n";
					echo '<noscript><link rel="stylesheet" href="'.$legacy->{'style.css'}.$version.'"></noscript>'."\n\n";
				}
			}
	 }

	 function criticalCSS(){
	 	$app_env = getenv('APP_ENV');

		if($app_env !=='local'){
			$criticalcss = file_get_contents(get_template_directory_uri().'/criticalcss/index_critical.min.css');

			if($criticalcss){
				echo "<style>$criticalcss</style>";
			}
		}
	 }

	add_action("wp_head", "criticalCSS");
	add_action("wp_head", "getCssModuleTags");
	add_action("wp_footer", "getJsModuleTags");

?>