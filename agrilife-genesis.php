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
add_action( 'get_header', 'agp_check_styles' );
function agp_check_styles(){

  if( !empty( get_header_image() ) ){
    add_action( 'wp_enqueue_scripts', 'register_agp_styles' );
    add_action( 'wp_enqueue_scripts', 'enqueue_agp_styles' );
  }

}

function register_agp_styles() {

  wp_register_style(
    'agp-styles',
    AGP_DIR_URL . '/styles.css',
    array(),
    '',
    'screen'
  );

}

function enqueue_agp_styles() {

  wp_enqueue_style( 'agp-styles' );

}
