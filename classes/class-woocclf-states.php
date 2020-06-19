<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCCLF_States')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCCLF_States extends WCSCL_Options
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
    public $defaults = array(
        'countriesAllowed' => array(),
        'countriesExtend' => array('CL'),
        'statesExtended' => array(),
        'states' => array(),
	);
    /**
	 * allowed countries
	 *
	 * @var array
	 */
    public $countries_allowed = array();
    /**
	 * default states from woocommerce and the plugin
	 *
	 * @var array
	 */
    public $states_default = array();

    /**
	 * allowed countries
	 *
	 * @var array
	 */
    public $states_woocom = array();

    /**
	 * allowed countries
	 *
	 * @var array
	 */
    public $states_plugin = array();
    /**
	 * allowed countries
	 *
	 * @var array
	 */
    public $states_full = array();
    /**
	 * Checks if the allowed countries change
	 *
	 * @var bool
	 */
	public $countries_change = false;

    /**
	 * Checks if the allowed countries change
	 *
	 * @var bool
	 */
	public $states_change = false;

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

    private function init(){
        $this->getListDefault();
        $this->get_Allowed_Countries();
        $this->get_states();
    }

    private function get_Allowed_Countries(){

        $countries_allowed=$this->woo_get_allowed_countries();
        if(empty($this->defaults['countriesAllowed']) || sizeof($this->defaults['countriesAllowed']) == 0){
            $this->countries_change=true;
        }
        else {
            $this->countries_change= WOOCFCL_Utils::array_equal($countries_allowed,$this->defaults['countriesAllowed'])===true?false:true;
        }

        if ($this->countries_change===true) {
            $this->defaults['countriesAllowed']=$countries_allowed;
            $this->setDefault();
        }
        $this->countries_allowed=$countries_allowed;
        return $this->countries_allowed;
    }

    private function get_default_states(){

        $this->states_default=$this->load_country_states_woo();
        $this->states_default=$this->load_country_states($this->states_default);
        // if(WOOCFCL_Utils::array_empty($this->defaults['states'])){
        //     $this->states_change=true;
        //     $this->defaults['states']=$this->load_country_states();
        //     $this->setDefault();
        //  }
        //  else {
        //      $this->states_change= WOOCFCL_Utils::array_equal($countries_allowed,$this->defaults['countriesAllowed'])===true?false:true;
        //  }
         //  if ($this->countries_change===true) {
        //     $this->defaults['countriesAllowed']=$countries_allowed;
        //     $this->setDefault();
        //  }
        //  $this->countries_allowed=$countries_allowed;
        //  return $this->countries_allowed;
        // return $states;
    }

    public function plug_get_country_states($statesWoo=array())
    {
        $this->woo_get_allowed_countries();
        $statesWooCfCl=array();
        if ($this->countries_allowed) {

            foreach ($this->countries_allowed as $code => $country) {
                $statepath=WOOCFCL_PATH_STATES . $code . '.php';
                if (file_exists($statepath)) {
                    $statestemp = include($statepath);
                    $statesWooCfCl=array_merge($statesWoo, $statestemp);
                    if (in_array($code, $this->defaults['countriesExtend'])) {
                        $res = array();
                        $res[$code]=array();
                        foreach($statestemp[$code] as $key => &$val) {
                            $res[$code][$key]=&$val['Name'];
                        }
                        $statesWoo = array_merge($statesWoo, $res);
                    }
                    else {
                        $statesWoo = array_merge($statesWoo, statestemp);
                    }
                    
                }
            }
        }
        $this->states_full=$statesWoo;
        $this->states_plugin=$statesWooCfCl;
        return $statesWoo;
    } 
    /***********************************
    ---- WOOCOMMERCE functions - START ---
    *****************************woo_get_country_states******/
    public function woo_get_country_states($statesWoo=array())
    {
        $this->woo_get_allowed_countries();
        if ($this->countries_allowed) {
                foreach ($this->countries_allowed as $code => $country) {
                    $statestemp = WC()->countries->get_states($code );
                    $statesWoo[$code] = WOOCFCL_Utils::array_empty($statestemp)==true? array():$statestemp;
                }
            }
        return $statesWoo;
    }   

    public function woo_get_allowed_countries(){
        $this->countries_allowed=WOOCFCL_Utils::array_empty($this->countries_allowed)==true? array_merge(WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries()):$this->countries_allowed;
        return $this->countries_allowed;
    }
}
endif;    