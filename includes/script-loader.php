<?php

class WP_GitHub_API_Script_Loader {

	/**
	 * @var WP_GitHub_API_Plugin
	 */
	private $plugin;

	public function __construct( WP_GitHub_API_Plugin $plugin ) {
		$this->plugin = $plugin;

		add_action( 'init', array( $this, 'register_scripts' ) );
	}

	public function register_scripts() {
		// Shortcut.
		$plugin = $this->plugin;

		wp_register_script( 'gh-d3', $plugin->js_url . 'd3.min.js', array(), $plugin->version );

		wp_register_style( 'gh-octicons', $plugin->css_url . 'octicons.min.css', array(), $plugin->version, 'all' );
	}
}
