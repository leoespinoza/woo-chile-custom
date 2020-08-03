<?php

defined( 'ABSPATH' ) || exit;

if(!trait_exists('WOOCFCL_WoocomFields')):

/**
 * Manages site options using the WordPress options API.
 */
trait WOOCFCL_WoocomFields 
{
        /**
         * The name used by all option.
         *
         * @var string
         */
        private $_option = ''; 

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


        public $is_woocom_field= false;   

        public $reserved_woocom_fieldaddress = array();
        public $reserved_woocom_fieldnames = array();


        /**
         * set the default option values to arrays.
         *
         *
         * @param string $name
         */
        public function set_reserved_fieldname($reserved_woocom_fieldaddress=array(),                                              $reserved_woocom_fieldnames=array())
        {
            $this->reserved_woocom_fieldaddress=$reserved_woocom_fieldaddress;
            $this->reserved_woocom_fieldnames=$reserved_woocom_fieldnames;
        }

        private function set_option_values() {

            $this->is_woocom_field = WOOCFCL_Utils::array_empty($this->option_value)?true:false;
            $this->option_woocom = $this->woocom_get_address_fields();
            $this->option_value=$this->is_woocom_field? $this->set_woocom_fields_toapp_adapter($this->option_woocom): $this->option_value;
            $this->option_datatable= $this->set_app_fields_todatatable_adapter($this->option_value);
        }

        private function set_woocom_fields_toapp_adapter($fields=array()){

            if (!WOOCFCL_Utils::array_empty($fields)) {
                array_walk(
                        $fields,
                        function (&$item, $key) {
                            $extfield=array('custom'=>0,'enabled'=>1,'show_in_email'=>1,'show_in_order'=>1);
                            $item=array_merge($extfield,$item);
                        }
                    );
            }
            return $fields;
        }

        private function set_app_fields_todatatable_adapter($fields=array()){

            if (!WOOCFCL_Utils::array_empty($fields)) {
                array_walk(
                        $fields,
                        function (&$item, $key) {
                            $extfield=array('name'=>$key);
                            $item=array_merge($extfield,$item);
                        }
                    );
            }
            return array_values($fields);
        }

        private function init(){
            if(method_exists($this, 'get_default')) $this->get_default();
            $this->set_option_values();  
        }   



    // private function get_default_woocomm(){
        
    //     $this->option_woocom =!empty($this->_option)? WOOCFCL()->woocom_get_address_fields($this->_option):array();
    //     $this->option_woocom = $this->get_fields_adapter($this->option_woocom);

    //     return $this->option_woocom;
    // } 
    
    
    // private function get_default_plugin(){
    //     $this->is_woocom_field = WOOCFCL_Utils::array_empty($this->option_default)?true:false;
    //     $this->option_value=$this->is_woocom_field? $this->option_woocom : $this->option_default;

    //     return $this->option_value;
    // }

    public function update_all_fields(
        $field_names=array(),
        $field_order=array(),
        $field_deleted=array(),
        $field_enabled=array()) {

        $max = max( array_map( 'absint', array_keys( $field_names) ) );
        for($i = 0; $i <= $max; $i++) {

            $name = $field_names[$i];
            $is_deleted = isset($field_deleted[$i]) && $field_deleted[$i] ? true : false;
            $order = isset($field_order[$i]) ? trim(stripslashes($field_order[$i])) : 0;
            $enabled = isset($field_enabled[$i]) ? trim(stripslashes($field_enabled[$i])) : 0;
            $priority = $this->prepare_field_priority($this->option_value, $order, false);

            if(isset($this->option_value[$name])){

                if($is_deleted){ unset($this->option_value[$name]); continue; }
                $this->option_value[$name]['priority'] = $priority;
                $this->option_value[$name]['enabled'] = $enabled;
            }
        }
        $this->option_value = $this->sort_fields($this->option_value);
        $this->option_default = $this->option_value;
        return $this->setDefault();
    }

    public function set_field($field, $new=false, $unsetfield=false) {
        
        if ( !(isset($field) && isset($field['name']) && $field['name'] )  ) return false;
        
        $name = isset($field['name']) && $field['name']? $field['name'] : false;

        // if($name){
        if($new){

            $priority = $this->prepare_field_priority($this->option_value, false, $new);
            $field['custom'] = 1;
            $field['priority'] = $priority;

        }else{
            if( $unsetfield ){
                unset($this->option_value[$unsetfield]);
            }
        }

        $this->option_value[$name] = $field;
        $this->option_default = $this->option_value;
        return $this->setDefault();

	}   

    /**
     * Removes the default option with the given name existing option.
     *
     * 
     *
     * @return bool
     */
    public function deleteDefault()
    {
        if ( $this->removeDefault()) {
            $this->option_default=array();  
            $this->get_default_plugin();
        }
    }

