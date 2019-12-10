<?php
// Security
if ( ! defined( 'MYCRED_VIDEO_SLUG' ) ) exit;

/**
 * Video Shortcode
 * @since 1.0
 * @version 1.2
 */
if ( ! function_exists( 'mycred_render_shortcode_video' ) ) :
	function mycred_render_shortcode_video( $atts ) {

		global $mycred_video_points;

		extract( shortcode_atts( array(
			'id'       => NULL,
			'width'    => 560,
			'height'   => 315,
			'amount'   => NULL,
			'logic'    => NULL,
			'interval' => NULL,
			'ctype'    => 'mycred_default',
			'notice'   => 1
		), $atts ) );

		$hooks = get_option( 'mycred_pref_hooks', false );
		if ( $ctype != 'mycred_default' )
			$hooks = get_option( 'mycred_pref_hooks_' . sanitize_key( $ctype ), false );

		if ( $hooks === false ) return;
		$prefs = $hooks['hook_prefs']['video_view'];

		if ( $amount === NULL )
			$amount = $prefs['creds'];

		if ( $logic === NULL )
			$logic = $prefs['logic'];

		if ( $interval === NULL )
			$interval = $prefs['interval'];

		// ID is required
		if ( $id === NULL || empty( $id ) ) return __( 'A video ID is required for this shortcode', 'mycred_video' );

		// Interval
		if ( strlen( $interval ) < 3 )
			$interval = abs( $interval * 1000 );

		// Video ID
		$video_id = str_replace( '-', '__', $id );

		// Find source
		if ( is_numeric( $id ) )
			$source = 'vimeo';
		else
			$source = 'youtube';

		// Create key
		$key = mycred_create_token( array( $source, $video_id, $amount, $logic, $interval ) );

		if ( ! isset( $mycred_video_points ) || ! is_array( $mycred_video_points ) )
			$mycred_video_points = array();

		// Handle sources accordingly
		switch ( $source ) {

			// Vimeo
			case 'vimeo' :

				// Construct Vimeo Query
				// @see http://developer.vimeo.com/player/embedding#universal-parameters
				$query = apply_filters( 'mycred_video_query_vimeo', array(
					'api'       => 1,
					'player_id' => 'mycred_vvideo_v' . $video_id,
					'autoplay'  => 0,
					'badge'     => 1,
					'byline'    => 1,
					'color'     => '00adef',
					'loop'      => 0,
					'portrait'  => 1,
					'title'     => 1
				), $atts, $video_id );

				// Construct Vimeo Query Address
				$url = 'https://player.vimeo.com/video/' . $video_id;
				$url = add_query_arg( $query, $url );

				$mycred_video_points[] = 'vimeo';

			break;

			// Default to YouTube
			default :

				// Construct YouTube Query
				$query = apply_filters( 'mycred_video_query_youtube', array(
					'enablejsapi' => 1,
					'version'     => 3,
					'playerapiid' => 'mycred_vvideo_v' . $video_id,
					'rel'         => 0,
					'controls'    => 1,
					'showinfo'    => 0
				), $atts, $video_id );

				// Construct Youtube Query Address
				$url = 'https://www.youtube.com/embed/' . $id;
				$url = add_query_arg( $query, $url );

				$mycred_video_points[] = 'youtube';

			break;

		}

		// Make sure video source ids are unique
		$mycred_video_points = array_unique( $mycred_video_points );

		ob_start();

?>
<div class="mycred-video-wrapper <?php echo $source . '-video'; ?>">
	<iframe id="mycred_vvideo_v<?php echo $video_id; ?>" class="mycred-video mycred-<?php echo $source; ?>-video" data-vid="<?php echo $video_id; ?>" src="<?php echo $url; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
	<script type="text/javascript">
function mycred_vvideo_v<?php echo $video_id; ?>( state ) {
	var nstate = '';
	if ( state.data === undefined )
		nstate = state;
	else
		nstate = state.data;

	if ( state.target !== undefined )
		duration[ "<?php echo $video_id; ?>" ] = state.target.getDuration();

	mycred_view_video( "<?php echo $video_id; ?>", nstate, "<?php echo $logic; ?>", "<?php echo $interval; ?>", "<?php echo $key; ?>", "<?php echo $ctype; ?>" );
}
	</script>
	<?php if ( $notice == 1 ) { ?><div class="mycred-video-update-box" id="mycred_vvideo_v<?php echo $video_id; ?>_box"></div><?php } ?>
</div>
<?php

		$output = ob_get_contents();
		ob_end_clean();
		
		// Return the shortcode output
		return apply_filters( 'mycred_video_output', $output, $atts );

	}
endif;

?>