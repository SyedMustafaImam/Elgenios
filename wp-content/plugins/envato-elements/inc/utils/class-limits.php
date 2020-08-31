<?php
/**
 * Envato Elements: Limits
 *
 * Limits
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Limits
 *
 * @since 2.0.0
 */
class Limits extends Base {

	/**
	 * We raise our memory and timeout limits during import because
	 * some operations take a lot of processing (i.e. large images in Template Kits).
	 */
	public function raise_limits() {

		// WordPress has a built in way to raise the memory limit thankfully:
		wp_raise_memory_limit( 'admin' );

		if ( wp_is_ini_value_changeable( 'max_execution_time' ) ) {
			ini_set( 'max_execution_time', 0 );
		}

		@ set_time_limit( 0 );
	}

}
