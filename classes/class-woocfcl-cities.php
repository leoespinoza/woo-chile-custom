<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Cities')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_Cities extends WOOCFCL_Options
{
    
    private static $_instance = null;
    /**
     * The name used by option.
     *
     * @var string
     */
    private $_option = 'cities'; 
    /**
	 * default states
	 *
	 * @var array
	 */
    public $defaults = array();
    

    /**
	 * default cities from woocommerce and the plugin
	 *
	 * @var array
	 */
    public $cities_defaultExtend = array();

    /**
	 * current cities from plugin and woocommerce
	 *
	 * @var array
	 */
    public $cities_Extend = array();

    /**
	 * current cities from plugin and woocommerce for woocommerce
	 *
	 * @var array
	 */
    public $cities_Woocom = array();

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
        
        $this->get_defaultExtend_cities();
        $this->get_AppExtend_cities();
        $this->set_Woocom_cities();


    }

    public function get_defaultExtend_cities($cities=array()){
        $this->cities_defaultExtend==WOOCFCL_Utils::array_empty($cities) ? $this->cities_defaultExtend : $cities;
        
        if (!WOOCFCL_Utils::array_empty($this->_appOpt->countriesAllowedWoocom) ) {
    
            foreach ($this->_appOpt->countriesAllowedWoocom as $code => $country) {
                //check if exist file code

                $citiespath=WOOCFCL_PATH_CITIES . $code . '.php';
                if (file_exists($citiespath)) {
                    $citiestemp = include($citiespath);
                    $this->cities_defaultExtend[$code]= WOOCFCL_Utils::array_empty($citiestemp)? array():$citiestemp[$code];
                }
                else {
                    $this->cities_defaultExtend[$code]=array();
                }
            }
        }
        if (WOOCFCL_Utils::array_empty($this->defaults) ) {
            $this->defaults=$this->cities_defaultExtend;
            $this->setDefault();
        }
        return $this->cities_defaultExtend;
    }

    public function get_AppExtend_cities($cities=array()){

        $this->cities_Extend=WOOCFCL_Utils::array_empty($cities)? $this->defaults : array_merge($cities,$this->defaults);
        
        if ($this->_appOpt->countriesChange) {

            if ($this->_appOpt->onlyWoocommCountry &&  !WOOCFCL_Utils::array_empty($this->_appOpt->countriesToDelete)) {
                foreach ($this->_appOpt->countriesToDelete as $code => $country) {
                    //check if exist file code
                    unset($this->cities_Extend[$code]);   
                }
            }

            if (!WOOCFCL_Utils::array_empty($this->_appOpt->countriesToAdd )) {
                foreach ($this->_appOpt->countriesToAdd as $code => $country) {
                    //check if exist file code
                    $this->cities_Extend[$code]=$this->cities_defaultExtend[$code];   
                }
            }

            if (!WOOCFCL_Utils::array_empty($this->cities_Extend) ) {
                $this->defaults=$this->cities_Extend;
                $this->setDefault();
            }
        } 
        return $this->cities_Extend;
    }

    public function set_Woocom_cities($cities=array())
    {   
        $this->cities_Woocom=WOOCFCL_Utils::array_empty($cities)? $this->cities_Extend : array_merge($cities,$this->cities_Extend);
    
        if (!WOOCFCL_Utils::array_empty($this->_appOpt->countriesExtend)) {
            
            foreach ($this->_appOpt->countriesExtend as $code => $country) {
                // 
                if (!WOOCFCL_Utils::array_empty($this->cities_Woocom[$country])) {
                    $res = array($country=>array());
                    foreach( $this->cities_Woocom[$country] as $keystates => &$valcities) {
                        $res[$country][$keystates]=array();
                        
                        foreach($valcities as $keycity => &$valcity) {
                            $res[$country][$keystates][]=$valcity['Name'] ;
                        }
                    }
                    $this->cities_Woocom[$country]=$res[$country];
                }
            }
        }
        return $this->cities_Woocom;
    } 

}
endif;    