<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Options')):

/**
 * Manages options using the WordPress options API.
 */
class WOOCFCL_Options
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

        $this->_ismultisite =  is_multisite() && defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE;
        $this->getListDefault();

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
        return !empty($name) && strpos($name, $this->_prefix) === false? $this->_prefix .'_'. $name:$this->_table;
    }  
    
    /**
     * add the option for the given name. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function add($name = '', $value = null)
    {
        $name=$this->get_option_name($name);
        if ( empty( $name ) || is_null( $value ) )  return false; 
        return $this->_ismultisite? add_site_option($name,$value):add_option($name, $value);

    }   

    /**
     * Gets the option for the given name. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function get($name='',$value = null)
    {
        // echo ' <br/>$option: '.$name.'<br/> ';
        $name=$this->get_option_name($name);
        // echo ' <br/>$option: '.$name.'<br/> ';
        if ( empty( $name ))  return false; 

        $option =$this->_ismultisite? get_site_option($name):get_option($name);
        // echo ' <br/>$option: '.$name.'<br/> '.var_export($option ,TRUE).'<br/> '.var_export(empty( $option ) ,TRUE).'<br/> '.var_export($value  ,TRUE);

        if (empty( $option ) ) {

            if ( is_null( $value ))  return false; 
            $option = $this->add($name,$value)?$value:false;
        }
        else {

            if (is_array($option)) {
                $option = array_filter($option);
            }
            elseif (is_array($default) && !is_array($option)) {
                $option = (array) $option;
            }

        }
        return $option;
    }

    /**
     * Gets the option for the given name. Returns the default array value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return array
     */
    public function getList($name='', $value = array())
    {
        return $this->get($name,$value);
    }

    /**
     * Gets the option for the given name. Returns the default array value if the value does not exist.
     *
     *
     * @return array
     */
    public function getListDefault()
    {
        $result=$this->getList($this->_table,$this->defaults);
        $this->defaults = empty($result)?array(): (array)$result  ;
        return $this->defaults;
    }

    /**
     * Removes the option with the given name.
     *
     * @param string $name
     * 
     *
     * @return bool
     */
    public function remove($name='')
    {
        $name=$this->get_option_name($name='');
        if ( empty( $name ))  return false; 
        return $this->_ismultisite? delete_option($name):delete_site_option($name);
    }

    /**
     * Removes the default option with the given name existing option.
     *
     * 
     *
     * @return bool
     */
    public function removeDefault()
    {
        return $this->remove($this->_table);
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
    public function set($name='', $value = null)
    {
        $name=$this->get_option_name($name);
        if ( empty( $name ) || is_null( $value ) )  return false; 
        return $this->_ismultisite? update_site_option($name, $value):update_option($name, $value);
    }
    /**
     * Sets an option. Overwrites the existing option if the name is already in use.
     *
     * 
     *
     * @return bool
     */
    public function setDefault()
    {
        return $this->set($this->_table,$this->defaults);
    }

    public function setProperty($name='', $value = null){
        if ( empty( $name ) || is_null( $value ) )  return false; 
        $this->{$name} = $value;
        return $this;
    }

    public function setArrayProperty($values = array())
    {
        if (WOOCFCL_Utils::array_empty($values)) return false;
        foreach($values as $attribute => $value) {
            $this->{$attribute} = $value=='yes' || $value=='no'?WOOCFCL_Utils::bool_fromvalue($value):$value ;
           // echo '<br/>'.$attribute.' '.var_export($value ,TRUE);
        }
            
        return $this;
    }

    public function setDefaultsProperty()
    {
        return $this->setArrayProperty($this->defaults);
    }

    public function printToArray(){
        // echo '<br/><br/><br/>';
        // foreach ($this as $property_name => $property_values) {
        //     echo '<br/>'.$property_name.': '.var_export($property_values ,TRUE);
        // }
        // echo '<br/><br/>'.$this->_table .'<pre>';
        // print_r((array) $this);
        // echo '</pre>';
        WOOCFCL_Utils::printToArray($this->_table,$this);
    }
}


endif;    