<?php

if (!is_singular(CGIT_PRODUCT_POST_TYPE)) {
    return;
}

$product = cgit_product();

?>
<div class="cart-add">
    <form action="" method="post">

        <input type="hidden" name="cart_product" value="<?= $product->ID ?>" />
        <input type="hidden" name="cart_action" value="add" />

        <p>
            <label for="cart_quantity">Quantity</label>
            <input type="number" name="cart_quantity" id="cart_quantity" value="1" />
        </p>

        <?php

        if ($product->product_variants) {
            ?>
            <p>
                <label for="cart_variant">Variant</label>
                <select name="cart_variant" id="cart_variant">
                <?php

                foreach ($product->product_variants as $key => $variant) {
                    $selected = '';

                    if (
                        isset($_POST['cart_variant']) &&
                        $_POST['cart_variant'] == $key
                    ) {
                        $selected = ' selected';
                    }

                    ?>
                    <option value="<?= $key ?>"<?= $selected ?>><?= $variant['variant_name'] ?></option>
                    <?php
                }

                ?>
                </select>
            </p>
            <?php

        }

        ?>

        <p>
            <button>Add to Cart</button>
        </p>

    </form>
</div>
