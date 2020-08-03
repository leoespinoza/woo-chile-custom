<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_WPStates')):

/**
 * Manages site options using the WordPress options API.
 */
    class WOOCFCL_WPStates 
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
            $this->_option = 'states';
            $this->option_default = array();
            $this->__vpoConstruct();
            $this->init();
        }  

        public function init(){
            $this->get_default();
            $this->set_states_status();

        }

        public function set_states_status(){
            $this->option_woocom=array();
            $this->option_value=WOOCFCL_Utils::array_empty($this->option_value)?array():$this->option_value;
            if (WOOCFCL()->app->countriesChange) {

                if (!WOOCFCL_Utils::array_empty(WOOCFCL()->app->countriesToAdd)) {
                    $stateswoo=WOOCFCL()->woocom_get_States();
                    foreach (WOOCFCL()->app->countriesToAdd as $code => $country) {
                        //check if exist file code
                        $statepath=WOOCFCL_PATH_STATES . $code . '.php';
                        if (file_exists($statepath)) {
                            $statestemp = include($statepath);
                            
                            $this->option_value[$code]= WOOCFCL_Utils::array_empty($statestemp)? $this->set_woocom_states_toapp_adapter($stateswoo,$code):$statestemp[$code];

                            $this->option_woocom[$code]=WOOCFCL_Utils::array_empty($statestemp)? WOOCFCL_Utils::array_get_value($stateswoo,$code):$this->set_app_states_towoocom_adapter($statestemp,$code);
                        }
                        else {
                            $this->option_value[$code]=$this->set_woocom_states_toapp_adapter($stateswoo,$code);
                            $this->option_woocom[$code]= WOOCFCL_Utils::array_get_value($stateswoo,$code);
                        }
                    }
                }  
                if (WOOCFCL()->app->onlyWoocommCountry &&  !WOOCFCL_Utils::array_empty(WOOCFCL()->app->countriesToDelete)) {
                    foreach (WOOCFCL()->app->countriesToDelete as $code => $country) {
                        //check if exist file code
                        unset($this->option_value[$code]);   
                    }
                }  

                $this->option_default=$this->option_value;
                $this->set_default();
            }

            if (WOOCFCL()->app->onlyWoocommCountry &&  !WOOCFCL_Utils::array_empty(WOOCFCL()->app->countriesToKeep)) {
                foreach (WOOCFCL()->app->countriesToKeep as $code => $country) {
                    //check if exist file code
                    $this->option_woocom[$code]= $this->set_app_states_towoocom_adapter($this->option_value,$code);   
                }
            } 

            $this->option_datatable=$this->set_app_states_todatatable_adapter($this->option_value);

        }

        private function set_woocom_states_toapp_adapter($states,$key){

            $fields=WOOCFCL_Utils::array_get_value($states,$key);
            if (!WOOCFCL_Utils::array_empty($fields)) {

                array_walk(
                        $fields,
                        function (&$item, $key) {
                            $name=$item;
                            $item=array('Name'=>$name,'AdditionalCode'=>'','NumberCode'=>0);
                        }
                    );
            }
            return $fields;
        }

        private function set_app_states_towoocom_adapter($states,$key){
            $fields=WOOCFCL_Utils::array_get_value($states,$key);

            return !WOOCFCL_Utils::array_empty($fields)?array_map(
                        function($state) { return $state['Name']; },
                        $fields
                ):$fields;
        }
        
        private function set_app_states_todatatable_adapter($country_state){
            $arrayDatatable=array();


            if (!WOOCFCL_Utils::array_empty($country_state)) {

                array_walk(
                    $country_state,
                    function (&$countries, $keycountry) use (&$arrayDatatable) {
                        array_walk(
                            $countries,
                            function (&$state, $keystate) use (&$arrayDatatable,$keycountry) {
                                $extstate=array('ID'=>$keystate,'country'=>$keycountry);
                                $state=array_merge($extstate,$state);
                                array_push($arrayDatatable, $state);
                            }
                        );
                    }
                );
            }
            return $arrayDatatable;
        }

        
        public function get_woocommmerce_state() {
            return $this->option_woocom;
        }
    }
endif;    