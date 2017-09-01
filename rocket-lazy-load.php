<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Plugin Name: Rocket Lazy Load
 * Plugin URI: http://wordpress.org/plugins/rocket-lazy-load/
 * Description: The tiny Lazy Load script for WordPress without jQuery or others libraries.
 * Version: 1.3
 * Requires PHP: 5.4
 * Author: WP Media
 * Author URI: https://wp-rocket.me
 * Text Domain: rocket-lazy-load
 * Domain Path: /languages
 *
 * Copyright 2015-2017 WP Media
 *
 * This program is free software; you can redistribute it and/or modify
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
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
define( 'ROCKET_LL_VERSION', '1.3' );
define( 'ROCKET_LL_PATH', realpath( plugin_dir_path( __FILE__ ) ) . '/' );
define( 'ROCKET_LL_3RD_PARTY_PATH', ROCKET_LL_PATH . '3rd-party/' );
define( 'ROCKET_LL_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
define( 'ROCKET_LL_FRONT_JS_URL', ROCKET_LL_ASSETS_URL . 'js/' );
define( 'ROCKET_LL_JS_VERSION'  , '8.0.3' );


/**
 * Initialize the plugin.
 *
 * @since 1.1
 * @return void
 */
function rocket_lazyload_init() {
	load_plugin_textdomain( 'rocket-lazy-load', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	require_once ROCKET_LL_PATH . 'vendor/autoload.php';
	require ROCKET_LL_3RD_PARTY_PATH . '3rd-party.php';

	if ( is_admin() ) {
		require ROCKET_LL_PATH . 'admin/admin.php';
	}
}

if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
	/**
	 * Warning if PHP version is less than 5.4.
	 *
	 * @since 1.3
	 */
	function rocket_lazyload_php_warning() {
		echo '<div class="error"><p>' . __( 'Rocket LazyLoad requires PHP 5.4 to function properly. Please upgrade PHP. The Plugin has been auto-deactivated.', 'rocket-lazy-load' ) . '</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
	add_action( 'admin_notices', 'rocket_lazyload_php_warning' );

	/**
	 * Deactivate plugin if needed.
	 *
	 * @since 1.3
	 */
	function rocket_lazyload_deactivate_self() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	add_action( 'admin_init', 'rocket_lazyload_deactivate_self' );

	return;
} else {
	add_action( 'plugins_loaded', 'rocket_lazyload_init' );
}

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
 * Set lazyload options
 *
 * @since 1.0
 */
function rocket_lazyload_script() {
	if ( ! rocket_lazyload_get_option( 'images' ) && ! rocket_lazyload_get_option( 'iframes' ) || ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return;
	}

	$threshold = apply_filters( 'rocket_lazyload_threshold', 300 );

	echo <<<HTML
	<script>
	window.lazyLoadOptions = {
		elements_selector: "img, iframe",
		data_src: "lazySrc",
		data_srcset: "lazySrcset",
		class_loading: "lazyloading",
		class_loaded: "lazyloaded",
		threshold: $threshold,
		callback_load: function(element) {
			if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
				if (element.classList.contains("lazyloaded") ) {
					if (typeof window.jQuery != 'undefined') {
						if (jQuery.fn.fitVids) {
							jQuery(element).parent().fitVids();
						}
					}
				}
			}
		}	
	};
	</script>
HTML;
}
add_action( 'wp_footer', 'rocket_lazyload_script', 9 );

/**
 * Enqueue the lazyload script
 *
 * @since 1.2
 * @author Remy Perona
 */
function rocket_lazyload_enqueue() {
	if ( ! rocket_lazyload_get_option( 'images' ) && ! rocket_lazyload_get_option( 'iframes' ) || ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return;
	}

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$ll_url = ROCKET_LL_FRONT_JS_URL . 'lazyload-' . ROCKET_LL_JS_VERSION . $suffix . '.js';

	wp_enqueue_script( 'rocket-lazyload', $ll_url, null, null, true );
}
add_action( 'wp_enqueue_scripts', 'rocket_lazyload_enqueue', PHP_INT_MAX );

/**
 * Add tags to the lazyload script to async and prevent concatenation
 *
 * @since 1.2
 * @author Remy Perona
 *
 * @param string $tag HTML for the script.
 * @param string $handle Handle for the script.
 *
 * @return string Updated HTML
 */
function rocket_lazyload_async_script( $tag, $handle ) {
	if ( 'rocket-lazyload' === $handle ) {
		return str_replace( '<script', '<script async data-cfasync="false" data-minify="1"', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'rocket_lazyload_async_script', 10, 2 );

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
	// Don't LazyLoad if the thumbnail is in admin, a feed, REST API or a post preview.
	if ( ! rocket_lazyload_get_option( 'images' ) || is_admin() || is_feed() || is_preview() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || empty( $html ) || ( defined( 'DONOTLAZYLOAD' ) && DONOTLAZYLOAD ) || wp_script_is( 'twentytwenty-twentytwenty', 'enqueued' ) ) {
		return $html;
	}

	// You can stop the LalyLoad process with a hook.
	if ( ! apply_filters( 'do_rocket_lazyload', true ) ) {
		return $html;
	}

	$dom = new PHPHtmlParser\Dom();
	$dom->load( $html );
	$images = $dom->getElementsByTag( 'img' );

	if ( ! $images ) {
		return $html;
	}

	foreach ( $images as $image ) {
		$image_attributes = $image->getAttributes();

		if ( rocket_is_excluded_lazyload( $image_attributes ) ) {
			continue;
		}

		$img = new PHPHtmlParser\Dom\Tag( 'img' );

		foreach ( $image_attributes as $key => $value ) {
			$img->setAttribute( $key, $value );
		}

		$original_image = new PHPHtmlParser\Dom\HtmlNode( $img );
		$noscript_tag   = new PHPHtmlParser\Dom\Tag( 'noscript' );
		$noscript       = new PHPHtmlParser\Dom\HtmlNode( $noscript_tag );

		/**
		 * Filter the LazyLoad placeholder on src attribute
		 *
		 * @since 1.1
		 *
		 * @param string $placeholder Placeholder that will be printed.
		 */
		$placeholder = apply_filters( 'rocket_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=' );

		$image->setAttribute( 'src', $placeholder );
		$image->setAttribute( 'data-lazy-src', $image_attributes['src'] );

		if ( isset( $image_attributes['srcset'] ) ) {
			$image->removeAttribute( 'srcset' );
			$image->setAttribute( 'data-lazy-srcset', $image_attributes['srcset'] );
		}

		if ( isset( $image_attributes['sizes'] ) ) {
			$image->removeAttribute( 'sizes' );
			$image->setAttribute( 'data-lazy-sizes', $image_attributes['sizes'] );
		}

		$noscript->addChild( $original_image );

		$parent = $image->getParent();
		$parent->insertAfter( $noscript, $image->id() );
	}

	return $dom;
}
add_filter( 'get_avatar'         , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'the_content'        , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'widget_text'        , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'get_image_tag'      , 'rocket_lazyload_images', PHP_INT_MAX );
add_filter( 'post_thumbnail_html', 'rocket_lazyload_images', PHP_INT_MAX );

/**
 * Determine if the current image should be excluded from lazyload
 *
 * @since 1.3 Moved check logic in the function directly
 * @since 1.1
 * @author Remy Perona
 *
 * @param array $attributes Array containing image attributes.
 * @return bool True if one of the excluded values was found, false otherwise
 */
function rocket_is_excluded_lazyload( $attributes ) {
	if ( function_exists( 'wr2x_picture_rewrite' ) ) {
		if ( wr2x_get_retina( trailingslashit( ABSPATH ) . wr2x_get_pathinfo_from_image_src( trim( $attributes['src'], '"' ) ) ) ) {
			return true;
		}
	}

	$excluded_attributes = apply_filters( 'rocket_lazyload_excluded_attributes', array(
		'data-no-lazy',
		'data-lazy-original',
		'data-lazy-src',
		'data-lazysrc',
		'data-lazyload',
		'data-bgposition',
		'data-envira-src',
		'fullurl',
		'lazy-slider-img',
		'data-srcset',
	) );

	$excluded_classes = apply_filters( 'rocket_lazyload_excluded_classes', array(
		'ls-l',
		'ls-bg',
	) );

	$excluded_src = apply_filters( 'rocket_lazyload_excluded_src', array(
		'/wpcf7_captcha/',
		'timthumb.php?src',
	) );

	if ( array_intersect( $attributes, $excluded_attributes, $excluded_classes, $excluded_src ) ) {
		return true;
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
	if ( ! rocket_lazyload_get_option( 'iframes' ) || ! apply_filters( 'do_rocket_lazyload_iframes', true ) || is_feed() || is_preview() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || empty( $html ) || ( defined( 'DONOTLAZYLOAD' ) && DONOTLAZYLOAD ) ) {
		return $html;
	}

	$dom = new PHPHtmlParser\Dom();
	$dom->load( $html );
	$iframes = $dom->getElementsByTag( 'iframe' );

	if ( ! $iframes ) {
		return $html;
	}

	foreach ( $iframes as $iframe ) {
		$iframe_attributes = $iframe->getAttributes();

		// Don't mess with the Gravity Forms ajax iframe.
		if ( isset( $iframe_attributes['id'] ) && false !== strpos( $iframe_attributes['id'], 'gform_ajax_frame' ) || isset( $iframe_attributes['name'] ) && false !== strpos( $iframe_attributes['name'], 'gform_ajax_frame' ) ) {
			continue;
		}

		// Don't lazyload if iframe has data-no-lazy attribute.
		if ( isset( $iframe_attributes['data-no-lazy'] ) ) {
			continue;
		}

		$iframe_tag = new PHPHtmlParser\Dom\Tag( 'iframe' );

		foreach ( $iframe_attributes as $key => $value ) {
			$iframe_tag->setAttribute( $key, $value );
		}

		$original_iframe = new PHPHtmlParser\Dom\HtmlNode( $iframe_tag );
		$noscript_tag    = new PHPHtmlParser\Dom\Tag( 'noscript' );
		$noscript        = new PHPHtmlParser\Dom\HtmlNode( $noscript_tag );

		/**
		 * Filter the LazyLoad placeholder on src attribute
		 *
		 * @since 1.1
		 *
		 * @param string $placeholder placeholder that will be printed.
		 */
		$placeholder = apply_filters( 'rocket_lazyload_placeholder', 'about:blank' );

		$iframe->setAttribute( 'src', $placeholder );
		$iframe->setAttribute( 'data-lazy-src', $iframe_attributes['src'] );
		$iframe->setAttribute( 'data-rocket-lazyload', 'fitvidscompatible' );

		$noscript->addChild( $original_iframe );

		$parent = $iframe->getParent();
		$parent->insertAfter( $noscript, $iframe->id() );
	}

	return $dom;
}
add_filter( 'the_content', 'rocket_lazyload_iframes', PHP_INT_MAX );
add_filter( 'widget_text', 'rocket_lazyload_iframes', PHP_INT_MAX );
