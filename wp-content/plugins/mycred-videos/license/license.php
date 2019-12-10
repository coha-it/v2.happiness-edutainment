<?php
//license system
add_filter( 'pre_set_site_transient_update_plugins', 'check_for_plugin_update_mycred_videos' , 80 );
/**
 * Plugin Update Check
 * @since 1.0
 * @version 1.1
 */
function check_for_plugin_update_mycred_videos( $checked_data ) {

	global $wp_version;

	if ( empty( $checked_data->checked ) )
		return $checked_data;

	$args = array(
		'slug'    => MYCRED_VIDEO_SLUG,
		'version' => MYCRED_VIDEO_VERSION,
		'site'    => site_url()
	);
	$request_string = array(
		'body'       => array(
			'action'     => 'version', 
			'request'    => serialize( $args ),
			'api-key'    => md5( get_bloginfo( 'url' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	// Start checking for an update
	$response = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

	if ( ! is_wp_error( $response ) ) {

		$result = maybe_unserialize( $response['body'] );

		if ( is_object( $result ) && ! empty( $result ) )
			$checked_data->response[ MYCRED_VIDEO_SLUG . '/' . MYCRED_VIDEO_SLUG . '.php' ] = $result;

	}

	return $checked_data;

}

add_filter( 'plugins_api', 'plugin_api_call_mycred_videos', 80, 3 );

/**
 * Plugin New Version Update
 * @since 1.0
 * @version 1.1
 */
function plugin_api_call_mycred_videos( $result, $action, $args ) {
  
	global $wp_version;

	if ( ! isset( $args->slug ) || ( $args->slug != MYCRED_VIDEO_SLUG ) )
		return $result;

	// Get the current version
	$args = array(
		'slug'    => MYCRED_VIDEO_SLUG,
		'version' => MYCRED_VIDEO_VERSION,
		'site'    => site_url()
	);
	 
	$request_string = array(
		'body'       => array(
			'action'     => 'info', 
			'request'    => serialize( $args ),
			'api-key'    => md5( get_bloginfo( 'url' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);
	

	$request = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );
	
	if ( ! is_wp_error( $request ) )
		$result = maybe_unserialize( $request['body'] );

	if ( $result->license_expires != '' )
		update_option( 'mycred-premium-' . MYCRED_VIDEO_SLUG . '-expires', $result->license_expires );

	if ( $result->license_renew != '' )
		update_option( 'mycred-premium-' . MYCRED_VIDEO_SLUG . '-renew',   $result->license_renew );

	return $result;

}

add_filter( 'plugin_row_meta', 'MYCRED_VIDEO_plugin_view_info' , 80, 3 );

/**
 * Plugin View Info
 * @since 1.1
 * @version 1.0
 */
function MYCRED_VIDEO_plugin_view_info( $plugin_meta, $file, $plugin_data ) {

	if ( $file != plugin_basename( MYCRED_VIDEO ) ) return $plugin_meta;
	
	if( function_exists('mycred_is_membership_active') && mycred_is_membership_active() && isset( MYCRED_VIDEO_is_membership_plugin()['addons'] ) ) {
	
		$addon_slug = array();
		foreach( MYCRED_VIDEO_is_membership_plugin()['addons'] as $addons ) {
			$addon_slug[] = $addons['folder'];
		}
	
		$plugin_folder = MYCRED_VIDEO_get_folder_name();
	
		if( in_array($plugin_folder,$addon_slug ) ) {
			$expire_days = MYCRED_VIDEO_is_membership_plugin()['order'][0]['expire'];
			
			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . MYCRED_VIDEO_SLUG .
				'&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( __( 'More information about this plugin', MYCRED_VIDEO_SLUG ) ),
				esc_attr( 'myCred Wheel of Fortune' ),
				__( 'View details', MYCRED_VIDEO_SLUG )
			);
			
			if( MYCRED_VIDEO_is_subscription_expired($expire_days) == false )
				$plugin_meta[] = 'Your Licesne Expire in '.MYCRED_VIDEO_calculate_license_expire_time($expire_days);
			else
				$plugin_meta[] = '<span style="color:red">Your membership has been expired </span><a href="#" style="font-weight:bold">Renew Membership</a>';
		}
	} else {
		
	$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
		esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . MYCRED_VIDEO_SLUG .
		'&TB_iframe=true&width=600&height=550' ) ),
		esc_attr( __( 'More information about this plugin', MYCRED_VIDEO_SLUG ) ),
		esc_attr( 'myCred Wheel of Fortune' ),
		__( 'View details', MYCRED_VIDEO_SLUG )
	);

	$url     = str_replace( array( 'https://', 'http://' ), '', get_bloginfo( 'url' ) );
	$expires = get_option( 'mycred-premium-' . MYCRED_VIDEO_SLUG . '-expires', '' );
	if(empty($expires)){
		$args = new stdClass;
		$args->slug = MYCRED_VIDEO_SLUG;
		$args->version = MYCRED_VIDEO_VERSION;
		$args->site = site_url();
		
		$action = '';
		$result = '';   
		plugin_api_call_mycred_videos( $result, $action, $args );
		
		$expires = get_option( 'mycred-premium-' . MYCRED_VIDEO_SLUG . '-expires', '' );
	}
	if ( $expires != '' ) {

		if ( $expires == 'never' )
			$plugin_meta[] = 'Unlimited License';

		elseif ( absint( $expires ) > 0 ) {

			$days = ceil( ( $expires - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );
			if ( $days > 0 )
				$plugin_meta[] = sprintf(
					'License Expires in <strong%s>%s</strong>',
					( ( $days < 30 ) ? ' style="color:red;"' : '' ),
					sprintf( _n( '1 day', '%d days', $days ), $days )
				);

			$renew = get_option( 'mycred-premium-' . MYCRED_VIDEO_SLUG . '-renew', '' );
			if ( $days < 30 && $renew != '' )
				$plugin_meta[] = '<a href="' . esc_url( $renew ) . '" target="_blank" class="">Renew License</a>';

		}

	}

	else $plugin_meta[] = '<a href="http://mycred.me/about/terms/#product-licenses" target="_blank">No license found for - ' . $url . '</a>';
	}
	return $plugin_meta;

}

function MYCRED_VIDEO_calculate_license_expire_time( $expire_date ) {
		
	$date1=date_create(date('Y-m-d'));
	$date2=date_create($expire_date);
	$diff=date_diff($date1,$date2);
	$sign = $diff->format("%R");
	return $diff->format("%a days");
}

function MYCRED_VIDEO_is_subscription_expired($expire_date) {
	$date1=date_create(date('Y-m-d'));
	$date2=date_create($expire_date);

	if( $date1 > $date2 )
		return true;
	else
		return false;
}

function MYCRED_VIDEO_is_membership_plugin() {
	
	$plugin_details = get_transient('MYCRED_VIDEO');
	if( !empty($plugin_details) ) {
		return $plugin_details;
	}

	$user_id = get_current_user_id();
    $url = 'https://mycred.me/wp-json/membership/v1/member/'.mycred_get_my_id($user_id);
    $data = wp_remote_get($url);

	$api_response = json_decode( $data['body'], true );
	set_transient('MYCRED_VIDEO', $api_response, WEEK_IN_SECONDS);

	return $api_response;
}

function MYCRED_VIDEO_get_folder_name() {
	$plugin_folder = plugin_basename( MYCRED_VIDEO );
	$plugin_folder = explode('/',$plugin_folder);
	return $plugin_folder[0];
}