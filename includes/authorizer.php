<?php
class WP_GitHub_API_Authorizer {

	/**
	 * @var WP_GitHub_API_Plugin
	 */
	private $plugin;

	public function __construct( WP_GitHub_API_Plugin $plugin ) {
		$this->plugin = $plugin;

		add_action( 'load-settings_page_' . $this->plugin->name, array( $this, 'on_page_setting_load' ) );
	}

	/**
	 * Perform Web Application Flow.
	 *
	 * @action load-{setting_page}
	 *
	 * @see https://developer.github.com/v3/oauth/#web-application-flow
	 */
	public function on_page_setting_load() {
		$redirect_uri = admin_url( 'options-general.php?page=' . $this->plugin->name );

		// Check error message.
		$this->check_error_message();

		// Shortcut for plugin instance.
		$plugin = $this->plugin;

		// Shortcut to client instance.
		$client = $plugin->client;

		// Check if GitHub is redirecting back.
		if ( isset( $_REQUEST['code'] ) && isset( $_REQUEST['state'] ) ) {
			// Verify the state.
			if ( ! $client->verify_state( $_REQUEST['state'] ) ) {
				$message = __( 'Failed to verify state', 'github-api' );
				$this->redirect_on_error( $redirect_uri, $message );
			}

			$client->code = $_REQUEST['code'];

			$resp   = $client->request( 'POST', 'oauth/access_token' );
			$status = intval( wp_remote_retrieve_response_code( $resp ) );

			if ( 200 === $status ) {
				/**
				 * @var object $token
				 */
				$token = json_decode( wp_remote_retrieve_body( $resp ) );

				$plugin->setting->set( 'access_token', $token->access_token );

				// We successfully got access token, redirect to setting page
				// with query string from GitHub removed.
				wp_redirect( $redirect_uri );
				exit();

			} else if ( is_wp_error( $resp ) ) {
				// Error during request.

				$message = '<p><strong>WP HTTP Error:</strong> ' . $resp->get_error_message() . '</p>';
				$plugin->setting->delete( 'access_token' );

			} else {
				// Unexpected response.

				$message = wp_remote_retrieve_body( $resp );

				$plugin->setting->delete( 'access_token' );

				$message = sprintf(
					'<p><strong>Status code %s:</strong> %s.</p>',
					$status,
					$message
				);
			}

			$this->redirect_on_error( $redirect_uri, $message );
		}
	}

	/**
	 * Redirect if error happens. Temporary message is is stored in option.
	 *
	 * @uses `wp_redirect`
	 *
	 * @param string $location Location to redirect
	 * @param string $message  Message to shown in notices bar after redirect
	 *
	 * @return void
	 */
	private function redirect_on_error( $location, $message = '' ) {
		// Stores the error message.
		$this->plugin->setting->set( 'error_message', $message );

		// Redirect it with error key indicator appended in URL.
		$location = add_query_arg( 'got_error', 1, $location );
		wp_redirect( $location );
		exit();
	}

	/**
	 * Check if error indicator appears in query string and hook it into `admin_notices`
	 * action if error needs to be noticed.
	 */
	private function check_error_message() {
		$message = $this->plugin->setting->get( 'error_message' );

		if ( isset( $_REQUEST['got_error'] ) && ! empty( $message ) ) {
			$plugin = $this->plugin;
			add_action( 'admin_notices', function() use( $plugin, $message ) {
				printf( '<div class="error">%s</div>', $message );
				$plugin->setting->delete( 'error_message' );
			} );
		}
	}

}
