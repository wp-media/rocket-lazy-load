=== Rocket Lazy Load ===
Contributors: juliobox, geekpress, wp_media
Tags: lazyload, lazy load, images, thumbnail, thumbnails, smiley, smilies, avatar, gravatar
Requires at least: 3.0
Tested up to: 4.7.1
Stable tag: 1.1.1

The tiny Lazy Load script for WordPress without jQuery or others libraries.

== Description ==

Lazy Load displays images on a page only when they are visible to the user. This reduces the number of HTTP requests mechanism and improves the loading time.

This plugin works on thumbnails, all images in a post content or in a widget text, avatars and smilies. No JavaScript library such as jQuery is used and the script weight is less than 6kb.

Simply install the plugin to enjoy a faster website. No options are available: you install it and the plugin takes care of everything.

= Related Plugins =
* <a href="https://wordpress.org/plugins/imagify/">Imagify</a>: Best Image Optimizer to speed up your website with lighter images.
* <a href="http://wp-rocket.me">WP Rocket</a>: Best caching plugin to speed-up your WordPress website.

== Installation ==

1. Upload the complete `rocket-lazy-load` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How can i deactivate Lazy Load on some pages? = 

You can use <em>do_rocket_lazyload</em> filter.

Here, an example to put in functions.php files :
`
add_action( 'wp', 'deactivate_rocket_lazyload_on_single' );
function deactivate_rocket_lazyload_on_single() {
	if ( is_single() ) {
		add_filter( 'do_rocket_lazyload', '__return_false' );
	}
}
`

= How can i deactivate Lazy Load on some images? = 

Simply add a 'data-no-lazy="1"' property in you IMG tag.

== Changelog ==
= 1.1.1 =
* 13 feb. 2017
* Bug fix: Remove use of short tag to prevent 500 error on some installations

= 1.1 =
* 12 feb. 2017
* *New*
 * JS library updated
 * Support for iFrame
 * Support for srcset and sizes
 * New options page

= 1.0.4 =
* 28 apr. 2015
* Bug Fix: Resolved a conflict between LazyLoad & Emoji since WordPress 4.2

= 1.0.3 =
* 08 jan. 2015
* Bug Fix: Don't apply LazyLoad on captcha from Really Simple CAPTCHA to prevent conflicts.

= 1.0.2 =
* 28 dec. 2014
* Improvement: Add « rocket_lazyload_html » filter to manage the output that will be printed. 

= 1.0.1.1 =
* 25 jul. 2014
* Fix stupid error with new regex in 1.0.1

= 1.0.1 =
* 16 jul. 2014
* Bug Fix: when a IMG tag or content (widget or post) contains the string "data-no-lazy", all IMG tags were ignored instead of one.
* Security fix: The preg_replace() could lead to a XSS vuln, thanks to Alexander Concha
* Code compliance

= 1.0 =
* 01 jan. 2014
* Initial release.
