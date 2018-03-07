<?php

if( ! function_exists ('hotbody_setup')) :

  function hotbody_setup() {
    // let wordpress handle title tags
    add_theme_support( 'title-tag');
  }

endif;

add_action('after_setup_theme', 'hotbody_setup');

// Register menus

function register_hotbody_menus() {
  register_nav_menus(
    array(
      'primary' => __('Primary menu')
      )
    );
}

add_action('init', 'register_hotbody_menus');

// Add style sheets

function hotbody_scripts() {
  // enqueue the main style sheet and google font url
  wp_enqueue_style('hotbody_styles', get_stylesheet_uri());
  wp_enqueue_style('hotbody_google_fonts', 'https://fonts.googleapis.com/css?family=Ubuntu|Viga' );
}

add_action('wp_enqueue_scripts', 'hotbody_scripts');

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<div id="woo-commerce">';
}

function my_theme_wrapper_end() {
  echo '</div>';
}


		/** Woocommerce support **/
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );


?>
