<?php
/**
 * GitHub API v3 client.
 *
 * @see https://developer.github.com/v3/
 */
class WP_GitHub_API_Client {

	/**
	 * GitHub API Base URL.
	 */
	const BASE_URL = 'https://api.github.com';

	/**
	 * GitHub OAuth Base URL.
	 */
	const BASE_AUTH_URL = 'https://github.com/login';

	/**
	 * @var WP_GitHub_API_Plugin
	 */
	private $plugin;

	/**
	 * Client settings.
	 *
	 * @var array
	 */
	private $settings;

	public function __construct( WP_GitHub_API_Plugin $plugin ) {
		$this->plugin = $plugin;

		$this->settings = apply_filters( 'wp_github_api_client_settings', array(
			// App related settings.
			'client_id'     => $this->plugin->setting->get( 'client_id' ),
			'client_secret' => $this->plugin->setting->get( 'client_secret' ),
			'access_token'  => $this->plugin->setting->get( 'access_token' ),
			'redirect_uri'  => admin_url( 'options-general.php?page=' . $this->plugin->name ),
			'code'          => isset( $_REQUEST['code'] ) ? $_REQUEST['code'] : '', // Received after GitHub redirects to `redirect_uri`.
			'state'         => isset( $_REQUEST['state'] ) ? $_REQUEST['state'] : '', // Nonce to protect against CSRF issues.
			'scope'         => 'user:email', // See https://developer.github.com/v3/oauth/#scopes

			// Request headers.
			'headers' => array(
				'User-Agent' => $this->plugin->name . ' v' . $this->plugin->version,
				'Accept'     => 'application/json',
			),

			// Request body.
			'body' => '',

			// Request params.
			'parameters' => array(),
		) );
	}

	/**
	 * Get authorization URL.
	 *
	 * @param  array $args
	 * @return string Authorization URL
	 */
	public function get_authorize_url( array $args = array() ) {
		$args = wp_parse_args( $args, array(
			'client_id'    => $this->settings['client_id'],
			'redirect_uri' => urlencode( $this->settings['redirect_uri'] ),
			'scope'        => $this->settings['scope'],
			'state'        => wp_create_nonce( $this->plugin->name . '_authorize_nonce' ),
		) );

		return add_query_arg( $args, self::BASE_AUTH_URL . '/oauth/authorize' );
	}

	/**
	 * Verify state.
	 *
	 * @param  string $nonce
	 * @return bool   True if verified
	 */
	public function verify_state( $nonce ) {
		return wp_verify_nonce( $nonce, $this->plugin->name . '_authorize_nonce' );
	}

	/**
	 * Makes a request to GitHub API.
	 *
	 * @param string $http_method GET|POST|PUT|DELETE
	 * @param string $endpoint    Endpoint like '/users/gedex'
	 * @param array  $settings    Override client settings with this param
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	public function request( $http_method, $endpoint, $settings = array() ) {
		$settings = wp_parse_args( $settings, $this->settings );

		if ( isset( $endpoint[0] ) && '/' !== $endpoint[0] ) {
			$endpoint = '/' . $endpoint;
		}

		if ( strpos( $endpoint, 'oauth' ) ) {
			$url = self::BASE_AUTH_URL . $endpoint;
		} else {
			$url = self::BASE_URL . $endpoint;
		}

		$http_method = strtoupper( $http_method );
		if ( ! in_array( $http_method, array( 'GET', 'POST', 'PUT', 'DELETE' ) ) ) {
			$http_method = 'GET';
		}

		switch ( $endpoint ) {
			case '/oauth/access_token':
				// Only client_id, client_secret, code, and redirect_uri
				$params = array_intersect_key(
					$settings,
					array_fill_keys( array( 'client_id', 'client_secret', 'code', 'redirect_uri' ), '' )
				);
				break;

			default:
				// Removes unnecessary params when make a call to non-OAuth endpoints.
				$params = array_diff_key(
					$settings,
					array_fill_keys( array( 'code', 'state', 'scope', 'redirect_uri', 'headers', 'body', 'parameters' ) , '' )
				);
		}
		$params = wp_parse_args( $params, $settings['parameters'] );
		if ( empty( $params['access_token'] ) ) {
			unset( $params['access_token'] );
		} else {
			unset( $params['client_id'] );
			unset( $params['client_secret'] );
		}

		switch ( $http_method ) {
			case 'POST':
				$settings['body'] = $params;
			default:
				$url = add_query_arg( $params, $url );
		}

		return wp_remote_request( $url, array(
			'method'  => $http_method,
			'headers' => $settings['headers'],
			'body'    => $settings['body'],
		) );
	}

	public function __set( $key, $value ) {
		$this->settings[ $key ] = $value;
	}

	public function __get( $key ) {
		if ( isset( $this->settings[ $key ] ) ) {
			return $this->settting[ $key ];
		}

		return null;
	}

	public function __isset( $key ) {
		return isset( $this->settings[ $key ] );
	}

	public function __unset( $key ) {
		if ( isset( $this->settings[ $key ] ) ) {
			unset( $this->settings[ $key ] );
		}
	}
}
