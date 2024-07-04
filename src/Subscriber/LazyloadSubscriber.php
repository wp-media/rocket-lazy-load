<?php

namespace RocketLazyLoadPlugin\Subscriber;

use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\EventManagement\ClassicSubscriberInterface;
use RocketLazyLoadPlugin\Dependencies\LaunchpadFrameworkOptions\Interfaces\SettingsAwareInterface;
use RocketLazyLoadPlugin\Dependencies\LaunchpadFrameworkOptions\Traits\SettingsAwareTrait;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe;

/**
 * Lazyload Subscriber
 *
 * @since 2.0
 * @author Remy Perona
 */
class LazyloadSubscriber implements ClassicSubscriberInterface, SettingsAwareInterface {

	use SettingsAwareTrait;

	/**
	 * Assets instance
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 * @var Assets
	 */
	private $assets;

	/**
	 * Image instance
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 * @var Image
	 */
	private $image;

	/**
	 * Iframe instance
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 * @var Iframe
	 */
	private $iframe;

	/**
	 * Constructor
	 *
	 * @param Assets $assets Assets instance.
	 * @param Image $image Image instance.
	 * @param Iframe $iframe Iframe instance.
	 *
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function __construct( Assets $assets, Image $image, Iframe $iframe ) {
		$this->assets = $assets;
		$this->image  = $image;
		$this->iframe = $iframe;
	}

	/**
	 * Returns an array of events that this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function get_subscribed_events(): array {
		return [
			'wp_footer'            => [
				[ 'insertLazyloadScript', \ROCKET_LL_INT_MAX ],
				[ 'insertYoutubeThumbnailScript', \ROCKET_LL_INT_MAX ],
			],
			'wp_head'              => [ 'insertNoJSStyle', \ROCKET_LL_INT_MAX ],
			'wp_enqueue_scripts'   => [ 'insertYoutubeThumbnailStyle', \ROCKET_LL_INT_MAX ],
			'template_redirect'    => [ 'lazyload', 2 ],
			'rocket_lazyload_html' => 'lazyloadResponsive',
			'init'                 => 'lazyloadSmilies',
		];
	}

	/**
	 * Inserts the lazyload script in the footer
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function insertLazyloadScript() {
		if ( ! $this->settings->get( 'images' ) && ! $this->settings->get( 'iframes' ) ) {
			return;
		}

		if ( ! $this->shouldLazyload() ) {
			return;
		}

		/**
		 * Filters the threshold at which lazyload is triggered
		 *
		 * @param int $threshold Threshold value.
		 *
		 * @author Remy Perona
		 *
		 * @since 1.2
		 */
		$threshold = apply_filters( 'rocket_lazyload_threshold', 300 );

		$script_args = [
			'base_url' => ROCKET_LL_FRONT_JS_URL,
			'version'  => '16.1',
			'polyfill' => false,
		];

		$inline_args = [
			'threshold' => $threshold,
		];

		if ( $this->is_native_images() ) {
			$inline_args['options'] = [
				'use_native' => 'true',
			];
		}

		if ( $this->settings->get( 'images' ) || $this->settings->get( 'iframes' ) ) {
			if ( $this->is_native_images() ) {
				$inline_args['elements']            = isset( $inline_args['elements'] ) ? $inline_args['elements'] : [];
				$inline_args['elements']['loading'] = '[loading=lazy]';
			}
		}

		if ( $this->settings->get( 'images' ) ) {
			$inline_args['elements']                     = isset( $inline_args['elements'] ) ? $inline_args['elements'] : [];
			$inline_args['elements']['image']            = 'img[data-lazy-src]';
			$inline_args['elements']['background_image'] = '.rocket-lazyload';
		}

		if ( $this->settings->get( 'iframes' ) ) {
			$inline_args['elements']           = isset( $inline_args['elements'] ) ? $inline_args['elements'] : [];
			$inline_args['elements']['iframe'] = 'iframe[data-lazy-src]';
		}

		/**
		 * Filters the arguments array for the lazyload script options
		 *
		 * @param array $inline_args Arguments used for the lazyload script options.
		 *
		 * @author Remy Perona
		 *
		 * @since 2.0
		 */
		$inline_args = apply_filters( 'rocket_lazyload_script_args', $inline_args );

		echo '<script>' . $this->assets->getInlineLazyloadScript( $inline_args ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$this->assets->insertLazyloadScript( $script_args );
	}

	/**
	 * Inserts the Youtube thumbnail script in the footer
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function insertYoutubeThumbnailScript() {
		if ( ! $this->settings->get( 'youtube' ) ) {
			return;
		}

		if ( ! $this->shouldLazyload() ) {
			return;
		}

		/**
		 * Filters the resolution of the YouTube thumbnail
		 *
		 * @param string $thumbnail_resolution The resolution of the thumbnail. Accepted values: default, mqdefault, sddefault, hqdefault, maxresdefault
		 *
		 * @author Arun Basil Lal
		 *
		 * @since 1.4.8
		 */
		$thumbnail_resolution = apply_filters( 'rocket_lazyload_youtube_thumbnail_resolution', 'hqdefault' );

