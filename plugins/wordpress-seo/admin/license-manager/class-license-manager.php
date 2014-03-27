<?php

if ( ! interface_exists( 'iYoast_License_Manager' ) ) {

	interface iYoast_License_Manager {

		public function specific_hooks();

		public function setup_auto_updater();

	}

}


if ( ! class_exists( 'Yoast_License_Manager' ) ) {

	/**
	 * Class Yoast_License_Manager
	 *
	 * @todo Maybe create a license class that contains key and option
	 * @todo Not sure if Yoast_License_Manager is a good name for this class, it's more managing the product (plugin or theme)
	 */
	abstract class Yoast_License_Manager implements iYoast_License_Manager {

		/**
		 * @const VERSION The version number of the License_Manager class
		 */
		const VERSION = 1;

		/**
		 * @var Yoast_License The license
		 */
		protected $product;

		/**
		 * @var string
		 */
		private $license_constant_name = '';

		/**
		 * @var boolean True if license is defined with a constant
		 */
		private $license_constant_is_defined = false;

		/**
		 * @var boolean True if remote license activation just failed
		 */
		private $remote_license_activation_failed = false;

		/**
		 * @var array Array of license related options
		 */
		private $options = array();

		/**
		 * @var string Used to prefix ID's, option names, etc..
		 */
		protected $prefix;

		/**
		 * Constructor
		 *
		 * @param Yoast_Product $product
		 */
		public function __construct( Yoast_Product $product ) {

			// Set the license
			$this->product = $product;

			// set prefix
			$this->prefix = sanitize_title_with_dashes( $this->product->get_item_name() . '_', null, 'save' );

			// maybe set license key from constant
			$this->maybe_set_license_key_from_constant();
		}

		/**
		 * Setup hooks
		 *
		 * @todo I'm not sure I want the setup_auto_updater() to be called from the setup_hooks method
		 */
		public function setup_hooks() {

			// show admin notice if license is not active
			add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );

			add_action( 'admin_init', array( $this, 'catch_post_request' ) );

			// perform a license check
			add_action( 'admin_init', array( $this, 'check_license' ), 20 );

			// setup item type (plugin|theme) specific hooks
			$this->specific_hooks();

			// setup the auto updater
			$this->setup_auto_updater();

		}

		/**
		 * Display license specific admin notices, namely:
		 *
		 * - License for the product isn't activated
		 * - External requests are blocked through WP_HTTP_BLOCK_EXTERNAL
		 */
		public function display_admin_notices() {

			// show notice if license is invalid
			if ( ! $this->license_is_valid() ) {

				if ( $this->get_license_key() === '' ) {
					$message = '<b>Warning!</b> You didn\'t set your %s license key yet, which means you\'re missing out on updates and support! <a href="%s">Enter your license key</a> or <a href="%s" target="_blank">get a license here</a>.';
				} else {
					$message = '<b>Warning!</b> Your %s license is inactive which means you\'re missing out on updates and support! <a href="%s">Activate your license</a> or <a href="%s" target="_blank">get a license here</a>.';
				}
				?>
				<div class="error">
					<p><?php printf( __( '<b>Warning!</b> Your %s license is inactive which means you\'re missing out on updates and support! <a href="%s">Enter your license key</a> or <a href="%s" target="_blank">get a license here</a>.', $this->product->get_text_domain() ), $this->product->get_item_name(), $this->product->get_license_page_url(), $this->product->get_tracking_url( 'activate-license-notice' ) ); ?></p>
				</div>
			<?php
			}

			// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
			if ( defined( "WP_HTTP_BLOCK_EXTERNAL" ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

				// check if our API endpoint is in the allowed hosts
				$host = parse_url( $this->product->get_api_url(), PHP_URL_HOST );

				if ( ! defined( "WP_ACCESSIBLE_HOSTS" ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
					?>
					<div class="error">
						<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->product->get_text_domain() ), $this->product->get_item_name(), '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
					</div>
				<?php
				}

			}
		}

		/**
		 * Set a notice to display in the admin area
		 *
		 * @param string $type error|updated
		 * @param string $message The message to display
		 */
		protected function set_notice( $message, $success = true ) {
			$css_class = ( $success ) ? 'updated' : 'error';
			add_settings_error( $this->prefix . 'license', 'license-notice', $message, $css_class );
		}

		/**
		 * Remotely activate License
		 * @return boolean True if the license is now activated, false if not
		 */
		public function activate_license() {

			$result = $this->call_license_api( 'activate' );

			if ( $result ) {

				// story expiry date
				if ( isset( $result->expires ) ) {
					$this->set_license_expiry_date( $result->expires );
					$expiry_date = strtotime( $result->expires );
				} else {
					$expiry_date = false;
				}

				// show success notice if license is valid
				if ( $result->license === 'valid' ) {

					// show a custom notice if users have an unlimited license
					if ( $result->license_limit == 0 ) {
						$message = sprintf( __( "Your %s license has been activated. You have an unlimited license. ", $this->product->get_text_domain() ), $this->product->get_item_name() );
					} else {
						$message = sprintf( __( "Your %s license has been activated. You have used %d/%d activations. ", $this->product->get_text_domain() ), $this->product->get_item_name(), $result->site_count, $result->license_limit );
					}

					// add upgrade notice if user has less than 3 activations left
					if ( $result->license_limit > 0 && ( $result->license_limit - $result->site_count ) <= 3 ) {
						$message .= sprintf( __( '<a href="%s">Did you know you can upgrade your license?</a>', $this->product->get_text_domain() ), $this->product->get_tracking_url( 'license-nearing-limit-notice' ) );
						// add extend notice if license is expiring in less than 1 month
					} elseif ( $expiry_date !== false && $expiry_date < strtotime( "+1 month" ) ) {
						$days_left = round( ( $expiry_date - strtotime( "now" ) ) / 86400 );
						$message .= sprintf( __( '<a href="%s">Your license is expiring in %d days, would you like to extend it?</a>', $this->product->get_text_domain() ), $this->product->get_tracking_url( 'license-expiring-notice' ), $days_left );
					}

					$this->set_notice( $message, true );

				} else {

					if ( isset( $result->error ) && $result->error === 'no_activations_left' ) {
						// show notice if user is at their activation limit
						$this->set_notice( sprintf( __( 'You\'ve reached your activation limit. You must <a href="%s">upgrade your license</a> to use it on this site.', $this->product->get_text_domain() ), $this->product->get_tracking_url( 'license-at-limit-notice' ) ), false );
					} elseif ( isset( $result->error ) && $result->error == "expired" ) {
						// show notice if the license is expired
						$this->set_notice( sprintf( __( 'Your license has expired. You must <a href="%s">extend your license</a> in order to use it again.', $this->product->get_text_domain() ), $this->product->get_tracking_url( 'license-expired-notice' ) ), false );
					} else {
						// show a general notice if it's any other error
						$this->set_notice( __( "Failed to activate your license, your license key seems to be invalid.", $this->product->get_text_domain() ), false );
					}

					$this->remote_license_activation_failed = true;
				}

				$this->set_license_status( $result->license );
			}

			return ( $this->license_is_valid() );
		}

		/**
		 * Remotely deactivate License
		 * @return boolean True if the license is now deactivated, false if not
		 */
		public function deactivate_license() {

			$result = $this->call_license_api( 'deactivate' );

			if ( $result ) {

				// show notice if license is deactivated
				if ( $result->license === 'deactivated' ) {
					$this->set_notice( sprintf( __( "Your %s license has been deactivated.", $this->product->get_text_domain() ), $this->product->get_item_name() ) );
				} else {
					$this->set_notice( sprintf( __( "Failed to deactivate your %s license.", $this->product->get_text_domain() ), $this->product->get_item_name() ), false );
				}

				$this->set_license_status( $result->license );
			}

			return ( $this->get_license_status() === 'deactivated' );
		}

		/**
		 * Checks the license status remotely
		 *
		 * @return boolean True if the function ran with success, false otherwise.
		 */
		public function check_license() {

			// Only run once every week
			$transient_name = $this->prefix . 'license_checked';

			if ( get_transient( $transient_name ) !== false ) {
				return false;
			}

			// call remote api
			$result = $this->call_license_api( 'check' );

			// did the request fail?
			if ( $result === false ) {
				return false;
			}

			// story expiry date
			if ( isset( $result->expires ) ) {
				$this->set_license_expiry_date( $result->expires );
			}

			// check if license status is still correct
			if ( $this->get_license_status() !== trim( $result->license ) ) {
				$this->set_license_status( $result->license );
			}

			// set transient to ensure license is only checked once a week
			set_transient( $transient_name, 1, strtotime( "+1 week" ) );

			return true;
		}

		/**
		 * @param string $action activate|deactivate
		 *
		 * @return mixed
		 */
		protected function call_license_api( $action ) {

			// don't make a request if license key is empty
			if ( $this->get_license_key() === '' ) {
				return false;
			}

			// data to send in our API request
			$api_params = array(
				'edd_action' => $action . '_license',
				'license'    => $this->get_license_key(),
				'item_name'  => urlencode( trim( $this->product->get_item_name() ) )
			);

			// create api request url
			$url = add_query_arg( $api_params, $this->product->get_api_url() );

			// request parameters
			$request_params = array(
				'timeout'   => 20,
				'sslverify' => false,
				'headers'   => array( 'Accept-Encoding' => '*' )
			);

			// fire request to shop
			$response = wp_remote_get( $url, $request_params );

			// make sure response came back okay
			if ( is_wp_error( $response ) ) {

				// set notice, useful for debugging why remote requests are failing
				$this->set_notice( sprintf( __( "Request error: %s", $this->product->get_text_domain() ), $response->get_error_message() ), false );

				return false;
			}

			// decode api response
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			return $license_data;
		}

		/**
		 * Set the license status
		 *
		 * @param string $license_status
		 */
		public function set_license_status( $license_status ) {
			$this->set_option( 'status', $license_status );
		}

		/**
		 * Get the license status
		 *
		 * @return string $license_status;
		 */
		public function get_license_status() {
			$license_status = $this->get_option( 'status' );

			return trim( $license_status );
		}

		/**
		 * Set the license key
		 *
		 * @param string $license_key
		 */
		public function set_license_key( $license_key ) {
			$this->set_option( 'key', $license_key );
		}

		/**
		 * Gets the license key from constant or option
		 *
		 * @return string $license_key
		 */
		public function get_license_key() {
			$license_key = $this->get_option( 'key' );

			return trim( $license_key );
		}

		/**
		 * Gets the license expiry date
		 *
		 * @return string
		 */
		public function get_license_expiry_date() {
			return $this->get_option( 'expiry_date' );
		}

		/**
		 * Stores the license expiry date
		 */
		public function set_license_expiry_date( $expiry_date ) {
			$this->set_option( 'expiry_date', $expiry_date );
		}

		/**
		 * Checks whether the license status is active
		 *
		 * @return boolean True if license is active
		 */
		public function license_is_valid() {
			return ( $this->get_license_status() === 'valid' );
		}

		/**
		 * Get all license related options
		 *
		 * @return array Array of license options
		 */
		protected function get_options() {

			// create option name
			$option_name = $this->prefix . 'license';

			// get array of options from db
			$options = get_option( $option_name, array() );

			// setup array of defaults
			$defaults = array(
				'key'         => '',
				'status'      => '',
				'expiry_date' => ''
			);

			// merge options with defaults
			$this->options = wp_parse_args( $options, $defaults );

			return $this->options;
		}

		/**
		 * Set license related options
		 *
		 * @param array $options Array of new license options
		 */
		protected function set_options( array $options ) {
			// create option name
			$option_name = $this->prefix . 'license';

			// update db
			update_option( $option_name, $options );
		}

		/**
		 * Gets a license related option
		 *
		 * @param string $name The option name
		 *
		 * @return mixed The option value
		 */
		protected function get_option( $name ) {
			$options = $this->get_options();

			return $options[ $name ];
		}

		/**
		 * Set a license related option
		 *
		 * @param string $name The option name
		 * @param mixed $value The option value
		 */
		protected function set_option( $name, $value ) {
			// get options
			$options = $this->get_options();

			// update option
			$options[ $name ] = $value;

			// save options
			$this->set_options( $options );
		}

		/**
		 * Show a form where users can enter their license key
		 *
		 * @param boolean $embedded Boolean indicating whether this form is embedded in another form?
		 */
		public function show_license_form( $embedded = true ) {

			$key_name    = $this->prefix . 'license_key';
			$nonce_name  = $this->prefix . 'license_nonce';
			$action_name = $this->prefix . 'license_action';


			$visible_license_key = $this->get_license_key();

			// obfuscate license key
			$obfuscate = ( strlen( $this->get_license_key() ) > 5 && ( $this->license_is_valid() || ! $this->remote_license_activation_failed ) );

			if ( $obfuscate ) {
				$visible_license_key = str_repeat( '*', strlen( $this->get_license_key() ) - 4 ) . substr( $this->get_license_key(), - 4 );
			}

			// make license key readonly when license key is valid or license is defined with a constant
			$readonly = ( $this->license_is_valid() || $this->license_constant_is_defined );

			require dirname( __FILE__ ) . '/views/form.php';

			// enqueue script in the footer
			add_action( 'admin_footer', array( $this, 'output_script' ), 99 );
		}

		/**
		 * Check if the license form has been submitted
		 */
		public function catch_post_request() {

			$name = $this->prefix . 'license_key';

			// check if license key was posted and not empty
			if ( ! isset( $_POST[ $name ] ) ) {
				return;
			}

			// run a quick security check
			$nonce_name = $this->prefix . 'license_nonce';

			if ( ! check_admin_referer( $nonce_name, $nonce_name ) ) {
				return;
			}

			// @TODO: check for user cap?

			// get key from posted value
			$license_key = $_POST[ $name ];

			// check if license key doesn't accidentally contain asterisks
			if ( strstr( $license_key, '*' ) === false ) {

				// sanitize key
				$license_key = trim( sanitize_key( $_POST[ $name ] ) );

				// save license key
				$this->set_license_key( $license_key );
			}

			// does user have an activated valid license
			if ( ! $this->license_is_valid() ) {

				// try to auto-activate license
				return $this->activate_license();

			}

			$action_name = $this->prefix . 'license_action';

			// was one of the action buttons clicked?
			if ( isset( $_POST[ $action_name ] ) ) {

				$action = trim( $_POST[ $action_name ] );

				switch ( $action ) {

					case 'activate':
						return $this->activate_license();
						break;

					case 'deactivate':
						return $this->deactivate_license();
						break;
				}

			}

		}

		/**
		 * Output the script containing the YoastLicenseManager JS Object
		 *
		 * This takes care of disabling the 'activate' and 'deactivate' buttons
		 */
		public function output_script() {
			require_once dirname( __FILE__ ) . '/views/script.php';
		}

		/**
		 * Set the constant used to define the license
		 *
		 * @param string $license_constant_name The license constant name
		 */
		public function set_license_constant_name( $license_constant_name ) {
			$this->license_constant_name = trim( $license_constant_name );
			$this->maybe_set_license_key_from_constant();
		}

		/**
		 * Maybe set license key from a defined constant
		 */
		private function maybe_set_license_key_from_constant() {

			if ( empty( $this->license_constant_name ) ) {
				// generate license constant name
				$this->set_license_constant_name( strtoupper( str_replace( array(
						' ',
						'-'
					), '', sanitize_key( $this->product->get_item_name() ) ) ) . '_LICENSE' );
			}

			// set license key from constant
			if ( defined( $this->license_constant_name ) ) {

				$license_constant_value = constant( $this->license_constant_name );

				// update license key value with value of constant
				if ( $this->get_license_key() !== $license_constant_value ) {
					$this->set_license_key( $license_constant_value );
				}

				$this->license_constant_is_defined = true;
			}
		}

	}

}