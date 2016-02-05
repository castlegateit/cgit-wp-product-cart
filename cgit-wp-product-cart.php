<?php

/*

Plugin Name: Castlegate IT WP Product Cart
Plugin URI: http://github.com/castlegateit/cgit-wp-product-cart
Description: Simple product cart plugin for WordPress, extending the Product Catalogue plugin.
Version: 1.0
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

use Cgit\Products\Cart;

/**
 * Load plugin
 *
 * This uses the plugins_loaded action to control the order in which plugins are
 * loaded. This plugin depends on the Product Catalogue, so must be loaded after
 * that plugin.
 */
add_action('plugins_loaded', function() {
    require __DIR__ . '/src/autoload.php';
    require __DIR__ . '/activation.php';
    require __DIR__ . '/functions.php';

    // Initialization
    Cart::getInstance();
}, 20);
