<?php
/**
 * Version: 1.1.0
 */
/**
 * Changelog:
 *
 * 1.1.0:
 *  - Complete change to Range framework
 *  - Coding standards
 *  - Move from camelCase to underscores for function names
 *  - Drop support for WP 2.8 & 2.9
 *
 * 1.0.15:
 *  - Fix support forum link
 *  - Update feed to Ran.ge
 *
 * 1.0.14:
 *  - Fix sidebar alignment on settings page
 *  - Fix forum link by passing it to the http://wordpress.org/tags/{slug}?forum_id=10
 *
 * 1.0.13:
 *  - Add the 'xpf-dashboard-widget' filter
 *
 * 1.0.12:
 *  - Add the xpf-show-general-settings-submit filter
 *
 * 1.0.11:
 *  - Add the xpf-pre-main-metabox action
 *
 * 1.0.10:
 *  - Allow the screen icon to be overridden
 *
 * 1.0.9:
 *  - Allow removal of Xavisys sidebar boxes
 *
 * 1.0.8:
 *  - Allow an auto-created options page that doesn't have a main meta box
 *
 * 1.0.7:
 *  - Add the ability to modify the form action on the options page
 *  - Add an action in the options page form tag
 *
 * 1.0.6:
 *  - Add ability to not have a settings page
 *
 * 1.0.5:
 *  - Added XavisysPlugin::_feed_url
 *  - Changed feed to the feed burner URL because of a redirect issue with 2.9.x
 *
 * 1.0.4:
 *  - Added donate link to the plugin meta
 *
 * 1.0.3:
 *  - Changed to use new cdn for images
 */
