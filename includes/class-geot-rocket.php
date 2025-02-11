<?php

use function GeotCore\check_key;

class GeotWP_Rocket {

	/**
	 * Construct
	 */
	public function __construct() {

		remove_action( 'geotWP/activated', 'rocket_activate_geotargetingwp', 11 );
		$name = defined('WP_ROCKET_SLUG') ? WP_ROCKET_SLUG : 'wp_rocket_settings';
		add_action( 'update_option_' . $name, [ $this, 'update_rocket_settings' ], 15 );
		// run an action schedule to clear all upon saving our settings
		add_action( 'geot_rocket_activate_geotargetingwp', function (){
			GeotWP_Rocket::activate_geotargetingwp();
		} );
		$opts = geot_settings();
		$enabled = check_key( $opts, 'wp_rocket_cache', false);
		if(! $enabled ) {
			add_filter('rocket_geotargetingwp_enabled_cookies', function(){ return [];});
		}
		add_filter( 'rocket_htaccess_mod_rewrite', [ $this, 'update_mod_rewrite' ], 999 );
		add_filter( 'before_rocket_htaccess_rules', [ $this, 'update_htaccess' ], 999 );

		add_action( 'geotWP/activated', [ self::class, 'activate_geotargetingwp'], 12 );
		add_action( 'geotWP/deactivated', [ self::class, 'deactivate_geotargetingwp'], 12 );

		add_filter( 'geot_rocket_country', 'geotwp_spaces_by_hyphen', 10, 1);
		add_filter( 'geot_rocket_state', 'geotwp_spaces_by_hyphen', 10, 1);
		add_filter( 'geot_rocket_city', 'geotwp_spaces_by_hyphen', 10, 1);
		if( $cookie = apply_filters( 'geot/custom_rocket_cookie', false ) ) {
			add_filter( $cookie['name'], 'geotwp_spaces_by_hyphen', 10, 1);
		}
	}

	/**
	 * When it updates the rocket options
	 * @return mixed
	 */
	public function update_rocket_settings() {
		remove_filter( 'rocket_htaccess_mod_rewrite', '__return_false', 72 );
	}

	/**
	 * Get all the variables
	 * @return ARRAY
	 */
	public static function get_vars_geot() {

		$enable = apply_filters(
			'rocket_geotargetingwp_enabled_cookies',
			[ 'country' ]
		);
		
		return (array)$enable;
	}

	/**
	 * Create enviroment variable
	 * @param  STRING $early
	 * @return STRING
	 */
	public static function update_htaccess($early) {

		if( count( self::get_vars_geot() ) == 0 )
			return $early;

		$output = '<IfModule mod_setenvif.c>' . PHP_EOL;

		foreach( self::get_vars_geot() as $var_geot ) {
			$output .= 'SetEnvIfNoCase Cookie geot_rocket_'.$var_geot.'=([^;]+) geot_'.$var_geot.'=$1' . PHP_EOL;
		}

		$output .='</IfModule>' . PHP_EOL;

		return $output . $early;
	}

	/**
	 * Lets add the geot variable to htaccess
	 * @param  STRING $rules
	 * @return STRING
	 */
	public static function update_mod_rewrite($rules) {

		if( count( self::get_vars_geot() ) == 0 )
			return $rules;


		$setenv = $setcond = '';
		foreach( self::get_vars_geot() as $var_geot ) {
			$setenv .= '-%{ENV:geot_'.$var_geot.'}';
			$setcond .= 'RewriteCond %{ENV:geot_'.$var_geot.'} .+' . PHP_EOL ;
		}

		$replaceWith = '}'.$setenv.'.html';


		$suf_pos = strrpos($rules, 'RewriteCond');
		if ($suf_pos === false) return $rules;

		$prefix = substr ( $rules, 0, $suf_pos);
		$suffix = substr ( $rules, $suf_pos );
		$suffix = str_replace('}.html', $replaceWith, $suffix);
		$suffix = $setcond . $suffix;

		return $prefix . $suffix;
	}

	/**
	 * Activate Geot
	 * @return Mixed
	 */
	public static function activate_geotargetingwp() {
		add_filter( 'rocket_htaccess_mod_rewrite', [ self::class, 'update_mod_rewrite' ], 999 );
		add_filter( 'before_rocket_htaccess_rules', [ self::class, 'update_htaccess'], 999 );
		add_filter('rocket_cache_dynamic_cookies', 'rocket_add_geotargetingwp_dynamic_cookies');
		add_filter('rocket_cache_mandatory_cookies', 'rocket_add_geotargetingwp_mandatory_cookie');
	
		self::rocket_flush();
	}

	/**
	 * Deactivate Geot
	 * @return mixed
	 */
	public static function deactivate_geotargetingwp() {
		remove_filter( 'rocket_htaccess_mod_rewrite', [  self::class, 'update_mod_rewrite' ], 999 );
		remove_filter( 'before_rocket_htaccess_rules', [  self::class, 'update_htaccess'], 999 );

		remove_filter( 'rocket_cache_dynamic_cookies'  , 'rocket_add_geotargetingwp_dynamic_cookies' );
		remove_filter( 'rocket_cache_mandatory_cookies', 'rocket_add_geotargetingwp_mandatory_cookie' );

		self::rocket_flush();
	}

	/**
	 * Flush Rocket
	 * @return mixed
	 */
	public static function rocket_flush() {
		if (function_exists('flush_rocket_htaccess')) {  // just in case whilst helper not in core
			
			// Update the WP Rocket rules on the .htaccess file.
			if ( function_exists('get_home_path')) {
				// if get_home_path function is not loaded Rocket code would 500 error on dashboard
				flush_rocket_htaccess();
			}
			// Regenerate the config file.
			rocket_generate_config_file();

			// Clear WP Rocket cache
			rocket_clean_domain();

		}
	}
}