<?php
/**
 * This is the plugin class and acts as container for component instances and
 * basic properties of a plugin. Using container like this will avoid poluting
 * global namespaces. There's no global constants and only one global object
 * defined, that's this class' instance.
 */
class WP_GitHub_API_Plugin {

	/**
	 * @var array
	 */
	private $items = array();

	/**
	 * @param string $path Path to main plugin file
	 */
	public function run( $path ) {
		// Basic plugin information.
		$this->name    = 'wp_github_api'; // This maybe used to prefix options, slug of menu or page, and filters/actions.
		$this->version = '0.1.0';

		// Path.
		$this->plugin_path   = trailingslashit( plugin_dir_path( $path ) );
		$this->plugin_url    = trailingslashit( plugin_dir_url( $path ) );
		$this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
		$this->views_path    = $this->plugin_path . trailingslashit( 'views' );

		// Instances.
		$this->setting    = new WP_GitHub_API_Setting( $this );
		$this->client     = new WP_GitHub_API_Client( $this );
		$this->rate_limit = new WP_GitHub_API_Rate_Limit( $this );
		$this->authorizer = new WP_GitHub_API_Authorizer( $this );
		$this->tools      = new WP_GitHub_API_Tools( $this );
	}

	public function __set( $key, $value ) {
		$this->items[ $key ] = $value;
	}

	public function __get( $key ) {
		if ( isset( $this->items[ $key ] ) ) {
			return $this->items[ $key ];
		}

		return null;
	}

	public function __isset( $key ) {
		return isset( $this->items[ $key ] );
	}

	public function __unset( $key ) {
		if ( isset( $this->items[ $key ] ) ) {
			unset( $this->items[ $key ], $this->raws[ $key ], $this->frozen[ $key ] );
		}
	}
}
