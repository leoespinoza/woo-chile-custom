<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_WPAdditional')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_WPAdditional
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
            $this->set_options_to_array('additional',array(
                'order_comments' => array(
                    'type'        => 'textarea',
                    'class'       => array('notes'),
                    'label'       => __('Order Notes', 'woocommerce'),
                    'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'woocommerce'),
                    'custom'      => 0,
                    'enabled'     => 1,
                    'show_in_email' => 1,
                    'show_in_order' => 1
                )
            ) );
            $this->set_reserved_fieldname(
            array('order_comments'),
            array('customer_note', 'order_comments'));
            $this->__vpoConstruct();
            $this->init();
        }   



	/**
	 * Hide Additional Fields title if no fields available.
	 */
	public function enable_order_notes_field() {

		if(!WOOCFCL_Utils::array_empty( $this->option_value)){
			$enabled = 0;
			foreach( $this->option_value as $field){
				if($field['enabled']){
					$enabled++;
				}
			}
			return $enabled > 0 ? true : false;
		}
		return true;
	}	

}
endif;    