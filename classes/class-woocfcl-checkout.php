<?php
/**
 * Woo Checkout Field Editor Public
 *
 * @link       https://themehigh.com
 * @since      1.3.6
 *
 * @package    woo-checkout-field-editor-pro
 * @subpackage woo-checkout-field-editor-pro/classes
 */

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Checkout')) :

class WOOCFCL_Checkout {
	public function __construct() {
		$this->init_action();
		
	}
	public function init_action() {
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles_and_scripts'));
		add_action('after_setup_theme', array($this, 'register_public_hooks'));
	}

	public function enqueue_styles_and_scripts() {
		if(is_checkout()){
			$in_footer = apply_filters( 'woocfcl_enqueue_script_in_footer', true );
			$deps = array('jquery', 'select2');

			wp_register_script('woocfcl-checkout-script', WOOCFCL_JS_URL.'woocfcl-checkout.js', $deps, WOOCFCL_VERSION, $in_footer);
			wp_enqueue_script('woocfcl-checkout-script');
			wp_enqueue_style('woocfcl-checkout-style', WOOCFCL_CSS_URL . 'woocfcl-public.css', WOOCFCL_VERSION);
		}
	}

	public function register_public_hooks(){
		echo 'register_public_hooks <br/>';
		$woocfcl_checkout_priority=10;//99999;
		$woocfcl_fields_priority=1000;
		$woocfcl_priority=10;
		// $hp_default_address_fields = apply_filters('woocfcl_default_address_fields_priority', 1000);
		// $hp_billing_fields  = apply_filters('woocfcl_billing_fields_priority', 1000);
		// $hp_shipping_fields = apply_filters('woocfcl_shipping_fields_priority', 1000);
		// $hp_checkout_fields = apply_filters('woocfcl_checkout_fields_priority', 1000);


		add_filter('woocommerce_billing_fields', array($this, 'billing_fields'), $woocfcl_checkout_priority, 2);
		add_filter('woocommerce_shipping_fields', array($this, 'shipping_fields'), $woocfcl_checkout_priority, 2);
		add_filter('woocommerce_checkout_fields', array($this, 'checkout_fields'), $woocfcl_checkout_priority);

		add_filter('woocommerce_get_country_locale_default', array($this, 'prepare_country_locale'),$woocfcl_priority);
		add_filter('woocommerce_get_country_locale_base', array($this, 'prepare_country_locale'),$woocfcl_priority);
		add_filter('woocommerce_get_country_locale', array($this, 'get_country_locale'),$woocfcl_priority);

		add_filter('woocommerce_enable_order_notes_field', array($this, 'enable_order_notes_field'), 1000);
		add_action('woocommerce_checkout_update_order_meta', array($this, 'checkout_update_order_meta'), $woocfcl_priority, 2);

		add_action('woocommerce_order_details_after_order_table', array($this, 'order_details_after_customer_details'), 20, 1);

		add_filter('woocommerce_default_address_fields' , array($this, 'default_address_fields'), $woocfcl_fields_priority);
		add_action('woocommerce_after_checkout_validation', array($this, 'checkout_fields_validation'), $woocfcl_priority, 2);
		add_filter('woocommerce_email_order_meta_fields', array($this, 'display_custom_fields_in_emails'), $woocfcl_priority, 3);

	}

	public function billing_fields($fields, $country){
				echo 'billing_fields <br/>';
		$wc_endpoints = WC()->query->get_query_vars();
		WOOCFCL_Utils::setSession('billing_fields',$wc_endpoints);
		return (is_wc_endpoint_url('edit-address'))?$fields:$this->prepare_address_fields(WOOCFCL()->get_checkout_fields('billing'), $fields, 'billing', $country);

	}

	public function shipping_fields($fields, $country){
		echo 'shipping_fields <br/>';
		return (is_wc_endpoint_url('edit-address'))?
		$fields:$this->prepare_address_fields(WOOCFCL()->get_checkout_fields('shipping'), $fields, 'shipping', $country);
	}
	
