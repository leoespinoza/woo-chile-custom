<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Form')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_Form 
{
    
    private static $_instance = null;
        /**
	 * countries allowed by the plugin
	 *
	 * @var array
	 */
    public $fields_types= array(); 

    /**
	 * countries allowed by the plugin
	 *
	 * @var array
	 */
    public $display_style = array(); 
    /**
	 * countries allowed by the plugin
	 *
	 * @var array
	 */
    public $validation_types= array(
        'email' => 'Email',
        'phone' => 'Phone',
        'postcode' => 'Postcode',
        'state' => 'State',
        'number' => 'Number',
    ); 


    /**
	 * countries allowed by the plugin
	 *
	 * @var array
	 */ 
    public $field_form_props=array();

    /**
     * Constructor.
     *
     * @param string  $option option name.
	 * @param string  $prefix prefix name.
     */
    public function __construct()
    {
        $this->display_style = array(
        'full' => WOOCFCL_Utils::translate('Full width'),
        'half_left' => WOOCFCL_Utils::translate('Half width left'),
        'half_right' => WOOCFCL_Utils::translate('Half width right'),
        );
        
        $this->fields_types=[
            'text'   => WOOCFCL_Utils::translate('Text'),
            'password' => WOOCFCL_Utils::translate('Password'),
            'email' => WOOCFCL_Utils::translate('Email'),
            'tel' => WOOCFCL_Utils::translate('Phone'),
            'select' => WOOCFCL_Utils::translate('Select'),
            'textarea' => WOOCFCL_Utils::translate('Textarea'),
            'radio' => WOOCFCL_Utils::translate('Radio'),
        ];
        $this->field_form_props=[
            'type' 		  => ['type'=>'select', 'name'=>'type', 'label'=>WOOCFCL_Utils::translate('Type'), 'required'=>1, 'options'=>$this->fields_types, 'onchange'=>'woocfclFieldTypeChangeListner(this)'],
            'name' 		  => ['type'=>'text', 'name'=>'name', 'label'=>WOOCFCL_Utils::translate('Name'), 'required'=>1],
            'label'       => ['type'=>'text', 'name'=>'label', 'label'=>WOOCFCL_Utils::translate('Label')],
            'default'     => ['type'=>'text', 'name'=>'default', 'label'=>WOOCFCL_Utils::translate('Default Value')],
            'placeholder' => ['type'=>'text', 'name'=>'placeholder', 'label'=>WOOCFCL_Utils::translate('Placeholder')],
            'class'       => ['type'=>'text', 'name'=>'class', 'label'=>WOOCFCL_Utils::translate('Class'), 'placeholder'=>WOOCFCL_Utils::translate('Seperate classes with comma')],
            'validate'    => ['type'=>'select', 'name'=>'validate', 'label'=>WOOCFCL_Utils::translate('Validation'), 'options'=>$this->validation_types, 'multiple'=>1],
            'disp_style' => ['type'=>'select', 'name'=>'disp_style', 'label'=>WOOCFCL_Utils::translate('Field Display'), 'options'=>$this->display_style],
                
            'required' => ['type'=>'checkbox', 'name'=>'required', 'label'=>WOOCFCL_Utils::translate('Required'), 'value'=>'1', 'checked'=>1],

            'enabled'  => ['type'=>'checkbox', 'name'=>'enabled', 'label'=>WOOCFCL_Utils::translate('Enabled'), 'value'=>'1', 'checked'=>1],

            'show_in_email' => ['type'=>'checkbox', 'name'=>'show_in_email', 'label'=>WOOCFCL_Utils::translate('Display in Emails'), 'value'=>'1', 'checked'=>1],
            'show_in_order' => ['type'=>'checkbox', 'name'=>'show_in_order', 'label'=>WOOCFCL_Utils::translate('Display in Order Detail Pages'), 'value'=>'1', 'checked'=>1]         
        ];
        
    }  

    public static function instance() {
        if (is_null(self::$_instance)) {
        self::$_instance = new self();
        }
        return self::$_instance;
    }
}
endif;    