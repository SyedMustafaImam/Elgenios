<?php
/**
 * Template Kit Import: Elementor
 *
 * Elementor template display/import.
 *
 * @package Envato/Envato_Template_Kit_Import
 * @since 0.0.2
 */

namespace Envato_Template_Kit_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Elementor builder code.
 *
 * @since 0.0.2
 */
class Builder_Elementor extends Builder {

	/*
	 * Which builder this is for (e.g. 'elementor' or 'gutenberg')
	 *
	 * @var string
	 */
	public $builder = 'elementor';

	public function __construct() {
		parent::__construct();
		add_action( 'before_delete_post', array( $this, 'check_if_imported_template_is_getting_deleted' ) );
	}

	/**
	 * Imports a template to the Elementor page builder.
	 *
	 * @param $template_index
	 *
	 * @return \WP_Error|int
	 */
	public function import_template( $template_index ) {

		if ( ! $this->kit_id ) {
			return new \WP_Error( 'kit_error', 'Failed to load kit' );
		}

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return new \WP_Error( 'plugin_error', 'Missing required plugin: Elementor' );
		}

		// We pipe off here to handle a special case for Global Styles.
		$global_result = $this->ensure_elementor_global_styles_are_imported_and_active( $template_index );
		if ( true !== $global_result ) {
			return $global_result;
		}

		$template_data = $this->get_template_data( $template_index );

