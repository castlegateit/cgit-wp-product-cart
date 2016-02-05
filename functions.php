<?php

use Cgit\Product\Cart;

/**
 * Get cart object
 */
function cgit_product_cart() {
    return Cart::getInstance();
}
