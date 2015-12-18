# Castlegate IT WP Product Cart #

The Castlegate IT WP Product Cart plugin extends the [Product Catalogue](http://github.com/castlegateit/cgit-wp-product-catalogue) plugin to include a shopping cart system. It supports all the features of the Product Catalogue plugin, including discounts and product variants. It does *not* provide a checkout system.

## Widgets ##

The plugin provides two widgets: Cart Contents and Add to Cart. The Add to Cart widget will only appear on single product pages. These plugins use `$cart->render('contents')` and `$cart->render('add')` respectively; see methods below for customization options.

## Functions ##

The plugin provides a single function `cgit_product_cart()`, which retrieves the cart object. The object uses a singleton pattern, which means it can only have one instance.

    $cart = cgit_product_cart();
    $cart = Cgit\ProductCart::getInstance(); // same as above

## Methods ##

The `Cgit\ProductCart` object provides various methods:

*   `$cart->render($view)` returns the compiled output of a PHP file from the `views` directory within the plugin. There are two views: `contents` and `add`, which are used by the widgets to render content.

*   `$cart->update()` this method is attached to the `wp` action and checks for submitted `$_POST` data, adding or removing products from the cart session accordingly.

*   `$cart-add($product, $quantity = false, $variant = false)` adds one or more products to the cart. If no quantity is set, one product is added.

*   `$cart-add($product, $quantity = false, $variant = false)` removes one or more products to the cart. If no quantity is set, all products are removed.

*   `$cart->contents()` returns the cart contents from the stored session, or an empty array if the cart is empty.

*   `$cart::formatCurrency($number, $after = false, $sep = '')` is the same as the `formatCurrency` method in the `Cgit\ProductCatalogue` class.

## Session ##

The cart contents are stored as an array in `$_SESSION['cart']`. A cart might something like:

    $_SESSION = array(
        'cart' => array(
            array(
                'product' => 7, // product ID
                'quantity' => 2, // quantity
                'variant' => 3 // variant index from repeater field
            ),
            array(
                'product' => 4,
                'quantity' => 1,
                'variant' => false // this product does not have variants
            )
        )
    );

## Filters ##

The `$cart->render()` method provides filters called `cgit_product_render_{name}`, where `{name}` is `contents` or `add`. These filters allow you to edit or replace the default output. The filter allows a second argument, which includes the cart contents (`$cart->contents()`).
