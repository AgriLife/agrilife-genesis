<?php
/**
 * Plugin Name: AgriLife Genesis
 * Plugin URI: 
 * Description: Extended functionality for Genesis child themes provided by AgriLife Communications
 * Version: 1.0
 * Author: Zach Watkins
 * Author URI: http://github.com/ZachWatkins
 * Author Email: zachary.watkins@ag.tamu.edu
 * License: GPL2+
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'AGP_DIR_URL', plugin_dir_url( __FILE__ ) );

add_theme_support( 'custom-header', array(
  'width' => 100,
  'height' => 100,
  'header-text' => true
));

// Replace Genesis function with modified version to suit our needs
function agp_replace_genesis_custom_header_style(){
  remove_action ('wp_head', 'genesis_custom_header_style' );
  // Remove header image from background
  add_action ( 'wp_head', 'agp_custom_header_style' );
  // Add header image before site title
  add_filter( 'genesis_seo_title', 'agp_insert_header_image', 11, 3 );
}
add_action( 'init', 'agp_replace_genesis_custom_header_style', 99 );

// Add custom styles
function register_agp_styles() {
  wp_register_style(
    'agp-styles',
    AGP_DIR_URL . '/styles.css',
    array(),
    '4.4.3',
    'screen'
  );
}
function enqueue_agp_styles(){
  wp_enqueue_style( 'agp-styles' );
}
add_action( 'wp_enqueue_scripts', 'register_agp_styles' );
add_action( 'wp_enqueue_scripts', 'enqueue_agp_styles' );

// Change option's label to make more sense
function agp_customizer( $wp_customize ){
  $wp_customize->get_control( 'display_header_text' )->label = "Display Site Title";
}
add_action( 'customize_register', 'agp_customizer', 99 );

// Add Header Image to HTML
function agp_insert_header_image( $title, $inside, $wrap ){

  $header_image = get_header_image();

  if ( !empty( $header_image ) ){

    $header_image = '<img class="header-image" src="' . $header_image . '">';

    $title = str_replace( '>' . get_bloginfo( 'name' ) . '<', '>' . $header_image . get_bloginfo( 'name' ) . '<', $title);

  }

  return $title;

}

function agp_custom_header_style(){

  // A modified copy of the genesis theme's header.php function we removed earlier
  // Only difference is it doesn't output a background image for the header image

    //* Do nothing if custom header not supported
  if ( ! current_theme_supports( 'custom-header' ) )
    return;

  //* Do nothing if user specifies their own callback
  if ( get_theme_support( 'custom-header', 'wp-head-callback' ) )
    return;

  $output = '';

  $header_image = get_header_image();
  $text_color   = get_header_textcolor();

  //* If no options set, don't waste the output. Do nothing.
  if ( empty( $header_image ) && ! display_header_text() && $text_color === get_theme_support( 'custom-header', 'default-text-color' ) )
    return;

  $header_selector = get_theme_support( 'custom-header', 'header-selector' );
  $title_selector  = genesis_html5() ? '.custom-header .site-title'       : '.custom-header #title';
  $desc_selector   = genesis_html5() ? '.custom-header .site-description' : '.custom-header #description';

  //* Header selector fallback
  if ( ! $header_selector )
    $header_selector = genesis_html5() ? '.custom-header .site-header' : '.custom-header #header';

  //* Header image CSS, if exists
  // if ( $header_image )
  //   $output .= sprintf( '%s { background: url(%s) no-repeat !important; }', $header_selector, esc_url( $header_image ) );

  //* Header text color CSS, if showing text
  if ( display_header_text() && $text_color !== get_theme_support( 'custom-header', 'default-text-color' ) )
    $output .= sprintf( '%2$s a, %2$s a:hover, %3$s { color: #%1$s !important; }', esc_html( $text_color ), esc_html( $title_selector ), esc_html( $desc_selector ) );

  if ( $output )
    printf( '<style type="text/css">%s</style>' . "\n", $output );

}
