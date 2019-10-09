<?php
/**
 * Contains the Heartbeat_Control\Notices class.
 *
 * simple to use message flashbag for amdin base on user_id.
 * @package Heartbeat_Control
 */

namespace RocketLazyLoadPlugin\Admin;

defined('ABSPATH') || die('Cheatin\' uh?');

class Notices {
	
	protected static $instance;
	
	protected $transient = 'rocket-lazy-load_notices';
	protected $notices = false;
	protected $user_id;

	static function get_instance(){
		if( !self::$instance ){
			self::$instance = new Notices;
		}
		return self::$instance;
	}
	
	function __construct( $transient = null ){
		if(!is_null($transient)){
			$this->transient = $transient;
		}
		$this->notices = get_transient( $this->transient );
		$this->user_id = get_current_user_id();
	}

	/**
	 * append a new notice for the current user
	 *
	 * @param string $class
	 * the class of the message use for styling and typing
	 * @param string $notice
	 * the message of the notice
	 */
	public function append( $class, $notice ){
		$new_notices = array();
		if( $this->notices ){
			$new_notices = json_decode( $this->notices, true );
		}
		$new_notices[$this->user_id][] = array(
			'class' => $class,
			'notice' => $notice,
		);
		$this->notices = json_encode( $new_notices );
		set_transient($this->transient,$this->notices,30);
	}

	/**
	 * echo notices for the current user
	 *
	 * @param boolean $trash [optional]
	 * if true the notices will be trash after the echo
	 */
	public function echoNotices( $trash = true ){
		if($this->notices ){
			$notices = json_decode($this->notices,true);
			if(isset($notices[$this->user_id])){
				foreach ($notices[$this->user_id] as $n){
					echo '<div class="notice notice-'.$n['class'].' is-dismissible"><p>'.$n['notice'].'</p></div>';
				}
				if($trash){
					$this->trash( $notices, $this->user_id );
				}
			}
		}
	}

	/**
	 * return the notices for the current user
	 *
	 * @param boolean $trash [optional]
	 * if true the notices will be trash after get returned
	 * @return mixed output a array if a notice or more exist, a false if not
	 */
	public function get( $trash = true ){
		if( $this->notices ){
			$notices = json_decode( $this->notices,true );
			if(isset( $notices[$this->user_id] ) ){
				if( $trash ){
					$this->trash( $notices, $this->user_id );
				}
				return $notices[$this->user_id];
			}
		}
		return false;
	}

	/**
	 * unset notice for a given user and save the new notices as a transient
	 *
	 * @param array $notices
	 * a array of $notices to clean
	 * @param int $user_id [optional]
	 * an user id, if null it's will find the current user id
	 */
	private function trash( $notices, $user_id = null ){
		if( is_null( $user_id ) ){
			$user_id = $this->user_id;
		}
		if( isset( $notices[$user_id] ) ){
			unset( $notices[$user_id] );
		}
		set_transient( $this->transient, json_encode( $notices ), 30 );
	}

}
