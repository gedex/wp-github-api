<?php
/**
 * Gets rate limit informations.
 */
class WP_GitHub_API_Rate_Limit {

	/**
	 * @var WP_GitHub_API_Plugin
	 */
	private $plugin;

	public function __construct( WP_GitHub_API_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Gets rate limit.
	 *
	 * Exception is not caught.
	 */
	public function get() {
		$client = $this->plugin->client;

		// Makes a request.
		$resp = $client->request( 'GET', 'rate_limit' );

		if ( 200 === intval( wp_remote_retrieve_response_code( $resp ) ) ) {
			$resp = (array) json_decode( wp_remote_retrieve_body( $resp ), true );
			if ( isset( $resp['resources'] ) ) {
				return $resp['resources'];
			}
			return $resp;
		} else {
			$message = 'Unexpected result when tried to call  "/rate_limit" endpoint.';
			$resp    = (array) json_decode( wp_remote_retrieve_body( $resp ) );

			if ( isset( $resp['message'] ) ) {
				$message = $resp['message'];
			}

			throw new Exception( $message );
		}

		return null;
	}
}
