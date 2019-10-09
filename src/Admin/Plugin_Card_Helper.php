<?php
/**
 * Contains the RocketLazyLoadPlugin\Admin\Plugin_Card_Helper class.
 * This check plugin info from plugins_api and help to build a functional installation plugin card
 */

namespace RocketLazyLoadPlugin\Admin;

class Plugin_Card_Helper {

	protected $nonce = 'plugin_card_helper_wpnonce';
	protected $plugin_slug;
	protected $plugin_file_path;
	protected $plugin_information;
	protected $activated;
	protected $installed;
	protected $wp_compatibility;
	protected $php_compatibility;
	protected $can_install;
	protected $args;
	protected $params = array(
		'title' => null,
		'description' => null,
		'icon' => null,
		'status_text' => null,
		'button_text' => null,
		'install_url' => null,
	);
	protected $helper_callback;
	protected $template_args;
	protected $init = false;

	/**
	 * constructor method, it's construct things.
	 * Set some basic parameters and register controller soon as possible.
	 * Else in some context install and activation route will not be register.
	 * @param $args array, required index plugin_slug. use this array to pass param ( force_activation active and install )
	 * @param $template_args mixed, what ever param you want to pass for the template.
	 * @return void
	 */
	public function __construct( $args = null, $template_args = null ){
		$this->args = wp_parse_args( $args, array(
			'plugin_slug' => null,
			'force_activation' => true,
		) );
		if( is_null( $this->args['plugin_slug'] ) ){ return; }
		$this->plugin_slug = preg_replace( '@[^a-z0-9_-]@', '', strtolower( (string)$this->args['plugin_slug'] ) );
		$this->template_args = $template_args;

		if( !$this->is_installed() ){
			add_action( 'admin_post_install_plugin_'.$this->plugin_slug, array( $this, 'install_callback' ) );
		}
		if( !$this->is_activated() ) {
			add_action( 'admin_post_activate_plugin_' . $this->plugin_slug, array($this, 'activate_callback') );
		}
	}

	/**
	 * init method, initialise things.
	 * Separate init form constructor, because route registering need to be early and this do not.
	 * This is execute only if install or activation route are reached or helper method is call.
	 * @return void
	 */
	protected function init(){
		if( $this->init ){ return; }
		require_once ABSPATH.'wp-admin/includes/plugin-install.php';
		require_once ABSPATH.'wp-admin/includes/plugin.php';

		$this->is_installed();
		$this->is_activated();

		$this->plugin_information = plugins_api( 'plugin_information', array(
			'slug'		=> $this->plugin_slug,
			'fields' 	=> array(
				'short_description' => true,
				'icons' 			=> true,
				'sections' 			=> false,
				'rating' 			=> false,
				'ratings' 			=> false,
				'downloaded' 		=> false,
				'last_updated' 		=> false,
				'added' 			=> false,
				'tags' 				=> false,
				'homepage' 			=> false,
				'donate_link' 		=> false,
			)
		) );
		
		if( is_wp_error( $this->plugin_information ) ){
			$this->can_install = false;
		}else{
			$this->wp_compatibility = ( $this->plugin_information->requires <= get_bloginfo('version') );
			$this->php_compatibility = ( $this->plugin_information->requires_php <= phpversion() );
		}

		$this->init = true;
	}

	//-- GETTER

	/**
	 * get plugin information return by wordpress function plugins_api()
	 * check https://developer.wordpress.org/reference/functions/plugins_api/ form more information
	 * @return array if the instance has reach information from wordpress plugin repository, null if not.
	 */
	public function get_plugin_information(){
		if( is_wp_error( $this->plugin_information ) ){ return null; }
		return $this->plugin_information;
	}

	/**
	 * get the plugin title
	 * @return string, the plugin title
	 */
	public function get_title(){
		$pi = ( is_wp_error( $this->plugin_information ) )?'':$this->plugin_information->name;
		return ( !is_null($this->params['title']) ) ?$this->params['title']:$pi;
	}

	/**
	 * get the plugin description
	 * @return string, the plugin short description
	 */
	public function get_description(){
		$pi = ( is_wp_error( $this->plugin_information ) )?'':$this->plugin_information->short_description;
		return ( !is_null($this->params['description']) ) ?$this->params['description']:$pi;
	}

	/**
	 * get the plugin icon
	 * @return string, the plugin icon as a img tag
	 */
	public function get_icon(){
		$pi = ( is_wp_error( $this->plugin_information ) )?'':'<img src="'.$this->plugin_information->icons['2x'].'"/>';
		return ( !is_null( $this->params['icon'] ) )?$this->params['icon']:$pi;
	}

	/**
	 * get the plugin activation ans installation status
	 * @return string, the plugin status as a one of this string [ 'activated', 'installed', 'not_installed' ]
	 */
	public function get_status(){
		return $this->is_installed()?( $this->is_activated()?'activated':'installed' ):'not_installed';
	}

