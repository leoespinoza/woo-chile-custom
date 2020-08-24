<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_WPBilling')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_WPBilling 
    {
    
        use WOOCFCL_WPOptions, WOOCFCL_WoocomFields {
            WOOCFCL_WPOptions::__construct as private __vpoConstruct;
            WOOCFCL_WPOptions::set_field_property_to_form insteadof WOOCFCL_WoocomFields;
        }


        /**
         * Constructor.
         *
         */
        public function __construct()
        {
            $this->set_options_to_array('billing');
            $this->set_reserved_fieldname(
                array('billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city'),
                array('billing_first_name', 'billing_last_name', 'billing_company',
                'billing_address_1', 'billing_address_2', 
                'billing_city', 'billing_state', 'billing_country', 'billing_postcode',
                'billing_phone', 'billing_email'));
            $this->__vpoConstruct();
            $this->init();
            
        }  
    }
endif;    