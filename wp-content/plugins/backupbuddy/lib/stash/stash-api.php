<?php

final class BackupBuddy_Stash_API {
	const API_URL = 'https://stash-api-2.ithemes.com';
	const LEGACY_API_URL = 'https://stash-api.ithemes.com';


	public static function send_file( $username, $token, $file ) {
		if ( ! self::can_send_file( $username, $token, $file ) ) {
			return false;
		}

		$settings = compact( 'username', 'token' );

		$params = array(
			'upload_files' => array(
				array(
					'var'  => 'upload',
					'file' => $file,
					'name' => basename( $file ),
				),
			),
		);

		$result = self::request( 'send-file', $settings, $params, true, false, HOUR_IN_SECONDS );

		if ( is_array( $result ) && isset( $result['success'] ) && $result['success'] ) {
			return true;
		}

		return $result;
	}

	public static function can_send_file( $username, $token, $file ) {
		$settings = compact( 'username', 'token' );

		$params = array(
			'file_size' => filesize( $file ),
			'file_name' => basename( $file ),
		);

		$result = self::request( 'can-send-file', $settings, $params );

		if ( is_array( $result ) && isset( $result['success'] ) && $result['success'] ) {
			return true;
		}

		return false;
	}

	public static function delete_files( $username, $token, $files ) {
		$settings = compact( 'username', 'token' );
		$params = compact( 'files' );

		$result = self::request( 'delete-files', $settings, $params );

		if ( is_array( $result ) && isset( $result['success'] ) && $result['success'] ) {
			return true;
		}

		return false;
	}

	public static function list_files( $username, $token ) {
		$settings = compact( 'username', 'token' );

		return self::request( 'files', $settings, array(), true, false, 600 );
	}

	public static function list_site_files( $username, $token ) {
		$settings = compact( 'username', 'token' );

		return self::request( 'site-files', $settings, array(), true, false, 600 );
	}

	public static function connect( $username, $password ) {
		$settings = compact( 'username', 'password' );

		return self::request( 'connect', $settings );
	}

	public static function disconnect( $username, $token, $password ) {
		$settings = compact( 'username', 'token', 'password' );

		return self::request( 'disconnect', $settings );
	}

	public static function request( $action, $settings, $params = array(), $blocking = true, $passthru_errors = false, $timeout = 60 ) {
		require_once( dirname( __FILE__ ) . '/http-request.php' );

		if ( 'live-put' === $action ) {
			$url = self::LEGACY_API_URL;
		} else {
			$url = self::API_URL;
		}

		$http = new BackupBuddy_HTTP_Request( $url, 'POST' );

		$http->set_timeout( $timeout );
		$http->set_blocking( $blocking );


		if ( isset( $settings['itxapi_username'] ) ) {
			$username = $settings['itxapi_username'];
		} else if ( isset( $settings['username'] ) ) {
			$username = $settings['username'];
		} else {
			$username = '';
		}


		$get_vars = array(
			'action'    => $action,
			'user'      => $username,
			'wp'        => $GLOBALS['wp_version'],
			'bb'        => pb_backupbuddy::settings( 'version' ),
			'site'      => str_replace( 'www.', '', site_url() ),
			'home'      => str_replace( 'www.', '', home_url() ),
			'timestamp' => time(),
		);
		$http->set_get_vars( $get_vars );


		$default_params = array();

		if ( isset( $settings['itxapi_password'] ) ) {
			$default_params['auth_token'] = $settings['itxapi_password'];
		} else if ( isset( $settings['password' ] ) ) {
			$default_params['auth_token'] = $settings['password'];
		}

		if ( isset( $settings['itxapi_token'] ) ) {
			$default_params['token'] = $settings['itxapi_token'];
		} else if ( isset( $settings['token'] ) ) {
			$default_params['token'] = $settings['token'];
		}

		$params = array_merge( $default_params, $params );
		$http->set_post_var( 'request', json_encode( $params ) );


		if ( isset( $params['upload_files'] ) ) {
			foreach ( $params['upload_files'] as $file ) {
				$http->add_file( $file['var'], $file['file'], $file['name'] );
			}

			unset( $params['upload_files'] );
		}

		$response = $http->get_response();


		if ( false === $blocking ) {
			return true;
		}

		if ( is_wp_error( $response ) ) {
			$error = 'Error #3892774: `' . $response->get_error_message() . '` connecting to `' . $http->get_built_url() . '`.';
			self::record_error( $error );

			if ( isset( $settings['type'] ) && 'live' == $settings['type'] ) {
				//backupbuddy_core::addNotification( 'live_error', 'BackupBuddy Stash Live Error', $error );
			}

			return $error;
		} else if ( null === ( $response_decoded = json_decode( $response['body'], true  ) ) ) {
			$error = 'Error #8393833: Unexpected server response: `' . htmlentities( $response['body'] ) . '` calling action `' . $action . '`. Full response: `' . print_r( $response, true ) . '`.';
			self::record_error( $error );
			return $error;
		} else if ( false === $passthru_errors && isset( $response_decoded['error'] ) ) {
			if ( isset( $response_decoded['error']['message'] ) ) {
				$error = 'Error #39752893d. Server reported an error performing action `' . $action . '` with additional params: `' . print_r( $params, true ) . '`. Body Details: `' . print_r( $response_decoded['error'], true ) . '`. Response Details: `' . print_r( $response['response'], true ) . '`.';
				self::record_error( $error );
				return $response_decoded['error']['message'];
			} else {
				$error = 'Error #3823973. Received Stash API error but no message found. Details: `' . print_r( $response_decoded, true ) . '`.';
				self::record_error( $error );
				return $error;
			}
		} else {
			return $response_decoded;
		}
	}

	private static function record_error( $message ) {
		global $pb_backupbuddy_destination_errors;

		$pb_backupbuddy_destination_errors[] = $message;
		pb_backupbuddy::status( 'error', 'Error #3892343283: ' . $message );
	}
}
