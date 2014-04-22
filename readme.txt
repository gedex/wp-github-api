=== GitHub API ===
Contributors:      akeda
Donate link:       http://goo.gl/DELyuR
Tags:              github, api, oauth, rest, http
Requires at least: 3.6
Tested up to:      3.9
Stable tag:        trunk
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

This plugin contains easy-to-use API that uses WP HTTP API to make a request to GitHub API v3 resources.

== Description ==

This plugin doesn't contain any feature (widget or shortcode) that you can use directly once the plugin is activated. This
plugin contains API to help you in making a call to GitHub API resources. There's `includes/tools.php` file that show
you how to use the API from this plugin to make a call to various GitHub resources (for instance to get repository information).

**Development of this plugin is done on [GitHub](https://github.com/gedex/wp-github-api). Pull requests are always welcome**.

If you're looking for widget or shortcode you can try following plugins (which depend on GitHub API plugin):

* [GitHub Profile Widget](https://github.com/gedex/wp-gh-profile-widget)
* [GitHub Members Show-off](https://github.com/gedex/wp-gh-members-showoff)

== Installation ==

1. Upload **GitHub API** plugin to your blog's `wp-content/plugins/` directory and activate.
1. There's **Settings** > **GitHub API** to view setting of the plugin.
1. There's **Tools** > **GitHub API** to test a call on GitHub resources.

== Screenshots ==

1. Settings page
1. Tools page
1. Tool as an example to search repositories
1. Tool as an example to render weekly commit count into a bar chart

== Changelog ==

= 0.4.0 =
* Supports pagination

= 0.3.0 =
* Moves CSS/JS into assets directory
* Register 'gh-d3' and 'gh-octicons'

= 0.2.0 =
Provides `github_api_init` hook

= 0.1.0 =
Initial release
