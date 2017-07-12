=== Kunze Law ===
Contributors: kunzemarketing
Tags: remote, content, shortcode, embed, import
Requires at least: 4.7
Tested up to: 4.8
Stable tag: 1.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Kunze Law Plug-In import remote content into a post, page or widget using a shortcode.

== Description ==

Use the Kunze Law Plug-In to import content (HTML snippets) from a central remote Address into a post or page using a shortcode. This is very beneficial in case you deliver content for several
WordPress installations which needs a periodical update. The Plug-In will cache the remote content locally. After a predefined time, which can be set within the Plug-In, the snippet will be
pulled again. The big advantage: In order to get all websites updated, you only have to edit the snippet on the remote server. 

###Example:

Imagine you are using the Plugin and a HTML snippet in hundreds of your websites. In case of content changes you only have to edit the snippet on the remote server. After expiry of the set cache time the new
content will be pulled from the remote server. No need to do this manually on every website.


###How to start:

1. Set up the remote server. Choose the url of your remote server.
2. If you would like to receive an E-mail in case of remote access problems, please activate the "Get E-Mail Error Message checkbox". Please provide a valid "E-Mail Error From Address" value.   
3. Create HTML snippets. A snippet is a html file. The content of the snippet will be delivered. Always use yourname.html as the file name. "yourname" can be replaced to your need.
4. Upload the snippets onto the remote server. 
5. Install the Plugin and fill in the following information:
	* Remote Server. e.g. http://yourserver.com/content
	* Cache time in seconds. This is the time in seconds after the plugin will look for updated content on the remote server.
6. In order to get the content of a snippet named **facebook.html**, you have to use `[kunze_facebook]` as the shortcode.
7. Embed the shortcode within WordPress. This works in pages, posts and widgets.

	
###Naming convention:

Shortcode within WordPress: `[kunze_yourname]`
HTML Snippet on remote Server: **yourname.html**

Example of a HTML snippet:

```<div class="myClass"><strong>lorem ipsum</strong></div>```

###The Kunze Law..

* in case of problems with the remote server, a message can be send to the WordPress administration email address. (Settings > General > E-Mail Address). The email is send only once.
* will cache locally your HTML snippets. If the remote server does not work any longer - the local saved snippets will be served.
* works within a WordPress Multisite installation
* is based on regular expressions



== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/kunze-law` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Kunze Law setting screen to configure the plugin



== Frequently Asked Questions ==

= What type of content will be served? =

HTML Snippets. A snippet is a html file.

= How to name HTML the snippets? =

Upload the snippets to a directory on your remote server. Use following naming convention: **yourname.html**
You can use any html code within the snippet.

= Where to put the shortcode? =

You can use the shortcodes within a page, a blog or widget. Use following naming convention: `[kunze_yourname]`
Replace "yourname" with your snippet name. 

= Could you give an example? =

**facebook.html** on the remote server can be included through the shortcode `[kunze_facebook]`




== Screenshots ==

1. Kunze Law Setting page.
2. HTML Shortcode Example. 



== Changelog ==

= 1.9 =
* Initial release.

