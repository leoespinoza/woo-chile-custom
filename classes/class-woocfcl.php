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
	 * @var WOOCFCL
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
	 * @var WOOCFCL_AppOptions
	 */
	public $app = null;
	public $wpapp = null;
	/**
	 * form instance.
	 *
	 * @var WOOCFCL_Form
	 */
	public $form = null;
	/**
	 * countries allowed by woocommerce
	 *
	 * @var array_Countries
	 */
	public $countries = null;
	/**
	 * Get the base country for the store.
	 *
	 * @var string
	 */
	public $base_country=null;
    /**
	 * Countries instance.
	 *
	 * @var WOOCFCL_States
	 */
	public $states = null;
	public $wpstates = null;
    /**
	 * Countries instance.
	 *
	 * @var WOOCFCL_Cities
	 */
	public $cities = null;
    /**
	 * Countries instance.
	 *
	 * @var WOOCFCL_Billing
	 */
	public $billing = null;
    /**
	 * Countries instance.
	 *
	 * @var WOOCFCL_Shipping
	 */
	public $shipping = null;
	/**
	 * Countries instance.
	 *
	 * @var WOOCFCL_Additional
	 */
	public $additional = null;
	/**
	 * Countries instance.
	 *
	 * @var WOOCFCL_Admin
	 */
	public $admin_page = null;
	/**
	 * Countries instance.
	 *
	 * @var WOOCFCL_Checkout
	 */
	public $user_page = null;
	
	public function __construct() {

		$this->load_constants();
		$this->load_dependencies();
		$this->load_global_hook();	
		$this->load_admin_hooks();
		$this->load_public_hooks();
	}
	/**
	 * Main WooCommerce Custom From Chile Instance.
	 *
	 * Ensures only one instance of WooCCL is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @see WC()
	 * @return WOOCCL - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * define WOOCFCL Constants.
	 */
	private function load_constants() {

		WOOCFCL_Utils::define('WOOCFCL_VERSION', $this->version );
		WOOCFCL_Utils::define('WOOCFCL_SITE_URL', 'https://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/ ');
		WOOCFCL_Utils::define('WOOCFCL_WPSITE_URL', 'https://wordpress.org/support/plugin/woo-checkout-field-editor-pro/reviews?rate=5#new-post');
		WOOCFCL_Utils::define('WOOCFCL_PLUGIN_NAME', 'Chile customization for woocommerce');
		WOOCFCL_Utils::define('WOOCFCL_TEXT_DOMAIN', 'woo-chile-custom');
		WOOCFCL_Utils::define('WOOCFCL_ACTION_LINK', 'woo_chile_designer');	
		WOOCFCL_Utils::define('WOOCFCL_SCREENID','woocommerce_page_'.WOOCFCL_ACTION_LINK);
		WOOCFCL_Utils::define('WOOCFCL_SCREENID_I18', strtolower(WOOCFCL_Utils::t('WooCommerce')) .'_page_'.WOOCFCL_ACTION_LINK);			
		WOOCFCL_Utils::define('WOOCFCL_PATH_CLASS', WOOCFCL_PATH .'classes/');
		WOOCFCL_Utils::define('WOOCFCL_PATH_VIEW_BACKEND', WOOCFCL_PATH .'view/backend/');
		WOOCFCL_Utils::define('WOOCFCL_PATH_COUNTRIES', WOOCFCL_PATH .'i18n/countries/');
		WOOCFCL_Utils::define('WOOCFCL_PATH_STATES', WOOCFCL_PATH .'i18n/states/');
		WOOCFCL_Utils::define('WOOCFCL_PATH_CITIES', WOOCFCL_PATH .'i18n/cities/');
		WOOCFCL_Utils::define('WOOCFCL_ASSETS_URL', WOOCFCL_URL .'assets/');
		WOOCFCL_Utils::define('WOOCFCL_ASSETS_ADM_URL', WOOCFCL_URL .'assets/admin/');
		WOOCFCL_Utils::define('WOOCFCL_ASSETS_ADM_VENDORS_URL', WOOCFCL_ASSETS_ADM_URL .'vendors/');

		WOOCFCL_Utils::define('WOOCFCL_CSS_URL', WOOCFCL_ASSETS_URL .'css/');
		WOOCFCL_Utils::define('WOOCFCL_JS_URL', WOOCFCL_ASSETS_URL .'js/');
		WOOCFCL_Utils::define('WOOCFCL_IMG_URL', WOOCFCL_ASSETS_URL .'img/');
		
	}

	private function load_dependencies() {
		if(!function_exists('is_plugin_active'))
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( ! class_exists( 'WP_List_Table' ) )
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-options.php';	
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpoptions.php';		
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-form.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-fields.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpfields.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpappoptions.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpstates.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpcities.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-cities.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpbilling.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-billing.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpshipping.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-shipping.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-wpadditional.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-additional.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-admin.php';
		require_once WOOCFCL_PATH_CLASS . 'class-woocfcl-checkout.php';


	}

    private function load_global_hook() {

		add_action('plugins_loaded', array($this, 'load_textdomain'));
		add_action('woocommerce_init', array($this, 'load_instances'));
		add_filter('woocommerce_states', array($this, 'load_states'));
		add_filter('woocommerce_default_address_fields', array($this, 'load_default_order'));
		
	}

	private function load_admin_hooks() {
		$this->admin_page = new WOOCFCL_Admin();
		add_action('admin_enqueue_scripts', array($this->admin_page, 'enqueue_styles_and_scripts'));
		add_action('admin_menu', array($this->admin_page, 'register_admin_page'));
		add_filter('woocommerce_screen_ids', array($this->admin_page, 'add_screen_id'));
		add_filter('plugin_action_links_'.WOOCFCL_BASE_NAME, array($this->admin_page, 'add_plugin_action_links'));
		add_action('after_setup_theme', array($this->admin_page, 'register_admin_hooks'));
		

		add_action('wp_ajax__ajax_fetch_custom_list', array($this->admin_page, '_ajax_fetch_custom_list_callback'));
		add_action('admin_footer', array($this->admin_page, 'ajax_script'));
	}

	private function load_public_hooks() {
		$this->user_page = new WOOCFCL_Checkout();
	}

	/**
	 * Load text domain for internationalitation         
	 */
	public function load_textdomain(){

		$this->langlocale =$this->wp_get_lang_locale();
		$mofile= WP_LANG_DIR.'/'.WOOCFCL_BASE_NAME.'/'.WOOCFCL_TEXT_DOMAIN.'-'.$this->langlocale['lang'].'.mo';
		$plugin_rel_path=WOOCFCL_BASE_NAME . '/languages/';
		load_textdomain(WOOCFCL_TEXT_DOMAIN,$mofile);
		load_plugin_textdomain(WOOCFCL_TEXT_DOMAIN, false, $plugin_rel_path);
	}
	/**
	 * Implement WC States
	 * @param mixed $states
	 * @return mixed
	 */
	public function  load_states($states=array()) {
		
		if (!isset($this->states)) $this->load_instances();
		return $this->states->get_woocommmerce_state();
	}

	public function load_instances() {

		$this->countries = $this->woocom_get_allowed_countries();
		$this->base_country=$this->woocom_get_base_country();
		
		$this->app = new WOOCFCL_WPAppOptions();
	    //$this->wpapp = new WOOCFCL_AppOptions();	
		$this->form = new WOOCFCL_Form();	
		$this->states = new WOOCFCL_WPStates();	
		//$this->states = new WOOCFCL_States();

		$this->cities  =new WOOCFCL_WPCities();
		$this->wpbilling = new WOOCFCL_WPBilling();
		$this->billing = new WOOCFCL_Billing();
		$this->shipping = new WOOCFCL_Shipping();
		$this->additional = new WOOCFCL_Additional();
		$this->wpshipping = new WOOCFCL_WPShipping();
		$this->wpadditional = new WOOCFCL_WPAdditional();
	}

	public function load_default_order($fields) {
		return $this->woocom_change_address_fields_order($fields);
	}

	public function set_field_property_to_form($nameobject) {

		$fields =isset($this->{$nameobject}) ?$this->{$nameobject}->set_field_property_to_form():false;
		return $fields;
	}

	public function get_option_datatable($nameobject) {

		return isset($this->{$nameobject}) ?$this->{$nameobject}->option_datatable:false;
	}

	public function reset_checkout_field() {

		return (
			$this->billing->deleteDefault() &&
			$this->shipping->deleteDefault() &&
			$this->additional->deleteDefault()
		);

	}

	public function save_checkout_field($name, $f_names,$f_order,$f_deleted,$f_enabled) {

		$result=(isset($this->{$name}))?$this->{$name}->update_all_fields($f_names,$f_order,$f_deleted,$f_enabled):false;
		return $result;
	}

	public function set_checkout_field($name,$field, $new,	$unsetfield) {

		$currentFields=(isset($this->{$name}))?$this->{$name}->set_field($field ,$new,$unsetfield):false;
		return $currentFields;
	}

	public function get_checkout_fields($name) {

		$currentFields=(isset($this->{$name}))?(isset($this->{$name}->fieldsPlugin)?$this->{$name}->fieldsPlugin:$this->{$name}->option_value):array();
		return $currentFields;
	}

	public function get_all_checkout_fields($order=false){
		$fields = array();
		$needs_shipping = true;

		if($order){
			$needs_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address() ? true : false;
		}
		
		if($needs_shipping){
			$fields = array_merge(self::get_checkout_fields('billing'), self::get_checkout_fields('shipping'), self::get_checkout_fields('additional'));
		}else{
			$fields = array_merge(self::get_checkout_fields('billing'), self::get_checkout_fields('additional'));
		}

		return $fields;
	}

    /***********************************
	----- woocommerce functions - START ------
	***********************************/



    private function wp_get_lang_locale() {
        $lng= apply_filters('plugin_locale', get_locale(), WOOCFCL_TEXT_DOMAIN);
		
		return array('lang'=> $lng,'shortlang'=>substr ($lng , 0, 2) );
    }
    
    public static function woocom_version_check( $version = '3.0' ) {
        if(function_exists( 'is_woocommerce_active' ) && is_woocommerce_active() ) {
            return( version_compare( WC_VERSION, $version, ">=" ) ) ;
        }
        return false;
    }

    private function woocom_get_allowed_countries(){

        return array_merge(WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries());
    }

	public static function woocom_get_order_id($order){
		$order_id = false;
		$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
		if(self::woocom_version_check() && !empty($order)){
			$order_id =self::woocom_version_check()? $order->get_id() : $order->id ;
		}
		return $order_id;
	}

	public function woocom_get_States()
    {
        return include WC()->plugin_path() . '/i18n/states.php';
    }  
	public function woocom_get_base_country()
    {
        return WC()->countries->get_base_country();
    } 
// ojo a borrar
	public function woocom_get_address_fields($option='')
    {
        return WC()->countries->get_address_fields($this->woocom_get_base_country(), $option . '_');
    }  

	/**
	 * Change the order of State and City fields to have more sense with the steps of form
	 * @param mixed $fields
	 * @return mixed
	 */         
	private function woocom_change_address_fields_order($fields) {
		$fields['state']['priority'] = 50;
		$fields['city']['priority'] = 60;
		$fields['address_1']['priority'] = 70;
		$fields['address_2']['priority'] = 80;            
		$fields['postcode']['priority'] = 90;  
		return $fields;
	}
}

endif;