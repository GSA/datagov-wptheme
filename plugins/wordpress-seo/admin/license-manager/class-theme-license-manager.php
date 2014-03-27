<?php

if ( ! class_exists( "Yoast_Theme_License_Manager" ) ) {

	class Yoast_Theme_License_Manager extends Yoast_License_Manager {

		/**
		 * Constructor
		 *
		 * @param string $api_url The URL running the EDD API
		 * @param string $item_name The item name in the EDD shop
		 * @param string $item_url The absolute url on which users can purchase a license
		 * @param string $slug The theme slug
		 * @param string $version The theme version
		 * @param string $text_domain The text domain used for translating strings (optional)
		 * @param string $author The theme author (optional)
		 */
		public function __construct( Yoast_Product $product ) {

			// store the license page url
			$license_page = 'themes.php?page=theme-license';

			parent::__construct( $product );
		}

		/**
		 * Setup auto updater for themes
		 */
		public function setup_auto_updater() {
			if ( $this->license_is_valid() ) {
				// setup auto updater
				require_once dirname( __FILE__ ) . '/class-update-manager.php';
				require_once dirname( __FILE__ ) . '/class-theme-update-manager.php'; // @TODO: Autoload?
				new Yoast_Theme_Update_Manager( $this->product, $this->get_license_key() );
			}
		}

		/**
		 * Setup hooks
		 */
		public function specific_hooks() {
			// remotely deactivate license upon switching away from this theme
			add_action( 'switch_theme', array( $this, 'deactivate_license' ) );

			// Add the license menu
			add_action( 'admin_menu', array( $this, 'add_license_menu' ) );
		}

		/**
		 * Add license page and add it to Themes menu
		 */
		public function add_license_menu() {
			$theme_page = add_theme_page( sprintf( __( '%s License', $this->product->get_text_domain() ), $this->product->get_item_name() ), __( 'Theme License', $this->product->get_text_domain() ), 'manage_options', 'theme-license', array(
				$this,
				'show_license_page'
			) );
		}

		/**
		 * Shows license page
		 */
		public function show_license_page() {
			?>
			<div class="wrap">
				<?php settings_errors(); ?>

				<?php $this->show_license_form( false ); ?>
			</div>
		<?php
		}


	}

}