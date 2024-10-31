<?php
/*
	Plugin Name: No Stock Notice
	Plugin URI: http://wordpress.org/plugins/no-stock-notice/
	Description: This plugin show the No stock label on out of stock products.
	Author: Srini Tamil
	Version: 2.0.1
	Author URI: https//:www.srinitamil.com
*/

// If this file is called directly, halt.
if ( ! defined( 'WPINC' ) ) {
	die("Ha Ha Ha...!!!");
}

final class no_stock_notice {

	/**
	 * The instance of the class
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Plugin name slug
	 *
	 * @var string
	 */
	private $plugin_name = 'No_Stock_Notice';

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version = '1.0.0';

	/**
	 * Holds various class instances
	 *
	 * @var array
	 */
	private $all_classes = array();

	/**
	 * Minimum PHP version required
	 *
	 * @var string
	 */
	private $min_php = '5.6';

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			// Define constants
			self::$instance->define_constants();

			// Initialize the classes
			add_action( 'plugins_loaded', array( self::$instance, 'init_classes' ) );

			// Load Vendor Files
			if ( file_exists( NO_STOCK_PATH . '/vendor/autoload.php' ) ) {
				include NO_STOCK_PATH . '/vendor/autoload.php';
			}

			// Register plugin activation activity
			register_activation_hook( __FILE__, array( self::$instance, 'activation' ) );
			register_deactivation_hook( __FILE__, array( self::$instance, 'deactivation' ) );
		}

		return self::$instance;
	}

	public function init_classes() {
		$this->all_classes['admin'] = Toolkit\admin\Admin::init();
		$this->all_classes['frontend'] = Toolkit\frontend\Frontend::init();
	}

	/**
	 * Run on plugin activation
	 */
	public function activation() {
		do_action( 'nostocknotice/activation' );
		flush_rewrite_rules();
	}

	/**
	 * Run on plugin deactivation
	 */
	public function deactivation() {
		do_action( 'nostocknotice/deactivation' );
		flush_rewrite_rules();
	}


	/**
	 * Define plugin constants
	 */
	private function define_constants() {
		define( 'nostocknotice', $this->plugin_name );
		define( 'NO_STOCK_VERSION', $this->version );
		define( 'NO_STOCK_FILE', __FILE__ );
		define( 'NO_STOCK_PATH', dirname( NO_STOCK_FILE ) );
		define( 'NO_STOCK_INCLUDES', NO_STOCK_PATH . '/includes' );
		define( 'NO_STOCK_URL', plugins_url( '', NO_STOCK_FILE ) );
		define( 'NO_STOCK_ASSETS', NO_STOCK_URL . '/assets' );
	}
}

/**
	* Begins execution of the plugin.
    * Since everything within the plugin is registered via hooks,
    * then kicking off the plugin from this point in the file does
    * not affect the page life cycle.
 */
function plugin_toolkit() {
	return no_stock_notice::init();
}

plugin_toolkit();