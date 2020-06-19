<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WCSCL_AbstractOptions')):

abstract class WCSCL_AbstractOptions
{
   
       /**
     * The prefix used by all option names.
     *
     * @var string
     */
    private $prefix;
    
    /**
     * Checks if the option with the given name exists or not.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
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
    public function get_option_name($name)
    {
        return $this->prefix . $name;
    }

    /**
     * Gets the option for the given name. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    abstract public function get($name, $default = null);
 
    /**
     * Removes the option with the given name.
     *
     * @param string $name
     */
    abstract public function remove($name);
 
    /**
     * Sets an option. Overwrites the existing option if the name is already in use.
     *
     * @param string $name
     * @param mixed  $value
     */
    abstract public function set($name, $value);
}

endif;