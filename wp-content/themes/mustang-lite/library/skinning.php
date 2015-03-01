<?php
/**
 * Skinning System
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 * @uses        Theme Customizer Options Array
 * @uses        Custom CSS Styles Generator
 *
 * @since       3.0
 * @version     3.4
 * @version  1.2.7
 *
 * CONTENT:
 * - 1) Required files
 * - 10) Actions and filters
 * - 20) Helpers
 * - 30) Main customizer function
 * - 40) Saving skins
 */





/**
 * 1) Required files
 */

	//Include function to generate the WordPress Customizer CSS
		locate_template( 'assets/css/_custom-styles.php', true );
	//Include sanitizing functions
		locate_template( WM_LIBRARY_DIR . 'includes/sanitize.php', true );





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Register customizer
			add_action( 'customize_register', 'wm_theme_customizer' );
		//Enqueue styles and scripts
			add_action( 'customize_controls_enqueue_scripts', 'wm_theme_customizer_assets' );
		//Regenerating main stylesheet
			add_action( 'update_option_' . WM_THEME_SETTINGS_SKIN, 'wm_generate_all_css', 10 );



	/**
	 * Filters
	 */

		//Save skin
			add_filter( 'pre_update_option_' . WM_THEME_SETTINGS_SKIN, 'wm_save_skin', 10, 2 );





/**
 * 20) Helpers
 */

	/**
	 * Enqueue styles and scripts to main customizer window
	 *
	 * You can actually control the customizer option fields here.
	 *
	 * @version  3.1
	 */
	if ( ! function_exists( 'wm_theme_customizer_assets' ) ) {
		function wm_theme_customizer_assets() {
			/**
			 * Scripts
			 */

				wp_localize_script( 'jquery', 'wmCustomizerHelper', array( 'wmThemeShortname' => WM_THEME_SHORTNAME ) );

				wp_enqueue_script( 'wm-customizer' );
		}
	} // /wm_theme_customizer_assets



	/**
	 * Outputs styles in customizer preview head
	 */
	if ( ! function_exists( 'wm_theme_customizer_css' ) ) {
		function wm_theme_customizer_css() {
			//Helper variables
				$output = wm_custom_styles();

			//Output
				if ( $output ) {
					echo apply_filters( 'wmhook_wm_theme_customizer_css_output', '<style type="text/css" id="' . WM_THEME_SHORTNAME . '-customizer-styles">' . "\r\n" . $output . "\r\n" . '</style>' );
				}
		}
	} // /wm_theme_customizer_css



	/**
	 * Outputs customizer JavaScript in footer
	 *
	 * Use this structure for customizer_js property:
	 * 'customizer_js' => array(
	 * 			'css'    => array(
	 * 					'.selector'         => array( 'css-property-name' ),
	 * 					'.another-selector' => array( array( 'padding-left', 'px' ) ),
	 * 				),
	 * 			'custom' => 'your_custom_JavaScript_here',
	 * 		)
	 */
	if ( ! function_exists( 'wm_theme_customizer_js' ) ) {
		function wm_theme_customizer_js() {
			//Helper variables
				$wm_skin_design = apply_filters( 'wmhook_theme_options_skin_array', array() );

				$output = $output_single = '';

			//Preparing output
				if ( is_array( $wm_skin_design ) && ! empty( $wm_skin_design ) ) {
					foreach ( $wm_skin_design as $skin_option ) {
						if ( isset( $skin_option['customizer_js'] ) ) {
							$output_single  = "wp.customize( '" . WM_THEME_SETTINGS_SKIN . "[" . WM_THEME_SETTINGS_PREFIX . $skin_option['id'] . "]" . "', function( value ) {"  . "\r\n";
							$output_single .= "\t" . 'value.bind( function( newval ) {' . "\r\n";

							if ( ! isset( $skin_option['customizer_js']['custom'] ) ) {
								foreach ( $skin_option['customizer_js']['css'] as $selector => $properties ) {
									if ( is_array( $properties ) ) {
										$output_single_css = '';

										foreach ( $properties as $property ) {
											if ( ! is_array( $property ) ) {
												$property = array( $property, '' );
											}
											if ( ! isset( $property[1] ) ) {
												$property[1] = '';
											}
											if ( trim( $property[1] ) ) {
												$property[1] = ' + "' . $property[1] . '"';
											}

											$output_single_css .= '.css( "' . $property[0] . '", newval' . $property[1] . ' )';
										}
									}

									$output_single .= "\t\t" . '$( "' . $selector . '" )' . $output_single_css . ";\r\n";
								}
							} else {
								$output_single .= "\t\t" . $skin_option['customizer_js']['custom'] . "\r\n";
							}

							$output_single .= "\t" . '} );' . "\r\n";
							$output_single .= '} );'. "\r\n";
							$output_single  = apply_filters( 'wmhook_wm_theme_customizer_js_option_' . $skin_option['id'], $output_single );

							$output .= $output_single;
						}
					}
				}

			//Output
				if ( trim( $output ) ) {
					echo apply_filters( 'wmhook_wm_theme_customizer_js_output', '<!-- Theme custom scripts -->' . "\r\n" . '<script type="text/javascript"><!--' . "\r\n" . '( function( $ ) {' . "\r\n\r\n" . $output . "\r\n\r\n" . '} )( jQuery );' . "\r\n" . '//--></script>' );
				}
		}
	} // /wm_theme_customizer_js





