<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Billing')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_Billing extends WOOCFCL_Fields
{
    
    private static $_instance = null;
    // /**
    //  * The name used by option.
    //  *
    //  * @var string
    //  */
    // private $_option = 'billing'; 


    /**
     * Constructor.
     *
     * @param string  $option option name.
	 * @param string  $prefix prefix name.
     */
    public function __construct($option=null,$prefix = null)
    {
        $this->default_fields=array('billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city');
;
        $this->default_names=array(
            'billing_first_name', 'billing_last_name', 'billing_company',
            'billing_address_1', 'billing_address_2', 
			'billing_city', 'billing_state', 'billing_country', 'billing_postcode',
            'billing_phone', 'billing_email');

        $this->_option = !isset($option)? 'billing':$option;
        parent::__construct( $this->_option ,$prefix);
        
    }  

    public static function instance($option=null,$prefix = null) {
        if (is_null(self::$_instance)) {
        self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }


}
endif;    