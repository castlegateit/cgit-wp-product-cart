<?php

/*

Plugin Name: Castlegate IT WP Product Cart
Plugin URI: http://github.com/castlegateit/cgit-wp-product-cart
Description: Simple product cart plugin for WordPress, extending the Product Catalogue plugin.
Version: 0.1
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

/**
 * ACF and Product Catalogue are required
 */
register_activation_hook(__FILE__, function() {
    if (
        !function_exists('acf_add_local_field_group') ||
        !function_exists('cgit_product')
    ) {
        $acf_url = 'http://www.advancedcustomfields.com/';
        $cat_url = 'http://github.com/castlegateit/cgit-wp-product-catalogue';
        $message = 'Plugin activation failed. The Product Cart plugin '
            . 'requires <a href="' . $acf_url . '">Advanced Custom Fields</a> '
            . 'and <a href="' . $cat_url . '">Product Catalogue</a>.'
            . '<br /><br /><a href="' . admin_url('/plugins.php')
            . '">Back to Plugins</a>';

        wp_die($message);
    }
});

/**
 * Includes
 */
include dirname(__FILE__) . '/cart.php';
include dirname(__FILE__) . '/functions.php';
include dirname(__FILE__) . '/widgets.php';

/**
 * Initialize cart
 */
Cgit\Cart::getInstance();
