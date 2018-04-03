<?php

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

add_action( 'init', 'jk_remove_storefront_header_search' );

function jk_remove_storefront_header_search() {
remove_action( 'storefront_header', 'storefront_product_search', 	40 );
}

add_action('init','delay_remove');

function delay_remove() {
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_coloralog_ordering', 10 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
}


function enqueue_parent_styles() {
	 wp_enqueue_script( 'parent-script', get_template_directory_child().'/build/bundle.js' );
	 wp_enqueue_style('twentysixteen-style', get_template_directory_child().'/build/bundle.css');

}

function get_template_directory_child() {
	$directory_template = get_template_directory_uri();
	$directory_child = str_replace('storefront', '', $directory_template) . 'marcus-child';
	return $directory_child;
}

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' ); //hooking/adding those scripts and stylesheets to wordpress

// wp_register_style( 'bundle.css', get_stylesheet_directory_uri());

// Remove "Storefront Designed by WooThemes" from Footer
// add_action( 'init', 'custom_remove_footer_credit', 10 );
// function custom_remove_footer_credit () {
//     remove_action( 'storefront_footer', 'storefront_credit', 20 );
//     add_action( 'storefront_footer', 'custom_storefront_credit', 20 );
// }

// remove default sorting dropdown
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


// Remove breadcrumbs from shop & categories
add_filter( 'woocommerce_before_main_content', 'remove_breadcrumbs');
function remove_breadcrumbs() {
	if(!is_product()) {
		remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);
	}
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_filter( 'woocommerce_get_breadcrumb', '__return_false' );

function add_defer_attribute($tag, $handle) {
	// add script handles to the array below
	$scripts_to_defer = array('my-js-handle', 'another-handle');

	foreach($scripts_to_defer as $defer_script) {
		 if ($defer_script === $handle) {
				return str_replace(' src', ' defer="defer" src', $tag);
		 }
	}
	return $tag;
}

add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
unset($fields['billing']['billing_company']);

return $fields;
}

add_filter( 'wc_stripe_payment_icons', 'change_my_icons' );
function change_my_icons( $icons ) {
        // var_dump( $icons ); to show all possible icons to change.
		$icons['visa'] = '<img src="http://localhost:8887/wp-content/plugins/woocommerce/assets/images/icons/credit-cards/visa.svg" />';
		$icons['mastercard'] = '<img src="http://localhost:8887/wp-content/plugins/woocommerce/assets/images/icons/credit-cards/mastercard.png" />';
		$icons['amex'] = '<img src="http://localhost:8887/wp-content/plugins/woocommerce/assets/images/icons/credit-cards/amex.png" />';

    return $icons;
}

?>
