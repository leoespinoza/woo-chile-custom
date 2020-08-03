<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Shipping')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_Shipping extends WOOCFCL_Fields
{
    
    private static $_instance = null;


    /**
     * Constructor.
     *
     * @param string  $option option name.
	 * @param string  $prefix prefix name.
     */
    public function __construct($option=null,$prefix = null)
    {
        $this->default_fields=array('shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city');
;
        $this->default_names=array(
            'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode');

        $this->_option = !isset($option)? 'shipping':$option;
        parent::__construct($this->_option ,$prefix);
        
    }  

    public static function instance($option=null,$prefix = null) {
        if (is_null(self::$_instance)) {
        self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }

}
endif;    