		return $this->import_json_file_to_elementor_library( $template_data['source'], $template_index );

	}

	/**
	 * Get details from the manifest file about a particular template within this kit.
	 *
	 * @param $template_index
	 *
	 * @return bool|array
	 */
	public function get_template_data( $template_index ) {
		$template_data = parent::get_template_data( $template_index );
		if ( $template_data && ! empty( $template_data['source'] ) ) {
			$template_kit_folder_name       = $this->get_template_kit_temporary_folder();
			$template_json_file             = $template_kit_folder_name . $template_data['source'];
			$template_data['template_json'] = json_decode( file_get_contents( $template_json_file ), true );
			return $template_data;
		}

		return false;
	}

	/**
	 * Reach out to Elementor to import a local JSON file into the library.
	 * Returns the ID of the locally imported post.
	 *
	 * @param $json_file_path
	 * @param $template_index
	 *
	 * @return int|\WP_Error
	 */
	private function import_json_file_to_elementor_library( $json_file_path, $template_index ) {
		// Found the template to import from the manifest file.
		$template_kit_folder_name = $this->get_template_kit_temporary_folder();
		$template_json_file       = $template_kit_folder_name . $json_file_path;
		$local_json_data          = json_decode( file_get_contents( $template_json_file ), true );
		$source                   = \Elementor\Plugin::$instance->templates_manager->get_source( 'local' );
		$result                   = $source->import_template( basename( $template_json_file ), $template_json_file );

		if ( is_wp_error( $result ) ) {
			return new \WP_Error( 'import_error', 'Failed to import template: ' . esc_html( $result->get_error_message() ) );
		}

		if ( $result[0] && $result[0]['template_id'] ) {
			$imported_template_id = $result[0]['template_id'];
			$this->record_import_event( $template_index, $imported_template_id );
			// Check if we've got any display conditions to import:
			if ( $local_json_data['metadata'] && ! empty( $local_json_data['metadata']['elementor_pro_conditions'] ) ) {
				update_post_meta( $imported_template_id, '_elementor_conditions', $local_json_data['metadata']['elementor_pro_conditions'] );
			}
			if ( $local_json_data['metadata'] && ! empty( $local_json_data['metadata']['wp_page_template'] ) ) {
				// If there is a page template set we keep it the same here
				update_post_meta( $imported_template_id, '_wp_page_template', $local_json_data['metadata']['wp_page_template'] );
			}
			// Record some metadata so we can link back to kit from imported template:
			update_post_meta( $imported_template_id, 'envato_tk_source_kit', $this->kit_id );
			update_post_meta( $imported_template_id, 'envato_tk_source_index', $template_index );
			return $imported_template_id;
		}

		return new \WP_Error( 'import_error', 'Unknown import error' );
	}

	/**
	 * What text to display on input buttons
	 *
	 * @return string
	 */
	public function get_import_button_text() {
		return esc_html__( 'Import into Elementor Library', 'template-kit-import' );
	}


	/**
	 * Ensure our global styles (if they exist) are installed and active on the site.
	 * But only installed + active once!
	 *
	 * @param $template_index_user_requested_to_import
	 *
	 * @return bool|\WP_Error
	 */
	public function ensure_elementor_global_styles_are_imported_and_active( $template_index_user_requested_to_import ) {
		$templates = $this->get_available_templates();
		foreach ( $templates as $template_index => $template ) {
			// Global styles are tagged with a metadata of 'global-styles` in the JSON
			if ( ! empty( $template['metadata'] ) && ! empty( $template['metadata']['template_type'] ) && 'global-styles' === $template['metadata']['template_type'] ) {
				// We have found some global styles!
				// First we check if we've previously imported these global styles, looking at the 'envato_tk_source_index' and 'envato_tk_source_kit' post meta.
				$existing_global_styles_query = array(
					'meta_query'     => array(
						array(
							'key'   => 'envato_tk_source_kit',
							'value' => $this->kit_id,
						),
						array(
							'key'   => 'envato_tk_source_index',
							'value' => $template_index,
						),
					),
					'post_type'      => 'elementor_library',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				);
				$existing_global_styles       = get_posts( $existing_global_styles_query );
				if ( ! $existing_global_styles ) {
					// We haven't imported these global styles before! Lets do it...

					$imported_global_styles_id = $this->import_json_file_to_elementor_library( $template['source'], $template_index );

					if ( is_wp_error( $imported_global_styles_id ) ) {
						// Failed to import global styles:
						return $imported_global_styles_id;
					} else {
						// We set some metadata around the global styles so Elementor can interpret them correctly:
						// From: wp-content/plugins/elementor/core/documents-manager.php:366
						update_post_meta( $imported_global_styles_id, '_elementor_edit_mode', 'builder' );
						update_post_meta( $imported_global_styles_id, '_elementor_template_type', 'kit' );

						wp_update_post(
							array(
								'ID'         => $imported_global_styles_id,
								'post_title' => 'Global Styles For Kit: ' . $this->get_name(),
							)
						);
					}
				} else {
					// We have imported this kit before, lets make sure it's still the default.
					// From: wp-content/plugins/elementor/core/kits/manager.php:17
					$imported_global_styles    = current( $existing_global_styles );
					$imported_global_styles_id = $imported_global_styles->ID;
				}

				if ( $imported_global_styles_id ) {
					// And now actually set this kit as active:
					// From: wp-content/plugins/elementor/core/kits/manager.php:17
					update_option( 'elementor_active_kit', $imported_global_styles_id );

					// Special case here if the user has clicked on "Import Global Styles" on its own
					// we return early because we don't want the parent caller to run the import a second time around.
					if ( $template_index_user_requested_to_import === $template_index ) {
						return $imported_global_styles_id;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get the URL to the list of imported templates.
	 *
	 * @param $imported_template_id int - Optional imported template ID.
	 *
	 * @return string
	 */
	public function get_imported_template_edit_url( $imported_template_id = 0 ) {
		if ( $imported_template_id && class_exists( '\Elementor\Plugin' ) ) {
			$imported_template = \Elementor\Plugin::$instance->documents->get( $imported_template_id );
			if ( $imported_template ) {
				return $imported_template->get_edit_url();
			}
		}

		return admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' );
	}

	/**
	 * Hook that runs when a user deletes a template from the Elementor library.
	 * This checks if the template getting deleted was imported from  us.
	 * If it was from us, we clean up our manifest metadata and flag this template as not "imported" any more
	 * so that the UI can show the correct buttons/status.
	 *
	 * @param $post_to_be_deleted
	 */
	public function check_if_imported_template_is_getting_deleted( $post_to_be_deleted ) {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$post = get_post( $post_to_be_deleted );
			if ( $post && 'elementor_library' === $post->post_type ) {
				$source_kit_id = get_post_meta( $post->ID, 'envato_tk_source_kit', true );
				if ( $source_kit_id ) {
					$source_kit_index = get_post_meta( $post->ID, 'envato_tk_source_index', true );
					try {
						$this->load_kit( $source_kit_id );
						$manifest = $this->get_manifest_data();
						if ( $manifest && ! empty( $manifest['templates'] ) ) {
							if ( isset( $manifest['templates'][ $source_kit_index ] ) && isset( $manifest['templates'][ $source_kit_index ]['imports'] ) ) {
								foreach ( $manifest['templates'][ $source_kit_index ]['imports'] as $import_id => $imported_data ) {
									if ( (int) $post_to_be_deleted === (int) $imported_data['imported_template_id'] ) {
										// we've found the record in the 'imports' manifest array that matches the imported template.
										// lets remove it!
										unset( $manifest['templates'][ $source_kit_index ]['imports'][ $import_id ] );
									}
								}
							}
						}
						update_post_meta( $this->kit_id, 'envato_tk_manifest', $manifest );
					} catch ( \Exception $e ) {
						// noop
					}
				}
			}
		}
	}

	/**
	 * Get any unmet requirements for importing this template to WordPress
	 *
	 * @param $template array
	 *
	 * @return array
	 */
	public function get_list_of_unmet_requirements( $template ) {
		$unmet_requirements = array();
		if ( ! empty( $template['elementor_pro_required'] ) && ! class_exists( '\ElementorPro\Plugin' ) ) {
			$unmet_requirements[] = __( 'Elementor Pro is required for this Template.', 'template-kit-export' );
		}
		return $unmet_requirements;
	}


	/**
	 * Gets any required theme for this Template Kit
	 *
	 * @return array
	 */
	public function get_required_theme() {
		$required_theme     = array(
			'slug' => 'hello-elementor',
			'name' => 'Hello Elementor',
		);
		$themes             = wp_get_themes();
		$current_theme      = wp_get_theme();
		$is_hello_installed = false;
		$is_hello_active    = false;
		foreach ( $themes as $theme ) {
			if ( $required_theme['slug'] === $theme->get_template() ) {
				$is_hello_installed = true;
			}
		}
		// get_template() checks for parent theme, so we handle a hello child theme using this just fine:
		if ( $required_theme['slug'] === $current_theme->get_template() ) {
			$is_hello_active = true;
		}
		$theme_action_url = admin_url( 'themes.php' );
		$theme_status     = $is_hello_active ? 'activated' : ( $is_hello_installed ? 'deactivated' : 'install' );
		if ( 'install' === $theme_status ) {
			// We want the user to install this theme
			$theme_action_url = add_query_arg( '_wpnonce', wp_create_nonce( 'install-theme_' . $required_theme['slug'] ), admin_url( 'update.php?action=install-theme&theme=' . $required_theme['slug'] ) );
		} elseif ( 'deactivated' === $theme_status ) {
			// We want the user to activate this theme
			$theme_action_url = add_query_arg( '_wpnonce', wp_create_nonce( 'switch-theme_' . $required_theme['slug'] ), admin_url( 'themes.php?action=activate&stylesheet=' . $required_theme['slug'] ) );
		}
		$required_theme['status'] = $theme_status;
		$required_theme['url']    = $theme_action_url;
		return $required_theme;
	}
}
