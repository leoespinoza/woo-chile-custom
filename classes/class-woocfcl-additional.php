<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Additional')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_Additional extends WOOCFCL_Fields
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
        
        $this->defaults=array(
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
        );
        $this->default_fields=array('order_comments');
        $this->default_names=array('customer_note', 'order_comments');
        $this->_option = !isset($option)? 'additional':$option;
        parent::__construct($this->_option ,$prefix);
        
    }   

    public static function instance($option=null,$prefix = null) {
        if (is_null(self::$_instance)) {
        self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }

	/**
	 * Hide Additional Fields title if no fields available.
	 */
	public function enable_order_notes_field() {

		if(!WOOCFCL_Utils::array_empty( $this->fieldsPlugin)){
			$enabled = 0;
			foreach( $this->fieldsPlugin as $field){
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