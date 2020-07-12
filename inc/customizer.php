<?php
/**
 * CAWeb Customizer
 *
 * @link https://codex.wordpress.org/Theme_Customization_API
 * @package CAWeb
 */

add_action( 'customize_preview_init', 'caweb_customize_preview_init' );
add_action( 'customize_controls_enqueue_scripts', 'caweb_customize_controls_enqueue_scripts' );
add_action( 'customize_controls_print_styles', 'caweb_customize_controls_print_styles' );
add_action( 'customize_register', 'caweb_customize_register' );

/**
 * CAWeb Customizer Preview Init
 * Fires once the Customizer preview has initialized and JavaScript settings have been printed.
 *
 * @link https://developer.wordpress.org/reference/hooks/customize_preview_init/
 * @return void
 */
function caweb_customize_preview_init() {
	wp_register_script( 'caweb-customizer-script', caweb_get_min_file( '/js/theme-customizer.js', 'js' ), array( 'jquery', 'customize-preview' ), wp_get_theme( 'CAWeb' )->get( 'Version' ), true );

	wp_enqueue_script( 'caweb-customizer-script' );

	/* Remove Divi Customizer Customizer Scripts */
	wp_dequeue_script( 'divi-customizer' );
}

/**
 * CAWeb Customizer Enqueue Scripts
 * Enqueues Customizer control scripts.
 *
 * @link https://developer.wordpress.org/reference/hooks/customize_controls_enqueue_scripts/
 * @return void
 */
function caweb_customize_controls_enqueue_scripts() {

	$bootstrap_css = caweb_get_min_file( '/css/bootstrap.css' );
	$customizer_css = caweb_get_min_file( '/css/theme-customizer.css' );

	wp_enqueue_style( 'caweb-bootstrap-styles', $bootstrap_css, array(), CAWEB_VERSION );
	wp_enqueue_style( 'caweb-customizer-styles', $customizer_css, array(), CAWEB_VERSION );

	wp_register_script( 'caweb-customize-controls-script', caweb_get_min_file( '/js/theme-customizer-controls.js', 'js' ), array(), wp_get_theme( 'CAWeb' )->get( 'Version' ), true );

	$schemes = array();
	foreach( caweb_template_versions() as $v => $label ){
		$schemes["$v"] = caweb_color_schemes( $v );
	}

	wp_localize_script(
		'caweb-customize-controls-script',
		'caweb_customizer_args',
		array(
			'caweb_colorschemes' => $schemes
		)
	);

	wp_enqueue_script( 'caweb-customize-controls-script' );
}

/**
 * CAWeb Customizer Styles
 * Fires when Customizer control styles are printed.
 *
 * @link https://developer.wordpress.org/reference/hooks/customize_controls_print_styles/
 * @return void
 */
function caweb_customize_controls_print_styles() {
}

/**
 * CAWeb Register Customizer
 *
 * @link https://developer.wordpress.org/reference/hooks/customize_register/
 * @param  WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 *
 * @return void
 */
