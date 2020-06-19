<?php
/**
 * The file that defines the core plugin class.
 *
 * @link       https://www.leoespinoz.dev
 * @since      1.0.0
 *
 * @package    woo-chile-custom
 * @subpackage woo-chile-custom/classes
 */

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL')):

class WOOCFCL {

	/**
	 * The single instance of the class.
	 *
	 * @var WCSCL
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * plugin version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * lang locale.
	 *
	 * @var string
	 */
	public $langlocale = '';
	/**
	 * App instance.
	 *
	 * @var WC_App
	 */
	public $app = null;

	/**
	 * Countries instance.
	 *
	 * @var WC_Countries
	 */
	public $countries = null;
    /**
	 * Countries instance.
	 *
	 * @var WC_States
	 */
	public $states = null;
    /**
	 * Countries instance.
	 *
	 * @var WC_Cities
	 */
	public $cities = null;
    /**
	 * Countries instance.
	 *
	 * @var WC_Billing
	 */
	public $billing = null;

    /**
	 * Countries instance.
	 *
	 * @var WC_Shipping
	 */
	public $shipping = null;
	/**
	 * Countries instance.
	 *
	 * @var WC_Additional
	 */
	public $additional = null;
	
	
	public function __construct() {
		add_action('plugins_loaded', array($this, 'on_init_plugin'));
		add_action( 'woocommerce_init', array( $this, 'on_init_woo' ) ); 

	}

	/**
	 * Main WooCommerce Custom From Chile Instance.
	 *
	 * Ensures only one instance of WooCCL is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return WooCCL - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Define WC Constants.
	 */
	private function define_constants() {

		WOOCFCL_Utils::define('WOOCFCL_VERSION', $this->version );
		WOOCFCL_Utils::define('WOOCFCL_PLUGIN_NAME', 'Chile customization for woocommerce');
		WOOCFCL_Utils::define('WOOCFCL_TEXT_DOMAIN', 'woo-chile-custom');
		WOOCFCL_Utils::define('WOOCFCL_PREFIX', 'woocfcl');
		WOOCFCL_Utils::define('WOOCFCL_ACTION_LINK', 'woo_chile_designer');	

		WOOCFCL_Utils::define('WOOCFCL_PATH_CLASS', WOOCFCL_PATH .'classes/');
		WOOCFCL_Utils::define('WOOCFCL_PATH_STATES', WOOCFCL_PATH .'states/');
		WOOCFCL_Utils::define('WOOCFCL_PATH_CITIES', WOOCFCL_PATH .'cities/');

		WOOCFCL_Utils::define('WCSCL_URL', plugins_url( '../', __FILE__ ));
		WOOCFCL_Utils::define('WCSCL_ASSETS_URL', WCSCL_URL .'assets/');

		
	}


	private function load_dependencies() {
		if(!function_exists('is_plugin_active')){
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		require_once WOOCFCL_PATH_CLASS . 'class-wcscl-options.php';
		require_once WOOCFCL_PATH_CLASS . 'class-wcscl-app-options.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocclf-states.php';
		require_once WOOCFCL_PATH_CLASS . 'class-wcscl-utils.php';
		require_once WOOCFCL_PATH_CLASS . 'class-wcscl-admin.php';
		require_once WOOCFCL_PATH_CLASS . 'class-wcscl-fields.php';
		require_once WOOCFCL_PATH_CLASS . 'class-wcscl-checkout.php';


	}

	public function load_textdomain(){
		$this->langlocale = WOOCFCL_Utils::get_lang_locale();
	
		load_textdomain(WOOCFCL_TEXT_DOMAIN, WP_LANG_DIR.'/'.WOOCFCL_BASE_NAME.'/'.WOOCFCL_TEXT_DOMAIN.'-'.$this->langlocale.'.mo');
		load_plugin_textdomain(WOOCFCL_TEXT_DOMAIN, false, WOOCFCL_BASE_NAME . '/languages/');
	}
	private function set_global_options() {
		$this->app=new WCSCL_AppOptions();
		$this->states=new WOOCCLF_States();
	}

	private function set_admin_hooks() {
		$plugin_admin = new WCSCL_Admin();
	}

	private function set_public_hooks() {
		$plugin_checkout = new WCSCL_Checkout();
	}
	public function on_init_plugin() {
		$this->define_constants();
		$this->load_dependencies();
		$this->load_textdomain();

		// $this->set_global_options();
		// $this->set_admin_hooks();
		// $this->set_public_hooks();
	}
	public function on_init_woo() {
		// $this->define_constants();
		// $this->load_dependencies();
		// $this->load_locale();
		
		$this->set_global_options();
		$this->set_admin_hooks();
		$this->set_public_hooks();
	}

}

endif;