	/**
	 * get the plugin status text
	 * @param $status string, override the current status by this param
	 * @return string, the plugin status text based on the current or given one.
	 */
	public function get_status_text( $status = null ){
		$s = ( is_string( $status ) && !empty( $status ) )?$status:$this->get_status();
		$st = array(
			'activated' => __( 'activated', 'rocket-lazy-load' ),
			'installed' =>  __( 'installed', 'rocket-lazy-load' ),
			'not_installed' => __( 'not installed', 'rocket-lazy-load' ),
		);
		if( isset($this->params['status_text'][$s] ) ){
			return $this->params['status_text'][$s];
		}
		return ( isset( $st[$s] ) )?$st[$s]:$st;
	}

	/**
	 * get the plugin button text
	 * @param $status string, override the current status by this param
	 * @return string, the plugin button text based on the current or given one.
	 */
	public function get_button_text( $status = null ){
		$s = ( is_string( $status ) && !empty( $status ) )?$status:$this->get_status();
		$bt = array(
			'activated' => __( 'Already activated', 'rocket-lazy-load' ),
			'installed' =>  __( 'Activate plugin', 'rocket-lazy-load' ),
			'not_installed' => __( 'Install plugin', 'rocket-lazy-load' ),
		);
		if( isset($this->params['button_text'][$s] ) ){
			return $this->params['button_text'][$s];
		}
		return ( isset( $bt[$s] ) )?$bt[$s]:$bt;
	}

	/**
	 * get the plugin activation or installation url
	 * @param $status string, override the current status by this param
	 * @return string, the appropriate activation/installation url based on the current or given one.
	 */
	public function get_install_url( $status = null ){
		$s = ( is_string( $status ) && !empty( $status ) )?$status:$this->get_status();
		$bl = array(
			'activated' => "#",
			'installed' => add_query_arg( array(
				'action' => 'activate_plugin_'.$this->plugin_slug,
				'_wpnonce' => wp_create_nonce( $this->nonce ),
				'_wp_http_referer' => rawurlencode( $this->get_current_url() ),
			), admin_url( 'admin-post.php' )),
			'not_installed' => add_query_arg( array(
				'action' => 'install_plugin_'.$this->plugin_slug,
				'_wpnonce' => wp_create_nonce( $this->nonce ),
				'_wp_http_referer' => rawurlencode( $this->get_current_url() ),
			), admin_url( 'admin-post.php' ) ),
		);
		if( isset($this->params['install_url'][$s] ) ){
			return $this->params['install_url'][$s];
		}
		return ( isset( $bl[$s] ) )?$bl[$s]:$bl;
	}

	/**
	 * get the plugin activation status as a boolean
	 * @return boolean, true if plugin is activated false if not
	 */
	public function is_activated(){
		if( is_null( $this->activated ) ) {
			require_once ABSPATH.'wp-admin/includes/plugin.php';
			if( is_null( $this->installed ) ) { $this->is_installed(); }
			$this->activated = is_plugin_active( $this->plugin_file_path );
		}
		
		return $this->activated;
	}

	/**
	 * get the plugin installation status as a boolean
	 * @return boolean, true if plugin is installed false if not
	 */
	public function is_installed(){
		if( is_null( $this->installed ) ) {
			require_once ABSPATH.'wp-admin/includes/plugin.php';
			$installed_plugins = get_plugins();
			$m = array();
			foreach ( $installed_plugins as $k => $p ) {
				preg_match('/([a-zA-Z0-9-_\s]+)\/([a-zA-Z0-9-_]+)\.php/', $k, $m );
				if( isset( $m[2] ) &&  $this->plugin_slug === $m[2] ){
					$this->plugin_file_path = $k;
					$this->installed = true;
					break;
				}
			}
		}
		return $this->installed;
	}


	//-- SETTER

	/**
	 * set a title override
	 * @param $title string, whatever you want, a appropriate title preferably
	 * @return void
	 */
	public function set_title( $title ){
		if( is_string( $title ) ){
			$this->params['title'] = $title;
		}
	}

	/*
	 * set a description override
	 * @param $desc string, your description
	 * @return void
	 */
	public function set_description( $desc ){
		if( is_string( $desc ) ){
			$this->params['description'] = $desc;
		}
	}

	/**
	 * set a icon override
	 * @param $string string, your icon, has a tag... no ? whatever.
	 * @return void
	 */
	public function set_icon( $string ){
		if( is_string( $string ) ){
			$this->params['icon'] = $string;
		}
	}

	/**
	 * set status text override
	 * @param $array array, a array of strings key must be valid status [ 'activated', 'installed', 'not_installed' ]
	 * @return void
	 */
	public function set_status_text( $array ){
		if( is_array( $array ) && !empty( $array ) ){
			$this->params['status_text'] = $array;
		}
	}

