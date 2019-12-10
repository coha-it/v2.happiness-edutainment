<?php
/**
 * Plugin Name: myCRED Video Add-on
 * Description: Replaced the built-in myCRED video hook with a premium version that suppots YouTube and Vimeo videos.
 * Version: 1.2.2
 * Author: myCRED
 * Author URI: http://mycred.me
 * Author Email: support@mycred.me
 * Requires at least: WP 4.8
 * Tested up to: WP 5.0.2
 * Text Domain: mycred_video
 * Domain Path: /lang
 * License: Copyrighted
 *
 * Copyright © 2013 - 2017 myCRED
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
if ( ! class_exists( 'myCRED_Video_Plus_Core' ) ) :
	final class myCRED_Video_Plus_Core {

		// Plugin Version
		public $version             = '1.2.2';

		public $id                  = 540;

		public $slug                = '';
		public $domain              = '';
		public $plugin              = NULL;
		public $plugin_name         = '';
		protected $update_url       = 'http://mycred.me/api/plugins/';

		// Instnace
		protected static $_instance = NULL;

		// Current session
		public $session             = NULL;

		/**
		 * Setup Instance
		 * @since 1.0.4
		 * @version 1.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Not allowed
		 * @since 1.0.4
		 * @version 1.0
		 */
		public function __clone() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.1.1' ); }

		/**
		 * Not allowed
		 * @since 1.0.4
		 * @version 1.0
		 */
		public function __wakeup() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.1.1' ); }

		/**
		 * Define
		 * @since 1.0.4
		 * @version 1.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) )
				define( $name, $value );
		}

		/**
		 * Require File
		 * @since 1.0.4
		 * @version 1.0
		 */
		public function file( $required_file ) {
			if ( file_exists( $required_file ) )
				require_once $required_file;
		}

		/**
		 * Construct
		 * @since 1.0.4
		 * @version 1.0
		 */
		public function __construct() {

			$this->slug        = 'mycred-videos';
			$this->plugin      = plugin_basename( __FILE__ );
			$this->domain      = 'mycred_video';
			$this->plugin_name = 'myCRED Video Add-on';

			$this->define_constants();
			$this->includes();

			add_action( 'mycred_init',                           array( $this, 'load_textdomain' ) );
			add_filter( 'mycred_setup_hooks',                    array( $this, 'setup_hook' ) );
			add_action( 'mycred_load_hooks',                     array( $this, 'load_hook' ) );
		}

		/**
		 * Define Constants
		 * @since 1.1.1
		 * @version 1.0
		 */
		private function define_constants() {

			$this->define( 'MYCRED_VIDEO_VERSION',      $this->version );
			$this->define( 'MYCRED_VIDEO_SLUG',         $this->slug );

			$this->define( 'MYCRED_DEFAULT_TYPE_KEY',   'mycred_default' );

			$this->define( 'MYCRED_VIDEO',              __FILE__ );
			$this->define( 'MYCRED_VIDEO_ROOT_DIR',     plugin_dir_path( MYCRED_VIDEO ) );
			$this->define( 'MYCRED_VIDEO_ASSETS_DIR',   MYCRED_VIDEO_ROOT_DIR . 'assets/' );
			$this->define( 'MYCRED_VIDEO_INCLUDES_DIR', MYCRED_VIDEO_ROOT_DIR . 'includes/' );

		}

		/**
		 * Include Plugin Files
		 * @since 1.1.1
		 * @version 1.0
		 */
		public function includes() {

		    $this->file( MYCRED_VIDEO_ROOT_DIR . 'license/license.php' );
			$this->file( MYCRED_VIDEO_INCLUDES_DIR . 'mycred-shortcodes.php' );

		}

		/**
		 * Setup Hooks
		 * @since 1.1
		 * @version 1.0
		 */
		function setup_hook( $installed ) {

			if ( array_key_exists( 'video_view', $installed ) )
				unset( $installed['video_view'] );

			$installed['video_view'] = array(
				'title'       => __( '%plural% for viewing Videos (Premium)', 'mycred_video' ),
				'description' => __( 'Award or deduct points from users for viewing YouTube or Vimeo videos.', 'mycred_video' ),
				'callback'    => array( 'myCRED_Hook_Video_Views_Plus' )
			);

			return $installed;

		}

		/**
		 * Load Hook
		 * @since 1.1.1
		 * @version 1.0
		 */
		public function load_hook() {

			$this->file( MYCRED_VIDEO_INCLUDES_DIR . 'mycred-video-pro-hook.php' );

		}

		/**
		 * Load Textdomain
		 * @since 1.0
		 * @version 1.0
		 */
		public function load_textdomain() {

			// Load Translation
			$locale = apply_filters( 'plugin_locale', get_locale(), $this->domain );

			load_textdomain( $this->domain, WP_LANG_DIR . '/' . $this->slug . '/' . $this->domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $this->domain, false, dirname( $this->plugin ) . '/lang/' );

		}

	}
endif;

function mycred_video_plus_core() {
	return myCRED_Video_Plus_Core::instance();
}
mycred_video_plus_core();