if (!class_exists('RangePlugin')) {
	/**
	 * Abstract class RangePlugin used as a WordPress Plugin framework
	 *
	 * @abstract
	 */
	abstract class RangePlugin {
		/**
		 * @var array Plugin settings
		 */
		protected $_settings;

		/**
		 * @var string - The options page name used in the URL
		 */
		protected $_hook = '';

		/**
		 * @var string - The filename for the main plugin file
		 */
		protected $_file = '';

		/**
		 * @var string - The options page title
		 */
		protected $_pageTitle = '';

		/**
		 * @var string - The options page menu title
		 */
		protected $_menuTitle = '';

		/**
		 * @var string - The access level required to see the options page
		 */
		protected $_accessLevel = '';

		/**
		 * @var string - The option group to register
		 */
		protected $_optionGroup = '';

		/**
		 * @var array - An array of options to register to the option group
		 */
		protected $_optionNames = array();

		/**
		 * @var array - An associated array of callbacks for the options, option name should be index, callback should be value
		 */
		protected $_optionCallbacks = array();

		/**
		 * @var string - The plugin slug used on WordPress.org
		 */
		protected $_slug = '';

		/**
		 * @var string - The feed URL for Range
		 */
		protected $_feed_url = 'http://ran.ge/feed/';

		/**
		 * @var string - The button ID for the PayPal button, override this generic one with a plugin-specific one
		 */
		protected $_paypalButtonId = '9925248';

		protected $_optionsPageAction = 'options.php';

		/**
		 * This is our constructor, which is private to force the use of getInstance()
		 * @return void
		 */
		protected function __construct() {
			if ( is_callable( array($this, '_init') ) )
				$this->_init();

			$this->_get_settings();
			if ( is_callable( array($this, '_post_settings_init') ) )
				$this->_post_settings_init();

			add_filter( 'init', array( $this, 'init_locale' ) );
			add_action( 'admin_init', array( $this, 'register_options' ) );
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_page_links' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );
			add_action( 'admin_menu', array( $this, 'register_options_page' ) );
			if ( is_callable(array( $this, 'add_options_meta_boxes' )) )
				add_action( 'admin_init', array( $this, 'add_options_meta_boxes' ) );

			add_action( 'admin_init', array( $this, 'add_default_options_meta_boxes' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ), null, 9 );
			add_action( 'admin_print_scripts', array( $this,'admin_print_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this,'admin_enqueue_scripts' ) );

			add_action ( 'in_plugin_update_message-'.$this->_file , array ( $this , 'changelog' ), null, 2 );
		}

		public function init_locale() {
			$lang_dir = basename(dirname(__FILE__)) . '/languages';
			load_plugin_textdomain( $this->_slug, 'wp-content/plugins/' . $lang_dir, $lang_dir);
		}

		protected function _get_settings() {
			foreach ( $this->_optionNames as $opt ) {
				$this->_settings[$opt] = apply_filters($this->_slug.'-opt-'.$opt, get_option($opt));
			}
		}

		public function register_options() {
			foreach ( $this->_optionNames as $opt ) {
				if ( !empty($this->_optionCallbacks[$opt]) && is_callable( $this->_optionCallbacks[$opt] ) ) {
					$callback = $this->_optionCallbacks[$opt];
				} else {
					$callback = '';
				}
				register_setting( $this->_optionGroup, $opt, $callback );
			}
		}

		public function changelog ($pluginData, $newPluginData) {
			require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

			$plugin = plugins_api( 'plugin_information', array( 'slug' => $newPluginData->slug ) );

			if ( !$plugin || is_wp_error( $plugin ) || empty( $plugin->sections['changelog'] ) ) {
				return;
			}

			$changes = $plugin->sections['changelog'];
			$pos = strpos( $changes, '<h4>' . preg_replace('/[^\d\.]/', '', $pluginData['Version'] ) );
			if ( $pos !== false ) {
				$changes = trim( substr( $changes, 0, $pos ) );
			}

			$replace = array(
				'<ul>'	=> '<ul style="list-style: disc inside; padding-left: 15px; font-weight: normal;">',
				'<h4>'	=> '<h4 style="margin-bottom:0;">',
			);
			echo str_replace( array_keys($replace), $replace, $changes );
		}

		public function register_options_page() {
			if ( apply_filters( 'rpf-options_page-'.$this->_slug, true ) && is_callable( array( $this, 'options_page' ) ) )
				add_options_page( $this->_pageTitle, $this->_menuTitle, $this->_accessLevel, $this->_hook, array( $this, 'options_page' ) );
		}

		protected function _filter_boxes_main($boxName) {
			if ( 'main' == strtolower($boxName) )
				return false;

			return $this->_filter_boxes_helper($boxName, 'main');
		}

		protected function _filter_boxes_sidebar($boxName) {
			return $this->_filter_boxes_helper($boxName, 'sidebar');
		}

		protected function _filter_boxes_helper($boxName, $test) {
			return ( strpos( strtolower($boxName), strtolower($test) ) !== false );
		}

		public function options_page() {
			global $wp_meta_boxes;
			$allBoxes = array_keys( $wp_meta_boxes['range-'.$this->_slug] );
			$mainBoxes = array_filter( $allBoxes, array( $this, '_filter_boxes_main' ) );
			unset($mainBoxes['main']);
			sort($mainBoxes);
			$sidebarBoxes = array_filter( $allBoxes, array( $this, '_filter_boxes_sidebar' ) );
			unset($sidebarBoxes['sidebar']);
			sort($sidebarBoxes);

			$main_width = empty( $sidebarBoxes )? '100%' : '75%';
			?>
				<div class="wrap">
					<?php $this->screen_icon_link(); ?>
					<h2><?php echo esc_html($this->_pageTitle); ?></h2>
					<div class="metabox-holder">
						<div class="postbox-container" style="width:<?php echo $main_width; ?>;">
						<?php
							do_action( 'rpf-pre-main-metabox', $main_width );
							if ( in_array( 'main', $allBoxes ) ) {
						?>
							<form action="<?php esc_attr_e( $this->_optionsPageAction ); ?>" method="post"<?php do_action( 'rpf-options-page-form-tag' ) ?>>
								<?php
								settings_fields( $this->_optionGroup );
								do_meta_boxes( 'range-' . $this->_slug, 'main', '' );
								if ( apply_filters( 'rpf-show-general-settings-submit'.$this->_slug, true ) ) {
								?>
								<p class="submit">
									<input type="submit" name="Submit" value="<?php esc_attr_e('Update Options &raquo;', $this->_slug); ?>" />
								</p>
								<?php
								}
								?>
							</form>
						<?php
							}
							foreach( $mainBoxes as $context ) {
								do_meta_boxes( 'range-' . $this->_slug, $context, '' );
							}
						?>
						</div>
						<?php
						if ( !empty( $sidebarBoxes ) ) {
						?>
						<div class="alignright" style="width:24%;">
							<?php
							foreach( $sidebarBoxes as $context ) {
								do_meta_boxes( 'range-' . $this->_slug, $context, '' );
							}
							?>
						</div>
						<?php
						}
						?>
					</div>
				</div>
				<?php
		}

		public function add_plugin_page_links( $links, $file ){
			if ( $file == $this->_file ) {
				// Add Widget Page link to our plugin
				$link = $this->get_options_link();
				array_unshift( $links, $link );

				// Add Support Forum link to our plugin
				$link = $this->get_support_forum_link();
				array_unshift( $links, $link );
			}
			return $links;
		}

		public function add_plugin_meta_links( $meta, $file ){
			if ( $file == $this->_file )
				$meta[] = $this->get_plugin_link(__('Rate Plugin'));
			return $meta;
		}

		public function get_support_forum_link( $linkText = '' ) {
			if ( empty($linkText) ) {
				$linkText = __( 'Support', $this->_slug );
			}
			return '<a href="' . $this->get_support_forum_url() . '">' . $linkText . '</a>';
		}

		public function get_donate_link( $linkText = '' ) {
			$url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . $this->_paypalButtonId;
			if ( empty($linkText) ) {
				$linkText = __( 'Donate to show your appreciation.', $this->_slug );
			}
			return "<a href='{$url}'>{$linkText}</a>";
		}

		public function get_support_forum_url() {
			return 'http://wordpress.org/support/plugin/' . $this->_slug;
		}

		public function get_plugin_link( $linkText = '' ) {
			if ( empty($linkText) )
				$linkText = __( 'Give it a good rating on WordPress.org.', $this->_slug );
			return "<a href='" . $this->get_plugin_url() . "'>{$linkText}</a>";
		}

		public function get_plugin_url() {
			return 'http://wordpress.org/extend/plugins/' . $this->_slug;
		}

		public function get_options_link( $linkText = '' ) {
			if ( empty($linkText) ) {
				$linkText = __( 'Settings', $this->_slug );
			}
			return '<a href="' . $this->get_options_url() . '">' . $linkText . '</a>';
		}

		public function get_options_url() {
			return admin_url( 'options-general.php?page=' . $this->_hook );
		}

		public function admin_enqueue_scripts() {
			if (isset($_GET['page']) && $_GET['page'] == $this->_hook) {
				wp_enqueue_style('dashboard');
				add_action( 'admin_print_styles-settings_page_' . $this->_hook, array( $this, 'option_page_styles' ) );
			}
		}

		public function option_page_styles() {
			$logo_url = sprintf( 'http%s://range-wphost.netdna-ssl.com/assets/range-icon-square-32x32.png' , is_ssl()? 's':'' );
			?>
			<style type="text/css">
				#icon-range {
					background:transparent url(<?php echo esc_url_raw( $logo_url ); ?>) no-repeat scroll bottom left;
				}
			</style>
			<?php
		}

		public function add_default_options_meta_boxes() {
			if ( apply_filters( 'show-range-like-this', true ) )
				add_meta_box( $this->_slug . '-like-this', __('Like this Plugin?', $this->_slug), array($this, 'like_this_meta_box'), 'range-' . $this->_slug, 'sidebar');

			if ( apply_filters( 'show-range-support', true ) )
				add_meta_box( $this->_slug . '-support', __('Need Support?', $this->_slug), array($this, 'support_meta_box'), 'range-' . $this->_slug, 'sidebar');

			if ( apply_filters( 'show-range-feed', true ) )
				add_meta_box( $this->_slug . '-range-feed', __('Latest news from Range', $this->_slug), array($this, 'range_feed_meta_box'), 'range-' . $this->_slug, 'sidebar');
		}

		public function like_this_meta_box() {
			echo '<p>';
			_e('Then please do any or all of the following:', $this->_slug);
			echo '</p><ul>';

			$url = apply_filters('range-plugin-url-'.$this->_slug, 'http://bluedogwebservices.com/wordpress-plugin/'.$this->_slug);
			echo "<li><a href='{$url}'>";
			_e('Link to it so others can find out about it.', $this->_slug);
			echo "</a></li>";

			echo '<li>' . $this->get_plugin_link() . '</li>';

			echo '<li>' . $this->get_donate_link() . '</li>';

			echo '</ul>';
		}

		public function support_meta_box() {
			echo '<p>';
			echo sprintf(__('If you have any problems with this plugin or ideas for improvements or enhancements, please use the <a href="%s">Support Forums</a>.', $this->_slug), $this->get_support_forum_url() );
			echo '</p>';
		}

		public function range_feed_meta_box() {
			$args = array(
				'url'			=> $this->_feed_url,
				'items'			=> '5',
			);
			echo '<div class="rss-widget">';
			wp_widget_rss_output( $args );
			echo "</div>";
		}

		public function add_dashboard_widgets() {
			if ( apply_filters( 'rpf-dashboard-widget', true ) )
				wp_add_dashboard_widget( 'dashboardb_range' , 'The Latest News From Range' , array( $this, 'dashboard_widget' ) );
		}

		public function dashboard_widget() {
			$args = array(
				'url'			=> $this->_feed_url,
				'items'			=> '3',
				'show_date'		=> 1,
				'show_summary'	=> 1,
			);
			$logo_url = sprintf( 'http%s://range-wphost.netdna-ssl.com/content/uploads/2012/06/range-trans.png' , is_ssl()? 's':'' );
			$icon = includes_url('images/rss.png');
			echo '<div class="rss-widget">';
			echo '<a href="http://ran.ge"><img class="alignright" style="padding:0 0 5px 10px;" src="' . esc_url_raw( $logo_url ) . '" /></a>';
			wp_widget_rss_output( $args );
			echo '<p style="border-top: 1px solid #CCC; padding-top: 10px; font-weight: bold;">';
			echo '<a href="' . $this->_feed_url . '"><img src="' . $icon . '" alt=""/> Subscribe with RSS</a>';
			echo "</p>";
			echo "</div>";
		}

		public function screen_icon_link($name = 'range') {
			$link = '<a href="http://ran.ge">';
			if ( function_exists( 'get_screen_icon' ) ) {
				$link .= get_screen_icon( $name );
			} else {
				ob_start();
				screen_icon($name);
				$link .= ob_get_clean();
			}
			$link .= '</a>';
			echo apply_filters('rpf-screen_icon_link', $link, $name );
		}

		public function admin_print_scripts() {
			if (isset($_GET['page']) && $_GET['page'] == $this->_hook) {
				wp_enqueue_script('postbox');
				wp_enqueue_script('dashboard');
			}
		}
	}
}
