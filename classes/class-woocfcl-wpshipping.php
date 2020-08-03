<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_WPShipping')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_WPShipping
    {
    
        use WOOCFCL_WPOptions, WOOCFCL_WoocomFields {
            WOOCFCL_WPOptions::__construct as private __vpoConstruct;
        }
        /**
         * Constructor.
         *
         */
        public function __construct()
        {
            $this->set_options_to_array('shipping');
            $this->set_reserved_fieldname(
            array('shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city'),
            array('shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
                'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode'));
            $this->__vpoConstruct();
            $this->init();
            
        }  

}
endif;    