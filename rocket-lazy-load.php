<?php
defined( 'ABSPATH' ) or	die( 'Cheatin\' uh?' );

/*
Plugin Name: Rocket Lazy Load
Plugin URI: http://wordpress.org/plugins/rocket-lazy-load/
Description: The tiny Lazy Load script for WordPress without jQuery or others libraries.
Version: 1.1
Author: WP Media
Author URI: http://wp-rocket.me

Copyright 2015 WP Media

	This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

define( 'ROCKET_LL_FRONT_JS_URL', plugin_dir_url( __FILE__ ) . 'js/' );
define( 'ROCKET_LL_JS_VERSION'  , '3.0' );

/**
 * Add Lazy Load JavaScript in the header
 * No jQuery or other library is required !!
 *
 * @since 1.0
 */
add_action( 'wp_head', 'rocket_lazyload_script', PHP_INT_MAX );
function rocket_lazyload_script() {
	if ( ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return;
	}

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$ll_url = ROCKET_LL_FRONT_JS_URL . 'lazyload-' . ROCKET_LL_JS_VERSION . $suffix . '.js';

	//echo '<script data-cfasync="false">(function(w,d){function a(){var b=d.createElement("script");b.async=!0;b.src="' . $ll_url .'";var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(b,a)}w.attachEvent?w.attachEvent("onload",a):w.addEventListener("load",a,!1)})(window,document);</script>';

	echo '<script data-cfasync="false">(function(w,d){function loadScript(c,b){var a=d.createElement("script");a.async=!0;a.readyState?a.onreadystatechange=function(){if("loaded"===a.readyState||"complete"===a.readyState)a.onreadystatechange=null,b()}:a.onload=function(){b()};a.src=c;d.getElementsByTagName("head")[0].appendChild(a)}loadScript("' . $ll_url . '",function(){
		new LazyLoad({
			elements_selector: 'img, iframe',
			callback_set: function(element) {
				if (  $( element ).filter( $('iframe') ).length ) {
					if ( $( element ).filter( $('iframe') ).hasClass( 'loaded' ) ) {
						$( element ).filter( $('iframe') ).fitVids();
					} else {
						var temp = setInterval( function() {
							if ( $( element ).filter( $('iframe.loaded') ).length ) {
								$( element ).filter( $('iframe.loaded') ).parent().fitVids();
								clearInterval( temp );
							}
						}, 50 );
					}
				}
			}	
		});
	});})(window,document);</script>';
}



/**
 * Replace Gravatar, thumbnails, images in post content and in widget text by LazyLoad
 *
 * @since 1.1 Support for get_image_tag filter.
 * @since 1.0
 */
add_filter( 'get_avatar'         , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'the_content'        , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'widget_text'        , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'get_image_tag'      , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'post_thumbnail_html', 'rocket_lazyload_images', PHP_INT_MAX );
function rocket_lazyload_images( $html ) {
	// Don't LazyLoad if the thumbnail is in admin, a feed or a post preview
	if ( is_admin() || is_feed() || is_preview() || empty( $html ) || ( defined( 'DONOTLAZYLOAD' ) && DONOTLAZYLOAD ) || wp_script_is( 'twentytwenty-twentytwenty', 'enqueued' ) ) {
		return $html;
	}

	// You can stop the LalyLoad process with a hook
	if ( ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return $html;
	}

	$html = preg_replace_callback( '#<img([^>]*) src=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))([^>]*)>#', '__rocket_lazyload_replace_callback', $html );

	return $html;
}


/**
 * Used to check if we have to LazyLoad this or not
 *
 * @since 1.1 Don't apply LazyLoad on images from WP Retina x2
 * @since 1.0.1
 */
