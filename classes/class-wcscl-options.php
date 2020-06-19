<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WCSCL_Options')):

/**
 * Manages options using the WordPress options API.
 */
class WCSCL_Options
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
    private $_prefix;  

    /**
     * The name used by all option.
     *
     * @var string
     */
    private $_option = ''; 

    /**
     * The name used by option table.
     *
     * @var string
     */
    private $_table = '';

    /**
    * define is site is multisite.
    *
    * @var bool
    */
    private $_ismultisite=false;

    public $defaults = array();
    public $items = array();

    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct($option=null,$prefix = null)
    {
        $this->_prefix = !isset($prefix)? WOOCFCL_PREFIX:$prefix;
        $this->_option = !isset($option)? $this->_option:$option;
        $this->_table = $this->get_option_name($this->_option);
        $this->_ismultisite = is_multisite()===1?true:false;


    }

    /**
     *
     *
     * Ensures only one instance of WCPA is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main WCPA instance
     */
    public static function instance($option=null,$prefix = null)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }
    
    /**
     * Checks if the option with the given name exists or not.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name = '')
    {
        return null !== $this->get($name);
    }


     /**
     * Get the option name used to store the option in the WordPress database.
     *
     * @param string $name
     *
     * @return string
     */
    public function get_option_name($name = '')
    {
        $name= !empty($name) && strpos($name, $this->_prefix) === false? $this->_prefix .'_'. $name:$this->_table;
        return $name;
    }  
    
      /**
     * add the option for the given name. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return bool
     */
    public function add($name = '', $default = null)
    {
        $name=$this->get_option_name($name);
        // echo 'add '.$name;
        // $v = var_export($default, true);
        // echo $v;
        $option =$this->_ismultisite? add_site_option($name, $default):add_option($name, $default);
        
        // $v = var_export($option, true);
        // echo $v;
        return $option;
    }   

    /**
     * Gets the option for the given name. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        $name=$this->get_option_name($name);
        $ismulti=$this->_ismultisite;

        $option =$this->_ismultisite===true? get_site_option($name, $default):get_option($name, $default);
    
       
        if (is_array($default) && !is_array($option)) {
            $option = (array) $option;
        }
        elseif (is_array($option)) {
            $option = array_filter($option);
        }
    
        return $option;
    }

     /**
     * Gets the option for the given name. Returns the default array value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return array
     */
    public function getList($name = null, $default = array())
    {
        return $this->get($name,$default);
    }

         /**
     * Gets the option for the given name. Returns the default array value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return array
     */
    public function getListDefault($name = null)
    {
        $options = $this->getList($name);
        if(empty($options) || sizeof($options) == 0){
            $options=$this->defaults;
            $this->add($name,$options);
        }
        $this->items=$options;
        return $options;
    }
    /**
     * Returns the default prefix.
     *
     *
     * @return prefix
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }
    /**
     * Returns the default table option.
     *
     *
     * @return option name
     */
    public function getTableOption()
    {
        return $this->_table;
    }
    /**
     * Removes the option with the given name.
     *
     * @param string $name
     * 
     *
     * @return bool
     */
    public function remove($name)
    {
        $name=$this->get_option_name($name);
        $option =$this->_ismultisite? delete_option($name):delete_site_option($name, $default);
        return $option;
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
    public function set($name='', $value)
    {
        $name=$this->get_option_name($name);
        $option =$this->_ismultisite? update_option($name, $value):update_site_option($name, $value);
        return $option;
    }
    /**
     * Sets an option. Overwrites the existing option if the name is already in use.
     *
     * @param array  $default
     * 
     *
     * @return bool
     */
    public function setDefault($default = array())
    {
        return $this->set($this->_table,$this->defaults);
    }

}


endif;    