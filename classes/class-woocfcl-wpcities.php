<?php

defined('ABSPATH') || exit;

if (!class_exists('WOOCFCL_WPCities')):

/**
 * Manages site options using the WordPress options API.
 */
    class WOOCFCL_WPCities
    {

        use WOOCFCL_WPOptions {
            WOOCFCL_WPOptions::__construct as private __vpoConstruct;
        }

        /**
         * Constructor.
         *
         */
        public function __construct()
        {
            $this->_option = 'cities';
            $this->option_default = array();
            $this->__vpoConstruct();
            $this->init();
        }

        public function init()
        {

            $this->get_default();
            $this->set_option_values();

        }

        private function set_option_values()
        {
            $this->option_woocom = array();
            $this->option_value = WOOCFCL_Utils::array_empty($this->option_value) ? array() : $this->option_value;
            if (WOOCFCL()->app->countriesChange) {

                if (!WOOCFCL_Utils::array_empty(WOOCFCL()->app->countriesToAdd)) {
                    foreach (WOOCFCL()->app->countriesToAdd as $code => $country) {
                        //check if exist file code
                        $citiespath = WOOCFCL_PATH_CITIES . $code . '.php';
                        if (file_exists($citiespath)) {
                            $citiestemp = include $citiespath;
                            $this->option_value[$code] = WOOCFCL_Utils::array_empty($citiestemp) ? array() :$this->set_plugin_fields_toapp_adapter($citiestemp[$code],$code) ;
                        } else {
                            $this->option_value[$code] = array();
                        }
                        $this->option_woocom[$code] = WOOCFCL_Utils::array_empty($this->option_value[$code]) ? array() : $this->set_app_fields_towoocom_adapter($this->option_value[$code], $code);
                    }
                }
                if (WOOCFCL()->app->onlyWoocommCountry && !WOOCFCL_Utils::array_empty(WOOCFCL()->app->countriesToDelete)) {
                    foreach (WOOCFCL()->app->countriesToDelete as $code => $country) {
                        //check if exist file code
                        unset($this->option_value[$code]);
                    }
                }

                $this->option_default = $this->option_value;
                $this->set_default();
            }

            if (WOOCFCL()->app->onlyWoocommCountry && !WOOCFCL_Utils::array_empty(WOOCFCL()->app->countriesToKeep)) {
                foreach (WOOCFCL()->app->countriesToKeep as $code => $country) {
                    //check if exist file code
                    $this->option_woocom[$code] = $this->set_app_fields_towoocom_adapter($this->option_value, $code);
                }
            }

            $this->option_datatable = $this->set_app_cities_todatatable_adapter($this->option_value);

        }

        private function set_plugin_fields_toapp_adapter($country_cities, $keycountry)
        {
            $coutryInfo = WOOCFCL()->app->countriesAllowed[$keycountry];
            if (!WOOCFCL_Utils::array_empty($country_cities)) {
                $i = $coutryInfo['IsoNumeric'] * 100;
                array_walk(
                    $country_cities,
                    function (&$states, $keystate) use (&$i, $keycountry) {
                        array_walk(
                            $states,
                            function (&$city, $keycity) use (&$i, $keycountry, $keystate) {
                                $extcity = array('enabled' => 1, 'RowOrder' => $i);
                                $city = array_merge($extcity, $city);
                                $i++;
                            }
                        );
                    }
                );

            }
            return $country_cities;
        }

        private function set_app_fields_towoocom_adapter($cities, $keycountry)
        {
            $country_cities= WOOCFCL_Utils::array_empty($cities)?array():$cities;
            $country_cities=array_filter(
                    $country_cities, function($state) {
                        $state= array_filter(
                            $state, function($city) {
                                return ( $city['enabled']==1); }
                        );
                        return $state; }
                );
            array_walk(
                $country_cities,
                function (&$states, $keystate) {
                    array_walk(
                        $states,
                        function (&$city, $keycity) {
                            $city = $city['Name'];
                        }
                    );
                }
            );

            return $country_cities;
        }

        private function set_app_cities_todatatable_adapter($country_cities)
        {
            $arrayDatatable = array();
            if (!WOOCFCL_Utils::array_empty($country_cities)) {

                array_walk(
                    $country_cities,
                    function (&$countries, $keycountry) use (&$arrayDatatable) {
                        array_walk(
                            $countries,
                            function (&$states, $keystate) use (&$arrayDatatable, $keycountry) {

                                array_walk(
                                    $states,
                                    function (&$city, $keycity) use (&$arrayDatatable, $keycountry, $keystate) {

                                        $extcity = array('ID' => $keycity, 'country' => $keycountry, 'state' => $keystate);
                                        $city = array_merge($extcity, $city);

                                        array_push($arrayDatatable, $city);

                                    }
                                );

                            }
                        );

                    }
                );

            }
            return $arrayDatatable;
        }

    }
endif;