	private function sort_fields($fields){
		uasort($fields, 'wc_checkout_fields_uasort_comparison');
		return $fields;
    }
    
    private function prepare_field_priority($fields, $order, $new=false){
		$priority = '';
		if(!$new){
			$priority = is_numeric($order) ? ($order+1)*10 : false;
		}

		if(!$priority){
			$max_priority = self::get_max_priority($fields);
			$priority = is_numeric($max_priority) ? $max_priority+10 : false;
		}
		return $priority;
	}

	private function get_max_priority($fields){
		$max_priority = 0;
		if(is_array($fields)){
			foreach ($fields as $key => $value) {
				$priority = isset($value['priority']) ? $value['priority'] : false;
				$max_priority = is_numeric($priority) && $priority > $max_priority ? $priority : $max_priority;
			}
		}
		return $max_priority;
	}

    public function display_fields_in_admin_order($order, $fields, $prefix_html = ''){
		if(is_array($fields)){
			$html = '';
			$order_id = WOOCFCL()->get_order_id($order);
		
			foreach($fields as $name => $field){
				if(WCSCL_Utils::is_active_custom_field($field) && isset($field['show_in_order']) && $field['show_in_order']){
					$value = get_post_meta( $order_id, $name, true );
					if(!empty($value)){
						$value = WCSCL_Utils::get_option_text($field, $value);
						$label = isset($field['label']) && $field['label'] ? WCSCL_Utils::t($field['label']) : $name;
						$html .= '<p><strong>'. $label .':</strong><br/> '. wptexturize($value) .'</p>';
					}
				}
			}

			if($html){
				echo $prefix_html.$html;	
			}
		}
	}

    public function display_defaultfields_in_admin_order($order, $prefix_html = ''){
		$this->display_fields_in_admin_order($order, $this->fieldsPlug, $prefix_html);

	}

    public function set_field_property_to_form(){
        $fields=false;

        if (isset($this->option_value)) {
            $fields =$this->option_value;
            $i=0;
            foreach( $fields as $name => $field ) {
                $fields[$name ]['name'] = $name;
                $fields[$name ]['type'] = isset($field['type']) ? $field['type'] : '';
                $fields[$name ]['label'] = isset($field['label']) ? WOOCFCL_Utils::t($field['label']) : '';
                $fields[$name ]['priority'] = isset($field['priority']) ? $field['priority'] : '';
                $fields[$name ]['placeholder'] = isset($field['placeholder']) ?  WOOCFCL_Utils::t($field['placeholder']) : '';
                $fields[$name ]['required'] = isset($field['required']) && $field['required'] ? 1 : 0;
                $fields[$name ]['enabled'] = isset($field['enabled']) && $field['enabled'] ? 1 : 0;
                $fields[$name ]['custom'] = isset($field['custom']) && $field['custom'] ? 1 : 0;
                $fields[$name ]['validate'] = isset($field['validate']) ? $field['validate'] : '';
                $fields[$name ]['validate'] = is_array($fields[$name ]['validate'] ) ? implode(",", $fields[$name ]['validate'] ) : '';
                if( $fields[$name ]['type']  === 'select' ||  $fields[$name ]['type']  === 'radio'){
                    $fields[$name ]['options'] = isset($field['options']) ? $field['options'] : '';
                    $fields[$name ]['options'] = $this->set_field_options_to_json($options);
                }
                $fields[$name ]['options']= isset($field['options']) ? $field['options'] : '';
                $fields[$name ]['json']= $this->set_field_property_to_json($field) ;
                $fields[$name ]['index']= $i;

                $i++; 
            }
        }

		return $fields;
    }
    private function set_field_property_to_json($field){
		$json = '';
		if(is_array($field)){
			foreach($field as $pname => $pvalue){
				$pvalue = is_array($pvalue) ? implode(',', $pvalue) : $pvalue;
				$pvalue = is_string($pvalue) ? esc_attr($pvalue) : $pvalue;
				
				$field[$pname] = $pvalue;
			}
			$json = json_encode($field);
			
		}
		return htmlspecialchars($json);
    }
    
    private function set_field_options_to_json($options){
		$json = '';
		if(is_array($options) && !empty($options)){
			$options_arr = array();

			foreach($options as $okey => $otext){
					array_push($options_arr, array("key" => $okey, "text" => $otext));
			}
			$options_json = json_encode($options_arr);
			$options_json = rawurlencode($options_json);
		}
		return $options_json;
	}

    public function woocom_get_address_fields()
    {
        return !empty($this->_option)?WC()->countries->get_address_fields(WOOCFCL()->base_country, $this->_option . '_'):array();
    }  
}
endif;    