<?php
/**
 * Woo Checkout Field Editor Settings
 *
 * @author   ThemeHigh
 * @category Admin
 */

defined( 'ABSPATH' ) || exit;

if(!class_exists('WOOCFCL_Admin')) :

class WOOCFCL_Admin {

	public function __construct() {
		$this->init();
	}
	
	public function init() {

		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles_and_scripts'));
		add_action('admin_menu', array($this, 'add_admin_menu'));
		add_filter('woocommerce_screen_ids', array($this, 'add_screen_id'));
		add_filter('plugin_action_links_'.WOOCFCL_BASE_NAME, array($this, 'add_plugin_action_links'));
		
		$field_admin = WCSCL_Fields::instance();
		$field_admin ->set_after_setup_theme();
	}

	public function enqueue_styles_and_scripts($hook) {
		
		if(strpos($hook, WOOCFCL_ACTION_LINK) === false)  return; 

		$deps = array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-tiptip', 'woocommerce_admin', 'select2', 'wp-color-picker');

		wp_enqueue_style('woocommerce_admin_styles');
		wp_enqueue_style('woocfcl-admin-style', WOOCFCL_ASSETS_URL . 'css/wcscl-admin.css', WOOCFCL_VERSION);
		wp_enqueue_script('woocfcl-admin-script', WOOCFCL_ASSETS_URL . 'js/wcscl-admin.js', $deps, WOOCFCL_VERSION, true);
	}

	public function wcscl_capability() {
		$allowed = array('manage_woocommerce', 'manage_options');
		$capability = apply_filters('wcscl_required_capability', 'manage_woocommerce');

		if(!in_array($capability, $allowed)){
			$capability = 'manage_woocommerce';
		}
		return $capability;
	}
	
	public function add_admin_menu() {
		$capability = $this->wcscl_capability();
		$this->screen_id = add_submenu_page('woocommerce', 
								WOOCFCL_Utils::translate('WooCommerce Checkout Form Designer'), 
								WOOCFCL_Utils::translate('Chile customization for woocommerce'), 
								$capability, 
								WOOCFCL_ACTION_LINK, 
								array($this, 'output_settings'));

		//add_action('admin_print_scripts-'. $this->screen_id, array($this, 'enqueue_admin_scripts'));
	}
	
	public function add_screen_id($ids){
		$ids[] = 'woocommerce_page_'.WOOCFCL_ACTION_LINK;
		$ids[] = strtolower(WOOCFCL_Utils::translate('WooCommerce')) .'_page_'.WOOCFCL_ACTION_LINK;

		return $ids;
	}

	public function add_plugin_action_links($links) {
		$settings_link = '<a href="'.admin_url('admin.php?page='.WOOCFCL_ACTION_LINK).'">'. WOOCFCL_Utils::translate('Settings') .'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	
	private function add_version_notice(){
		?>
        <div id="message" class="wc-connect updated thpladmin-notice">
            <div class="squeezer">
				<table>
					<tr>
						<td width="70%">
							<p><strong><i>WooCommerce Personalización para Chile</i></strong> premium version provides more features to design your checkout page.</p>
							<ul>
								<li>17 field types available,  (<i>Text, Hidden, Password, Telephone, Email, Number, Textarea, Radio, Checkbox, Checkbox Group, Select, Multi-select, Date Picker, Time Picker, File Upload, Heading, Label</i>).</li>
								<li>Conditionally display fields based on cart items and other field(s) values.</li>
								<li>Add an extra cost to the cart total based on field selection.</li>
								<li>Custom validation rules using RegEx.</li>
								<li>Option to add more sections in addition to the core sections (billing, shipping and additional) in checkout page.</li>
							</ul>
						</td>
						<td>
						<a target="_blank" href="https://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/">
							<img src="<?php echo WOOCFCL_ASSETS_URL ?>css/upgrade-btn.png" />
						</a>
						</td>
					</tr>
				</table>
            </div>
        </div>
        <?php
	}

	private function add_review_request_link(){
		?>
		<p>Si te gusta nuestro plugin <strong>Woocommerce personalización para Chile</strong>, please leave us a <a href="https://wordpress.org/support/plugin/woo-checkout-field-editor-pro/reviews?rate=5#new-post" target="_blank" aria-label="five star" data-rated="Thanks :)">★★★★★</a> rating. A huge thanks in advance!</p>
		<?php 
	}
	
	public function output_settings(){
		$this->add_version_notice();
		$this->add_review_request_link();

		$tab = $this->get_current_tab();
		if($tab === 'fields'){
			$field_admin = WCSCL_Fields::instance();	
			$field_admin->render_page();
		}
	}

	public function get_current_tab(){
		return isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';
	}


}

endif;