function caweb_customize_register( $wp_customize ) {
	$site_version = caweb_template_version();

	/* Remove Divi Customization Panels and Sections */
	$divi_panels = array(
		'et_divi_general_settings',
		'et_divi_header_panel',
		'et_divi_footer_panel',
		'et_divi_blog_settings',
		'et_divi_buttons_settings',
		'et_divi_mobile',
	);

	foreach ( $divi_panels as $p ) {
		$wp_customize->remove_panel( $p );
	}

	$wp_customize->remove_section( 'et_color_schemes' );
	$wp_customize->remove_section( 'themes' );
	$wp_customize->remove_panel( 'themes' );
	$wp_customize->remove_section( 'custom_css' );

	/*
	All CAWeb Option sections, settings, and controls will be added here
	*/
	$wp_customize->add_panel(
		'caweb_options',
		array(
			'title'    => 'CAWeb Options',
			'priority' => 30,
		)
	);

	/* General Settings */
	caweb_customize_register_general_settings( $wp_customize );

	/* Utility Header */
	caweb_customize_register_utility_header_settings( $wp_customize );

	/* Page Header */
	caweb_customize_register_page_header( $wp_customize );

	/* Google */
	$wp_customize->add_section(
		'caweb_google',
		array(
			'title'    => 'Google',
			'priority' => 30,
			'panel'    => 'caweb_options',
		)
	);

	$wp_customize->add_setting(
		'ca_google_search_id',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_google_search_id', '' ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_google_search_id',
			array(
				'label'      => 'Search Engine ID',
				'type'       => 'text',
				'section'    => 'caweb_google',
				'settings'   => 'ca_google_search_id',
			)
		)
	);

	$wp_customize->add_setting(
		'ca_google_analytic_id',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_google_analytic_id', '' ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_google_analytic_id',
			array(
				'label'      => 'Analytics ID',
				'type'       => 'text',
				'section'    => 'caweb_google',
				'settings'   => 'ca_google_analytic_id',
			)
		)
	);

	$wp_customize->add_setting(
		'ca_google_meta_id',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_google_meta_id', '' ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_google_meta_id',
			array(
				'label'      => 'Meta ID',
				'type'       => 'text',
				'section'    => 'caweb_google',
				'settings'   => 'ca_google_meta_id',
			)
		)
	);

	$wp_customize->add_setting(
		'ca_google_trans_enabled',
		array(
			'type'    => 'option',
			'default' => get_option( 'ca_google_trans_enabled' ),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_google_trans_enabled',
			array(
				'label'      => 'Enable Google Translate',
				'type'       => 'radio',
				'choices'    => array(
					'none'     => 'None',
					'standard' => 'Standard',
					'custom'   => 'Custom',
				),
				'section'    => 'caweb_google',
				'settings'   => 'ca_google_trans_enabled',
			)
		)
	);

	$wp_customize->add_setting(
		'ca_google_trans_page',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_google_trans_page', '' ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_google_trans_page',
			array(
				'label'           => 'Translate Page',
				'type'            => 'text',
				'section'         => 'caweb_google',
				'settings'        => 'ca_google_trans_page',
				'active_callback' => 'caweb_customizer_google_trans_custom_option',
			)
		)
	);

	$wp_customize->add_setting(
		'ca_google_trans_icon',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_google_trans_icon', 'globe' ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new CAWeb_Customize_Icon_Control(
			$wp_customize,
			'ca_google_trans_icon',
			array(
				'section'         => 'caweb_google',
				'settings'        => 'ca_google_trans_icon',
				'active_callback' => 'caweb_customizer_google_trans_custom_option',
			)
		)
	);

	$wp_customize->add_setting(
		'ca_google_shortcode',
		array(
			'type'    => 'option',
			'default' => '[caweb_google_translate /]',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_google_shortcode',
			array(
				'label'           => 'Google Translate Shortcode',
				'type'            => 'text',
				'section'         => 'caweb_google',
				'settings'        => 'ca_google_shortcode',
				'input_attrs'     => array( 'readonly' => true ),
				'active_callback' => 'caweb_customizer_google_trans_custom_option',
			)
		)
	);

	/* Social Media Links */
	$wp_customize->add_section(
		'caweb_social_media',
		array(
			'title'    => 'Social Media Links',
			'priority' => 30,
			'panel'    => 'caweb_options',
		)
	);

	$social_options = caweb_get_site_options( 'social' );

	foreach ( $social_options as $social => $option ) {
		$wp_customize->add_setting(
			$option,
			array(
				'type'    => 'option',
				'default' => get_option(
					$option,
					''
				),
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				$option,
				array(
					'label'      => $social,
					'type'       => 'text',
					'section'    => 'caweb_social_media',
					'settings'   => $option,
				)
			)
		);

		$wp_customize->add_setting(
			sprintf( '%1$s_header', $option ),
			array(
				'type'              => 'option',
				'default'           => get_option( sprintf( '%1$s_header', $option ) ),
				'sanitize_callback' => 'caweb_sanitize_customizer_checkbox',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				sprintf( '%1$s_header', $option ),
				array(
					'label'           => 'Show in Header',
					'type'            => 'checkbox',
					'section'         => 'caweb_social_media',
					'settings'        => sprintf( '%1$s_header', $option ),
				)
			)
		);

		$wp_customize->add_setting(
			sprintf( '%1$s_footer', $option ),
			array(
				'type'              => 'option',
				'default'           => get_option( sprintf( '%1$s_footer', $option ) ),
				'sanitize_callback' => 'caweb_sanitize_customizer_checkbox',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				sprintf( '%1$s_footer', $option ),
				array(
					'label'      => 'Show in Footer',
					'type'       => 'checkbox',
					'section'    => 'caweb_social_media',
					'settings'   => sprintf( '%1$s_footer', $option ),
				)
			)
		);

		if ( 'ca_social_email' !== $option ) {
			$wp_customize->add_setting(
				sprintf( '%1$s_new_window', $option ),
				array(
					'type'              => 'option',
					'default'           => get_option( sprintf( '%1$s_new_window', $option ) ),
					'sanitize_callback' => 'caweb_sanitize_customizer_checkbox',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					sprintf( '%1$s_new_window', $option ),
					array(
						'label'      => 'Open in New Tab',
						'type'       => 'checkbox',
						'section'    => 'caweb_social_media',
						'settings'   => sprintf( '%1$s_new_window', $option ),
					)
				)
			);
		}
	}

	/* Custom CSS */
	$wp_customize->add_section(
		'caweb_custom_css',
		array(
			'title'    => 'Custom CSS',
			'priority' => 30,
			'panel'    => 'caweb_options',
		)
	);

	$wp_customize->add_setting(
		'ca_custom_css',
		array(
			'type'    => 'option',
			'default' => get_option( 'ca_custom_css' ),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_custom_css',
			array(
				'label'      => 'CSS',
				'type'       => 'textarea',
				'section'    => 'caweb_custom_css',
				'settings'   => 'ca_custom_css',
			)
		)
	);

	add_filter( 'sanitize_option_ca_custom_css', 'caweb_sanitize_option_ca_custom_css', 10, 2 );
}