	public function checkout_fields($fields) {
		echo 'checkout_fields <br/>';
		$additional_fields = WOOCFCL()->get_checkout_fields('additional');

		if(is_array($additional_fields)){
			if(isset($fields['order']) && is_array($fields['order'])){
				$fields['order'] = $additional_fields + $fields['order'];
			}

			// check if order_comments is enabled/disabled
			if(isset($additional_fields['order_comments']['enabled']) && !$additional_fields['order_comments']['enabled']){
				unset($fields['order']['order_comments']);
			}
		}
				
		if(isset($fields['order']) && is_array($fields['order'])){
			$fields['order'] = $this->prepare_checkout_fields($fields['order'], false);
		}

		if(isset($fields['order']) && !is_array($fields['order'])){
			unset($fields['order']);
		}
		
		return $fields;
	}


	/**
	 * Hide Additional Fields title if no fields available.
	 */
	public function enable_order_notes_field() {
		echo 'enable_order_notes_field <br/>';
		return WOOCFCL()->additional->enable_order_notes_field();
	}	
	
	public function prepare_country_locale($fields) {
		if(is_array($fields)){


			$sname = apply_filters('woocfcl_address_field_override_with', 'billing');

			WOOCFCL_Utils::setSession($sname,$fields);
			// $address_fields = get_option(WOOCFCL_PREFIX.'_'.$sname);
			$address_fields = WOOCFCL()->get_checkout_fields( $sname);
			
			foreach($fields as $key => $props){
				$override_ph = apply_filters('woocfcl_address_field_override_placeholder', true);
				$override_label = apply_filters('woocfcl_address_field_override_label', true);
				$override_required = apply_filters('woocfcl_address_field_override_required', false);
				$override_priority = apply_filters('woocfcl_address_field_override_priority', true);
				
				if($override_ph && isset($props['placeholder'])){
					unset($fields[$key]['placeholder']);
				}

				if($override_label && isset($props['label'])){
					unset($fields[$key]['label']);
				}

				if($override_required && isset($props['required'])){
					$fkey = $sname.'_'.$key;
					if(is_array($address_fields) && isset($address_fields[$fkey])){
						$cf_props = $address_fields[$fkey];
						if(is_array($cf_props) && isset($cf_props['required'])){
							$fields[$key]['required'] = $cf_props['required'] ? true : false;
						}
					}
				}
				
				if($override_priority && isset($props['priority'])){
					unset($fields[$key]['priority']);
				}
			}
		}
		return $fields;
	}

	public function get_country_locale($locale) {
		if(is_array($locale)){

			WOOCFCL_Utils::setSession('locale',$locale);
			foreach($locale as $country => $fields){
				$locale[$country] = $this->prepare_country_locale($fields);
			}
		}
		return $locale;
	}
	
	public function default_address_fields($fields) {
		$sname = apply_filters('woocfcl_address_field_override_with', 'billing');
		
		if($sname === 'billing' || $sname === 'shipping'){
			$address_fields =  WOOCFCL()->get_checkout_fields($sname);
			
			if(is_array($address_fields) && !empty($address_fields) && !empty($fields)){
				$override_required = apply_filters( 'woocfcl_address_field_override_required', true );
				
				foreach($fields as $name => $field) {
					$fname = $sname.'_'.$name;
					
					if(WOOCFCL_Utils::is_address_field($fname) && $override_required){
						$custom_field = isset($address_fields[$fname]) ? $address_fields[$fname] : false;
						
						if(WOOCFCL_Utils::is_enabled($custom_field)){
							$fields[$name]['required'] = isset($custom_field['required']) && $custom_field['required'] ? true : false;
						}
					}
				}
			}
		}
		
		return $fields;
	}

	public function prepare_address_fields($fieldset, $original_fieldset = false, $sname = 'billing', $country){
		if(is_array($fieldset) && !empty($fieldset)) {
			$locale = WC()->countries->get_country_locale();

			if(isset($locale[ $country ]) && is_array($locale[ $country ])) {
				foreach($locale[ $country ] as $key => $value){
					$fname = $sname.'_'.$key;

					if(is_array($value) && isset($fieldset[$fname])){
						if(isset($value['required'])){
							$fieldset[$fname]['required'] = $value['required'];
						}
					}
				}
			}

			$fieldset = $this->prepare_checkout_fields($fieldset, $original_fieldset);
			return $fieldset;
		}else {
			return $original_fieldset;
		}
	}

