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
include_once WOOCFCL_PATH . 'classes/class-woocfcl-utils.php';

//print_r(WOOCFCL_Utils::is_woocommerce_active());

if(WOOCFCL_Utils::is_woocommerce_active()):
	
	WOOCFCL_Utils::define('WOOCFCL_BASE_NAME', dirname(plugin_basename( __FILE__ )));
	WOOCFCL_Utils::define('WOOCFCL_URL', plugin_dir_url( __FILE__ ));
	WOOCFCL_Utils::define('WOOCFCL_PREFIX','woocfcl');
	include_once WOOCFCL_PATH . 'classes/class-woocfcl.php';
	// Include the main WooCommerce customization for chile class.

	/**
	 * Returns the main instance of WC customization for chile.
	 *
	 * @since  2.1
	 * @return WOOCFCL customization for chile
	 */
	function WOOCFCL() { 
		// return WOOCFCL::instance();
		if(!isset($GLOBALS[WOOCFCL_PREFIX])) {
			// put it in the global scope
			$GLOBALS[WOOCFCL_PREFIX] = new WOOCFCL();
		}

		return $GLOBALS[WOOCFCL_PREFIX];
	}
         // Global for backwards compatibility.
	WOOCFCL();

endif;