/**
 * 30) Main customizer function
 */

	/**
	 * Registering sections and options for WP Customizer
	 *
	 * @version  1.2.7
	 *
	 * @param  object $wp_customize WP customizer object.
	 */
	if ( ! function_exists( 'wm_theme_customizer' ) ) {
		function wm_theme_customizer( $wp_customize ) {
			/**
			 * Custom customizer controls
			 *
			 * @link  https://github.com/bueltge/Wordpress-Theme-Customizer-Custom-Controls
			 * @link  http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
			 */

				locate_template( WM_LIBRARY_DIR . 'includes/controls/class-WM_Customizer_Hidden.php',      true );
				locate_template( WM_LIBRARY_DIR . 'includes/controls/class-WM_Customizer_HTML.php',        true );
				locate_template( WM_LIBRARY_DIR . 'includes/controls/class-WM_Customizer_Image.php',       true );
				locate_template( WM_LIBRARY_DIR . 'includes/controls/class-WM_Customizer_Multiselect.php', true );
				locate_template( WM_LIBRARY_DIR . 'includes/controls/class-WM_Customizer_Radiocustom.php', true );
				locate_template( WM_LIBRARY_DIR . 'includes/controls/class-WM_Customizer_Slider.php',      true );
				if ( ! wm_check_wp_version( 4 ) ) {
					locate_template( WM_LIBRARY_DIR . 'includes/controls/class-WM_Customizer_Textarea.php',  true );
				}



			//Helper variables
				$wm_skin_design = (array) apply_filters( 'wmhook_theme_options_skin_array', array() );

				$allowed_option_types = apply_filters( 'wmhook_wm_theme_customizer_allowed_option_types', array(
						'background',
						'checkbox',
						'color',
						'email',
						'hidden',
						'html',
						'image',
						'multiselect',
						'password',
						'radio',
						'radiocustom',
						'select',
						'slider',
						'text',
						'textarea',
						'theme-customizer-html',
						'url',
					) );

				//To make sure our customizer sections start after WordPress default ones
					$priority = apply_filters( 'wmhook_wm_theme_customizer_priority', 900 );
				//Default section name in case not set (should be overwritten anyway)
					$customizer_panel   = '';
					$customizer_section = WM_THEME_SHORTNAME;

			//Generate customizer options
				if ( is_array( $wm_skin_design ) && ! empty( $wm_skin_design ) ) {

					foreach ( $wm_skin_design as $skin_option ) {

						if (
								is_array( $skin_option )
								&& isset( $skin_option['type'] )
								&& (
										in_array( $skin_option['type'], $allowed_option_types )
										|| isset( $skin_option['theme-customizer-section'] )
									)
							) {

							//Helper variables
								$priority++;

								$option_id   = ( isset( $skin_option['id'] ) ) ? ( WM_THEME_SETTINGS_PREFIX . $skin_option['id'] ) : ( null );
								$default     = ( isset( $skin_option['default'] ) ) ? ( $skin_option['default'] ) : ( null );
								$description = ( isset( $skin_option['description'] ) ) ? ( $skin_option['description'] ) : ( '' );
								$transport   = ( isset( $skin_option['customizer_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' );



							/**
							 * Panels
							 *
							 * Panels were introduced in WordPress 4.0 and are wrappers for customizer sections.
							 * Note that the panel will not be displayed unless sections are assigned to it.
							 * The panel you define in theme options array will be active unless you reset its name.
							 * Set the panel name in the section declaration with 'theme-customizer-panel' attribute.
							 *
							 * @link   http://make.wordpress.org/core/2014/07/08/customizer-improvements-in-4-0/
							 * @since  3.2, WordPress 4.0
							 */
							if (
									wm_check_wp_version( 4 )
									&& isset( $skin_option['theme-customizer-panel'] )
									&& $customizer_panel != $skin_option['theme-customizer-panel']
								) {
								$customizer_panel = sanitize_title( trim( $skin_option['theme-customizer-panel'] ) );

								$wp_customize->add_panel(
										$customizer_panel, //panel ID
										array(
											'title'       => $skin_option['theme-customizer-panel'], //panel title
											'description' => ( isset( $skin_option['theme-customizer-panel-description'] ) ) ? ( $skin_option['theme-customizer-panel-description'] ) : ( '' ), //Displayed at the top of panel
											'priority'    => $priority,
										)
									);

							}



							/**
							 * Sections
							 *
							 * @version  3.2
							 */
							if (
									isset( $skin_option['theme-customizer-section'] )
									&& trim( $skin_option['theme-customizer-section'] )
								) {

								$customizer_section = array(
										'id'    => sanitize_title( trim( $skin_option['theme-customizer-section'] ) ),
										'setup' => array(
												'title'       => $skin_option['theme-customizer-section'], //section title
												'description' => ( isset( $skin_option['theme-customizer-section-description'] ) ) ? ( $skin_option['theme-customizer-section-description'] ) : ( '' ), //Displayed at the top of section
												'priority'    => $priority,
											)
									);

								if ( wm_check_wp_version( 4 ) ) {
									$customizer_section['setup']['panel'] = $customizer_panel;
								}

								$wp_customize->add_section(
										$customizer_section['id'],
										$customizer_section['setup']
									);

								$customizer_section = $customizer_section['id'];
								$customizer_panel   = ''; //The panel need to be defined for each section separatelly, otherwise all the sections would reside within a single panel.

							}



							/**
							 * Options
							 *
							 * With add_setting() use a 'type' => 'option' (available: 'option' and 'theme_mod').
							 * Read more at @link  http://wordpress.stackexchange.com/questions/155072/get-option-vs-get-theme-mod-why-is-one-slower
							 */
							switch ( $skin_option['type'] ) {

								/**
								 * Background combo options
								 */
								case 'background':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-color]',
											array(
												'type'                 => 'option',
												'default'              => ( isset( $default['color'] ) ) ? ( $default['color'] ) : ( null ),
												'transport'            => $transport,
												'sanitize_callback'    => 'sanitize_hex_color_no_hash',
												'sanitize_js_callback' => 'maybe_hash_hex_color',
											)
										);

										$wp_customize->add_control( new WP_Customize_Color_Control(
												$wp_customize,
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-color]',
												array(
													'label'    => __( 'Background color', 'wm_domain' ),
													'section'  => $customizer_section,
													'priority' => $priority,
												)
											) );

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-url]',
											array(
												'type'                 => 'option',
												'default'              => ( isset( $default['url'] ) ) ? ( $default['url'] ) : ( null ),
												'transport'            => $transport,
												'sanitize_callback'    => 'wm_sanitize_return_value',
												'sanitize_js_callback' => 'wm_sanitize_return_value',
											)
										);

										$wp_customize->add_control( new WM_Customizer_Image(
												$wp_customize,
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-url]',
												array(
													'label'    => __( 'Background image', 'wm_domain' ),
													'section'  => $customizer_section,
													'priority' => ++$priority,
													'context'  => WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-url]',
												)
											) );

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-url-hidpi]',
											array(
												'type'                 => 'option',
												'default'              => ( isset( $default['url-hidpi'] ) ) ? ( $default['url-hidpi'] ) : ( null ),
												'transport'            => $transport,
												'sanitize_callback'    => 'wm_sanitize_return_value',
												'sanitize_js_callback' => 'wm_sanitize_return_value',
											)
										);

										$wp_customize->add_control( new WM_Customizer_Image(
												$wp_customize,
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-url-hidpi]',
												array(
													'label'    => __( 'High DPI background image', 'wm_domain' ),
													'section'  => $customizer_section,
													'priority' => ++$priority,
													'context'  => WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-url-hidpi]',
												)
											) );

									if ( function_exists( 'wm_helper_var' ) ) {

										$wp_customize->add_setting(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-position]',
												array(
													'type'                 => 'option',
													'default'              => ( isset( $default['position'] ) ) ? ( $default['position'] ) : ( '50% 0' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control( new WM_Customizer_Radiocustom(
													$wp_customize,
													WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-position]',
													array(
														'label'    => __( 'Background position', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'choices'  => wm_helper_var( 'bg-css', 'position' ),
														'class'    => 'matrix',
													)
												) );

										$wp_customize->add_setting(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-repeat]',
												array(
													'type'                 => 'option',
													'default'              => ( isset( $default['repeat'] ) ) ? ( $default['repeat'] ) : ( 'no-repeat' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control( new WM_Customizer_Radiocustom(
													$wp_customize,
													WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-repeat]',
													array(
														'label'    => __( 'Background repeat', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'choices'  => wm_helper_var( 'bg-css', 'repeat' ),
														'class'    => 'image-radio',
													)
												) );

										$wp_customize->add_setting(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-attachment]',
												array(
													'type'                 => 'option',
													'default'              => ( isset( $default['attachment'] ) ) ? ( $default['attachment'] ) : ( 'scroll' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control(
													WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-attachment]',
													array(
														'label'    => __( 'Background attachment', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'type'     => 'select',
														'choices'  => wm_helper_var( 'bg-css', 'scroll' ),
													)
												);

										$wp_customize->add_setting(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-size]',
												array(
													'type'                 => 'option',
													'default'              => ( isset( $default['size'] ) ) ? ( $default['size'] ) : ( '' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control( new WM_Customizer_Radiocustom(
													$wp_customize,
													WM_THEME_SETTINGS_SKIN . '[' . $option_id . '-bg-size]',
													array(
														'label'    => __( 'CSS3 background size', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'choices'  => wm_helper_var( 'bg-css', 'size' ),
														'class'    => 'image-radio',
													)
												) );

									}

								break;

								/**
								 * Color
								 */
								case 'color':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => 'sanitize_hex_color_no_hash',
												'sanitize_js_callback' => 'maybe_hash_hex_color',
											)
										);

									$wp_customize->add_control( new WP_Customize_Color_Control(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
											)
										) );

								break;

								/**
								 * Email
								 *
								 * @since  3.2, WordPress 4.0
								 */
								case 'email':

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_setting(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'type'                 => 'option',
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => 'wm_sanitize_email',
													'sanitize_js_callback' => 'wm_sanitize_email',
												)
											);

										$wp_customize->add_control(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'type'        => 'email',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									}

								break;

								/**
								 * Hidden
								 */
								case 'hidden':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Hidden(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => 'HIDDEN FIELD',
												'section'  => $customizer_section,
												'priority' => $priority,
											)
										) );

								break;

								/**
								 * HTML
								 */
								case 'html':
								case 'theme-customizer-html':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[custom-title-' . $priority . ']',
											array(
												'sanitize_callback'    => 'wm_sanitize_return_value',
												'sanitize_js_callback' => 'wm_sanitize_return_value',
											)
										);

									$wp_customize->add_control( new WM_Customizer_HTML(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[custom-title-' . $priority . ']',
											array(
												'label'    => $skin_option['content'],
												'section'  => $customizer_section,
												'priority' => $priority,
											)
										) );

								break;

								/**
								 * Image
								 */
								case 'image':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Image(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'context'     => WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											)
										) );

								break;

								/**
								 * Checkbox, radio & select
								 */
								case 'checkbox':
								case 'radio':
								case 'select':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
											)
										);

									$wp_customize->add_control(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'type'        => $skin_option['type'],
												'choices'     => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
											)
										);

								break;

								/**
								 * Multiselect
								 */
								case 'multiselect':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Multiselect(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'choices'     => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
											)
										) );

								break;

								/**
								 * Password
								 *
								 * @since  3.2, WordPress 4.0
								 */
								case 'password':

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_setting(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'type'                 => 'option',
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
													'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												)
											);

										$wp_customize->add_control(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'type'        => 'password',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									}

								break;

								/**
								 * Radio custom labels
								 */
								case 'radiocustom':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Radiocustom(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'choices'     => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
												'class'       => ( isset( $skin_option['class'] ) ) ? ( $skin_option['class'] ) : ( '' ),
											)
										) );

								break;

								/**
								 * Slider
								 *
								 * Since WP4.0 there is also a "range" native input field. This will output
								 * HTML5 <input type="range" /> element - thus still using custom one.
								 */
								case 'slider':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_intval' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_intval' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Slider(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'json'        => array( $skin_option['min'], $skin_option['max'], $skin_option['step'] ),
											)
										) );

								break;

								/**
								 * Text
								 */
								case 'text':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
											)
										);

									$wp_customize->add_control(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
											)
										);

								break;

								/**
								 * Textarea
								 *
								 * Since WordPress 4.0 this is native input field.
								 */
								case 'textarea':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
											)
										);

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_control(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'type'        => 'textarea',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									} else {

										$wp_customize->add_control( new WM_Customizer_Textarea(
												$wp_customize,
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											) );

									}

								break;

								/**
								 * URL
								 *
								 * @since  3.2, WordPress 4.0
								 */
								case 'url':

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_setting(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'type'                 => 'option',
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_url' ),
													'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_url' ),
												)
											);

										$wp_customize->add_control(
												WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
												array(
													'type'        => 'url',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									}

								break;

								/**
								 * Default
								 */
								default:
								break;

							} // /switch

						} // /if suitable option array

					} // /foreach

				} // /if skin options are non-empty array

			//Assets needed for customizer preview
				if ( $wp_customize->is_preview() ) {
					add_action( 'wp_head',   'wm_theme_customizer_css'    );
					add_action( 'wp_footer', 'wm_theme_customizer_js', 99 );
				}
		}
	} // /wm_theme_customizer





