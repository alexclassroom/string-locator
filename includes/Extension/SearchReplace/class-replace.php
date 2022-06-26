<?php

namespace JITS\StringLocator\Extension\SearchReplace;

class Replace {

	public function __construct() {
		add_action( 'string_locator_search_results_tablenav_controls', array( $this, 'add_replace_button' ) );
		add_action( 'string_locator_search_results_tablenav_controls', array( $this, 'output_replace_form' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );
	}

	public function maybe_enqueue_assets( $hook ) {
		// Break out early if we are not on a String Locator page
		if ( 'tools_page_string-locator' !== $hook && 'toplevel_page_string-locator' !== $hook ) {
			return;
		}

		$replace = STRING_LOCATOR_PLUGIN_DIR . 'build/string-locator-replace.asset.php';

		$replace = file_exists( $replace ) ? require $replace : array( 'version' => false );

		/**
		 * String Locator Styles and Scripts.
		 */
		wp_enqueue_style( 'string-locator-replace', trailingslashit( STRING_LOCATOR_PLUGIN_URL ) . 'build/string-locator-replace.css', array(), $replace['version'] );
		wp_enqueue_script( 'string-locator-replace', trailingslashit( STRING_LOCATOR_PLUGIN_URL ) . 'build/string-locator-replace.js', array(), $replace['version'], true );

		wp_localize_script(
			'string-locator-replace',
			'stringLocatorReplace',
			array(
				'rest_nonce'    => wp_create_nonce( 'wp_rest' ),
				'replace_nonce' => wp_create_nonce( 'string-locator-replace' ),
				'url'           => array(
					'replace' => get_rest_url( null, 'string-locator/v1/replace' ),
				),
				'string'        => array(
					'replace_started' => __( 'Running replacemenets...', 'string-locator' ),
					'button_show'     => __( 'Show replacement controls', 'string-locator' ),
					'button_hide'     => __( 'Hide replacement controls', 'string-locator' ),
					'confirm_all'     => __( 'Are you sure you want to replace all strings?', 'string-locator' ),
				),
			)
		);
	}

	public function add_replace_button() {
		printf(
			'<button type="button" class="button button-link" id="string-locator-toggle-replace-controls" aria-expanded="false" aria-controls="string-locator-replace-form">%s</button>',
			esc_html__( 'Show replacement controls', 'string-locator' )
		);
	}

	public function output_replace_form() {
		include_once __DIR__ . '/views/replace-form.php';
	}

}

new Replace();
