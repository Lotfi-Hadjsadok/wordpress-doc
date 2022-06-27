<?php
// Order meta data
add_action('woocommerce_checkout_create_order_line_item', 'save_cart_item_custom_meta_as_order_item_meta', 10, 4);
function save_cart_item_custom_meta_as_order_item_meta($item, $cart_item_key, $values, $order)
{
    $item->update_meta_data("meta_ke", "data");
}

// Cart meta data
add_filter('woocommerce_get_item_data', 'display_cart_item_custom_meta_data', 10, 2);
function display_cart_item_custom_meta_data($item_data, $cart_item)
{
    $item_data[] = array(
        'key'       => "meta_key",
        'value'     => $cart_item['meta_key'],
    );
    return $item_data;
}

// Custom add to cart button
if (!function_exists('woocommerce_template_loop_add_to_cart')) {
    function woocommerce_template_loop_add_to_cart()
    {
        global $product;
        if ($product->get_type() == "wdm_custom_product") {
            wc_get_template('loop/add-to-cart-wcm.php');
        } else {
            wc_get_template('loop/add-to-cart.php');
        }
    }
}

// puchasable or not product
add_filter('woocommerce_is_purchasable', 'remove_add_to_cart_for_tag_id', 10, 2);
function remove_add_to_cart_for_tag_id($purchasable, $product)
{
    return $purchasable; // true or false with logic
}

// Custom add to cart 
add_action('template_redirect', function () {
    if ($_POST['action'] == "add_to_cart") {
        $price = sanitize_text_field($_POST['price']);
        $pid = sanitize_text_field($_POST['pid']);
        WC()->cart->add_to_cart($pid, 1, 0, array(), array('prix' => $price, 'pipo' => 'lotfi was here'));
    }
});


// Before add to cart done put custom prices
add_action('woocommerce_before_calculate_totals', 'custom_cart_item_price', 30, 1);
function custom_cart_item_price($cart)
{
    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['custom_price']))
            $cart_item['data']->set_price($cart_item['custom_price']);
    }
}
