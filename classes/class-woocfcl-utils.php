<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Utils')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_Utils
{
    
    private static $_instance = null;

    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct()
    {
    }  

    public static function instance() {
        if (is_null(self::$_instance)) {
        self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	public static function define( $name, $value ) {
		// if ( ! defined( $name ) ) {
		// 	define( $name, $value );
        // }
        !defined($name) && define($name, $value);
    }
    


    public static function version_check( $version = '1.0.0' ) {
		return WOOCFCL_VERSION && version_compare( WOOCFCL_VERSION, $version, ">=" ) ? true:false;
    }
    
	/**
	 * check if current screen is the plugin.
	 *
     *
     * @return bool
     */

	public static function plug_is_current_screen() {
        if (function_exists('is_admin') && function_exists('get_current_screen')){
			if ( is_admin() ) {
				$my_current_screen = get_current_screen();
		
				return  ( isset( $my_current_screen->id ) && WOOCFCL_SCREENID === $my_current_screen->id ) ? true:false;
			}
        }
        return false;
    } 

	/**
	 * Define if woocommerce active.
	 *
     *
     * @return bool
     */

	public static function is_woocommerce_active() {
        if (!function_exists('is_woocommerce_active')){
            $active_plugins =is_multisite()? get_site_option('active_sitewide_plugins') : get_option('active_plugins');
            return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins) || class_exists('WooCommerce');
        }
        else return is_woocommerce_active();
    }

    public function woo_get_allowed_countries(){
        return array_merge(WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries());
    }

    /***********************************
	----- i18n functions - START ------
	***********************************/
	public static function t($text){
		if(!empty($text)){	
			$otext = $text;						
			$text = WOOCFCL_Utils::translate($text);
            $text = ($text !== $otext)? :__($text, 'woocommerce');	
		}
		return $text;
	}

	public static function et($text){
		echo self::t($text);
	}
    /**
     * Retrieve the translation of $text.
     *
     * If there is no translation, or the text domain isn't loaded, the original text is returned.
     *
     *
     * @param string $text   Text to translate.
     * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
     *                       Default 'default'.
     * @return string Translated text.
     */
    public static function translate( $text ) {
        return translate( $text, WOOCFCL_TEXT_DOMAIN );
    }
    /**
     * Display translated text.
     *
     * @since 1.2.0
     *
     * @param string $text   Text to translate.
     * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
     *                       Default 'default'.
     */
    public static function etranslate( $text, $domain = 'default' ) {
        echo translate( $text, WOOCFCL_TEXT_DOMAIN );
    }



    
    /***********************************
	----- utilities functions - START ------
	***********************************/
    
    public static function array_equal($a, $b) {
		return (
            is_array($a) 
            && is_array($b) 
            && count($a) == count($b) 
            && array_diff($a, $b) === array_diff($b, $a)
		);
    }
    public static function array_empty($a) {
        if (!is_array($a) || is_null($a)) return  true; 
		return (
            empty($a) 
            || count($a) == 0 
            || sizeof($a) == 0 
		);
	}

	public static function array_get_value($a,$key,$default=array()) {
        if (!is_array($a) || is_null($key))  return  $default; 
		return isset($a[$key])? $a[$key]:$default;
	}
    public static function is_blank($value) {
		return empty($value) && !is_numeric($value);
    }
    
    public static function bool_fromvalue($value) {
		return !empty($value) && $value=='yes'?true:false;
    }
    
    public static function bool_valueto($value) {
		return !empty($value) && $value==true?'yes':'no';
    }
    
    public static function get_querystring($var , $default){
		return isset( $_GET[$var] ) ? esc_attr( $_GET[$var] ) : $default;
    }
    
    public static function get_postvar($var , $default){
		return isset( $_POST[$var] ) && !empty( $_POST[$var] )? $_POST[$var] : $default;
    } 

    public static function printToArray($name,$obj){

        echo '<br/><br/>'.$name .'<pre>';
        print_r((array) $obj);
        echo '</pre>';
    }  

	public static function setSession($name,$value){
		if (version_compare(phpversion(), '5.4.0', '<')) {
			if(session_id() == '') {
				session_start();
			}
		}
		else
		{
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
		}

		if ( ! isset($_SESSION[$name])) $_SESSION[$name] = $value;
    } 
    /***********************************
	----- Checkout Field ------
    ***********************************/

	public static function is_address_field($name){
		$address_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
		);

		if($name && in_array($name, $address_fields)){
			return true;
		}
		return false;
	}

	public static function is_default_field($name){
		$default_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
			'order_comments'
		);

		if($name && in_array($name, $default_fields)){
			return true;
		}
		return false;
	}

	public static function is_default_field_name($field_name){
		$default_fields = array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 
			'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		);

		if($name && in_array($name, $default_fields)){
			return true;
		}
		return false;
	}

	public static function is_reserved_field_name( $field_name ){
		$reserved_names = array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 
			'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		);
		
		if($name && in_array($name, $reserved_names)){
			return true;
		}
		return false;
	}

	public static function is_valid_field($field){
		$return = false;
		if(is_array($field)){
			$return = true;
		}
		return $return;
	}

	public static function is_enabled($field){
		$enabled = false;
		if(is_array($field)){
			$enabled = isset($field['enabled']) && $field['enabled'] == false ? false : true;
		}
		return $enabled;
	}

	public static function is_custom_field($field){
		return (isset($field['custom']) && $field['custom'])?true:false;
	}

	public static function is_active_custom_field($field){
		$return = false;
		if(self::is_valid_field($field) && self::is_enabled($field) && self::is_custom_field($field)){
			$return = true;
		}
		return $return;
	}

	public static function prepare_field_options($options){
		if(is_string($options)){
			$options = array_map('trim', explode('|', $options));
		}
		return is_array($options) ? $options : array();
	}

	public static function prepare_options_array($options_json){
		$options_json = rawurldecode($options_json);
		$options_arr = json_decode($options_json, true);
		$options = array();
		
		if($options_arr){
			foreach($options_arr as $option){
				$okey = isset($option['key']) ? $option['key'] : '';
				$otext = isset($option['text']) ? $option['text'] : '';

				$options[$okey] = $otext;
			}
		}
		return $options;
    }

	


}
endif;    