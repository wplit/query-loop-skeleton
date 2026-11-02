<?php
/**
 * Plugin Name: Bricks JSON Loop Skeleton
 * Description: A minimal custom Bricks element that runs a query loop but prints no item per post. Its display is built in the browser from JSON, as a calendar, map or chart would do. For testing query filters.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		if ( class_exists( '\Bricks\Elements' ) ) {
			\Bricks\Elements::register_element( __DIR__ . '/element-json-loop.php' );
		}
	},
	11
);