		$this->assets->insertYoutubeThumbnailScript(
			[
				'resolution' => $thumbnail_resolution,
				'lazy_image' => (bool) $this->settings->get( 'images' ),
			]
		);
	}

	/**
	 * Inserts the no JS CSS compatibility in the header
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0.3
	 */
	public function insertNoJSStyle() {
		if ( ! $this->shouldLazyload() ) {
			return;
		}

		$this->assets->insertNoJSCSS();
	}

	/**
	 * Inserts the Youtube thumbnail CSS in the header
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function insertYoutubeThumbnailStyle() {
		if ( ! $this->settings->get( 'youtube' ) ) {
			return;
		}

		if ( ! $this->shouldLazyload() ) {
			return;
		}

		$this->assets->insertYoutubeThumbnailCSS(
			[
				'base_url'          => ROCKET_LL_ASSETS_URL,
				'responsive_embeds' => current_theme_supports( 'responsive-embeds' ),
			]
		);
	}

	/**
	 * Checks if lazyload should be applied
	 *
	 * @return bool
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	private function shouldLazyload() {
		if ( is_admin() || is_feed() || is_preview() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'DONOTLAZYLOAD' ) && DONOTLAZYLOAD ) ) {
			return false;
		}

		if ( $this->isPageBuilder() ) {
			return false;
		}

		/**
		 * Filters the lazyload application
		 *
		 * @param bool $do_rocket_lazyload True to apply lazyload, false otherwise.
		 *
		 * @author Remy Perona
		 *
		 * @since 2.0
		 */
		if ( ! apply_filters( 'do_rocket_lazyload', true ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
			return false;
		}

		return true;
	}

	/**
	 * Checks if current page is a page builder editor.
	 *
	 * @return bool
	 * @author Remy Perona
	 *
	 * @since 2.2.2
	 */
	private function isPageBuilder() {
		// Exclude Page Builders editors.
		$excluded_parameters = [
			'fl_builder',
			'et_fb',
			'ct_builder',
		];

		foreach ( $excluded_parameters as $excluded ) {
			if ( isset( $_GET[ $excluded ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets the content to lazyload
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function lazyload() {
		if ( ! $this->shouldLazyload() ) {
			return;
		}

		ob_start( [ $this, 'lazyloadBuffer' ] );
	}

	/**
	 * Applies lazyload on the provided content
	 *
	 * @param string $html HTML content.
	 *
	 * @return string
	 * @since 2.0
	 * @author Remy Perona
	 *
	 */
	public function lazyloadBuffer( $html ) {
		$buffer = $this->ignoreScripts( $html );
		$buffer = $this->ignoreNoscripts( $buffer );

		if ( $this->settings->get( 'images' ) ) {
			$html = $this->image->lazyloadImages( $html, $buffer , $this->is_native_images() );
			$html = $this->image->lazyloadPictures( $html, $buffer );
			$html = $this->image->lazyloadBackgroundImages( $html, $buffer );
		}

		if ( $this->settings->get( 'iframes' ) ) {
			$args = [
				'youtube' => $this->settings->get( 'youtube' ),
			];

			$html = $this->iframe->lazyloadIframes( $html, $buffer, $args );
		}

		return $html;
	}

	/**
	 * Applies lazyload on responsive images attributes srcset and sizes
	 *
	 * @param string $html Image HTML.
	 *
	 * @return string
	 * @since 2.0
	 * @author Remy Perona
	 *
	 */
	public function lazyloadResponsive( $html ) {
		return $this->image->lazyloadResponsiveAttributes( $html );
	}

	/**
	 * Applies lazyload on WordPress smilies
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function lazyloadSmilies() {
		if ( ! $this->shouldLazyload() ) {
			return;
		}

		if ( ! $this->settings->get( 'images' ) ) {
			return;
		}

		$filters = [
			'the_content'  => 10,
			'the_excerpt'  => 10,
			'comment_text' => 20,
		];

		foreach ( $filters as $filter => $prio ) {
			if ( ! has_filter( $filter ) ) {
				continue;
			}

			remove_filter( $filter, 'convert_smilies', $prio );
			add_filter( $filter, [ $this->image, 'convertSmilies' ], $prio );
		}
	}

	/**
	 * Remove inline scripts from the HTML to parse
	 *
	 * @param string $html HTML content.
	 *
	 * @return string
	 */
	private function ignoreScripts( $html ) {
		return preg_replace( '/<script\b(?:[^>]*)>(?:.+)?<\/script>/Umsi', '', $html );
	}

	/**
	 * Remove noscript tags from the HTML to parse
	 *
	 * @param string $html HTML content.
	 *
	 * @return string
	 */
	private function ignoreNoscripts( $html ) {
		return preg_replace( '#<noscript>(?:.+)</noscript>#Umsi', '', $html );
	}

    /**
     * Checks if native lazyload is enabled for images
     **
     * @return bool
     */
    private function is_native_images(): bool {
        /**
         * Filters the use of native lazyload for images
         *
         * @param bool $use_native True to use native lazyload for images, false otherwise.
         */
        return (bool) apply_filters( 'rocket_use_native_lazyload', false );
    }
}