/**
 * CAWeb Register Customizer
 * Registers CAWeb Options General Settings
 * 
 * @link https://developer.wordpress.org/reference/hooks/customize_register/
 * @param  WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 *
 * @return void
 */
function caweb_customize_register_general_settings( $wp_customize ){
	$site_version = caweb_template_version();

	$wp_customize->add_section(
		'caweb_settings',
		array(
			'title'    => 'General Settings',
			'priority' => 30,
			'panel'    => 'caweb_options',
		)
	);

	// Site Version.
	$wp_customize->add_setting(
		'ca_site_version',
		array(
			'type'    => 'option',
			'default' => $site_version,
		)
	);

	$versions = caweb_template_versions(); 

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_site_version',
			array(
				'label'      => 'State Template Version',
				'type'       => 'select',
				'choices'    => $versions,
				'section'    => 'caweb_settings',
				'settings'   => 'ca_site_version',
			)
		)
	);

	// Header Menu Type.
	$wp_customize->add_setting(
		'ca_default_navigation_menu',
		array(
			'type'    => 'option',
			'default' => get_option(
				'ca_default_navigation_menu',
				'megadropdown'
			),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_default_navigation_menu',
			array(
				'label'      => 'Header Menu Type',
				'type'       => 'select',
				'choices'    => array(
					'megadropdown' => 'Mega Drop',
					'dropdown'     => 'Drop Down',
					'singlelevel'  => 'Single Level',
				),
				'section'    => 'caweb_settings',
				'settings'   => 'ca_default_navigation_menu',
			)
		)
	);

	// Color Scheme.
	$wp_customize->add_setting(
		'ca_site_color_scheme',
		array(
			'type'    => 'option',
			'default' => get_option(
				'ca_site_color_scheme',
				'oceanside'
			),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_site_color_scheme',
			array(
				'label'      => 'Color Scheme',
				'type'       => 'select',
				'choices'    => caweb_color_schemes( $site_version, 'displayname' ),
				'section'    => 'caweb_settings',
				'settings'   => 'ca_site_color_scheme',
			)
		)
	);

	// Show Search on Front Page.
	$wp_customize->add_setting(
		'ca_frontpage_search_enabled',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_frontpage_search_enabled', true ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_frontpage_search_enabled',
			array(
				'label'           => 'Show Search on Front Page',
				'type'            => 'checkbox',
				'section'         => 'caweb_settings',
				'settings'        => 'ca_frontpage_search_enabled',
			)
		)
	);

	// Sticky Navigation.
	$wp_customize->add_setting(
		'ca_sticky_navigation',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_sticky_navigation', true ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_sticky_navigation',
			array(
				'label'           => 'Sticky Navigation',
				'type'            => 'checkbox',
				'section'         => 'caweb_settings',
				'settings'        => 'ca_sticky_navigation',
			)
		)
	);

	// Menu Home Link.
	$wp_customize->add_setting(
		'ca_home_nav_link',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_home_nav_link', true ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_home_nav_link',
			array(
				'label'      => 'Menu Home Link',
				'type'       => 'checkbox',
				'section'    => 'caweb_settings',
				'settings'   => 'ca_home_nav_link',
			)
		)
	);

}

