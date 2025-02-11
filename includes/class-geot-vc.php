<?php

/**
 * Visual Composer Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_VC {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {
		if( apply_filters( 'geot/deactivate_vc_integration', false ) ) {
			return;
		}
		add_action( 'init', [ $this, 'hook_to_visual' ] );

		//WPBakery support
		add_filter('vc_basic_grid_filter_query_suppress_filters', '__return_false');

		// Upgrade Shortcodes
		add_action( 'admin_init', [ $this, 'action_upgrade' ] );
		add_action( 'admin_notices', [ $this, 'notice_upgrade' ] );
	}

	public function hook_to_visual() {
		// Check if Visual Composer is installed
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		// Updater
		require_once GEOWP_PLUGIN_DIR . 'includes/vc/vc_geot_updater.php';


		vc_add_shortcode_param( 'geot_dropdown', [ $this, 'dropdown_field' ] );
		
		$regions         = geot_country_regions();
		$dropdown_values = [ esc_html__( 'Choose one', 'geot' ) => '' ];

		if ( ! empty( $regions ) ) {
			foreach ( $regions as $r ) {
				if ( isset( $r['name'] ) ) {
					$dropdown_values[ $r['name'] ] = $r['name'];
				}
			}
		}

		$city_regions         = geot_city_regions();
		$city_dropdown_values = [ esc_html__( 'Choose one', 'geot' ) => '' ];

		if ( ! empty( $city_regions ) ) {
			foreach ( $city_regions as $k => $r ) {
				if ( isset( $r['name'] ) ) {
					$city_dropdown_values[ $r['name'] ] = $r['name'];
				}
			}
		}

		$state_regions         = geot_state_regions();
		$state_dropdown_values = [ esc_html__( 'Choose one', 'geot' ) => '' ];

		if ( ! empty( $state_regions ) ) {
			foreach ( $state_regions as $k => $r ) {
				if ( isset( $r['name'] ) ) {
					$state_dropdown_values[ $r['name'] ] = $r['name'];
				}
			}
		}

		$zip_regions         = geot_zip_regions();
		$zip_dropdown_values = [ esc_html__( 'Choose one', 'geot' ) => '' ];

		if ( ! empty( $zip_regions ) ) {
			foreach ( $zip_regions as $k => $r ) {
				if ( isset( $r['name'] ) ) {
					$zip_dropdown_values[ $r['name'] ] = $r['name'];
				}
			}
		}
		/*
		Add your Visual Composer logic here.
		Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

		More info: http://kb.wpbakery.com/index.php?title=Vc_map
		*/
		vc_map( [
			'name'                    => esc_html__( 'Target Countries', 'geot' ),
			'is_container'            => true,
			'content_element'         => true,
			'base'                    => 'vc_geotwp_country',
			'icon'                    => GEOWP_PLUGIN_URL . '/admin/img/world.png',
			'show_settings_on_create' => true,
			'category'                => esc_html__( 'Geotargeting', 'geot' ),
			'description'             => esc_html__( 'Place elements inside this geot container', 'geot' ),
			'html_template'           => GEOWP_PLUGIN_DIR . '/includes/vc/vc_geot.php',
			'js_view'                 => 'VcColumnView',
			"params"                  => [
				[
					"type"        => "dropdown",
					"class"       => '',
					"heading"     => esc_html__( 'Visibility', 'geot' ),
					"param_name"  => 'countries_mode',
					"value"       => [
						esc_html__( 'Show', 'geot' )	=> 'include',
						esc_html__( 'Hide', 'geot' )	=> 'exclude',
					],
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "textfield",
					"class"       => "",
					"heading"     => esc_html__( "Countries", 'geot' ),
					"param_name"  => "countries_input",
					"value"       => "",
					"description" => esc_html__( "Type country name or ISO code. Also you can write a comma separated list of countries", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "geot_dropdown",
					"class"       => "",
					"heading"     => esc_html__( "Regions", 'geot' ),
					"param_name"  => "countries_region",
					"multiple"    => true,
					"value"       => $dropdown_values,
					"description" => esc_html__( "Choose region name to show content to", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],
			],
		] );

		vc_map( [
			'name'                    => esc_html__( 'Target Cities', 'geot' ),
			'is_container'            => true,
			'content_element'         => true,
			'base'                    => 'vc_geotwp_city',
			'icon'                    => GEOWP_PLUGIN_URL . '/admin/img/cities.png',
			'show_settings_on_create' => true,
			'category'                => esc_html__( 'Geotargeting', 'geot' ),
			'description'             => esc_html__( 'Place elements inside this geot container', 'geot' ),
			'html_template'           => GEOWP_PLUGIN_DIR . '/includes/vc/vc_geot_city.php',
			'js_view'                 => 'VcColumnView',
			"params"                  => [
				[
					"type"        => "dropdown",
					"class"       => '',
					"heading"     => esc_html__( 'Visibility', 'geot' ),
					"param_name"  => 'cities_mode',
					"value"       => [
						esc_html__( 'Show', 'geot' )	=> 'include',
						esc_html__( 'Hide', 'geot' )	=> 'exclude',
					],
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "textfield",
					"class"       => "",
					"heading"     => esc_html__( "Cities", 'geot' ),
					"param_name"  => "cities_input",
					"value"       => "",
					"description" => esc_html__( "Type city name. Also you can write a comma separated list of cities", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "geot_dropdown",
					"class"       => "",
					"heading"     => esc_html__( "Regions", 'geot' ),
					"param_name"  => "cities_region",
					"value"       => $city_dropdown_values,
					"description" => esc_html__( "Choose region name to show content to", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],
			],
		] );

		vc_map( [
			'name'                    => esc_html__( 'Target States', 'geot' ),
			'is_container'            => true,
			'content_element'         => true,
			'base'                    => 'vc_geotwp_state',
			'icon'                    => GEOWP_PLUGIN_URL . '/admin/img/states.png',
			'show_settings_on_create' => true,
			'category'                => esc_html__( 'Geotargeting', 'geot' ),
			'description'             => esc_html__( 'Place elements inside this geot container', 'geot' ),
			'html_template'           => GEOWP_PLUGIN_DIR . '/includes/vc/vc_geot_state.php',
			'js_view'                 => 'VcColumnView',
			"params"                  => [
				[
					"type"        => "dropdown",
					"class"       => '',
					"heading"     => esc_html__( 'Visibility', 'geot' ),
					"param_name"  => 'states_mode',
					"value"       => [
						esc_html__( 'Show', 'geot' )	=> 'include',
						esc_html__( 'Hide', 'geot' )	=> 'exclude',
					],
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "textfield",
					"class"       => "",
					"heading"     => esc_html__( "States", 'geot' ),
					"param_name"  => "states_input",
					"value"       => "",
					"description" => esc_html__( "Type state name or ISO code. Also you can write a comma separated list of states", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "geot_dropdown",
					"class"       => "",
					"heading"     => esc_html__( "Regions", 'geot' ),
					"param_name"  => "states_region",
					"value"       => $state_dropdown_values,
					"description" => esc_html__( "Choose region name to show content to", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],
			],
		] );
		
		vc_map(	[
			'name'                    => esc_html__( 'Target Zip', 'geot' ),
			'is_container'            => true,
			'content_element'         => true,
			'base'                    => 'vc_geotwp_zip',
			'icon'                    => GEOWP_PLUGIN_URL . '/admin/img/world.png',
			'show_settings_on_create' => true,
			'category'                => esc_html__( 'Geotargeting', 'geot' ),
			'description'             => esc_html__( 'Place elements inside this geot container', 'geot' ),
			'html_template'           => GEOWP_PLUGIN_DIR . '/includes/vc/vc_geot_zip.php',
			'js_view'                 => 'VcColumnView',
			"params"                  => [
				[
					"type"        => "dropdown",
					"class"       => '',
					"heading"     => esc_html__( 'Visibility', 'geot' ),
					"param_name"  => 'zipcodes_mode',
					"value"       => [
						esc_html__( 'Show', 'geot' )	=> 'include',
						esc_html__( 'Hide', 'geot' )	=> 'exclude',
					],
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "textfield",
					"class"       => "",
					"heading"     => esc_html__( "Zips", 'geot' ),
					"param_name"  => "zipcodes_input",
					"value"       => "",
					"description" => esc_html__( "Type zip code. Also you can write a comma separated list of zip codes", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "geot_dropdown",
					"class"       => "",
					"heading"     => esc_html__( "Regions", 'geot' ),
					"param_name"  => "zipcodes_region",
					"value"       => $zip_dropdown_values,
					"description" => esc_html__( "Choose region name to show content to", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],
			],
		] );

		vc_map(	[
			'name'                    => esc_html__( 'Radius', 'geot' ),
			'is_container'            => true,
			'content_element'         => true,
			'base'                    => 'vc_geotwp_radius',
			'icon'                    => GEOWP_PLUGIN_URL . '/admin/img/world.png',
			'show_settings_on_create' => true,
			'category'                => esc_html__( 'Geotargeting', 'geot' ),
			'description'             => esc_html__( 'Place elements inside this geot container', 'geot' ),
			'html_template'           => GEOWP_PLUGIN_DIR . '/includes/vc/vc_geot_radius.php',
			'js_view'                 => 'VcColumnView',
			"params"                  => [
				[
					"type"        => "dropdown",
					"class"       => '',
					"heading"     => esc_html__( 'Visibility', 'geot' ),
					"param_name"  => 'radius_mode',
					"value"       => [
						esc_html__( 'Choose one', 'geot' )	=> '',
						esc_html__( 'Show', 'geot' )	=> 'include',
						esc_html__( 'Hide', 'geot' )	=> 'exclude',
					],
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"			=> "textfield",
					"class"			=> "",
					"heading"		=> esc_html__( "Radius", 'geot' ),
					"param_name"	=> "radius_km",
					"value"			=> "",
					"description"	=> esc_html__( "Type the range in km or miles", 'geot' ),
					'group'			=> esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "textfield",
					"class"       => "",
					"heading"     => esc_html__( "Latitude", 'geot' ),
					"param_name"  => "radius_lat",
					"value"       => "",
					"description" => esc_html__( "Type the latitude", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				],[
					"type"        => "textfield",
					"class"       => "",
					"heading"     => esc_html__( "Longitude", 'geot' ),
					"param_name"  => "radius_lng",
					"value"       => "",
					"description" => esc_html__( "Type the longitude", 'geot' ),
					'group'       => esc_html__( 'GeoTargeting', 'geot' ),
				]
			],
		] );

	}

	public  function dropdown_field( $settings, $value ) {
		$output     = '';
		$css_option = str_replace( '#', 'hash-', vc_get_dropdown_option( $settings, $value ) );
		$output     .= '<select name="'
		               . $settings['param_name']
		               . '" class="wpb_vc_param_value wpb-input wpb-select '
		               . $settings['param_name']
		               . ' ' . $settings['type']
		               . ' ' . $css_option
		               . '" data-option="' . $css_option . '" multiple>';

		$value = is_array( $value ) ? $value : explode( ',', $value );
		if ( ! empty( $settings['value'] ) ) {
			foreach ( $settings['value'] as $index => $data ) {
				if ( is_numeric( $index ) && ( is_string( $data ) || is_numeric( $data ) ) ) {
					$option_label = $data;
					$option_value = $data;
				} elseif ( is_numeric( $index ) && is_array( $data ) ) {
					$option_label = isset( $data['label'] ) ? $data['label'] : array_pop( $data );
					$option_value = isset( $data['value'] ) ? $data['value'] : array_pop( $data );
				} else {
					$option_value = $data;
					$option_label = $index;
				}

				$selected            = '';
				$option_value_string = (string) $option_value;
				
				if( in_array( $option_value_string, $value ) ) {
					$selected = ' selected="selected"';
				}

				$option_class = str_replace( '#', 'hash-', $option_value );
				$output       .= '<option class="' . esc_attr( $option_class ) . '" value="' . esc_attr( $option_value ) . '"' . $selected . '>'
				                 . htmlspecialchars( $option_label ) . '</option>';
			}
		}
		$output .= '</select>';

		return $output;
	}

	/**
	 * Admin Notice to upgrade
	 * @return mixed
	 */
	public function notice_upgrade() {

		// Check if Visual Composer is installed
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		$status = get_option('geot_vc_upgraded', false);

		if( $status )
			return;

		$class = 'notice notice-warning ';
		$title = esc_html__( 'GeotargetingWP', 'geot' );

		$link		= add_query_arg( 'geot_upgrade', 1 );
    	$message	= sprintf( __( 'We need to update your database, please make a backup and <a href="%s">click here to start</a>', 'geot' ), esc_url( $link ) );
 
    	printf( '<div class="%s"><h2>%s</h2><p>%s</p></div>',
    		esc_attr( $class ),
    		esc_html( $title ),
    		wp_kses_post( $message )
    	);
	}

	/**
	 * Action upgrade
	 * @return redirect
	 */
	function action_upgrade() {
		if( isset( $_GET['geot_upgrade'] ) && $_GET['geot_upgrade'] == 1 ) {

			global $wpdb;

			// Country
			$args = [
				'ini_find'		=> '[vc_geot ',
				'ini_replace' 	=> '[vc_geotwp_country ',
				'fin_find'		=> '[/vc_geot]',
				'fin_replace' 	=> '[/vc_geotwp_country]',
				'like' 			=> '%[vc_geot%',
				'notlike'		=> '%[vc_geotwp_country%',
			];
			geotwp_update_like($args);


			// City
			$args = [
				'ini_find'		=> '[vc_geot_city ',
				'ini_replace' 	=> '[vc_geotwp_city ',
				'fin_find'		=> '[/vc_geot_city]',
				'fin_replace' 	=> '[/vc_geotwp_city]',
				'like' 			=> '%[vc_geot_city%',
				'notlike'		=> '%[vc_geotwp_city%',
			];
			geotwp_update_like($args);


			// States
			$args = [
				'ini_find'		=> '[vc_geot_state ',
				'ini_replace' 	=> '[vc_geotwp_state ',
				'fin_find'		=> '[/vc_geot_state]',
				'fin_replace' 	=> '[/vc_geotwp_state]',
				'like' 		=> '%[vc_geot_state%',
				'notlike'	=> '%[vc_geotwp_state%',
			];
			geotwp_update_like($args);


			update_option('geot_vc_upgraded', true);
			$link = remove_query_arg('geot_upgrade' );

			wp_redirect($link);
			exit();
		}
	}

}

add_action( 'init', function () {
	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_VC_GeotWP_Country extends WPBakeryShortCodesContainer {
		}

		class WPBakeryShortCode_VC_GeotWP_City extends WPBakeryShortCodesContainer {
		}

		class WPBakeryShortCode_VC_GeotWP_State extends WPBakeryShortCodesContainer {
		}

		class WPBakeryShortCode_VC_GeotWP_Zip extends WPBakeryShortCodesContainer {
		}

		class WPBakeryShortCode_VC_GeotWP_Radius extends WPBakeryShortCodesContainer {
		}
	}
} );
