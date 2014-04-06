<?php
/**
 * Plugin Name: GitHub API
 * Plugin URI: https://github.com/gedex/wp-github-api
 * Description: This plugin contains easy-to-use API that uses WP HTTP API to make a request to GitHub API v3 resources.
 * Version: 0.1.0
 * Author: Akeda Bagus
 * Author URI: http://gedex.web.id
 * Text Domain: github-api
 * Domain Path: /languages
 * License: GPL v2 or later
 * Requires at least: 3.6
 * Tested up to: 3.8
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

require_once __DIR__ . '/includes/autoloader.php';

// Register the autoloader. The first param is a prefix of class
// name that can be loaded. The second param is directory path where
// autoloader will look for classes.
WP_GitHub_API_Autoloader::register( 'WP_GitHub_API', trailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/' );

// Runs this plugin. We only expose one global from this plugin
// which is instance of WP_GitHub_API_Plugin. Instance of WP_GitHub_API_Plugin
// is a container to various components (instances of classes in includes dir).
$GLOBALS['wp_github_api'] = new WP_GitHub_API_Plugin();
$GLOBALS['wp_github_api']->run( __FILE__ );