/**
 * CAWeb Register Customizer
 * Registers CAWeb Options Utility Header Settings
 * 
 * @link https://developer.wordpress.org/reference/hooks/customize_register/
 * @param  WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 *
 * @return void
 */
function caweb_customize_register_utility_header_settings( $wp_customize ){
	$wp_customize->add_section(
		'caweb_utility_header',
		array(
			'title'    => 'Utility Header',
			'priority' => 30,
			'panel'    => 'caweb_options',
		)
	);

	// Contact Us Page.
	$wp_customize->add_setting(
		'ca_contact_us_link',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_contact_us_link', '' ),
			'transport' => 'postMessage',
		)
	);

	
	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_contact_us_link',
			array(
				'label'           => 'Contact Us Page',
				'type'            => 'text',
				'section'         => 'caweb_utility_header',
				'settings'        => 'ca_contact_us_link',
			)
		)
	);

	// Enable Geo Locator.
	$wp_customize->add_setting(
		'ca_geo_locator_enabled',
		array(
			'type'              => 'option',
			'default'           => get_option( 'ca_geo_locator_enabled' ),
			'sanitize_callback' => 'caweb_sanitize_customizer_checkbox',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_geo_locator_enabled',
			array(
				'label'           => 'Enable Geo Locator',
				'type'            => 'checkbox',
				'section'         => 'caweb_utility_header',
				'settings'        => 'ca_geo_locator_enabled',
			)
		)
	);

	// Home Link.
	$wp_customize->add_setting(
		'ca_utility_home_icon',
		array(
			'type'      => 'option',
			'default'   => get_option( 'ca_utility_home_icon', true ),
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'ca_utility_home_icon',
			array(
				'label'           => 'Home Link',
				'type'            => 'checkbox',
				'section'         => 'caweb_utility_header',
				'settings'        => 'ca_utility_home_icon',
			)
		)
	);

	// Custom Utility Links.
	for ( $link = 1; $link < 4; $link++ ) {
		$url    = get_option( "ca_utility_link_${link}", '' );
		$label  = get_option( "ca_utility_link_${link}_name", '' );
		$target = get_option( "ca_utility_link_${link}_new_window" );
		$enable = get_option( "ca_utility_link_${link}_enable", 'init' );
		$enable = 'init' === $enable && ! empty( $url ) && ! empty( $label ) || $enable ? true : false;

		// Link Enabled.
		$wp_customize->add_setting(
			"ca_utility_link_${link}_enable",
			array(
				'type'      => 'option',
				'default'   => $enable,
				'sanitize_callback' => 'caweb_sanitize_customizer_checkbox',
			)
		);
	
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				"ca_utility_link_${link}_enable",
				array(
					'label'           => "Custom Link $link",
					'type'            => 'checkbox',
					'section'         => 'caweb_utility_header',
					'settings'        => "ca_utility_link_${link}_enable",
				)
			)
		);

		// Link Label.
		$wp_customize->add_setting(
			"ca_utility_link_${link}_name",
			array(
				'type'      => 'option',
				'default'   => $label,
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				"ca_utility_link_${link}_name",
				array(
					'label'           => "Custom Link ${link} Label",
					'type'            => 'text',
					'section'         => 'caweb_utility_header',
					'settings'        => "ca_utility_link_${link}_name",
					'active_callback' => 'caweb_is_custom_link_enabled',
				)
			)
		);

		// Link URL.
		$wp_customize->add_setting(
			"ca_utility_link_${link}",
			array(
				'type'      => 'option',
				'default'   => $url,
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				"ca_utility_link_${link}",
				array(
					'label'           => "Custom Link ${link} URL",
					'type'            => 'text',
					'section'         => 'caweb_utility_header',
					'settings'        => "ca_utility_link_${link}",
					'active_callback' => 'caweb_is_custom_link_enabled',
				)
			)
		);

		/* Target */
		$wp_customize->add_setting(
			"ca_utility_link_${link}_new_window",
			array(
				'type'              => 'option',
				'default'           => $target,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'caweb_sanitize_customizer_checkbox',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				"ca_utility_link_${link}_new_window",
				array(
					'label'           => 'Open in New Tab',
					'type'            => 'checkbox',
					'section'         => 'caweb_utility_header',
					'settings'        => "ca_utility_link_${link}_new_window",
					'active_callback' => 'caweb_is_custom_link_enabled',
				)
			)
		);
	}

}

