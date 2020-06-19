<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WCSCL_SiteOptions')):

/**
 * Manages site options using the WordPress options API.
 */
class WCSCL_SiteOptions extends WCSCL_AbstractOptions
{
    
    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }
    
    /**
     * Gets the site option for the given name. Returns the default value if the value does not exist.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $option = get_site_option($this->get_option_name($name), $default);

        if (is_array($default) && !is_array($option)) {
            $option = (array) $option;
        }

        return $option;
    }

    /**
     * Removes the site option with the given name.
     *
     * @param string $name
     */
    public function remove($name)
    {
        delete_site_option($this->get_option_name($name));
    }

    /**
     * Sets a site option. Overwrites the existing site option if the name is already in use.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value)
    {
        update_site_option($this->get_option_name($name), $value);
    }
}
endif;    