function __rocket_lazyload_replace_callback( $matches ) {

	if ( function_exists( 'wr2x_picture_rewrite' ) ) {
		if ( wr2x_get_retina( trailingslashit( ABSPATH ) . wr2x_get_pathinfo_from_image_src( trim( $matches[2], '"' ) ) ) ) {
			return $matches[0];
		}
	}

	// TO DO - improve this code with a preg_match - it's ugly!!!!
	if ( strpos( $matches[1] . $matches[3], 'data-no-lazy=' ) === false && strpos( $matches[1] . $matches[3], 'data-lazy-original=' ) === false && strpos( $matches[1] . $matches[3], 'data-lazy-src=' ) === false && strpos( $matches[1] . $matches[3], 'data-lazysrc=' ) === false && strpos( $matches[1] . $matches[3], 'data-src=' ) === false && strpos( $matches[1] . $matches[3], 'data-lazyload=' ) === false && strpos( $matches[1] . $matches[3], 'data-bgposition=' ) === false && strpos( $matches[2], '/wpcf7_captcha/' ) === false && strpos( $matches[2], 'timthumb.php?src' ) === false && strpos( $matches[1] . $matches[3], 'data-envira-src=' ) === false && strpos( $matches[1] . $matches[3], 'fullurl=' ) === false && strpos( $matches[1] . $matches[3], 'lazy-slider-img=' ) === false && strpos( $matches[1] . $matches[3], 'data-srcset=' ) === false && strpos( $matches[1] . $matches[3], 'class="ls-l' ) === false && strpos( $matches[1] . $matches[3], 'class="ls-bg' ) === false ) {

		/**
		 * Filter the LazyLoad placeholder on src attribute
		 *
		 * @since 1.1
		 *
		 * @param string Output that will be printed
		*/
		$placeholder = apply_filters( 'rocket_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=' );
		
		$html = sprintf( '<img%1$s src="%4$s" data-lazy-original=%2$s%3$s>', $matches[1], $matches[2], $matches[3], $placeholder );

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
	} else {
		return $matches[0];
	}
}

/**
 * Replace WordPress smilies by Lazy Load
 *
 * @since 1.0
 */
remove_filter( 'the_content', 'convert_smilies' );
remove_filter( 'the_excerpt', 'convert_smilies' );
remove_filter( 'comment_text', 'convert_smilies' );

add_filter( 'the_content', 'rocket_convert_smilies' );
add_filter( 'the_excerpt', 'rocket_convert_smilies' );
add_filter( 'comment_text', 'rocket_convert_smilies' );

/**
 * Convert text equivalent of smilies to images.
 *
 * @source convert_smilies() in /wp-includes/formattings.php
 * @since 1.0
 */
function rocket_convert_smilies( $text ) {

	global $wp_smiliessearch;
	$output = '';

	if ( get_option( 'use_smilies' ) && ! empty( $wp_smiliessearch ) ) {
		// HTML loop taken from texturize function, could possible be consolidated
		$textarr = preg_split( '/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // capture the tags as well as in between
		$stop = count( $textarr );// loop stuff

		// Ignore proessing of specific tags
		$tags_to_ignore = 'code|pre|style|script|textarea';
		$ignore_block_element = '';

		for ( $i = 0; $i < $stop; $i++ ) {
			$content = $textarr[ $i ];

			// If we're in an ignore block, wait until we find its closing tag
			if ( '' == $ignore_block_element && preg_match( '/^<(' . $tags_to_ignore . ')>/', $content, $matches ) )  {
				$ignore_block_element = $matches[1];
			}

			// If it's not a tag and not in ignore block
			if ( '' ==  $ignore_block_element && strlen( $content ) > 0 && '<' != $content[0] ) {
				$content = preg_replace_callback( $wp_smiliessearch, 'rocket_translate_smiley', $content );
			}

			// did we exit ignore block
			if ( '' != $ignore_block_element && '</' . $ignore_block_element . '>' == $content )  {
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
 */
function rocket_translate_smiley( $matches ) {
	global $wpsmiliestrans;

	if ( count( $matches ) == 0 )
		return '';

	$smiley = trim( reset( $matches ) );
	$img = $wpsmiliestrans[ $smiley ];

	$matches = array();
	$ext = preg_match( '/\.([^.]+)$/', $img, $matches ) ? strtolower( $matches[1] ) : false;
	$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );

	// Don't convert smilies that aren't images - they're probably emoji.
	if ( ! in_array( $ext, $image_exts ) ) {
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

	// Don't lazy-load if process is stopped with a hook
	if ( apply_filters( 'do_rocket_lazyload', true ) ) {
		return sprintf( ' <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" data-lazy-original="%s" alt="%s" class="wp-smiley" /> ', esc_url( $src_url ), esc_attr( $smiley ) );
	} else {
		return sprintf( ' <img src="%s" alt="%s" class="wp-smiley" /> ', esc_url( $src_url ), esc_attr( $smiley ) );
	}

}