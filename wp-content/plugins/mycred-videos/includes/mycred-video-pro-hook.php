<?php
// Security
if ( ! defined( 'MYCRED_VIDEO_SLUG' ) ) exit;

/**
 * Video Plus Hook
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Hook_Video_Views_Plus' ) ) :
	class myCRED_Hook_Video_Views_Plus extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = 'mycred_default' ) {

			parent::__construct( array(
				'id'       => 'video_view',
				'defaults' => array(
					'creds'    => 1,
					'log'      => '%plural% for viewing video',
					'logic'    => 'play',
					'interval' => '',
					'leniency' => 10,
					'template' => 'You have received %amount% %_plural% for watching this video'
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.0
		 * @version 1.0
		 */
		public function run() {

			add_action( 'mycred_front_enqueue',          array( $this, 'register_script' ) );

			if ( ! shortcode_exists( 'mycred_video' ) )
				add_shortcode( 'mycred_video',           'mycred_render_shortcode_video' );

			add_action( 'wp_footer',                        array( $this, 'footer' ) );
			add_action( 'wp_ajax_mycred-viewing-provideos', array( $this, 'ajax_call_video_points' ) );

		}

		/**
		 * Register Script
		 * @since 1.0
		 * @version 1.0
		 */
		public function register_script() {

			if ( ! is_user_logged_in() ) return;

			// Core video script
			wp_register_script(
				'mycred-video-plus',
				plugins_url( 'assets/js/viewing-video.js', MYCRED_VIDEO ),
				array(),
				MYCRED_VIDEO_JS_VERSION,
				true
			);

			$template = '';
			if ( isset( $this->prefs['template'] ) )
				$template = $this->prefs['template'];

			wp_localize_script(
				'mycred-video-plus',
				'myCRED_Video',
				array(
					'ajaxurl'          => admin_url( 'admin-ajax.php' ),
					'token'            => wp_create_nonce( 'mycred-video-points' ),
					'default_interval' => abs( $this->prefs['interval']*1000 ),
					'default_logic'    => $this->prefs['logic'],
					'template'         => $this->core->template_tags_general( $template )
				)
			);

			// Load core
			wp_enqueue_script( 'mycred-video-plus' );

			// Vimeo
			wp_register_script(
				'mycred-video-vimeo',
				plugins_url( 'assets/js/vimeo.js', MYCRED_VIDEO ),
				array( 'jquery' ),
				MYCRED_VIDEO_JS_VERSION,
				true
			);

			// YouTube
			wp_register_script(
				'mycred-video-youtube',
				plugins_url( 'assets/js/youtube.js', MYCRED_VIDEO ),
				array( 'jquery' ),
				MYCRED_VIDEO_JS_VERSION,
				true
			);

		}

		/**
		 * Load Scripts in Footer
		 * @since 1.0
		 * @version 1.0
		 */
		public function footer() {

			global $mycred_video_points;

			// If vimeo videos are used
			if ( in_array( 'vimeo', (array) $mycred_video_points ) )
				wp_enqueue_script( 'mycred-video-vimeo' );

			// If youtube videos are used
			if ( in_array( 'youtube', (array) $mycred_video_points ) )
				wp_enqueue_script( 'mycred-video-youtube' );

		}

		/**
		 * AJAX Call Handler
		 * @since 1.0
		 * @version 1.0
		 */
		public function ajax_call_video_points() {

			// We must be logged in
			if ( ! is_user_logged_in() ) die();

			// Security
			check_ajax_referer( 'mycred-video-points', 'token' );

			// We are only interested in handling our own point type
			// We can not die here since then the hook will not fire for the correct point type.
			if ( ! isset( $_POST['type'] ) || $this->mycred_type != sanitize_key( $_POST['type'] ) ) return;

			// Get user id
			$user_id = get_current_user_id();

			// Decode the key giving us the video shortcode setup
			// This will prevent users from manipulating the shortcode output
			$setup = mycred_verify_token( $_POST['setup'], 5 );
			if ( $setup === false ) die( 1 );

			list ( $source, $video_id, $amount, $logic, $interval ) = array_pad( $setup, 5, '' );

			// Required
			if ( empty( $source ) || empty( $video_id ) ) die( 2 );

			// Prep
			$amount   = $this->core->number( $amount );
			$interval = abs( $interval / 1000 );

			// Get playback details
			$actions  = sanitize_text_field( $_POST['video_a'] );
			$seconds  = absint( $_POST['video_b'] );
			$duration = absint( $_POST['video_c'] );
			$state    = absint( $_POST['video_d'] );

			// Apply Leniency
			$leniency = $duration * ( $this->prefs['leniency'] / 100 );
			$leniency = floor( $leniency );
			$watched  = $seconds + $leniency;

			$status = 'silence';

			switch ( $logic ) {

				// Award points when video starts
				case 'play' :

					if ( $state == 1 ) {
						if ( ! $this->has_entry( 'watching_video', '', $user_id, $video_id, $this->mycred_type ) ) {
							// Execute
							$this->core->add_creds(
								'watching_video',
								$user_id,
								$amount,
								$this->prefs['log'],
								0,
								$video_id,
								$this->mycred_type
							);

							$status = 'added';
						}
						else {
							$status = 'max';
						}
					}

				break;

				// Award points when video is viewed in full
				case 'full' :

					// Check for skipping or if we watched more (with leniency) then the video length
					if ( ! preg_match( '/22/', $actions, $matches ) || $watched >= $duration ) {
						if ( $state == 0 ) {
							if ( ! $this->has_entry( 'watching_video', '', $user_id, $video_id, $this->mycred_type ) ) {
								// Execute
								$this->core->add_creds(
									'watching_video',
									$user_id,
									$amount,
									$this->prefs['log'],
									0,
									$video_id,
									$this->mycred_type
								);

								$status = 'added';
							}
							else {
								$status = 'max';
							}
						}
					}

				break;

				// Award points in intervals
				case 'interval' :

					// The maximum points a video can earn you
					$num_intervals = floor( $duration / $interval );
					$max           = abs( $num_intervals * $amount );
					$users_log     = $this->get_users_video_log( $video_id, $user_id );

					// Film is playing and we just started
					if ( $state == 1 && $users_log === NULL ) {
						// Add points without using mycred_add to prevent
						// notifications from being sent as this amount will change.
						$this->core->update_users_balance( $user_id, $amount );
						$this->core->add_to_log(
							'watching_video',
							$user_id,
							$amount,
							$this->prefs['log'],
							0,
							$video_id,
							$this->mycred_type
						);

						$status = 'added';
					}

					// Film is playing and we have not yet reached maximum on this movie
					elseif ( $state == 1 && isset( $users_log->creds ) && $users_log->creds+$amount <= $max ) {
						$this->update_creds( $users_log->id, $user_id, $users_log->creds+$amount );
						$this->core->update_users_balance( $user_id, $amount );
						$amount = $users_log->creds+$amount;

						$status = 'added';
					}

					// Film has ended and we have not reached maximum
					elseif ( $state == 0 && isset( $users_log->creds ) && $users_log->creds+$amount <= $max ) {
						$this->update_creds( $users_log->id, $user_id, $users_log->creds+$amount );
						$this->core->update_users_balance( $user_id, $amount );
						$amount = $users_log->creds+$amount;

						$status = 'max';

						// If enabled, add notification
						if ( function_exists( 'mycred_add_new_notice' ) ) {
							if ( $amount < 0 )
								$color = '<';
							else
								$color = '>';

							$message = str_replace( '%amount%', $amount, $this->prefs['template'] );
							if ( ! empty( $message ) )
								mycred_add_new_notice( array( 'user_id' => $user_id, 'message' => $message, 'color' => $color ) );
						}
					}

				break;

			}

			die( json_encode( array(
				'status'   => $status,
				'video_id' => $video_id,
				'amount'   => $amount,
				'duration' => $duration,
				'seconds'  => $seconds,
				'watched'  => $watched,
				'actions'  => $actions,
				'state'    => $state,
				'logic'    => $logic,
				'interval' => $interval
			) ) );

		}

		/**
		 * Get Users Video Log
		 * Returns the log for a given video id.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_users_video_log( $video_id, $user_id ) {

			global $wpdb;

			$sql = "SELECT * FROM {$this->core->log_table} WHERE user_id = %d AND data = %s AND ctype = %s;";
			return $wpdb->get_row( $wpdb->prepare( $sql, $user_id, $video_id, $this->mycred_type ) );

		}

		/**
		 * Update Points
		 * @since 1.0
		 * @version 1.0
		 */
		public function update_creds( $row_id, $user_id, $amount ) {

			// Prep format
			if ( !isset( $this->core->format['decimals'] ) )
				$decimals = $this->core->core['format']['decimals'];
			else
				$decimals = $this->core->format['decimals'];

			if ( $decimals > 0 )
				$format = '%f';
			else
				$format = '%d';

			$amount = $this->core->number( $amount );

			global $wpdb;

			$wpdb->update(
				$this->core->log_table,
				array( 'creds' => $amount ),
				array( 'id'    => $row_id ),
				array( $format ),
				array( '%d' )
			);

		}

		/**
		 * Preference for Viewing Videos
		 * @since 1.0
		 * @version 1.0
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<label class="subheader"><?php echo $this->core->plural(); ?></label>
<ol>
	<li>
		<div class="h2"><input type="text" name="<?php echo $this->field_name( 'creds' ); ?>" id="<?php echo $this->field_id( 'creds' ); ?>" value="<?php echo $this->core->number( $prefs['creds'] ); ?>" size="8" /></div>
		<span class="description"><?php _e( 'Amount to award for viewing videos.', 'mycred_video' ); ?></span>
	</li>
</ol>
<label class="subheader"><?php _e( 'Log Template', 'mycred_video' ); ?></label>
<ol>
	<li>
		<div class="h2"><input type="text" name="<?php echo $this->field_name( 'log' ); ?>" id="<?php echo $this->field_id( 'log' ); ?>" value="<?php echo $prefs['log']; ?>" class="long" /></div>
		<span class="description"><?php _e( 'Available template tags: General', 'mycred_video' ); ?></span>
	</li>
</ol>
<label class="subheader"><?php _e( 'Award Logic', 'mycred_video' ); ?></label>
<ol>
	<li><?php echo $this->core->template_tags_general( __( 'Select when %_plural% should be awarded or deducted.', 'mycred_video' ) ); ?></li>
	<li><input type="radio" name="<?php echo $this->field_name( 'logic' ); ?>" id="<?php echo $this->field_id( array( 'logic' => 'play' ) ); ?>"<?php checked( $prefs['logic'], 'play' ); ?> value="play" /> <label for="<?php echo $this->field_id( array( 'logic' => 'play' ) ); ?>"><?php _e( 'Play - As soon as video starts playing.', 'mycred_video' ); ?></label></li>
	<li><input type="radio" name="<?php echo $this->field_name( 'logic' ); ?>" id="<?php echo $this->field_id( array( 'logic' => 'full' ) ); ?>"<?php checked( $prefs['logic'], 'full' ); ?> value="full" /> <label for="<?php echo $this->field_id( array( 'logic' => 'full' ) ); ?>"><?php _e( 'Full - First when the entire video has played.', 'mycred_video' ); ?></label></li>
	<li><input type="radio" name="<?php echo $this->field_name( 'logic' ); ?>" id="<?php echo $this->field_id( array( 'logic' => 'interval' ) ); ?>"<?php checked( $prefs['logic'], 'interval' ); ?> value="interval" /> <label for="<?php echo $this->field_id( array( 'logic' => 'interval' ) ); ?>"><?php echo $this->core->template_tags_general( __( 'Interval - For each x number of seconds watched.', 'mycred_video' ) ); ?></label></li>
</ol>
<div id="video-interval"<?php if ( $prefs['logic'] == 'play' || $prefs['logic'] == 'full' ) echo ' style="display: none;"';?>>
	<label class="subheader"><?php _e( 'Interval', 'mycred_video' ); ?></label>
	<ol>
		<li><?php _e( 'Number of seconds', 'mycred_video' ); ?></li>
		<li>
			<div class="h2"><input type="text" name="<?php echo $this->field_name( 'interval' ); ?>" id="<?php echo $this->field_id( 'interval' ); ?>" value="<?php echo $prefs['interval']; ?>" size="8" /></div>
		</li>
	</ol>
</div>
<div id="video-leniency"<?php if ( $prefs['logic'] == 'play' ) echo ' style="display: none;"';?>>
	<label class="subheader"><?php _e( 'Leniency', 'mycred_video' ); ?></label>
	<ol>
		<li><?php _e( 'The maximum percentage a users view of a movie can differ from the actual length.', 'mycred_video' ); ?></li>
		<li>
			<div class="h2"><input type="text" name="<?php echo $this->field_name( 'leniency' ); ?>" id="<?php echo $this->field_id( 'leniency' ); ?>" value="<?php echo $prefs['leniency']; ?>" size="4" /> %</div>
			<span class="description"><?php echo _e( 'Do not set this value to zero! A lot of thing can happen while a user watches a movie and sometimes a few seconds can drop of the counter due to buffering or play back errors.', 'mycred_video' ); ?></span>
		</li>
	</ol>
</div>
<label class="subheader"><?php _e( 'Update Notice', 'mycred_video' ); ?></label>
<ol>
	<li>
		<div class="h2"><input type="text" name="<?php echo $this->field_name( 'template' ); ?>" id="<?php echo $this->field_id( 'template' ); ?>" value="<?php echo $this->core->template_tags_general( $prefs['template'] ); ?>" class="long" /></div>
		<span class="description"><?php echo $this->core->template_tags_general( __( 'If set, this notice is shown under the video once the user has received %plural%.', 'mycred_video' ) ); ?></span>
	</li>
</ol>
<script type="text/javascript">
jQuery(function($){

	$( 'input[name="<?php echo $this->field_name( 'logic' ); ?>"]' ).change(function(){
		if ( $(this).val() == 'interval' ) {
			$( '#video-interval' ).show();
			$( '#video-leniency' ).show();
		}
		else if ( $(this).val() == 'full' ) {
			$( '#video-interval' ).hide();
			$( '#video-leniency' ).show();
		}
		else {
			$( '#video-interval' ).hide();
			$( '#video-leniency' ).hide();
		}
	});

});
</script>
<?php

		}

	}
endif;

?>