<?php

defined( 'ABSPATH' ) || exit;

if(!trait_exists('WOOCFCL_WPOptions')):

/**
 * Manages options using the WordPress options API.
 */
trait WOOCFCL_WPOptions
{
    
        /**
         * @var    object
         * @access  private
         * @since    1.0.0
         */
        private static $_instance = null;
        /**
         * The prefix used by all option names.
         *
         * @var string
         */
        private $_prefix = ''; 

        /**
         * The name used by all option.
         *
         * @var string
         */
        private $_option = ''; 

        /**
         * The name used by option in table WP_Option.
         *
         * @var string
         */
        private $_optionName = '';

        /**
        * define is site is multisite.
        *
        * @var bool
        */
        private $_ismultisite=false;

        /**
        * default value for option
        *
        * @var Object 
        */
        public $option_default;

        /**
        * value for option
        *
        * @var Object 
        */
        public $option_value;


        /**
         * current option from plugin for woocommerce
         *
         * @var array
         */
        public $option_woocom = array();
        /**
         * current option from plugin and woocommerce for datatable javascript
         *
         * @var array
         */
        public $option_datatable = array();
    /**
     * Constructor.
     *
     * @param string $option
     * @param string $prefix
     */
    public function __construct($option=null,$prefix = null)
    {
        $this->_prefix = empty($prefix)? WOOCFCL_PREFIX:$prefix;
        $this->_option = empty($option)? $this->_option :$option;
        $this->_optionName = $this->set_OptionName($this->_option);
        $this->_ismultisite =  is_multisite() && defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE;
    }
    /**
     *
     *
     * Ensures only one instance is loaded or can be loaded.
     *
     * @static
     * @return Main WOOCFCL_WPOptions instance
     */
    public static function instance($option=null,$prefix = null)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }
    public function __clone() {
        trigger_error('Cloning '.__CLASS__.' is not allowed.',E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Unserializing '.__CLASS__.' is not allowed.',E_USER_ERROR);
    }

    /**
     * set the default option values to arrays.
     *
     *
     * @param string $name
     */
    public function set_options_to_array($name='',$option_default=array())
    {
        $this->_option = $name;
        $this->option_default = $option_default;
        $this->option_value = array();
        $this->option_woocom = array();
        $this->option_datatable = array();
    }


    /**
     * Returns the default prefix.
     *
     *
     * @return prefix
     */
    public function get_prefix()
    {
        return $this->_prefix;
    }
    /**
     * Returns the default table option.
     *
     *
     * @return option name
     */
    public function get_optionName()
    {
        return $this->_optionName;
    }

    /**
     * Get the option name used to store the option in the WordPress database.
     *
     * @param string $name
     *
     * @return string
     */
    private function set_OptionName($name = '')
    {
        return !empty($name) && strpos($name, $this->_prefix) === false? $this->_prefix .'_'. $name:$name;
    }     
    /**
     * Checks if the option with the given name exists or not.
     *
     *
     * @return bool
     */
    public function has()
    {
        return null !== $this->get();
    }

    /**
     * add the option for the current option. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function add( $value = null)
    {
        if (empty( $value ) )  return false; 
        return $this->_ismultisite? add_site_option($this->_optionName,$value):add_option($this->_optionName, $value);

    }   

    /**
     * Gets the option for the given name. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function get($value = null)
    {
        $option =$this->_ismultisite? get_site_option($this->_optionName):get_option($this->_optionName);

        if (empty( $option ) ) {
            if ( empty( $value ))  return false; 
            $option = $this->add($value)?$value:false;
        }
        else {
            if (is_array($option)) $option = array_filter($option);
            elseif (is_array($value) && !is_array($option)) $option = (array) $option;
        }
        return $option;
    }

    /**
     * Gets the option for the given name. Returns the default array value if the value does not exist.
     *
     * @param bool  $asArray
     * 
     * 
     * @return object
     */
    public function get_default($asArray=true)
    {
        $result=$this->get($this->option_default);
        if ($asArray) $this->option_value = empty($result)?array(): (array)$result;
        else  $this->option_value = empty($result)?'': $result;
        return $this->option_value;
    }

    /**
     * Removes the option with the given name.
     *
     * @return bool
     */
    public function remove()
    {
        return $this->_ismultisite? delete_option($this->_optionName):delete_site_option($this->_optionName);
    }

    /**
     * Sets an option. Overwrites the existing option if the name is already in use.
     *
     * @param string $name
     * @param mixed  $value
     * 
     *
     * @return bool
     */
    public function set($value = null)
    {
        if (empty( $value ) )  return false; 
        return $this->_ismultisite? update_site_option($this->_optionName, $value):update_option($this->_optionName, $value);
    }
    /**
     * Sets an option. Overwrites the existing option if the name is already in use.
     *
     * 
     *
     * @return bool
     */
    public function set_default()
    {
        return $this->set($this->option_default);
    }

    public function set_property($name='', $value = null){
        if ( empty( $name ) || empty( $value ) )  return false; 
        $this->{$name} = $value;
        return $this;
    }

    public function set_property_from_array($values = array())
    {
        if (WOOCFCL_Utils::array_empty($values)) return false;
        foreach($values as $attribute => $value) {
            $this->{$attribute} = $value=='yes' || $value=='no'?WOOCFCL_Utils::bool_fromvalue($value):$value ;
           // echo '<br/>'.$attribute.' '.var_export($value ,TRUE);
        }
            
        return $this;
    }

    public function set_property_from_optionvalue()
    {
        return $this->set_property_from_array($this->option_value);
    }

    public function print_to_array(){

        WOOCFCL_Utils::printToArray($this->_optionName,$this);
    }
}


endif;    