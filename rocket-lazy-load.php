<?php
defined( 'ABSPATH' ) or	die( 'Cheatin\' uh?' );

/**
 * Plugin Name: Rocket Lazy Load
 * Plugin URI: http://wordpress.org/plugins/rocket-lazy-load/
 * Description: The tiny Lazy Load script for WordPress without jQuery or others libraries.
 * Version: 1.1.1
 * Author: WP Media
 * Author URI: https://wp-rocket.me
 * Text Domain: rocket-lazy-load
 * Domain Path: /languages
 *
 * Copyright 2015 WP Media
 *
 * 	This program is free software; you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation; either version 2 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 * 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
define( 'ROCKET_LL_VERSION', '1.1' );
define( 'ROCKET_LL_PATH', realpath( plugin_dir_path( __FILE__ ) ) . '/' );
define( 'ROCKET_LL_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
define( 'ROCKET_LL_FRONT_JS_URL', ROCKET_LL_ASSETS_URL . 'js/' );
define( 'ROCKET_LL_JS_VERSION'  , '3.0' );


/**
 * Initialize the plugin.
 *
 * @since 1.1
 * @return void
 */
function rocket_lazyload_init() {
	load_plugin_textdomain( 'rocket-lazy-load', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	if ( is_admin() ) {
		require( ROCKET_LL_PATH . 'admin/admin.php' );
	}
}
add_action( 'plugins_loaded', 'rocket_lazyload_init' );

/**
 * A wrapper to easily get rocket lazyload option
 *
 * @since 1.1
 * @author Remy Perona
 *
 * @param string $option  The option name.
 * @param bool   $default (default: false) The default value of option.
 * @return mixed The option value
 */
function rocket_lazyload_get_option( $option, $default = false ) {
	$options = get_option( 'rocket_lazyload_options' );
	return isset( $options[ $option ] ) && '' !== $options[ $option ] ? $options[ $option ] : $default;
}

/**
 * Add Lazy Load JavaScript in the header
 * No jQuery or other library is required
 *
 * @since 1.0
 */
function rocket_lazyload_script() {
	if ( ! rocket_lazyload_get_option( 'images' ) && ! rocket_lazyload_get_option( 'iframes' ) || ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return;
	}

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$ll_url = ROCKET_LL_FRONT_JS_URL . 'lazyload-' . ROCKET_LL_JS_VERSION . $suffix . '.js';

	echo <<<HTML
	<script data-cfasync="false">(function(w,d){function loadScript(c,b){var a=d.createElement("script");a.async=!0;a.readyState?a.onreadystatechange=function(){if("loaded"===a.readyState||"complete"===a.readyState)a.onreadystatechange=null,b()}:a.onload=function(){b()};a.src=c;d.getElementsByTagName("head")[0].appendChild(a)}loadScript("$ll_url",function(){
		var rocket_ll = new LazyLoad({
			elements_selector: "img, iframe",
			data_src: "lazy-src",
			data_srcset: "lazy-srcset",
			class_loading: "lazyloading",
			class_loaded: "lazyloaded",
			callback_set: function(element) {
				//todo: check fitvids compatibility (class or data-attribute)
				if (  element.tagName === "IFRAME" && element.classList.contains("fitvidscompatible") ) {
					if ( element.classList.contains("lazyloaded") ) {
						//todo: check if $.fn.fitvids() is available
						if ( typeof $ === "function" ) {
							$( element ).parent().fitVids();
						}
					} else {
						var temp = setInterval( function() {
							//todo: check if $.fn.fitvids() is available
							if ( element.classList.contains("lazyloaded") && typeof $ === "function" ) {
								$( element ).parent().fitVids();
								clearInterval( temp );
							} else {
								clearInterval( temp );
							}
						}, 50 );
					}
				} // if element is an iframe
			}	
		});
	});})(window,document);</script>
HTML;
}
add_action( 'wp_head', 'rocket_lazyload_script', PHP_INT_MAX );

/**
 * Replace Gravatar, thumbnails, images in post content and in widget text by LazyLoad
 *
 * @since 1.1 Support for get_image_tag filter.
 * @since 1.0
 *
 * @param string $html HTML code to parse.
 * @return string Updated HTML code
 */
function rocket_lazyload_images( $html ) {
	// Don't LazyLoad if the thumbnail is in admin, a feed or a post preview.
	if ( ! rocket_lazyload_get_option( 'images' ) || is_admin() || is_feed() || is_preview() || empty( $html ) || ( defined( 'DONOTLAZYLOAD' ) && DONOTLAZYLOAD ) || wp_script_is( 'twentytwenty-twentytwenty', 'enqueued' ) ) {
		return $html;
	}

	// You can stop the LalyLoad process with a hook.
	if ( ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return $html;
	}

	$html = preg_replace_callback( '#<img([^>]*) src=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))([^>]*)>#', 'rocket_lazyload_replace_callback', $html );

	return $html;
}
add_filter( 'get_avatar'         , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'the_content'        , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'widget_text'        , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'get_image_tag'      , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'post_thumbnail_html', 'rocket_lazyload_images', PHP_INT_MAX );

/**
 * Used to check if we have to LazyLoad this or not
 *
 * @since 1.1 Don't apply LazyLoad on images from WP Retina x2
 * @since 1.0.1
 *
 * @param string $matches a string matching the pattern to find images in HTML code.
 * @return string Updated string with lazyload data
 */
function rocket_lazyload_replace_callback( $matches ) {

	if ( function_exists( 'wr2x_picture_rewrite' ) ) {
		if ( wr2x_get_retina( trailingslashit( ABSPATH ) . wr2x_get_pathinfo_from_image_src( trim( $matches[2], '"' ) ) ) ) {
			return $matches[0];
		}
	}

	$excluded_attributes = apply_filters( 'rocket_lazyload_excluded_attributes', array(
		'data-no-lazy=',
		'data-lazy-original=',
		'data-lazy-src=',
		'data-lazysrc=',
		'data-lazyload=',
		'data-bgposition=',
		'data-envira-src=',
		'fullurl=',
		'lazy-slider-img=',
		'data-srcset=',
		'class="ls-l',
		'class="ls-bg',
	) );

	$excluded_src = apply_filters( 'rocket_lazyload_excluded_src', array(
		'/wpcf7_captcha/',
		'timthumb.php?src',
	) );

	if ( rocket_is_excluded_lazyload( $matches[1] . $matches[3], $excluded_attributes ) ||  rocket_is_excluded_lazyload( $matches[2], $excluded_src ) ) {
		return $matches[0];
	}

	/**
	 * Filter the LazyLoad placeholder on src attribute
	 *
	 * @since 1.1
	 *
	 * @param string $placeholder Placeholder that will be printed.
	 */
	$placeholder = apply_filters( 'rocket_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=' );

	$html = sprintf( '<img%1$s src="%4$s" data-lazy-src=%2$s%3$s>', $matches[1], $matches[2], $matches[3], $placeholder );

	$html_noscript = sprintf( '<noscript><img%1$s src=%2$s%3$s></noscript>', $matches[1], $matches[2], $matches[3] );

	/**
	 * Filter the LazyLoad HTML output
	 *
	 * @since 1.0.2
	 *
	 * @param array $html Output that will be printed
	 */
	$html = apply_filters( 'rocket_lazyload_html', $html, true );

	return $html . $html_noscript;
}

/**
 * Determine if the current image should be excluded from lazyload
 *
 * @since 1.1
 * @author Remy Perona
 *
 * @param string $string String to search.
 * @param array  $excluded_values Array of excluded values to search in the string.
 * @return bool True if one of the excluded values was found, false otherwise
 */
function rocket_is_excluded_lazyload( $string, $excluded_values ) {
	foreach ( $excluded_values as $excluded_value ) {
		if ( strpos( $string, $excluded_value ) !== false ) {
			return true;
		}
	}

	return false;
}

/**
 * Replace WordPress smilies by Lazy Load
 *
 * @since 1.0
 */
function rocket_lazyload_smilies() {
	if ( ! rocket_lazyload_get_option( 'images' ) || ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return;
	}

	remove_filter( 'the_content', 'convert_smilies' );
	remove_filter( 'the_excerpt', 'convert_smilies' );
	remove_filter( 'comment_text', 'convert_smilies', 20 );

	add_filter( 'the_content', 'rocket_convert_smilies' );
	add_filter( 'the_excerpt', 'rocket_convert_smilies' );
	add_filter( 'comment_text', 'rocket_convert_smilies', 20 );
}
add_action( 'init', 'rocket_lazyload_smilies' );

/**
 * Convert text equivalent of smilies to images.
 *
 * @source convert_smilies() in /wp-includes/formattings.php
 * @since 1.0
 *
 * @param string $text text content to parse.
 * @return string Updated text content
 */
function rocket_convert_smilies( $text ) {

	global $wp_smiliessearch;
	$output = '';

	if ( get_option( 'use_smilies' ) && ! empty( $wp_smiliessearch ) ) {
		// HTML loop taken from texturize function, could possible be consolidated.
		$textarr = preg_split( '/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // capture the tags as well as in between.
		$stop = count( $textarr );// loop stuff.

		// Ignore proessing of specific tags.
		$tags_to_ignore = 'code|pre|style|script|textarea';
		$ignore_block_element = '';

		for ( $i = 0; $i < $stop; $i++ ) {
			$content = $textarr[ $i ];

			// If we're in an ignore block, wait until we find its closing tag.
			if ( '' === $ignore_block_element && preg_match( '/^<(' . $tags_to_ignore . ')>/', $content, $matches ) ) {
				$ignore_block_element = $matches[1];
			}

			// If it's not a tag and not in ignore block.
			if ( '' === $ignore_block_element && strlen( $content ) > 0 && '<' !== $content[0] ) {
				$content = preg_replace_callback( $wp_smiliessearch, 'rocket_translate_smiley', $content );
			}

			// did we exit ignore block.
			if ( '' !== $ignore_block_element && '</' . $ignore_block_element . '>' === $content ) {
				$ignore_block_element = '';
			}

			$output .= $content;
		}
	} else {
		// return default text.
		$output = $text;
	}
	return $output;
}

/**
 * Convert one smiley code to the icon graphic file equivalent.
 *
 * @source translate_smiley() in /wp-includes/formattings.php
 * @since 1.0
 *
 * @param string $matches a string matching the pattern for smilies.
 * @return string The updated HTML code to display smilies
 */
function rocket_translate_smiley( $matches ) {
	global $wpsmiliestrans;

	if ( count( $matches ) === 0 ) {
		return '';
	}

	$smiley = trim( reset( $matches ) );
	$img = $wpsmiliestrans[ $smiley ];

	$matches = array();
	$ext = preg_match( '/\.([^.]+)$/', $img, $matches ) ? strtolower( $matches[1] ) : false;
	$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );

	// Don't convert smilies that aren't images - they're probably emoji.
	if ( ! in_array( $ext, $image_exts, true ) ) {
		return $img;
	}

	/**
	 * Filter the Smiley image URL before it's used in the image element.
	 *
	 * @since WP 2.9.0
	 *
	 * @param string $smiley_url URL for the smiley image.
	 * @param string $img        Filename for the smiley image.
	 * @param string $site_url   Site URL, as returned by site_url().
	 */
	$src_url = apply_filters( 'smilies_src', includes_url( "images/smilies/$img" ), $img, site_url() );

	// Don't lazy-load if process is stopped with a hook.
	if ( apply_filters( 'do_rocket_lazyload', true ) ) {
		return sprintf( ' <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" data-lazy-original="%s" alt="%s" class="wp-smiley" /> ', esc_url( $src_url ), esc_attr( $smiley ) );
	} else {
		return sprintf( ' <img src="%s" alt="%s" class="wp-smiley" /> ', esc_url( $src_url ), esc_attr( $smiley ) );
	}

}

/**
 * Compatibility with images with srcset attribute
 *
 * @since 1.1
 * @author Geoffrey Crofte (code from WP Rocket plugin)
 *
 * @param string $html the HTML code to parse.
 * @return string the updated HTML code
 */
function rocket_lazyload_on_srcset( $html ) {
	if ( preg_match( '/srcset=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))/i', $html ) ) {
		$html = str_replace( 'srcset=', 'data-lazy-srcset=', $html );
	}

	if ( preg_match( '/sizes=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))/i', $html ) ) {
		$html = str_replace( 'sizes=', 'data-lazy-sizes=', $html );
	}

	return $html;
}
add_filter( 'rocket_lazyload_html', 'rocket_lazyload_on_srcset' );

/**
 * Replace iframes by LazyLoad
 *
 * @since 1.1
 * @author Geoffrey Crofte (code from WP Rocket plugin)
 *
 * @param string $html the HTML code to parse.
 * @return string the updated HTML code
 */
function rocket_lazyload_iframes( $html ) {
	// Don't LazyLoad if process is stopped for these reasons.
	if ( ! rocket_lazyload_get_option( 'iframes' ) || ! apply_filters( 'do_rocket_lazyload_iframes', true ) || is_feed() || is_preview() || empty( $html ) || ( defined( 'DONOTLAZYLOAD' ) && DONOTLAZYLOAD ) ) {
		return $html;
	}

	$matches = array();
	preg_match_all( '/<iframe\s+.*?>/', $html, $matches );

	foreach ( $matches[0] as $k => $iframe ) {

		// Don't mess with the Gravity Forms ajax iframe.
		if ( strpos( $iframe, 'gform_ajax_frame' ) ) {
			continue;
		}

		// Don't lazyload if iframe has data-no-lazy attribute.
		if ( strpos( $iframe, 'data-no-lazy=' ) ) {
			continue;
		}

		/**
	 	 * Filter the LazyLoad placeholder on src attribute
	 	 *
	 	 * @since 1.1
	 	 *
	 	 * @param string $placeholder placeholder that will be printed.
	 	 */
		$placeholder = apply_filters( 'rocket_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=' );

		// todo: add "fitvids compatible" class or data-attribute to check in JS (see JS L.57).
		$iframe = preg_replace( '/<iframe(.*?)src=/is', '<iframe$1src="' . $placeholder . '" data-lazy-src=', $iframe );

		$html = str_replace( $matches[0][ $k ], $iframe, $html );

		/**
		 * Filter the LazyLoad HTML output on iframes
		 *
		 * @since 1.1
		 *
		 * @param array $html Output that will be printed.
		*/
		$html = apply_filters( 'rocket_lazyload_iframe_html', $html );
	}

	return $html;
}
add_filter( 'the_content', 'rocket_lazyload_iframes', PHP_INT_MAX );
add_filter( 'widget_text', 'rocket_lazyload_iframes', PHP_INT_MAX );
