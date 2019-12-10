<?php
/**
 * Plugin Name: myCRED Videos
 * Plugin URI: http://mycred.me/add-ons/videos/
 * Description: Overrides the default "Points for viewing videos" hook in <strong>my</strong>CRED and allows you to embed videos into mobile devices and award / deduct points for Vimeo videos as well as YouTube videos.
 * Version: 1.1
 * Author: Gabriel Sebastian Merovingi
 * Author URI: http://www.merovingi.com
 * Author Email: info@merovingi.com
 * Requires at least: WP 3.8
 * Tested up to: WP 4.3.1
 * Text Domain: mycred_video
 * Domain Path: /lang
 * License: Copyrighted
 *
 * Copyright © 2013-2015 Gabriel S Merovingi
 * 
 * Permission is hereby granted, to the licensed domain to install and run this
 * software and associated documentation files (the "Software") for an unlimited
 * time with the followning restrictions:
 *
 * - This software is only used under the domain name registered with the purchased
 *   license though the myCRED website (mycred.me). Exception is given for localhost
 *   installations or test enviroments.
 *
 * - This software can not be copied and installed on a website not licensed.
 *
 * - This software is supported only if no changes are made to the software files
 *   or documentation. All support is voided as soon as any changes are made.
 *
 * - This software is not copied and re-sold under the current brand or any other
 *   branding in any medium or format.
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
define( 'MYCRED_VIDEO_VERSION',      '1.1' );
define( 'MYCRED_VIDEO_JS_VERSION',   MYCRED_VIDEO_VERSION . '.5' );
define( 'MYCRED_VIDEO_CSS_VERSION',  MYCRED_VIDEO_VERSION . '.5' );

define( 'MYCRED_VIDEO_SLUG',         'mycred-videos' );
define( 'MYCRED_VIDEO',              __FILE__ );
define( 'MYCRED_VIDEO_ROOT_DIR',     plugin_dir_path( MYCRED_VIDEO ) );
define( 'MYCRED_VIDEO_ASSETS_DIR',   MYCRED_VIDEO_ROOT_DIR . 'assets/' );
define( 'MYCRED_VIDEO_INCLUDES_DIR', MYCRED_VIDEO_ROOT_DIR . 'includes/' );

require_once MYCRED_VIDEO_INCLUDES_DIR . 'mycred-shortcodes.php';

/**
 * myCRED_Video_Plugin class
 * @since 1.0
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Video_Plugin' ) ) :
	class myCRED_Video_Plugin {

		/**
		 * Construct
		 */
		function __construct() {

			register_activation_hook( MYCRED_VIDEO,              array( $this, 'activate_mycred_video' ) );

			add_action( 'mycred_pre_init',                       array( $this, 'mycred_pre_init' ) );
			add_filter( 'mycred_setup_hooks',                    array( $this, 'setup_hooks' ), 50 );

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_plugin_update' ), 14 );
			add_filter( 'plugins_api',                           array( $this, 'plugin_api_call' ), 14, 3 );

		}

		/**
		 * Load Translation
		 * @since 1.1
		 * @version 1.0
		 */
		function mycred_pre_init() {

			// Load required files
			require_once MYCRED_VIDEO_INCLUDES_DIR . 'mycred-video-pro-hook.php';

			// Load Translation
			$locale = apply_filters( 'plugin_locale', get_locale(), 'mycred_video' );
			load_textdomain( 'mycred_video', WP_LANG_DIR . "/mycred-videos/mycred_video-$locale.mo" );
			load_plugin_textdomain( 'mycred_video', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		}

		/**
		 * Setup Hooks
		 * @since 1.1
		 * @version 1.0
		 */
		function setup_hooks( $installed ) {

			if ( isset( $installed['video_view'] ) )
				unset( $installed['video_view'] );

			$installed['video_view'] = array(
				'title'       => __( '%plural% for viewing Videos (Premium)', 'mycred_video' ),
				'description' => __( 'Award or deduct points from users for viewing YouTube or Vimeo videos.', 'mycred_video' ),
				'callback'    => array( 'myCRED_Hook_Video_Views_Plus' )
			);

			return $installed;

		}

		/**
		 * Activate
		 * @since 1.0
		 * @version 1.0.1
		 */
		function activate_mycred_video() {

			global $wpdb;

			$message = array();
			// WordPress check
			$wp_version = $GLOBALS['wp_version'];
			if ( version_compare( $wp_version, '3.8', '<' ) )
				$message[] = __( 'This myCRED Add-on requires WordPress 3.8 or higher. Version detected:', 'mycred_video' ) . ' ' . $wp_version;

			// PHP check
			$php_version = phpversion();
			if ( version_compare( $php_version, '5.3', '<' ) )
				$message[] = __( 'This myCRED Add-on requires PHP 5.3 or higher. Version detected: ', 'mycred_video' ) . ' ' . $php_version;

			// SQL check
			$sql_version = $wpdb->db_version();
			if ( version_compare( $sql_version, '5.0', '<' ) )
				$message[] = __( 'This myCRED Add-on requires SQL 5.0 or higher. Version detected: ', 'mycred_video' ) . ' ' . $sql_version;

			// Not empty $message means there are issues
			if ( !empty( $message ) ) {
				$error_message = implode( "\n", $message );
				die( __( 'Sorry but your WordPress installation does not reach the minimum requirements for running this add-on. The following errors were given:', 'mycred_video' ) . "\n" . $error_message );
			}

		}
		
		/**
		 * Plugin Update Check
		 * @since 1.0
		 * @version 1.0
		 */
		function check_for_plugin_update( $checked_data ) {

			global $wp_version;

			if ( empty( $checked_data->checked ) )
				return $checked_data;

			$args = array(
				'slug'    => MYCRED_VIDEO_SLUG,
				'version' => $checked_data->checked[ MYCRED_VIDEO_SLUG . '/' . MYCRED_VIDEO_SLUG . '.php' ],
				'site'    => site_url()
			);
			$request_string = array(
				'body'       => array(
					'action'     => 'basic_check', 
					'request'    => serialize( $args ),
					'api-key'    => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);

			// Start checking for an update
			$raw_response = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

			$response = '';
			if ( ! is_wp_error( $raw_response ) && ( $raw_response['response']['code'] == 200 ) )
				$response = maybe_unserialize( $raw_response['body'] );

			if ( is_object( $response ) && ! empty( $response ) )
				$checked_data->response[ MYCRED_VIDEO_SLUG . '/' . MYCRED_VIDEO_SLUG . '.php' ] = $response;

			return $checked_data;

		}

		/**
		 * Plugin New Version Update
		 * @since 1.0
		 * @version 1.0
		 */
		function plugin_api_call( $def, $action, $args ) {

			global $wp_version;

			if ( ! isset( $args->slug ) || ( $args->slug != MYCRED_VIDEO_SLUG ) )
				return false;

			// Get the current version
			$plugin_info = get_site_transient( 'update_plugins' );
			$args = array(
				'slug'    => MYCRED_VIDEO_SLUG,
				'version' => $plugin_info->checked[ MYCRED_VIDEO_SLUG . '/' . MYCRED_VIDEO_SLUG . '.php' ],
				'site'    => site_url()
			);
			$request_string = array(
				'body'       => array(
					'action'     => 'basic_check', 
					'request'    => serialize( $args ),
					'api-key'    => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);

			$request = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

			if ( is_wp_error( $request ) ) {
				$res = new WP_Error( 'plugins_api_failed', 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', $request->get_error_message() );
			}
			else {
				$res = maybe_unserialize( $request['body'] );
				if ( $res === false )
					$res = new WP_Error( 'plugins_api_failed', 'An unknown error occurred', $request['body'] );
			}

			return $res;

		}

	}

	new myCRED_Video_Plugin();

endif;

?>