<?php

if (count($contents) == 0) {
    include 'empty.php';
    return;
}

// Initial values
$products = array();
$total = 0;
$discount = 0;

foreach ($contents as $item) {
    $product = cgit_product($item['product']);

    // Product information
    $output = array(
        'id' => $product->ID,
        'name' => $product->post_title,
        'url' => get_permalink($product->ID),
        'quantity' => $item['quantity'],
        'price' => $product->product_price * $item['quantity'],
        'original' => $product->product_price_original * $item['quantity'],
    );

    // Increment total price and discount
    $total += $output['price'];
    $discount += $output['original'] - $output['price'];

    // Variant information
    if ($item['variant'] !== false) {
        $variant = $product->product_variants[$item['variant']];
        $output['variant'] = $item['variant'];
        $output['name'] .= ' (' . $variant['variant_name'] . ')';
    }

    // Append to list of products in cart
    $products[] = $output;
}

usort($products, function($a, $b) {
    return $a['name'] > $b['name'];
});

?>
<div class="cart-contents">
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td>
                    <a href="<?= $product['url'] ?>"><?= $product['name'] ?></a>
                    <form action="" method="post">
                        <input type="hidden" name="cart_action" value="remove" />
                        <input type="hidden" name="cart_product" value="<?= $product['id'] ?>" />
                        <?php if (isset($product['variant'])): ?>
                            <input type="hidden" name="cart_variant" value="<?= $product['variant'] ?>" />
                        <?php endif; ?>
                        <button>Remove</button>
                    </form>
                </td>
                <td><?= $product['quantity'] ?></td>
                <td><?= Cgit\Cart::formatCurrency($product['price']) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2">Total</td>
            <td><?= Cgit\Cart::formatCurrency($total) ?></td>
        </tr>
        <?php if ($discount): ?>
            <tr>
                <td colspan="2">Total discount</td>
                <td><?= Cgit\Cart::formatCurrency($discount) ?></td>
            </tr>
        <?php endif; ?>
    </table>
</div>