	public function prepare_checkout_fields($fields, $original_fields) {
		if(is_array($fields) && !empty($fields)) {
			foreach($fields as $name => $field) {
				if(WOOCFCL_Utils::is_enabled($field)) {
					$new_field = false;
					$allow_override = apply_filters('woocfcl_allow_default_field_override_'.$name, false);
					
					if($original_fields && isset($original_fields[$name]) && !$allow_override){
						$new_field = $original_fields[$name];
						
						$new_field['label'] = isset($field['label']) ? $field['label'] : '';
						$new_field['default'] = isset($field['default']) ? $field['default'] : '';
						$new_field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
						$new_field['class'] = isset($field['class']) && is_array($field['class']) ? $field['class'] : array();
						$new_field['label_class'] = isset($field['label_class']) && is_array($field['label_class']) ? $field['label_class'] : array();
						$new_field['validate'] = isset($field['validate']) && is_array($field['validate']) ? $field['validate'] : array();
						
						$new_field['required'] = isset($field['required']) ? $field['required'] : 0;
						$new_field['priority'] = isset($field['priority']) ? $field['priority'] : '';

					} else {
						$new_field = $field;
					}

					$type = isset($new_field['type']) ? $new_field['type'] : 'text';

					$new_field['class'][] = 'woocfcl-field-wrapper';
					$new_field['class'][] = 'woocfcl-field-'.$type;
					
					if($type === 'select' || $type === 'radio'){
						if(isset($new_field['options'])){
							$options_arr = WOOCFCL_Utils::prepare_field_options($new_field['options']);
							$options = array();
							foreach($options_arr as $key => $value) {
								$options[$key] = WOOCFCL_Utils::translate($value);
							}
							$new_field['options'] = $options;
						}
					}

					if($type === 'select' && apply_filters('woocfcl_enable_select2_for_select_fields', true)){
						$new_field['input_class'][] = 'woocfcl-enhanced-select';
					}
					
					if(isset($new_field['label'])){
						$new_field['label'] = WOOCFCL_Utils::t($new_field['label']);
					}

					if(isset($new_field['placeholder'])){
						$new_field['placeholder'] = WOOCFCL_Utils::t($new_field['placeholder']);
					}
					
					$fields[$name] = $new_field;
				}else{
					unset($fields[$name]);
				}
			}								
			return $fields;
		}else {
			return $original_fields;
		}
	}

