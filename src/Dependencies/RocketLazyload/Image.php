<?php
/**
 * Handles lazyloading of images
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\Dependencies\RocketLazyload;

/**
 * A class to provide the methods needed to lazyload images in WP Rocket and Lazyload by WP Rocket
 */
class Image
{
    /**
     * Finds the images to be lazyloaded and call the callback method to replace them.
     *
     * @param string $html   Original HTML.
     * @param string $buffer Content to parse.
     * @return string
     */
    public function lazyloadImages($html, $buffer)
    {
        if (! preg_match_all('#<img(?<atts>\s.+)\s?/?>#iUs', $buffer, $images, PREG_SET_ORDER)) {
            return $html;
        }

        $images = array_unique($images, SORT_REGULAR);

        foreach ($images as $image) {
            $image = $this->canLazyload($image);
    
            if (! $image) {
                continue;
            }

            $image_lazyload  = $this->replaceImage($image);
            $image_lazyload .= $this->noscript($image[0]);
            $html            = str_replace($image[0], $image_lazyload, $html);

            unset($image_lazyload);
        }

        return $html;
    }

    /**
     * Applies lazyload on background images defined in style attributes
     *
     * @param string $html   Original HTML.
     * @param string $buffer Content to parse.
     * @return string
     */
    public function lazyloadBackgroundImages($html, $buffer)
    {
        if (! preg_match_all('#<(?<tag>div|section|span|li)\s+(?<before>[^>]+[\'"\s])?style\s*=\s*([\'"])(?<styles>.*?)\3(?<after>[^>]*)>#is', $buffer, $elements, PREG_SET_ORDER)) {
            return $html;
        }

        foreach ($elements as $element) {
            if ($this->isExcluded($element['before'] . $element['after'], $this->getExcludedAttributes())) {
                continue;
            }

            if (! preg_match('#background-image\s*:\s*(?<attr>\s*url\s*\((?<url>[^)]+)\))\s*;?#is', $element['styles'], $url)) {
                continue;
            }
 
            $url['url'] = trim($url['url'], '\'" ');

            if ($this->isExcluded($url['url'], $this->getExcludedSrc())) {
                continue;
            }

            $lazy_bg = $this->addLazyCLass($element[0]);
            $lazy_bg = str_replace($url[0], '', $lazy_bg);
            $lazy_bg = str_replace('<' . $element['tag'], '<' . $element['tag'] . ' data-bg="url(' . esc_attr($url['url']) . ')"', $lazy_bg);

            $html = str_replace($element[0], $lazy_bg, $html);
            unset($lazy_bg);
        }

        return $html;
    }

    /**
     * Add the identifier class to the element
     *
     * @param string $element Element to add the class to.
     * @return string
     */
    private function addLazyClass($element)
    {
        if (preg_match('#class=["\']?(?<classes>[^"\'>]*)["\']?#is', $element, $class)) {
            $classes = str_replace($class['classes'], $class['classes'] . ' rocket-lazyload', $class[0]);
            $element = str_replace($class[0], $classes, $element);

            return $element;
        }

        return preg_replace('#<(img|div|section|li|span)([^>]*)>#is', '<\1 class="rocket-lazyload"\2>', $element);
    }

    /**
     * Applies lazyload on picture elements found in the HTML.
     *
     * @param string $html   Original HTML.
     * @param string $buffer Content to parse.
     * @return string
     */
    public function lazyloadPictures($html, $buffer)
    {
        if (! preg_match_all('#<picture(?:.*)?>(?<sources>.*)</picture>#iUs', $buffer, $pictures, PREG_SET_ORDER)) {
            return $html;
        }

        $pictures = array_unique($pictures, SORT_REGULAR);
        $excluded = array_merge($this->getExcludedAttributes(), $this->getExcludedSrc());

        foreach ($pictures as $picture) {
            if ($this->isExcluded($picture[0], $excluded)) {
                continue;
            }

            if (preg_match_all('#<source(?<atts>\s.+)>#iUs', $picture['sources'], $sources, PREG_SET_ORDER)) {
                $sources = array_unique($sources, SORT_REGULAR);

                $lazy_sources = 0;

                foreach ($sources as $source) {
                    $lazyload_srcset = preg_replace('/([\s"\'])srcset/i', '\1data-lazy-srcset', $source[0]);
                    $html            = str_replace($source[0], $lazyload_srcset, $html);

                    unset($lazyload_srcset);
                    $lazy_sources++;
                }
            }

            if (0 === $lazy_sources) {
                continue;
            }

            if (! preg_match('#<img(?<atts>\s.+)\s?/?>#iUs', $picture[0], $img)) {
                continue;
            }

            $img = $this->canLazyload($img);

            if (! $img) {
                continue;
            }

            $img_lazy = $this->replaceImage($img);
            $html     = str_replace($img[0], $img_lazy, $html);

            unset($img_lazy);
        }

        return $html;
    }