	/**
	 * set button text override
	 * @param $array array, a array of strings key must be valid status [ 'activated', 'installed', 'not_installed' ]
	 * @return void
	 */
	public function set_button_text( $array ){
		if( is_array( $array ) && !empty( $array ) ){
			$this->params['button_text'] = $array;
		}
	}

	/**
	 * override helper default behavior with a callable
	 * @param $callback callable, your callable.
	 * @return void
	 */
	public function set_helper_callback( $callback ){
		if( is_callable( $callback ) ){
			$this->helper_callback = $callback;
		}
	}

	//-- Install and activation route and logic

	/**
	 * install plugin controller
	 * @return void
	 */
	public function install_callback(){
		if ( !check_admin_referer( $this->nonce ) ) { return false; }
		if ( !current_user_can( is_multisite() ? 'manage_network_plugins' : 'install_plugins' ) ){ return false; }
		
		$notices = Notices::get_instance();
		$result = $this->install();
		
		if ( is_wp_error( $result ) ) {
			$notices->append( 'error', $result->get_error_code().' : '.$result->get_error_message() );
			wp_safe_redirect( wp_get_referer() );
		}
		
		if( $this->args['force_activation'] ){
			$result = $this->activate();
			if ( is_wp_error( $result ) ) {
				//$notices->append( 'error', $result->get_error_code().' : '.$result->get_error_message() );
				wp_safe_redirect( wp_get_referer() );
			}
			$notices->append( 'success', __( 'This plugin has been successfully installed and activated.', 'heartbeat-control' ) );
		}else{
			$notices->append( 'success', __( 'This plugin has been successfully installed.', 'heartbeat-control' ) );
		}
		
		wp_safe_redirect( wp_get_referer() );
	}

	/**
	 * activate plugin controller
	 * @return void
	 */
	public function activate_callback(){
		if ( !check_admin_referer( $this->nonce ) ) { return false; }
		if ( !current_user_can( is_multisite()?'manage_network_plugins':'install_plugins' ) ){ return false; }

		$notices = Notices::get_instance();
		$result = $this->activate();
		
		if ( is_wp_error( $result ) ) {
			$notices->append( 'error', $result->get_error_code().' : '.$result->get_error_message() );
			wp_safe_redirect( wp_get_referer() );
		}
		$notices->append( 'success', __( 'This plugin has been successfully activated.', 'heartbeat-control' ) );
		wp_safe_redirect( wp_get_referer() );
	}

	/**
	 * install plugin
	 * @return void
	 */
	protected function install() {
		$this->init();
		if( $this->installed ){ return null; }
		require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';

		ob_start();
		@set_time_limit( 0 );
		$upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );
		$result = $upgrader->install( $this->plugin_information->download_link );
		ob_end_clean();
		if ( is_wp_error( $result ) ) { return $result; }
		clearstatcache();
		$this->plugin_file_path = $upgrader->plugin_info();
		$this->installed = true;
		return null;
	}

	/**
	 * activate plugin
	 * @return void
	 */
	protected function activate(){
		$this->init();
		if( $this->is_activated() ){ return null; }
		require_once ABSPATH.'wp-admin/includes/plugin-install.php';
		$result = activate_plugin( $this->plugin_file_path, false, is_multisite() );
		if ( is_wp_error( $result ) ) { return $result; }
		$this->activated = true;
		return null;
	}

	//-- Helper

	/**
	 * card helper, construct a functional card.
	 * @param $echo boolean, echo the result if true
	 * @return void if echo is false, else it's return the card as a sting.
	 */
	public function helper( $echo = true ){
		$this->init();
		if( false === $echo ){ ob_start(); }
		if( is_callable( $this->helper_callback ) ){
			call_user_func( $this->helper_callback, $this );
		}else{
			$this->default_helper();
		}
		if( false === $echo ){
			$r = ob_get_contents();
			ob_end_clean();
			return $r;
		}
	}

	/**
	 * card helper, the real one.
	 * @return void
	 */
	protected function default_helper(){
		$template_args = $this->template_args;
		$helper = $this;
		$file_paths = array(
			ROCKET_LL_PATH.'views/plugin-cards/'.$this->plugin_slug.'.php',
			ROCKET_LL_PATH.'views/plugin-cards/default.php',
		);
		foreach ( $file_paths as $fp ){
			if( file_exists( $fp ) ){ include( $fp ); break; }
		}
	}

	//-- tools

	/**
	 * rebuilt current url
	 * @return string, the current url.
	 */
	public function get_current_url() {
		$port = (int) $_SERVER['SERVER_PORT'];
		$port = 80 !== $port && 443 !== $port ? ( ':' . $port ) : '';
		$url  = ! empty( $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] ) ? $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] : ( ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
		return 'http' . ( is_ssl() ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'] . $port . $url;
	}

}
