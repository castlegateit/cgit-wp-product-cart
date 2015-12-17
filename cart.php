<?php

namespace Cgit;

class Cart
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

        // Modify cart in response to query parameters
        add_action('wp', array($this, 'update'));
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
     * Format currency
     */
    public static function formatCurrency($num, $after = false, $sep = '') {
        $value = number_format($num, 2);
        $str = CGIT_PRODUCT_CURRENCY . $sep . $value;

        if ($after) {
            $str = $value . $sep . CGIT_PRODUCT_CURRENCY;
        }

        return $str;
    }

    /**
     * Make sure cart is available in session
     */
    public function create()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
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
        $this->create();

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
        $this->create();
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
            $quantity > $_SESSION['cart'][$key]['quantity']
        ) {
            unset($_SESSION['cart'][$key]);
        } else {
            $_SESSION['cart'][$key]['quantity'] -= $quantity;
        }
    }

    /**
     * Render cart contents and forms
     *
     * Returns the compiled PHP output of a file within the views directory. If
     * the file extension is missing, '.php' will be appended to the file name.
     * The contents of the cart are available to the view files as $cart.
     *
     * The output of each view can be modified using the cgit_cart_render_{name}
     * filter, where the name is the view filename without the extension. The
     * second argument to this function includes the $cart array.
     */
    public function render($view)
    {
        if (substr($view, -4) != '.php') {
            $view = $view . '.php';
        }

        $file = dirname(__FILE__) . '/views/' . $view;
        $name = substr($view , 0, -4);
        $filter = 'cgit_cart_render_' . $name;

        // Make cart contents available to view file
        $contents = $this->contents();

        // Check view file exists
        if (!file_exists($file)) {
            return false;
        }

        ob_start();

        include $file;

        $output = ob_get_clean();
        $output = apply_filters($filter, $output, $contents);

        return $output;
    }
}
