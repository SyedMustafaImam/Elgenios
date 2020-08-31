<?php
/**
 * Envato Elements: Settings API
 *
 * Settings API
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Settings API
 *
 * @since 2.0.0
 */
class Settings extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function reset_users_settings( $request ) {

		Options::get_instance()->reset_user();

		// Return some success to react:
		return $this->format_success( [
			'reset' => true,
		] );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function save_preferred_start_page( $request ) {

		$start_page = $request->get_param( 'startPage' );

		$start_page_allow_list = [
			'welcome',
			'premium-kits',
			'free-kits',
			'installed-kits',
			'photos',
		];

		if ( in_array( $start_page, $start_page_allow_list ) ) {
			Options::get_instance()->set( 'start_page', $start_page );

			// Return some success to react:
			return $this->format_success( [
				'saved' => true,
			] );
		}

		return $this->format_error(
			'savePreferredStartPage',
			'generic_api_error',
			'Failed to save start page preference'
		);
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'resetUserSettings', [ $this, 'reset_users_settings' ] );
		$this->register_endpoint( 'savePreferredStartPage', [ $this, 'save_preferred_start_page' ] );
	}
}
