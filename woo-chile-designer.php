<?php
/**
 * Plugin Name: woocommerce customization for chile
 * Description: woocommerce customization for chile and checkout fields Editor(Add, Edit, Delete and re-arrange fields).
 * Author:      Leonardo Espinoza
 * Version:     1.0.0
 * Author URI:  https://www.leoespinoz.dev
 * Plugin URI:  https://www.leoespinoz.dev
 * Text Domain: woo-chile-custom
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 5.4.1
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2.0
 */

defined( 'ABSPATH' ) || exit;

!defined('WOOCFCL_PATH') && define('WOOCFCL_PATH', plugin_dir_path( __FILE__ ));
!defined('WOOCFCL_BASE_NAME') && define('WOOCFCL_BASE_NAME', dirname(plugin_basename( __FILE__ )));
!defined('WOOCFCL_PREFIX') && define('WOOCFCL_PREFIX', 'woocfcl');

include_once WOOCFCL_PATH . 'classes/class-woocfcl-utils.php';


if(WOOCFCL_Utils::is_woocommerce_active()):

	// Include the main WooCommerce customization for chile class.
	include_once WOOCFCL_PATH . 'classes/class-woocfcl.php';
	/**
	 * Returns the main instance of WC customization for chile.
	 *
	 * @since  2.1
	 * @return WC customization for chile
	 */
	function WOOCFCL() { 
			return WOOCFCL::instance();
	}
         // Global for backwards compatibility.
	$GLOBALS[WOOCFCL_PREFIX] = WOOCFCL();

endif;