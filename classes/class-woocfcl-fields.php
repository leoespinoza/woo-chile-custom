<?php

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Fields')):

/**
 * Manages site options using the WordPress options API.
 */
class WOOCFCL_Fields extends WOOCFCL_Options
{
    
    private static $_instance = null;
    // /**
    //  * The name used by option.
    //  *
    //  * @var string
    //  */
    // private $_option = ''; 
    /**
	 * plugin defaults values
	 *
	 * @var array
	 */
	public $defaults=null;
    /**
	 * countries allowed by the plugin
	 *
	 * @var array
	 */
    public $fieldsPlugin= array();
    /**
	 * countries allowed by woocommerce
	 *
	 * @var array
	 */
    public $fieldsWoocom= array();


    public $isWoocomField= false;   

	public $default_fields = array();
    public $default_names = array();

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
        $this->init();
        
    }  

    public static function instance($option=null,$prefix = null) {
        if (is_null(self::$_instance)) {
        self::$_instance = new self($option,$prefix);
        }
        return self::$_instance;
    }

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
            $priority = $this->prepare_field_priority($this->fieldsPlugin, $order, false);

            if(isset($this->fieldsPlugin[$name])){

                if($is_deleted){ unset($this->fieldsPlugin[$name]); continue; }
                $this->fieldsPlugin[$name]['priority'] = $priority;
                $this->fieldsPlugin[$name]['enabled'] = $enabled;
            }
        }
        $this->fieldsPlugin = $this->sort_fields($this->fieldsPlugin);
        $this->defaults = $this->fieldsPlugin;
        return $this->setDefault();
    }

    public function set_field($field, $new=false, $unsetfield=false) {
        
        if ( !(isset($field) && isset($field['name']) && $field['name'] )  ) return false;
        
        $name = isset($field['name']) && $field['name']? $field['name'] : false;

        // if($name){
        if($new){

            $priority = $this->prepare_field_priority($this->fieldsPlugin, false, $new);
            $field['custom'] = 1;
            $field['priority'] = $priority;

        }else{
            if( $unsetfield ){
                unset($this->fieldsPlugin[$unsetfield]);
            }
        }

        $this->fieldsPlugin[$name] = $field;
        $this->defaults = $this->fieldsPlugin;
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
            $this->defaults=array();  
            $this->get_default_plugin();
        }
    }

    private function init(){
        $this->get_default_woocomm();  
        $this->get_default_plugin();    
    }

    private function get_default_woocomm(){
        
        $this->fieldsWoocom =!empty($this->_option)? WOOCFCL()->woocom_get_address_fields($this->_option):array();
        $this->fieldsWoocom = $this->get_fields_adapter($this->fieldsWoocom);

        return $this->fieldsWoocom;
    }

    private function get_default_plugin(){
        $this->isWoocomField = WOOCFCL_Utils::array_empty($this->defaults)?true:false;
        $this->fieldsPlugin=$this->isWoocomField? $this->fieldsWoocom : $this->defaults;

        return $this->fieldsPlugin;
    }

    private function get_fields_adapter($fields){
		foreach ($fields as $key => $value) {
			$fields[$key]['custom'] = 0;
			$fields[$key]['enabled'] = 1;
			$fields[$key]['show_in_email'] = 1;
			$fields[$key]['show_in_order'] = 1;
		}
		return $fields;
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

        if (isset($this->fieldsPlugin)) {
            $fields =$this->fieldsPlugin;
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
}
endif;    