<?php
/*
Plugin Name: No Stock Notice
Plugin URI: http://wordpress.org/plugins/no-stock-notice/
Description: This plugin show the No stock label on out of stock products.
Author: Srini Tamil
Version: 2.0.1
Author URI: https//:www.srinitamil.com
*/

namespace Toolkit\frontend;

/**
 * Class Frontend
 * @package Toolkit\frontend
 */
class Frontend
{
    /**
     * The instance of the class
     *
     * @var self
     */
    protected static $instance;

    /**
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @return self
     */
    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();

            add_action('woocommerce_before_shop_loop_item_title', [self::$instance, 'Nostock_nsn_sold_out_shop_woocommerce']);
            add_action('wp_head', [self::$instance, 'Nostock_nsn_load_plugin_css']);
            add_filter('woocommerce_output_related_products_args', [self::$instance, 'add_custom_class_to_related_products'], 10, 3);
        }

        return self::$instance;
    }

    public function add_custom_class_to_related_products($args)
    {
        // Add your custom class to the 'class' parameter
        $args['class'] .= 'custom-related-products';

        return $args;
    }


    /**
     *adding action for the Showing No stock label on Product Shop pages
     */
    public function Nostock_nsn_sold_out_shop_woocommerce()
    {
        global $product;
        $label_text = get_option('no_stock_label_name', 'Sold Out');

        if (!$product->is_in_stock()) {
            echo '<span class="soldout">' . __($label_text, 'woocommerce') . '</span>';
        }
    }

    /*
     *  Enqueue Stylesheet
     */
    public function Nostock_nsn_load_plugin_css()
    {

        $color = get_option('no_stock_label_color', '#fff');
        $bg_color = get_option('no_stock_label_bg_color', '#A00');

        echo "<style>

                    .no-stock-notices {
                        position: relative;
                        overflow: hidden; 
                    }
                    
					.soldout {
					    top: 2em;
					    left: -5em;
					    color: " . $color . ";
					    display: block;
					    position: absolute;
					    text-align: center;
					    text-decoration: none;
					    letter-spacing: .06em;
					    background-color: " . $bg_color . " ;
					    padding: 0.5em 5em 0.4em 5em;
					    text-shadow: 0 0 0.75em #444;
					    box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
					    font: bold 16px/1.2em Arial, Sans-Serif;
					    -webkit-text-shadow: 0 0 0.75em #444;
					    -webkit-box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
					    -webkit-transform: rotate(-45deg) scale(0.75,1);
					    z-index: 10;
					}
					
						.soldout:before {
					    content: '';
					    top: 0;
					    left: 0;
					    right: 0;
					    bottom: 0;
					    position: absolute;
					    margin: -0.3em -5em;
					    transform: scale(0.7);
					    -webkit-transform: scale(0.7);
					    border: 2px rgba(255,255,255,0.7) dashed;
					}
					
			</style>";
    }


}