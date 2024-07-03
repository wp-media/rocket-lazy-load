<?php
/**
 * Admin Page Class
 *
 * @package RocketLazyloadPlugin
 */

namespace RocketLazyLoadPlugin\Admin;

defined('ABSPATH') || die('Cheatin\' uh?');

use RocketLazyLoadPlugin\Dependencies\LaunchpadFrameworkOptions\Interfaces\SettingsAwareInterface;
use RocketLazyLoadPlugin\Dependencies\LaunchpadFrameworkOptions\Traits\SettingsAwareTrait;


/**
 * Admin page configuration
 *
 * @since 2.0
 * @author Remy Perona
 */
class AdminPage implements SettingsAwareInterface
{
	use SettingsAwareTrait;

    /**
     * Plugin slug
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var string
     */
    private $slug = 'rocket_lazyload';

    /**
     * Template path
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var string
     */
    private $template_path;

    /**
     * Constructor
     *
     * @param string      $template_path Template path.
     *
     * @author Remy Perona
     *
     * @since 2.0
     */
    public function __construct( string $template_path)
    {
        $this->template_path = $template_path;
    }

    /**
     * Registers plugin settings with WordPress
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function configure()
    {
       register_setting($this->getSlug(), $this->getSlug() . '_options');
    }

    /**
     * Gets the settings page title
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return string
     */
    public function getPageTitle()
    {
        return __('LazyLoad by WP Rocket', 'rocket-lazy-load');
    }

    /**
     * Gets the settings submenu title
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return string
     */
    public function getMenuTitle()
    {
        return __('LazyLoad', 'rocket-lazy-load');
    }

    /**
     * Gets the plugin slug
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Gets the plugin required capability
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return string
     */
    public function getCapability()
    {
        return 'manage_options';
    }

    /**
     * Renders the admin page template
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function renderPage()
    {
        $this->renderTemplate('admin-page');
    }

    /**
     * Renders the given template if it's readable.
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $template Template name.
     */
    protected function renderTemplate($template)
    {
        $template_path = $this->template_path . $template . '.php';

        if (! is_readable($template_path)) {
            return;
        }

        include $template_path;
    }
}
