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

// Add custom header support and options to Theme Customizer page in admin
add_action( 'plugins_loaded', 'agp_add_theme_support' );
function agp_add_theme_support(){

  if( get_theme_mod( 'header_textcolor' ) != 'blank' ){
    add_theme_support( 'custom-header', array(
      'header-text' => true,
      'height' => 100,
      'width' => 100,
    ));
  } else {
    add_theme_support( 'custom-header', array(
      'header-text' => true,
      'height' => 100,
      'width' => 340,
      'flex-height' => true,
      'flex-width' => true,
    ));
  }

  if( is_admin() ){

    add_action( 'customize_register', 'agp_customizer', 99 );

  }

}

// Change option's label to make more sense
function agp_customizer( $wp_customize ){

  $wp_customize->get_control( 'display_header_text' )->label = "Display Site Title";

}

// Add class to identify header content configuration
add_filter( 'body_class', 'agp_body_class' );
function agp_body_class($classes = ''){

  if( empty( get_header_image() ) ){
    // No header image
    $classes[] = 'agp-header-noimage';
  } else {
    $classes[] = 'agp-header-image';
  }
  if( get_theme_mod( 'header_textcolor' ) == 'blank' ){
    // No header text
    $classes[] = 'agp-header-notitle';
  }
  if( defined( 'CHILD_THEME_NAME' ) ){
    $classes[] = strtolower( 'agp-header-' . str_replace( ' ', '-', CHILD_THEME_NAME ) );
  }

  return $classes;
}

// Replace Genesis function with modified version to suit our needs
add_action( 'init', 'agp_replace_genesis_custom_header_style', 99 );
function agp_replace_genesis_custom_header_style(){

  remove_action( 'wp_head', 'genesis_custom_header_style' );

  // Add header image to page before site title
  add_filter( 'genesis_seo_title', 'agp_insert_header_image', 11, 3 );

}

function agp_insert_header_image( $title, $inside, $wrap ){

  $header_image_url = get_header_image();

  if ( !empty( $header_image_url ) ){
    $header_html = '<a class="headerimage" href="%s"><img src="%s" alt="%s"></a>';
    $header_image = sprintf( $header_html, trailingslashit( home_url() ), $header_image_url, get_bloginfo( 'title' ) );

    $title = $header_image . $title;

  }

  return $title;

}

// Load CSS when a header image is used
add_action( 'get_header', 'agp_check_header_styles' );
function agp_check_header_styles(){

  if( !empty( get_header_image() ) ){
    add_action( 'wp_enqueue_scripts', 'agp_register_header_styles' );
    add_action( 'wp_enqueue_scripts', 'agp_enqueue_header_styles' );
  }

}

function agp_register_header_styles() {

  wp_register_style(
    'agp-header-styles',
    AGP_DIR_URL . '/css/styles_headerimage.css',
    array(),
    '',
    'screen'
  );

}

function agp_enqueue_header_styles() {

  wp_enqueue_style( 'agp-header-styles' );

}

// Add styles dependent on Genesis theme
add_action( 'wp_enqueue_scripts', 'agp_register_theme_styles' );
function agp_register_theme_styles(){

  $theme = wp_get_theme();

  if(strpos(wp_get_theme(), 'Outreach Pro') !== false){
    wp_register_style(
      'agp-op-styles',
      AGP_DIR_URL . 'css/styles_outreach-pro.css',
      array(),
      '',
      'screen'
    );
  } else if(strpos(wp_get_theme(), 'Executive Pro') !== false){
    wp_register_style(
      'agp-ep-styles',
      AGP_DIR_URL . 'css/styles_executive-pro.css',
      array(),
      '',
      'screen'
    );
  }

}

add_action( 'wp_enqueue_scripts', 'agp_enqueue_theme_styles' );
function agp_enqueue_theme_styles(){

  $theme = wp_get_theme();

  if(strpos(wp_get_theme(), 'Outreach Pro') !== false){
    wp_enqueue_style( 'agp-op-styles' );
  } else if(strpos(wp_get_theme(), 'Executive Pro') !== false){
    wp_enqueue_style( 'agp-ep-styles' );
  }

}

// Add theme support for color variations
add_action('admin_init', 'agp_add_color_variations');
function agp_add_color_variations(){

  $colors = get_theme_support('genesis-style-selector')[0];

  if(strpos(wp_get_theme(), 'Outreach Pro') !== false){

    $colors['outreach-pro-maroon'] = __( 'Outreach Pro Texas A&M Maroon', 'outreach' );
    $colors['outreach-pro-extensionunit'] = __( 'Outreach Pro AgriLife Extension Unit', 'extensionunit' );

    add_theme_support( 'genesis-style-selector', $colors );

  } else if(strpos(wp_get_theme(), 'Executive Pro') !== false){

    $colors['executive-pro-maroon'] = __( 'Executive Pro Texas A&M Maroon', 'executive' );

    add_theme_support( 'genesis-style-selector', $colors );

  }

}
