<?php
/*
Plugin Name: No Stock Notice
Plugin URI: http://wordpress.org/plugins/no-stock-notice/
Description: This plugin show the No stock label on out of stock products.
Author: Srini Tamil
Version: 2.0.1
Author URI: https//:www.srinitamil.com
*/


namespace Toolkit\admin;


// If this file is called directly, halt.
use Toolkit\LOGGER;

if (!defined('WPINC')) {
    die("Ha Ha Ha...!!!");
}

class Admin
{

    /**
     * The instance of the class
     *
     * @var self
     */
    protected static $instance;

    /**
     * The Options
     *
     * @var self
     */
    protected $options = array('no_stock_label_name', 'no_stock_label_color', 'no_stock_label_bg_color');

    /**
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @return self
     */
    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            add_action('admin_init', [self::$instance, 'register_mysettings']);
            add_action('admin_menu', [self::$instance, 'register_admin_menu_page']);
            add_action('admin_notices', [self::$instance, 'check_woocommerce_plugin']);
            add_filter('post_class', [self::$instance, 'add_custom_class_to_product_li'], 10, 3);
            add_filter('woocommerce_single_product_image_thumbnail_html', [self::$instance, 'custom_add_thumbnail_class'], 10, 2);
            add_filter('woocommerce_single_product_image_gallery_classes', [self::$instance, 'custom_product_gallery_classes'], 10, 2);

        }

        return self::$instance;
    }

    // Add custom class to WooCommerce product gallery wrapper
    public function custom_product_gallery_classes($classes)
    {
        // Add your custom class
        $classes[] = 'no-stock-notices';
        return $classes;
    }


    // Add custom class to WooCommerce product thumbnails
    public function custom_add_thumbnail_class($html, $post_id)
    {
        global $product;
        $label_text = get_option('no_stock_label_name', 'Sold Out');

        if (!$product->is_in_stock()) {
            $html .= '<span class="soldout">' . __($label_text, 'woocommerce') . '</span>';
        }

        return $html;
    }

    /**
     *    Add custom class to the product listing items
     */
    public function add_custom_class_to_product_li($classes, $class, $post_id)
    {

        if (class_exists('WooCommerce')) {

            // single product page, so return
            if (is_product()) return $classes;


            // Check if the product is out of stock
            if (get_post_meta($post_id, '_stock_status', true) === 'outofstock') {

                // Add your custom class here
                $classes[] = 'no-stock-notices';
            }
        }
        return $classes;
    }


    /**
     * Check woocommerce is installed and activated in website
     */
    public function check_woocommerce_plugin()
    {
        // Check if WooCommerce plugin is active
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            // WooCommerce is not active, display a warning message
            echo '<div class="notice notice-warning is-dismissible"><p>Warning: No Stock Notice plugin requires WooCommerce to be activated. Please activate WooCommerce first!</p></div>';
        }
    }

    /**
     * Register a custom menu page.
     */
    public function register_admin_menu_page()
    {
        add_menu_page(
            __('No Stock Notice', 'my-textdomain'),
            __('No Stock Notice', 'my-textdomain'),
            'manage_options',
            'no-stock-notice',
            [self::$instance, 'option_page'],
            'dashicons-schedule',
            3
        );

    }


    public function option_page()
    { ?>
        <div class="wrap">
            <h2>No Stock Notice</h2>
            <br/>
            <form method="post" action="options.php">
                <?php settings_fields('no-stock-notice');
                ?>
                <table style="text-align: left">
                    <tr style="line-height:40px;  text-align: left">
                        <th scope="row"><label for="no-stock-label">Label Text</label></th>
                        <td><input style="width: 200px;" type="text" id="no-stock-label" name="no_stock_label_name"
                                   value="<?php echo get_option('no_stock_label_name'); ?>"/></td>
                    </tr>
                    <tr style="line-height:40px; text-align: left">
                        <th scope="row"><label for="no-stock-label">Label Text Color</label></th>
                        <td><input style="width: 100px;" type="color" name="no_stock_label_color"
                                   value="<?php echo get_option('no_stock_label_color'); ?>"/></td>
                    </tr>
                    <tr style="line-height:40px; text-align: left">
                        <th scope="row"><label for="no-stock-label">Label BG Color</label></th>
                        <td><input style="width: 100px;" type="color" name="no_stock_label_bg_color"
                                   value="<?php echo get_option('no_stock_label_bg_color'); ?>"/></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /*
     * Register Settings Page
     */
    public function register_mysettings()
    {
        foreach ($this->options as $option) {
            register_setting('no-stock-notice', $option);
        }
    }


}