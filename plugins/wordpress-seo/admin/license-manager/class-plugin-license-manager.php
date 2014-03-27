<?php
if ( ! class_exists( "Yoast_Plugin_License_Manager" ) ) {

	class Yoast_Plugin_License_Manager extends Yoast_License_Manager {

		/**
		 * Setup auto updater for plugins
		 */
		public function setup_auto_updater() {
			if ( $this->license_is_valid() ) {
				// setup auto updater
				require_once( dirname( __FILE__ ) . '/class-update-manager.php' );
				require_once( dirname( __FILE__ ) . '/class-plugin-update-manager.php' );
				new Yoast_Plugin_Update_Manager( $this->product, $this->get_license_key() );
			}
		}

		/**
		 * Setup hooks
		 */
		public function specific_hooks() {

			// deactivate the license remotely on plugin deactivation
			register_deactivation_hook( $this->product->get_slug(), array( $this, 'deactivate_license' ) );
		}
	}
}