/**
 * 40) Saving skins
 */

	/**
	 * Saves and loads a skin
	 *
	 * Creates a new skin JSON file and/or loads a selected skin settings.
	 *
	 * @version  1.2.7
	 *
	 * @param  array $value
	 * @param  array $old_value
	 */
	if ( ! function_exists( 'wm_save_skin' ) ) {
		function wm_save_skin( $value = array(), $old_value = array() ) {
			//Requirements check
				if ( empty( $value ) && ! is_array( $value ) ) {
					return $value;
				}

			//Helper variables
				$skin_new = $skin_load = '';

				$wp_upload_dir  = wp_upload_dir();
				$theme_skin_dir = apply_filters( 'wmhook_wm_save_skin_theme_skin_dir', trailingslashit( $wp_upload_dir['basedir'] ) . 'wmtheme-' . WM_THEME_SHORTNAME . '/skins' );

			//Preparing output
				//Set a new skin file name
					//New skin
						if ( isset( $value[ WM_THEME_SETTINGS_PREFIX . 'skin-new' ] ) ) {
							$skin_new = trim( sanitize_title( $value[ WM_THEME_SETTINGS_PREFIX . 'skin-new' ] ) );
							unset( $value[ WM_THEME_SETTINGS_PREFIX . 'skin-new' ] );
						}

					//Load skin
						if ( isset( $value[ WM_THEME_SETTINGS_PREFIX . 'skin-load' ] ) ) {
							$skin_load = trim( $value[ WM_THEME_SETTINGS_PREFIX . 'skin-load' ] );

							if ( $pos = strpos( $skin_load, ( trailingslashit( WM_SETUP_DIR ) . 'skins' ) ) ) {
								$skin_load = substr( $skin_load, $pos, -4 );
								if ( is_child_theme() ) {
									$skin_load = trailingslashit( get_stylesheet_directory() ) . $skin_load;
								} else {
									$skin_load = trailingslashit( get_template_directory() ) . $skin_load;
								}
								$skin_load .= 'json';
							}

							unset( $value[ WM_THEME_SETTINGS_PREFIX . 'skin-load' ] );
						}

				//Create a new skin
					if ( $skin_new ) {

						//Create the theme skins folder
							if ( ! wma_create_folder( $theme_skin_dir ) ) {
								set_transient( 'wmamp-admin-notice', array( "<strong>ERROR: Wasn't able to create a theme skins folder! Contact the theme support.</strong>", 'error', 'switch_themes', 2 ), ( 60 * 60 * 48 ) );
								delete_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . '-skins' );
							}

						//Write the skin JSON file
							$json_path = apply_filters( 'wmhook_wm_save_skin_json_path', trailingslashit( $theme_skin_dir ) . $skin_new . '.json' );

							$value = apply_filters( 'wmhook_wm_save_skin_output', $value );

							if ( wma_write_local_file( $json_path, json_encode( $value ) ) ) {
								update_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . '-skins', array_unique( array( WM_SKINS, WM_SKINS_CHILD, $theme_skin_dir ) ) );
							} else {
								delete_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . '-skins' );
							}

							//Run additional actions
								do_action( 'wmhook_save_skin', $skin_new, $value, $old_value );

				//Load a selected skin
					} elseif ( $skin_load && file_exists( $skin_load ) ) {

						//Get the skin slug
							$skin_slug = str_replace( array( '.json', WM_SKINS, WM_SKINS_CHILD, $theme_skin_dir ), '', $skin_load );

						//We don't need to write to the file, so just open for reading.
							$skin_load = wma_read_local_file( $skin_load );

							$replacements = (array) apply_filters( 'wmhook_generate_css_replacements', array() );
							$skin_load    = strtr( $skin_load, $replacements );

						//Decoding new imported skin JSON string and converting object to array
							if ( ! empty( $skin_load ) ) {
								$value = json_decode( trim( $skin_load ), true );
								update_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . '-skin-used', $skin_slug );
							}

						//Run additional actions
							do_action( 'wmhook_load_skin', $skin_load, $value, $old_value );

					}

			//Output
				return $value;
		}
	} // /wm_save_skin

?>