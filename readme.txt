=== WHO Team Manager ===
Contributors: chrisfromthelc
Tags: team, members, grid, staff, custom post type
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.1
License: MIT
License URI: https://opensource.org/licenses/MIT

A simple custom post type for team members with a configurable, drag-and-drop sortable grid block.

== Description ==

WHO Team Manager provides a Team Members custom post type and a Gutenberg block for displaying them in a responsive grid layout.

**Key Features:**

* Team Member custom post type with Name, Job Title, Description, and Photo
* Gutenberg block with configurable grid columns (1-6)
* Drag-and-drop ordering per block instance
* Responsive layout: collapses to 2 columns on tablet, 1 on mobile
* Minimal CSS to inherit theme styles
* Auto-updates from GitHub Releases

== Installation ==

1. Upload the `who-team-manager` folder to `wp-content/plugins/`
2. Activate the plugin through the Plugins menu in WordPress
3. Navigate to **Team Members** to add your team

== Frequently Asked Questions ==

= How do I change the display order? =

Edit the page containing the Team Grid block and drag members to reorder them. The order is saved per block instance.

= Can I have different orders on different pages? =

Yes. Each Team Grid block maintains its own member order.

= Does this create a public archive page? =

No. Team members are only displayed via the Team Grid block. They have no individual front-end pages.

== Screenshots ==

1. Team Grid block in the editor with drag-and-drop ordering
2. Frontend grid display

== Changelog ==

= 1.0.1 =
* Release ZIPs are now built and tested automatically before each release
* No changes to plugin behavior

= 1.0.0 =
* Initial release
* Team Member custom post type
* Team Grid block with drag-and-drop sorting
* Responsive grid layout
* GitHub Releases auto-updater

== Upgrade Notice ==

= 1.0.0 =
Initial release.
