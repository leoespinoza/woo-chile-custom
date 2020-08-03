<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_AppOptions')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_AppOptions extends WOOCFCL_Options
{
    
    private static $_instance = null;
    /**
     * The name used by option.
     *
     * @var string
     */
    private $_option = 'app'; 
    /**
	 * plugin defaults values
	 *
	 * @var array
	 */
	public $defaults = array(
		'statesEnabled' => 'yes',
        'citiesEnabled' => 'yes',
        'onlyWoocommCountry' => 'yes',
        'countriesAllowed' => array(),
        'countriesExtend' => array('CL'),
    );
    
    /**
	 * plugin options
	 *
	 * @var bool
	 */
    public $statesEnabled= true;
    /**
	 * plugin options
	 *
	 * @var bool
	 */
    public $citiesEnabled= true;
    /**
	 * plugin options
	 *
	 * @var bool
	 */
    public $onlyWoocommCountry=true;
    /**
	 * countries allowed by the plugin
	 *
	 * @var array
	 */
    public $countriesAllowed= array();
    /**
	 * determine if the allowed countries have changed 
	 * between woocommerce and the plugin
     * 
	 * @var bool
	 */
    public $countriesChange= false;
    /**
	 * countries allowed by woocommerce
	 *
	 * @var array
	 */
    public $countriesExtend=array('CL');

    public $countriesToAdd= array();
    public $countriesToDelete= array();
    public $countriesToKeep= array();


    
    /**
     * Constructor.
     *
     * @param string  $option option name.
	 * @param string  $prefix prefix name.
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
        $this->setDefaultsProperty();   
        $this->setAllowedCountries(); 
    }

    private function setAllowedCountries(){
        $countriesAllowedWoocom=WOOCFCL()->countries;

        if(WOOCFCL_Utils::array_empty($this->countriesAllowed)){
            $this->countriesChange=true;
        }
        else {
            $this->countriesChange= WOOCFCL_Utils::array_equal($this->countriesAllowed,$countriesAllowedWoocom)?false:true;
        }
        if(!WOOCFCL_Utils::array_empty($countriesAllowedWoocom)){
            foreach ($countriesAllowedWoocom as $code => $country) {
                //check if exist file code
                $statepath=WOOCFCL_PATH_STATES . $code . '.php';
                if (file_exists($statepath) && !in_array ( $code, $this->countriesExtend )) {
                    $this->countriesChange=true;
                    array_push($this->countriesExtend,$code);
                }
            }
        }


        if ($this->countriesChange) {

            $this->countriesToAdd = array_diff_key($countriesAllowedWoocom, $this->countriesAllowed);
            $this->countriesToDelete = array_diff_key($this->countriesAllowed, $countriesAllowedWoocom);
            $this->countriesToKeep = array_diff_key($countriesAllowedWoocom,$this->countriesToAdd , $this->countriesToDelete);

            $this->defaults['countriesAllowed']=$countriesAllowedWoocom;
            $this->defaults['countriesExtend']=$this->countriesExtend;
            $this->setDefault();
        }
        return $countriesAllowedWoocom;
    }
}
endif;    