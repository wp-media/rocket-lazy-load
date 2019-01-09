=== Lazy Load by WP Rocket ===
Contributors: creativejuiz, tabrisrp, wp_media
Tags: lazyload, lazy load, images, iframes, thumbnail, thumbnails, smiley, smilies, avatar, gravatar, youtube
Requires at least: 4.7
Tested up to: 5.0.2
Requires PHP: 5.4
Stable tag: 2.0.2

Lazy Load your images and iframes, replace Youtube videos by a preview thumbnail.

== Description ==

Lazy Load by WP Rocket displays images and/or iframes on a page only when they are visible to the user. This reduces the number of HTTP requests mechanism and improves the loading time.

This plugin works on thumbnails, all images in a post content or in a widget text, avatars, smilies and iframes. No JavaScript library such as jQuery is used and the script weight is less than 10KB.

You can also replace Youtube iframes by a preview thumbnail to further speed up the loading time of your website.

= Dependencies =

Lazyload script: https://github.com/verlok/lazyload

= Related Plugins =
* <a href="https://wordpress.org/plugins/imagify/">Imagify</a>: Best Image Optimizer to speed up your website with lighter images.
* <a href="https://wp-rocket.me">WP Rocket</a>: Best caching plugin to speed-up your WordPress website.

== Installation ==

1. Upload the complete `rocket-lazy-load` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How can I deactivate Lazy Load on some pages? = 

You can use the `do_rocket_lazyload` filter.

Here is an example to put in functions.php files that disable lazyload on posts:

`
add_action( 'wp', 'deactivate_rocket_lazyload_on_single' );
function deactivate_rocket_lazyload_on_single() {
	if ( is_single() ) {
		add_filter( 'do_rocket_lazyload', '__return_false' );
	}
}
`

= How can I deactivate Lazy Load on some images? = 

Simply add a `data-no-lazy="1"` property in you `img` or `iframe` tag.

You can also use the filters `rocket_lazyload_excluded_attributes` or `rocket_lazyload_excluded_src` to exclude specific patterns.

= How can I change the threshold to trigger the load? =

You can use the `rocket_lazyload_threshold` filter.

Code sample:

`
function rocket_lazyload_custom_threshold( $threshold ) {
	return 100;
}
add_filter( 'rocket_lazyload_threshold', 'rocket_lazyload_custom_threshold' );
`

= I use plugin X and my images don't show anymore =

Some plugins are not compatible without lazy loading. Please open a support thread, and we will see how we can solve the issue by excluding lazy loading for this plugin.

= How can I lazyload a background-image? =

The element you want to apply lazyload on must have this specific markup:

`<div class="rocket-lazyload-bg" data-bg="url(../img/image.jpg)"></div>`

The element must have the class `rocket-lazyload-bg`, and a `data-bg` attribute, which value is the CSS url for the image.

== Changelog ==
= 2.0.2 =
* Bugfix: Fix an error in the compatibility for the AMP plugin

= 2.0.1 =
* Bugfix: Prevent a fatal error on case sensitive operating systems

= 2.0 =
* Enhancement: Lazyload is now applied on the template_redirect hook, which should allow the plugin to apply the optimization on more images and encountering less conflicts at the same time
* Enhancement: Specifically target with the lazyload script images/iframes elements with a data-lazy-src attribute
* Enhancement: Update lazyload script to the latest version
* Enhancement: Possibility to apply lazyload on background-images with a specific markup, see FAQ
* Enhancement: Use a svg image as placeholder instead of a base64 gif
* Bugfix: Only use MutationObserver if available in the browser
* Bugfix: When using the Youtube thumbnail option, correctly format the Youtube query if the video URL is encoded 
* Bugfix: Improve iframe matching to prevent unexpected results
* Bugfix: Update CSS for the Youtube thumbnail option to prevent issue with the Gutenberg embeds block

