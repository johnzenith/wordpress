<?php

/**
 * Display Woocommerce product images
 * 
 * Works with WC 3+
 */

/**
 * Get the product images if the order ID or order object is known
 * 
 * @param int|WC_Order $order Specifies the order ID or object whose product images 
 *                            should be retrieved
 * 
 * @return array  The product images on success. An empty array is returned on failure.
 */
function get_wc_product_images($order)
{
    if (is_int($order)) {
        $_order = wc_get_order($order);
    } elseif (is_object($order)) {
        $_order = &$order;
    } else {
        $_order = false;
    }

    // Make sure the order is valid
    if (!$_order || !method_exists($order, 'get_items'))
        return [];

    $items            = $order->get_items();
    $product_img_urls = [];

    if (empty($items)) return [];

    foreach ($items as $item_id => $item) {
        $order_data = $item->get_data();

        if (isset($order_data['product_id'])) {
            $product = wc_get_product($order_data['product_id']);

            if (!method_exists($product, 'get_image_id'))
                continue;

            $image_id                   = $product->get_image_id();
            $product_img_urls[$item_id] = wp_get_attachment_image_url($image_id, 'full');
        }
    }
    return $product_img_urls;
}

/**
 * Displaying the product images
 */
function display_wc_product_images(array $images = [])
{
    if (empty($images)) return;
    foreach ($images as $item_id => $image) {
        $img_alt_text = sprintf(__('Item-%s', 'text-domain'), $item_id);
        echo sprintf(
            '<div><img width="200px" height="200px" src="%s" alt="%s"></div>',
            esc_url($image),
            $img_alt_text,
        );
    }
}

/**
 * Optionally, you even display the images using any carousel of choice.
 * 
 * Let's use the OwlCarousel library
 * @link https://owlcarousel2.github.io/OwlCarousel2/
 */
function display_wc_product_images_with_owlcarousel(array $images = [])
{
?>
    <div class="owl-carousel owl-theme">
        <?php foreach ($images as $item_id => $image) : ?>
            <div class="item img-<?php echo $item_id; ?>">
                <img class="product-img" src="<?php echo esc_url($image); ?>" alt="<?php echo sprintf(__('Item-%s', 'text-domain'), $item_id); ?>">
            </div>
        <?php endforeach; ?>
    </div>
<?php
}

/**
 * The OwlCarousel 2 JS script
 * 
 * Make sure to include jQuery and the OwlCarousel js and css scripts
 * @link https://jQuery.com
 * @link https://owlcarousel2.github.io/OwlCarousel2/
 */
function owlcarousel2_js_script()
{
?>
    <script>
    $('.owl-carousel').owlCarousel({
        loop: false,
        items: 1,
        center: true,
        margin: 10,
        nav: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    });
    </script>
<?php
}
