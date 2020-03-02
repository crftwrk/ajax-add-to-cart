<?php
/*Plugin Name: Ajax add to cart for bootScore
Plugin URI: https://bootscore.me
Description: Adds products directly to the Ajax Off Canvas cart on product detail pages without reloading. Does NOT work with affiliate- and group products!
Version: 1.0.0
Author: Bastian Kreiter
Author URI: https://crftwrk.de
License: GPLv2
*/






// Register Scripts
function bootscore_add_cart() {

    wp_enqueue_script( 'add_cart', plugins_url( '/js/ajax_add_to_cart.js', __FILE__ ));
    
    }

add_action('wp_enqueue_scripts','bootscore_add_cart');
// Register Scripts End










// Ajax

add_action('wp_ajax_bootscore_woocommerce_ajax_add_to_cart', 'bootscore_woocommerce_ajax_add_to_cart'); 

add_action('wp_ajax_nopriv_bootscore_woocommerce_ajax_add_to_cart', 'bootscore_woocommerce_ajax_add_to_cart');          

function bootscore_woocommerce_ajax_add_to_cart() {  

    $product_id = apply_filters('bootscore_woocommerce_add_to_cart_product_id', absint($_POST['product_id']));

    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);

    $variation_id = absint($_POST['variation_id']);

    $passed_validation = apply_filters('bootscore_woocommerce_add_to_cart_validation', true, $product_id, $quantity);

    $product_status = get_post_status($product_id); 

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) { 

        do_action('bootscore_woocommerce_ajax_added_to_cart', $product_id);

            if ('yes' === get_option('bootscore_woocommerce_cart_redirect_after_add')) { 

                wc_add_to_cart_message(array($product_id => $quantity), true); 

            } 

            WC_AJAX :: get_refreshed_fragments(); 

            } else { 

                $data = array( 

                    'error' => true,

                    'product_url' => apply_filters('bootscore_woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

                echo wp_send_json($data);

            }

            wp_die();

        }

// Ajax End