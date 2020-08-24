<?php

defined('ABSPATH') || exit;

if (!class_exists('WOOCFCL_WPAppOptions')):

/**
 * Manages site options using the WordPress options API.
 */
    class WOOCFCL_WPAppOptions
    {

        use WOOCFCL_WPOptions {
            WOOCFCL_WPOptions::__construct as private __vpoConstruct;
        }

        /**
         * plugin options
         *
         * @var bool
         */
        public $statesEnabled = true;
        /**
         * plugin options
         *
         * @var bool
         */
        public $citiesEnabled = true;
        /**
         * plugin options
         *
         * @var bool
         */
        public $onlyWoocommCountry = true;
        /**
         * countries allowed by the plugin
         *
         * @var array
         */
        public $countriesAllowed = array();
        /**
         * countries allowed by woocommerce
         *
         * @var array
         */
        public $countriesExtend = array('CL');
        /**
         * determine if the allowed countries have changed
         * between woocommerce and the plugin
         *
         * @var bool
         */
        public $countriesChange = false;


        public $countriesToAdd = array();
        public $countriesToDelete = array();
        public $countriesToKeep = array();

        /**
         * Constructor.
         *
         */
        public function __construct()
        {
            $this->_option = 'app';
            $this->optionDefault = array(
                'statesEnabled' => 'yes',
                'citiesEnabled' => 'yes',
                'onlyWoocommCountry' => 'yes',
                'countriesAllowed' => array(),
                'countriesExtend' => array('CL'),
            );
            $this->__vpoConstruct();
            self::init();
        }

        private function init()
        {
            $this->get_default();
            $this->set_property_from_optionvalue();
            $this->set_option_values();
        }

        private function set_option_values()
        {
            $countriesAllowedWoocom = WOOCFCL()->countries;
            $this->countriesAllowed=WOOCFCL_Utils::array_empty($this->countriesAllowed)?array():$this->countriesAllowed;
            $countriesAllowedkey=empty($this->countriesAllowed)?array():array_keys($this->countriesAllowed);
            $this->countriesChange = WOOCFCL_Utils::array_equal($countriesAllowedkey, $countriesAllowedWoocom) ? false : true;

            if ($this->countriesChange) {
                $this->countriesToAdd = array_diff_key($countriesAllowedWoocom, $countriesAllowedkey);
                $this->countriesToDelete = array_diff_key($countriesAllowedkey, $countriesAllowedWoocom);
                $this->countriesToKeep = array_diff_key($countriesAllowedWoocom, $this->countriesToAdd, $this->countriesToDelete);

                if (!WOOCFCL_Utils::array_empty($this->countriesToAdd )) {
                    $countriesinfo=  include(WOOCFCL_PATH_COUNTRIES . 'countries.php');
                    foreach ($this->countriesToAdd as $code => $country) {
                        //check if exist file code
                        $this->countriesAllowed[$code]=$countriesinfo[$code];
                        $statepath = WOOCFCL_PATH_STATES . $code . '.php';
                        if (file_exists($statepath) && !in_array($code, $this->countriesExtend)) {
                            array_push($this->countriesExtend, $code);
                        }
                    }
                }
                //$this->countriesAllowed=$countriesAllowedWoocom;
                $this->option_default['countriesAllowed'] = $this->countriesAllowed;
                $this->option_default['countriesExtend'] = $this->countriesExtend;
                if ($this->set_default()) {
                    $this->option_value=$this->option_default;
                }
            }
            else {
                $this->countriesToKeep=$countriesAllowedkey;
            }
            $this->option_datatable=array_values($this->countriesAllowed);
        }
    }
endif;