    /**
     * Checks if the image can be lazyloaded
     *
     * @param Array $image Array of image data coming from Regex.
     * @return bool|Array
     */
    private function canLazyload($image)
    {
        if ($this->isExcluded($image['atts'], $this->getExcludedAttributes())) {
            return false;
        }

        // Given the previous regex pattern, $image['atts'] starts with a whitespace character.
        if (! preg_match('@\ssrc\s*=\s*(\'|")(?<src>.*)\1@iUs', $image['atts'], $atts)) {
            return false;
        }

        $image['src'] = trim($atts['src']);

        if ('' === $image['src']) {
            return false;
        }

        if ($this->isExcluded($image['src'], $this->getExcludedSrc())) {
            return false;
        }

        // Don't apply LazyLoad on images from WP Retina x2.
        if (function_exists('wr2x_picture_rewrite')) {
            if (wr2x_get_retina(trailingslashit(ABSPATH) . wr2x_get_pathinfo_from_image_src(trim($image['src'], '"')))) {
                return false;
            }
        }

        return $image;
    }

    /**
     * Checks if the provided string matches with the provided excluded patterns
     *
     * @param string $string          String to check.
     * @param array  $excluded_values Patterns to match against.
     * @return boolean
     */
    public function isExcluded($string, $excluded_values)
    {
        if (! is_array($excluded_values)) {
            (array) $excluded_values;
        }

        if (empty($excluded_values)) {
            return false;
        }

        foreach ($excluded_values as $excluded_value) {
            if (strpos($string, $excluded_value) !== false) {
                return true;
            }
        }
    
        return false;
    }

    /**
     * Returns the list of excluded attributes
     *
     * @return array
     */
    public function getExcludedAttributes()
    {
        /**
         * Filters the attributes used to prevent lazylad from being applied
         *
         * @since 1.0
         * @author Remy Perona
         *
         * @param array $excluded_attributes An array of excluded attributes.
         */
        return apply_filters(
            'rocket_lazyload_excluded_attributes',
            [
                'data-src=',
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
                'soliloquy-image',
                'loading="eager"',
                'swatch-img',
                'data-height-percentage',
                'data-large_image',
                'avia-bg-style-fixed',
            ]
        );
    }

    /**
     * Returns the list of excluded src
     *
     * @return array
     */
    public function getExcludedSrc()
    {
        /**
         * Filters the src used to prevent lazylad from being applied
         *
         * @since 1.0
         * @author Remy Perona
         *
         * @param array $excluded_src An array of excluded src.
         */
        return apply_filters(
            'rocket_lazyload_excluded_src',
            [
                '/wpcf7_captcha/',
                'timthumb.php?src',
                'woocommerce/assets/images/placeholder.png',
            ]
        );
    }

    /**
     * Replaces the original image by the lazyload one
     *
     * @param array $image Array of matches elements.
     * @return string
     */
    private function replaceImage($image)
    {
        $width  = 0;
        $height = 0;

        if (preg_match('@[\s"\']width\s*=\s*(\'|")(?<width>.*)\1@iUs', $image['atts'], $atts)) {
            $width = absint($atts['width']);
        }

        if (preg_match('@[\s"\']height\s*=\s*(\'|")(?<height>.*)\1@iUs', $image['atts'], $atts)) {
            $height = absint($atts['height']);
        }

        $placeholder_atts = preg_replace('@\ssrc\s*=\s*(\'|")(?<src>.*)\1@iUs', ' src="' . $this->getPlaceholder($width, $height) . '"', $image['atts']);

        $image_lazyload = str_replace($image['atts'], $placeholder_atts . ' data-lazy-src="' . $image['src'] . '"', $image[0]);

        if (! preg_match('@\sloading\s*=\s*(\'|")(?:lazy|auto)\1@i', $image_lazyload)) {
            $image_lazyload = str_replace('<img', '<img loading="lazy"', $image_lazyload);
        }

        /**
         * Filter the LazyLoad HTML output
         *
         * @since 1.0
         *
         * @param string $html Output that will be printed
         */
        $image_lazyload = apply_filters('rocket_lazyload_html', $image_lazyload);

        return $image_lazyload;
    }

