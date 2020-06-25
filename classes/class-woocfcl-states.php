<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_States')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_States extends WOOCFCL_Options
{
    
    private static $_instance = null;
    /**
     * The name used by option.
     *
     * @var string
     */
    private $_option = 'states'; 
    /**
	 * default states
	 *
	 * @var array
	 */
    public $defaults = array();
    

    /**
	 * default states from woocommerce and the plugin
	 *
	 * @var array
	 */
    public $states_defaultExtend = array();

    /**
	 * current states from plugin and woocommerce
	 *
	 * @var array
	 */
    public $states_Extend = array();


    /**
	 * current states from plugin and woocommerce for woocommerce
	 *
	 * @var array
	 */
    public $states_Woocom = array();
    

    private $_appOpt;
    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct($option=null,$prefix = null)
    {
        $option = !isset($option)? $this->_option:$option;
        parent::__construct($option,$prefix);

    }  

    public static function instance($option=null,$prefix = null) {
        if (is_null(self::$_instance)) {
        self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }

    public function init($appOpt){

        $this->_appOpt = $appOpt;          

        $this->get_defaultExtend_states();
        $this->get_AppExtend_states();
        $this->set_Woocom_states();


    }

    public function get_defaultExtend_states($states=array()){
        $this->states_defaultExtend=WOOCFCL_Utils::array_empty($states) ? $this->states_defaultExtend:$states;
        
        if (!WOOCFCL_Utils::array_empty($this->_appOpt->countriesAllowedWoocom) ) {
    
            foreach ($this->_appOpt->countriesAllowedWoocom as $code => $country) {
                //check if exist file code

                $statepath=WOOCFCL_PATH_STATES . $code . '.php';
                if (file_exists($statepath)) {
                    $statestemp = include($statepath);
                    $this->states_defaultExtend[$code]= WOOCFCL_Utils::array_empty($statestemp)? $this->get_woocom_States($code):$statestemp[$code];
                }
                else {
                    $this->states_defaultExtend[$code] =$this->get_woocom_States($code);
                }
            }
        }
        if (WOOCFCL_Utils::array_empty($this->defaults) ) {
            $this->defaults=$this->states_defaultExtend;
            $this->setDefault();
        }
        return $this->states_defaultExtend;
    }

    public function get_AppExtend_states($states=array()){
        
        $this->states_Extend=WOOCFCL_Utils::array_empty($states)?$this->defaults:array_merge($states,$this->defaults);
    
        if ($this->_countriesChange) {

            if ($this->_appOpt->onlyWoocommCountry &&  !WOOCFCL_Utils::array_empty($this->_appOpt->countriesToDelete)) {
                foreach ($this->_appOpt->countriesToDelete as $code => $country) {
                    //check if exist file code
                    unset($this->states_Extend[$code]);   
                }
            }

            if (!WOOCFCL_Utils::array_empty($this->_appOpt->countriesToAdd )) {
                foreach ($this->_appOpt->countriesToAdd as $code => $country) {
                    //check if exist file code
                    $this->states_Extend[$code]=$this->states_defaultExtend[$code];   
                }
            }

            if (!WOOCFCL_Utils::array_empty($this->states_Extend) ) {
                $this->defaults=$this->states_Extend;
                $this->setDefault();
            }
        } 
        return $this->states_Extend;
    }

    public function set_Woocom_states($states=array())
    {   
        $this->states_Woocom=WOOCFCL_Utils::array_empty($states) ? $this->states_Extend : array_merge($states,$this->states_Extend);
        
        if (!WOOCFCL_Utils::array_empty($this->_appOpt->countriesExtend)) {
            foreach ($this->_appOpt->countriesExtend as $code => $country) {
                // 
                if (!WOOCFCL_Utils::array_empty($this->states_Woocom[$country])) {
                    $res = array($country=>array());
                    foreach( $this->states_Woocom[$country] as $key => &$val) {
                        $res[$country][$key]=&$val['Name'];
                    }
                    $this->states_Woocom[$country]=$res[$country];
                }
            }
        }
        return $this->states_Woocom;
    } 

    /***********************************
    ---- WOOCOMMERCE functions - START ---
    *****************************woo_get_country_states******/
    public function get_woocom_States($country)
    {
        $statestemp = WC()->countries->get_states($country );
        return WOOCFCL_Utils::array_empty($statestemp)? array():$statestemp;
    }   

}
endif;    