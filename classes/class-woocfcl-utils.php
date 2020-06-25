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
    
    /***********************************
	----- woocommerce functions - START ------
	***********************************/
    public static function woo_version_check( $version = '3.0' ) {
        if(function_exists( 'is_woocommerce_active' ) && is_woocommerce_active() ) {
            global $woocommerce;
            if( version_compare( $woocommerce->version, $version, ">=" ) ) {
                return true;
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
            $active_plugins = (array) get_option('active_plugins', array());
            if(is_multisite()){
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
            }
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
			if($text === $otext){
				$text = __($text, 'woocommerce');
			}
		}
		return $text;
	}

	public static function et($text){
		if(!empty($text)){	
			$otext = $text;						
			$text = WOOCFCL_Utils::translate($text);	
			if($text === $otext){
				$text = __($text, 'woocommerce');
			}
		}
		echo $text;
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


    public static function get_lang_locale() {
        return apply_filters('plugin_locale', get_locale(), WOOCFCL_TEXT_DOMAIN);
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
        if (!is_array($a) || is_null($a)) { return  true;} 
		return (
            empty($a) 
            || count($a) == 0 
            || sizeof($a) == 0 
		);
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
}
endif;    