    /**
     * Returns the HTML tag wrapped inside noscript tags
     *
     * @param string $element Element to wrap.
     * @return string
     */
    private function noscript($element)
    {
        return '<noscript>' . $element . '</noscript>';
    }

    /**
     * Applies lazyload on srcset and sizes attributes
     *
     * @param string $html HTML image tag.
     * @return string
     */
    public function lazyloadResponsiveAttributes($html)
    {
        $html = preg_replace('/[\s|"|\'](srcset)\s*=\s*("|\')([^"|\']+)\2/i', ' data-lazy-$1=$2$3$2', $html);
        $html = preg_replace('/[\s|"|\'](sizes)\s*=\s*("|\')([^"|\']+)\2/i', ' data-lazy-$1=$2$3$2', $html);
    
        return $html;
    }

    /**
     * Finds patterns matching smiley and call the callback method to replace them with the image
     *
     * @param string $text Content to search in.
     * @return string
     */
    public function convertSmilies($text)
    {
        global $wp_smiliessearch;

        if (! get_option('use_smilies') || empty($wp_smiliessearch)) {
            return $text;
        }

        $output = '';
        // HTML loop taken from texturize function, could possible be consolidated.
        $textarr = preg_split('/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between.
        $stop    = count($textarr);// loop stuff.

        // Ignore proessing of specific tags.
        $tags_to_ignore       = 'code|pre|style|script|textarea';
        $ignore_block_element = '';

        for ($i = 0; $i < $stop; $i++) {
            $content = $textarr[ $i ];

            // If we're in an ignore block, wait until we find its closing tag.
            if ('' === $ignore_block_element && preg_match('/^<(' . $tags_to_ignore . ')>/', $content, $matches)) {
                $ignore_block_element = $matches[1];
            }

            // If it's not a tag and not in ignore block.
            if ('' === $ignore_block_element && strlen($content) > 0 && '<' !== $content[0]) {
                $content = preg_replace_callback($wp_smiliessearch, [$this, 'translateSmiley'], $content);
            }

            // did we exit ignore block.
            if ('' !== $ignore_block_element && '</' . $ignore_block_element . '>' === $content) {
                $ignore_block_element = '';
            }

            $output .= $content;
        }

        return $output;
    }

    /**
     * Replace matches by smiley image, lazyloaded
     *
     * @param array $matches Array of matches.
     * @return string
     */
    private function translateSmiley($matches)
    {
        global $wpsmiliestrans;

        if (count($matches) === 0) {
            return '';
        }

        $smiley = trim(reset($matches));
        $img    = $wpsmiliestrans[ $smiley ];

        $matches    = [];
        $ext        = preg_match('/\.([^.]+)$/', $img, $matches) ? strtolower($matches[1]) : false;
        $image_exts = ['jpg', 'jpeg', 'jpe', 'gif', 'png'];

        // Don't convert smilies that aren't images - they're probably emoji.
        if (! in_array($ext, $image_exts, true)) {
            return $img;
        }

        /**
         * Filter the Smiley image URL before it's used in the image element.
         *
         * @since 2.9.0
         *
         * @param string $smiley_url URL for the smiley image.
         * @param string $img        Filename for the smiley image.
         * @param string $site_url   Site URL, as returned by site_url().
         */
        $src_url = apply_filters('smilies_src', includes_url("images/smilies/$img"), $img, site_url());

        // Don't LazyLoad if process is stopped for these reasons.
        if (is_feed() || is_preview()) {
            return sprintf(' <img src="%s" alt="%s" class="wp-smiley" /> ', esc_url($src_url), esc_attr($smiley));
        }

        return sprintf(' <img src="%s" data-lazy-src="%s" alt="%s" class="wp-smiley" /> ', $this->getPlaceholder(), esc_url($src_url), esc_attr($smiley));
    }

    /**
     * Returns the placeholder for the src attribute
     *
     * @since 1.2
     * @author Remy Perona
     *
     * @param int $width  Width of the placeholder image. Default 0.
     * @param int $height Height of the placeholder image. Default 0.
     * @return string
     */
    public function getPlaceholder($width = 0, $height = 0)
    {
        $width  = 0 === $width ? 0 : absint($width);
        $height = 0 === $height ? 0 : absint($height);

        $placeholder = str_replace(' ', '%20', "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 $width $height'%3E%3C/svg%3E");
        /**
         * Filter the image lazyLoad placeholder on src attribute
         *
         * @since 1.1
         *
         * @param string $placeholder Placeholder that will be printed.
         * @param int    $width Placeholder width.
         * @param int    $height Placeholder height.
         */
        return apply_filters('rocket_lazyload_placeholder', $placeholder, $width, $height);
    }
}