= 1.4.9 =
* Enhancement: Update lazyload script to the latest available version
* Enhancement: Use lazy-sizes to prevent W3C validation error when sizes is defined but srcset is not
* Enhancement: Parse images or iframes only if the element is selected to be lazyloaded in the options
* Fix: Prevent warning for lazyload+v in Google Search Console
* Fix: Prevent PHP Notice with WooCommerce for product images

= 1.4.8 =
* Notice: Minimum WordPress version required is now 4.7
* Enhancement: Update lazyload script version
* Enhancement: Remove placeholder image to improve perceived loading time
* Enhancement: Compatibility with Youtube privacy URL
* Enhancement: Update play image to match Youtube logo
* Enhancement: Support Youtube URL parameters
* Enhancement: Lazyload images displayed with wp_get_attachment_image(). /!\ no fallback if JavaScript is disabled
* Fix: Use the correct size set in srcset for the lazyloaded image
* Fix: Prevent Youtube thumbnail replacement on playlists
* Fix: Prevent iframe lazyload on AMP pages
* Fix: Correct text domain for translations (thanks @ Chantal Coolsma)

= 1.4.7 =
* Fix compatibility with infinite scroll
* Prevent lazyload on masterSlider images

= 1.4.6 =
* Correctly include version 8.5.2 of lazyload script
* Prevent 404 error on lazyload script if URL contains "-v"

= 1.4.5 =
* Rename Setting Page Name in WP Menu
* New Product Banner in Settings Page
* Conditionally load a different version of the script depending on browser support of IntersectionObserver
* Fix a bug where images initially hidden are not correctly displayed when coming into view (slider, tabs, accordion)

= 1.4.4 =
* Admin Redesign

= 1.4.3 =
* Plugin is compatible again with PHP < 5.4

= 1.4.2 =
* Update lazyload script to bring back compatibility with IE9/10

= 1.4.1 =
* Fix bug caused by a too aggressive cleanup

= 1.4 =
* New option: replace Youtube videos by thumbnail. This option can improve your loading time a lot, especially if you have multiple videos on the same page

= 1.3.3 =
* 2017-09-16
* Prevent scripts and styles being removed during html parsing

= 1.3.2 =
* 2017-09-12
* Fix images not displaying in certain conditions because image attributes exclusion was not working correctly

= 1.3.1 =
* 2017-09-07
* Don't apply lazyload on Divi slider

= 1.3 =
* 2017-09-01
* Improve HTML parsing of images and iframes to be faster and more efficient
* Make the lazyload compatible with fitVids for iframes
* Don't apply lazyload on AMP pages (compatible with AMP plugin from Automattic)
* Use about:blank as default iframe placeholder to prevent warning in browser console
* Don't apply lazyload on upPrev thumbnail

= 1.2.1 =
* 2017-08-22
* Fix missing lazyload script
* Don't lazyload for images in REST API requests

= 1.2 =
* 2017-08-22
* Update lazyload script to latest version
* Change the way the script is loaded

= 1.1.1 =
* 2017-02-13
* Bug fix: Remove use of short tag to prevent 500 error on some installations

= 1.1 =
* 2017-02-12
* *New*
 * JS library updated
 * Support for iFrame
 * Support for srcset and sizes
 * New options page

= 1.0.4 =
* 2015-04-28
* Bug Fix: Resolved a conflict between LazyLoad & Emoji since WordPress 4.2

= 1.0.3 =
* 2015-01-08
* Bug Fix: Don't apply LazyLoad on captcha from Really Simple CAPTCHA to prevent conflicts.

= 1.0.2 =
* 2014-12-28
* Improvement: Add « rocket_lazyload_html » filter to manage the output that will be printed. 

= 1.0.1.1 =
* 2014-07-25
* Fix stupid error with new regex in 1.0.1

= 1.0.1 =
* 2014-07-16
* Bug Fix: when a IMG tag or content (widget or post) contains the string "data-no-lazy", all IMG tags were ignored instead of one.
* Security fix: The preg_replace() could lead to a XSS vuln, thanks to Alexander Concha
* Code compliance

= 1.0 =
* 2014-01-01
* Initial release.
