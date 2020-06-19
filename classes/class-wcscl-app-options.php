<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WCSCL_AppOptions')):

/**
 * Manages site options using the WordPress options API.
 */
class WCSCL_AppOptions extends WCSCL_Options
{
    
    private static $_instance = null;
    /**
     * The name used by option.
     *
     * @var string
     */
    private $_option = 'app'; 
    	/**
	 * Query instance.
	 *
	 * @var WC_Query
	 */
	public $defaults = array(
		'statesEnabled' => 'yes',
		'citiesEnabled' => 'yes'
	);
    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct($option=null,$prefix = null)
    {
        $option = !isset($option)? $this->_option:$option;
        parent::__construct($option,$prefix);
        self::init();
    }  

    public static function instance($option=null,$prefix = null) {
        if (is_null(self::$_instance)) {
          self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }

    private function  init(){

        $this->getListDefault();
    }
}
endif;    