<?php

class WP_GitHub_API_Tools {

	/**
	 * Capability needed to access tools page.
	 *
	 * @var setting
	 */
	private $cap = 'manage_options';

	/**
	 * @var WP_GitHub_API_Plugin
	 */
	private $plugin;

	public function __construct( WP_GitHub_API_Plugin $plugin ) {
		$this->plugin = $plugin;

		add_action( 'admin_menu', array( $this, 'create_setting_menu' ) );
	}

	/**
	 * Create menu and page for plugin tools.
	 *
	 * @action admin_menu
	 */
	public function create_setting_menu() {
		add_management_page(
			__( 'GitHub API Tools', 'github-api' ), // Page title.
			__( 'GitHub API',       'github-api' ), // Menu title.

			// Cap to view the tools.
			$this->cap,

			// Menus slug.
			$this->plugin->name . '_tools',

			// Page setting renderer.
			array( $this, 'render_tools_page' )
		);
	}

	public function render_tools_page() {
		if ( ! current_user_can( $this->cap ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'github-api' ) );
		}

		// Declare vars to make it available on view.
		$page  = $this->plugin->name . '_tools';
		$title = __( 'GitHub API Tools', 'github-api' );
		$tools = $this->get_tools();

		// If tool page.
		if ( isset( $_REQUEST['tool'] ) && isset( $tools[ $_REQUEST['tool'] ] ) ) {
			$tool = $tools[ $_REQUEST['tool'] ];

			if ( isset( $tool['onrender'] ) && is_callable( $tool['onrender'] ) ) {
				call_user_func( $tool['onrender'], $tool );
			}
			require $this->plugin->views_path . 'tool.php';
		} else {
			require $this->plugin->views_path . 'tools.php';
		}
	}

	private function get_tools() {
		return array(
			'get_user_info' => array(
				'title'    => __( 'User Information', 'github-api' ),
				'desc'     => __( 'Makes a call to "/users/gedex" to get GitHub user with login "gedex".', 'github-api' ),
				'callback' => array( $this, 'get_user_info' ),
				'view'     => $this->plugin->views_path . 'tool_get_user_info.php',
			),
			'get_org_info' => array(
				'title'    => __( 'Org Information', 'github-api' ),
				'desc'     => __( 'Makes a call to "/orgs/Automattic" to get GitHub organization named "Automattic".', 'github-api' ),
				'callback' => array( $this, 'get_org_info' ),
				'view'     => $this->plugin->views_path . 'tool_get_org_info.php',
			),
			'get_repo_info' => array(
				'title'    => __( 'Repository Information', 'github-api' ),
				'desc'     => __( 'Makes a call to "/repos/WordPress/WordPress" to get GitHub repository named "WordPress".', 'github-api' ),
				'callback' => array( $this, 'get_repo_info' ),
				'view'     => $this->plugin->views_path . 'tool_get_repo_info.php',
			),
			'search_repositories' => array(
				'title'    => __( 'Search Repositories', 'github-api' ),
				'desc'     => __( 'Makes a call to "/search/repositories?q=WordPress+plugin" to search repositories that matches "WordPress plugin".', 'github-api' ),
				'callback' => array( $this, 'search_repositories' ),
				'view'     => $this->plugin->views_path . 'tool_search_repositories.php',
			),
			'weekly_commit_count' => array(
				'title'    => __( 'Repo Weekly Commit Count', 'github-api' ),
				'desc'     => __( 'Makes a call to "/repos/WordPress/WordPress/stats/participation" and render the stats into d3js bar chart.', 'github-api' ),
				'callback' => array( $this, 'weekly_commit_count' ),
				'onrender' => array( $this, 'weekly_commit_count_onrender' ),
				'view'     => $this->plugin->views_path . 'tool_weekly_commit_count.php',
			),
		);
	}

	public function get_user_info() {
		return $this->get( 'users/gedex' );
	}

	public function get_org_info() {
		return $this->get( 'orgs/Automattic' );
	}

	public function get_repo_info() {
		return $this->get( 'repos/WordPress/WordPress' );
	}

	public function search_repositories() {
		return $this->get( 'search/repositories', array(
			'parameters' => array(
				'q' => urlencode( 'WordPress plugin' ),
			),
		) );
	}

	public function weekly_commit_count() {
		return $this->get( 'repos/WordPress/WordPress/stats/participation' );
	}

	public function weekly_commit_count_onrender( $tool ) {
		$this->enqueue_d3js();

		global $wp_scripts;
		$exported = sprintf(
			'
				var chartData = %s;
			',
			json_encode( call_user_func( $tool['callback'] ) )
		);
		$wp_scripts->add_data( 'github-api-d3js', 'data', $exported );

		wp_enqueue_script(
			'github-api-weekly_commit_count',

			$this->plugin->plugin_url . 'js/tool-chart-repo-weekly-commit-count.js',

			array( 'github-api-d3js' ),

			filemtime( $this->plugin->plugin_path . 'js/tool-chart-repo-weekly-commit-count.js' ),

			// In footer.
			true
		);
	}

	public function get( $endpoint, $params = array() ) {
		$resp   = $this->plugin->client->request( 'GET', $endpoint, $params );
		$status = intval( wp_remote_retrieve_response_code( $resp ) );

		if ( 200 === $status ) {
			return json_decode( wp_remote_retrieve_body( $resp ) );
		} else {
			return null;
		}
	}

	public function enqueue_d3js() {
		// Enqueue d3js for rendering chart.
		wp_enqueue_script(
			// Handle.
			'github-api-d3js',

			// Src.
			$this->plugin->plugin_url . 'js/d3.min.js',

			// Dependencies.
			array(),

			// Version.
			filemtime( $this->plugin->plugin_path . 'js/d3.min.js' )
		);
	}
}