	/*************************************
	----- Validate & Update - START ------
	*************************************/
	public function checkout_fields_validation($posted, $errors){
		$checkout_fields = WC()->checkout->checkout_fields;
		
		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($this->maybe_skip_fieldset($fieldset_key, $posted)){
				continue;
			}
			
			foreach($fieldset as $key => $field) {
				if(isset($posted[$key]) && !WOOCFCL_Utils::is_blank($posted[$key])){
					$this->validate_custom_field($field, $posted, $errors);
				}
			}
		}
	}

	public function validate_custom_field($field, $posted, $errors=false, $return=false){
		$err_msgs = array();
		$key = isset($field['name']) ? $field['name'] : false;
		
		if($key){
			$value = isset($posted[$key]) ? $posted[$key] : '';
			$validators = isset($field['validate']) ? $field['validate'] : '';

			if($value && is_array($validators) && !empty($validators)){					
				foreach($validators as $vname){
					$err_msg = '';
					$flabel = isset($field['label']) ? WOOCFCL_Utils::t($field['label']) : $key;

					if($vname === 'number'){
						if(!is_numeric($value)){
							$err_msg = '<strong>'. $flabel .'</strong> '. WOOCFCL_Utils::t('is not a valid number.');	
						}
					}

					if($err_msg){
						if($errors || !$return){
							$this->add_validation_error($err_msg, $errors);
						}
						$err_msgs[] = $err_msg;
					}
				}
			}
		}
		return !empty($err_msgs) ? $err_msgs : false;
	}

	public function add_validation_error($msg, $errors=false){
		if($errors){
			$errors->add('validation', $msg);
		}else if(WOOCFCL_Utils::woo_version_check('2.3.0')){
			wc_add_notice($msg, 'error');
		} else {
			WC()->add_error($msg);
		}
	}

	public function checkout_update_order_meta($order_id, $posted){
		$types = array('billing', 'shipping', 'additional');

		foreach($types as $type){
			if($this->maybe_skip_fieldset($type, $posted)){
				continue;
			}

			$fields = WOOCFCL()->get_checkout_fields($type);
			
			foreach($fields as $name => $field){
				if(WOOCFCL_Utils::is_active_custom_field($field) && isset($posted[$name])){
					$value = wc_clean($posted[$name]);
					if($value){
						update_post_meta($order_id, $name, $value);
					}
				}
			}
		}
	}

	private function maybe_skip_fieldset( $fieldset_key, $data ) {
		$ship_to_different_address = isset($data['ship_to_different_address']) ? $data['ship_to_different_address'] : false;

		if ( 'shipping' === $fieldset_key && ( ! $ship_to_different_address || ! WC()->cart->needs_shipping_address() ) ) {
			return true;
		}
		return false;
	}
	
	/****************************************
	----- Display Field Values - START ------
	*****************************************/
	/**
	 * Display custom fields in emails
	 */
	public function display_custom_fields_in_emails($ofields, $sent_to_admin, $order){
		$custom_fields = array();
		$fields = WOOCFCL()->get_all_checkout_fields();

		// Loop through all custom fields to see if it should be added
		foreach( $fields as $key => $field ) {
			if(isset($field['show_in_email']) && $field['show_in_email']){
				$order_id = WOOCFCL_Utils::get_order_id($order);
				$value = get_post_meta( $order_id, $key, true );
				
				if($value){
					$label = isset($field['label']) && $field['label'] ? $field['label'] : $key;
					$label = esc_attr($label);
					$value = WOOCFCL_Utils::get_option_text($field, $value);
					
					$custom_field = array();
					$custom_field['label'] = WOOCFCL_Utils::t($label);
					$custom_field['value'] = $value;
					
					$custom_fields[$key] = $custom_field;
				}
			}
		}

		return array_merge($ofields, $custom_fields);
	}	
	
	/**
	 * Display custom checkout fields on view order pages
	 */
	public function order_details_after_customer_details($order){
		$order_id = WOOCFCL()->get_order_id($order);
		$fields = WOOCFCL()->get_all_checkout_fields($order);
		
		if(is_array($fields) && !empty($fields)){
			$fields_html = '';
			// Loop through all custom fields to see if it should be added
			foreach($fields as $key => $field){			
				if(WCSCL_Utils::is_active_custom_field($field) && isset($field['show_in_order']) && $field['show_in_order']){
					$value = get_post_meta( $order_id, $key, true );
					
					if($value){
						$label = isset($field['label']) && $field['label'] ? WOOCFCL_Utils::t($field['label']) : $key;

						$label = esc_attr($label);
						//$value = wptexturize($value);
						$value = WOOCFCL_Utils::get_option_text($field, $value);
						
						if(is_account_page()){
							if(apply_filters( 'woocfcl_view_order_customer_details_table_view', true )){
								$fields_html .= '<tr><th>'. $label .':</th><td>'. $value .'</td></tr>';
							}else{
								$fields_html .= '<br/><dt>'. $label .':</dt><dd>'. $value .'</dd>';
							}
						}else{
							if(apply_filters( 'woocfcl_thankyou_customer_details_table_view', true )){
								$fields_html .= '<tr><th>'. $label .':</th><td>'. $value .'</td></tr>';
							}else{
								$fields_html .= '<br/><dt>'. $label .':</dt><dd>'. $value .'</dd>';
							}
						}
					}
				}
			}
			
			if($fields_html){
				do_action( 'woocfcl_order_details_before_custom_fields_table', $order ); 
				?>
				<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php
						echo $fields_html;
					?>
				</table>
				<?php
				do_action( 'woocfcl_order_details_after_custom_fields_table', $order ); 
			}
		}
	}
	/*****************************************
	----- Display Field Values - END --------
	*****************************************/



}

endif;

