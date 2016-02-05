<?php

namespace Cgit\Products;

/**
 * Product cart
 *
 * The product cart extends the Cgit\Products\Utilities class, which provides
 * basic methods for rendering views and formatting currency values.
 */
class Cart extends Utilities
{

    /**
     * Reference to the singleton instance of the class
     */
    private static $instance;

    /**
     * Cart variables
     *
     * These are the array keys used in POST request when adding or removing
     * products from the cart.
     */
    private static $cartVars = array(
        'cart_action',
        'cart_product',
        'cart_quantity',
        'cart_variant',
    );

    /**
     * Constructor
     *
     * Private constructor ...
     */
    private function __construct()
    {
        // Start session to store cart contents
        session_start();

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Set view path
        $this->viewPath = self::pluginDir(__FILE__) . '/views';

        // Modify cart in response to query parameters
        add_action('wp', array($this, 'update'));

        // Register widgets
        add_action('widgets_init', [$this, 'registerWidgets']);
    }

    /**
     * Return the singleton instance of the class
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Update cart contents
     *
     * Checks that the cart array is available in the session variable and adds
     * or removes one or more items for a given product, based on the query
     * parameters.
     */
    public function update()
    {
        // Assign POST data to variables
        foreach (self::$cartVars as $var) {
            $name = str_replace('cart_', '', $var);
            $$name = isset($_POST[$var]) ? $_POST[$var] : false;
        }

        // Need to know product ID and action before we can do anything
        if (!$product || !$action) {
            return;
        }

        // Check the product exists, that the product ID matches a genuine
        // product, and that the action matches a permitted action.
        $obj = get_post($product);
        $is_product = false;

        if ($obj) {
            $is_product = $obj->post_type == CGIT_PRODUCT_POST_TYPE;
        }

        $permitted = array('add', 'remove');
        $is_permitted = in_array($action, $permitted);

        if (!$is_product || !$is_permitted) {
            return false;
        }

        // Perform action
        $this->$action($product, $quantity, $variant);
    }

    /**
     * Get cart contents
     */
    public function contents()
    {
        return $_SESSION['cart'];
    }

    /**
     * Get array key of item in cart contents
     */
    public function itemKey($product, $variant = false)
    {
        foreach ($_SESSION['cart'] as $key => $item) {
            $is_product = $item['product'] == $product;
            $is_variant = $item['variant'] == $variant;

            if ($is_product && $is_variant) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Add product to cart
     *
     * If no quantity is specified, one item will be added to the cart.
     * Comparisons check type and value because $quantity and $variant could be
     * 0 or false.
     */
    public function add($product, $quantity = false, $variant = false)
    {
        if ($quantity === false) {
            $quantity = 1;
        }

        $key = $this->itemKey($product, $variant);

        if ($key === false) {
            $_SESSION['cart'][] = array(
                'product' => $product,
                'quantity' => $quantity,
                'variant' => $variant,
            );
        } else {
            $_SESSION['cart'][$key]['quantity'] += $quantity;
        }
    }

    /**
     * Remove product from cart
     *
     * If no quantity is specified, the entire product will be removed from the
     * cart. If the product is not already in the cart, this method does nothing.
     */
    public function remove($product, $quantity = false, $variant = false)
    {
        $key = $this->itemKey($product, $variant);

        if ($key === false) {
            return;
        }

        if (
            $quantity === false ||
            $quantity >= $_SESSION['cart'][$key]['quantity']
        ) {
            unset($_SESSION['cart'][$key]);
        } else {
            $_SESSION['cart'][$key]['quantity'] -= $quantity;
        }
    }

    /**
     * Register widgets
     */
    public function registerWidgets()
    {
        register_widget('Cgit\Products\CartContentsWidget');
        register_widget('Cgit\Products\CartAddWidget');
    }
}