/**
 * CAWeb Register Customizer
 * Registers CAWeb Options Page Header Settings
 * 
 * @link https://developer.wordpress.org/reference/hooks/customize_register/
 * @param  WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 *
 * @return void
 */
function caweb_customize_register_page_header( $wp_customize ){
	$wp_customize->add_section(
		'caweb_page_header',
		array(
			'title'    => 'Page Header',
			'priority' => 30,
			'panel'    => 'caweb_options',
		)
	);

	// Organization Logo-Brand.
	$wp_customize->add_setting(
		'header_ca_branding',
		array(
			'type'    => 'option',
			'default' => get_option( 'header_ca_branding', '' ),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'header_ca_branding',
			array(
				'label'      => 'Organization Logo-Brand',
				'section'    => 'caweb_page_header',
				'settings'   => 'header_ca_branding',
			)
		)
	);

}

/**
 * CAWeb Utility Header Custom Link Options Check
 * Default callback used when invoking WP_Customize_Control::active().
 * Determine if the Utility Header Custom Link is enabled.
 * 
 * @link https://developer.wordpress.org/reference/classes/wp_customize_control/active_callback/
 * @param   WP_Customize_Control $customizer  WP_Customize_Control instance.
 *
 * @return bool
 */
function caweb_is_custom_link_enabled( $customizer ){
	$parent_option = preg_replace('/.*(ca_utility_link_\d).*/', '$1', $customizer->get_link() );
	return '1' === $customizer->manager->get_control("${parent_option}_enable")->value() ? true : false;
}

/**
 * CAWeb Google Translate Option Check
 * Default callback used when invoking WP_Customize_Control::active().
 * Determine if the Google Translate Option is set to 'custom'.
 * 
 * @link https://developer.wordpress.org/reference/classes/wp_customize_control/active_callback/
 * @param   WP_Customize_Control $customizer  WP_Customize_Control instance.
 *
 * @return bool
 */
function caweb_customizer_google_trans_custom_option( $customizer ) {
	$manager = $customizer->manager;

	return 'custom' === $manager->get_control( 'ca_google_trans_enabled' )->value() ? true : false;
}

/**
 * CAWeb Sanitize Callback for Checkbox Options
 * Default sanitize callback used when invoking WP_Customize_Control::active().
 *
 * @link https://developer.wordpress.org/reference/classes/wp_customize_control/active_callback/
 * @param   bool $checked  Checkbox checked state.
 *
 * @return bool
 */
function caweb_sanitize_customizer_checkbox( $checked ) {
	return ( isset( $checked ) && true === $checked ) ? '1' : '0';
}

/**
 * CAWeb Sanitize Callback for Custom CSS Option
 * Default sanitize callback used when invoking WP_Customize_Control::active().
 *
 * @link https://developer.wordpress.org/reference/classes/wp_customize_control/active_callback/
 * @param   string $value CAWeb Custom CSS.
 * @param   string $option CAWeb Option Name.
 *
 * @return bool
 */
function caweb_sanitize_option_ca_custom_css( $value, $option ) {
	return addslashes( $value );
}
