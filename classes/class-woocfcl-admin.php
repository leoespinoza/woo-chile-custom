<?php

/**
 * Woo Checkout Field Editor Settings
 *
 * @author   ThemeHigh
 * @category Admin
 */

defined('ABSPATH') || exit;

if (!class_exists('WOOCFCL_Admin')):

    class WOOCFCL_Admin
	{

        public $hook_suffix = array();
        public $screen_ids = array();
        public $admin_link = '';
        protected $tabs = array();
        protected $sections = array();
        protected $nav = array();
        private $currentTab = '';
        private $currentSection = '';
        private $currentAction = '';
        private $currentMessage = '';
        private $currentFields = array();
        private $currentFieldsForm = array();
        private $form_new = array();
        private $form_edit = array();
        private $form_display = array();

		public function __construct()
		{
				$this->init();
		}

		private function init()
		{
			//declaraciòn de variables

			$this->admin_link = 'admin.php?page=' . WOOCFCL_ACTION_LINK;

			$this->screen_ids = (WOOCFCL_SCREENID === WOOCFCL_SCREENID_I18) ? array(WOOCFCL_SCREENID) : array(
				WOOCFCL_SCREENID,
				WOOCFCL_SCREENID_I18,
			);

			$this->tabs = [
				'fields' => WOOCFCL_Utils::translate('Checkout Fields'),
				'states' => WOOCFCL_Utils::translate('States Options')
			];
			$this->sections = [
				'billing' => WOOCFCL_Utils::translate('Billing Fields'),
				'shipping' => WOOCFCL_Utils::translate('Shipping Fields'),
				'additional' => WOOCFCL_Utils::translate('Additional Fields'),
				'states' => WOOCFCL_Utils::translate('States Options'),
				'cities' => WOOCFCL_Utils::translate('Cities Options'),
			];
			$this->nav = [
				'fields' => [
					'billing' => WOOCFCL_Utils::translate('Billing Fields'),
					'shipping' => WOOCFCL_Utils::translate('Shipping Fields'),
					'additional' => WOOCFCL_Utils::translate('Additional Fields'),
				],
				'states' => [
					'states' => WOOCFCL_Utils::translate('States Options'),
					'cities' => WOOCFCL_Utils::translate('Cities Options'),
				],
			];

			$this->form_new = array(
				'id' => 'woocfcl_new_field_form_pp',
				'title' => WOOCFCL_Utils::translate('New Checkout Field'),
				'type' => 'new',
			);
			$this->form_edit = array(
				'id' => 'woocfcl_edit_field_form_pp',
				'title' => WOOCFCL_Utils::translate('Edit Checkout Field'),
				'type' => 'edit',
			);

		}

		public function register_admin_page()
		{
			$this->hook_suffix[] = add_submenu_page(
				'woocommerce',
				WOOCFCL_Utils::translate('Woocommerce customization for Chile'),
				WOOCFCL_Utils::translate('WooCommerce for Chile'),
				'manage_woocommerce',
				WOOCFCL_ACTION_LINK,
				array($this, 'set_admin_ui')
			);
		}

		/**
		 * set WooCommerce screen ids.
		 *
		 * @return array
		 */
		public function add_screen_id($ids)
		{
			$ids = array_merge($ids, $this->screen_ids);
			return $ids;
		}

		public function add_plugin_action_links($links)
		{
			$elementAdmLink = '<a href="' . admin_url($this->admin_link) . '">' . WOOCFCL_Utils::translate('Settings') . '</a>';
			array_unshift($links, $elementAdmLink);
			return $links;
		}

		/**
		 * Load css  andScript in admin zone
		 */
		public function enqueue_styles_and_scripts($hook)
		{

			if (!WOOCFCL_Utils::plug_is_current_screen()) return;
	
			$deps = array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-tiptip', 'woocommerce_admin', 'select2', 'wp-color-picker');

			// styles
			wp_enqueue_style('woocommerce_admin_styles');
			wp_enqueue_style('datatable-style-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'datatable/css/jquery.dataTables.min.css', array('jquery-ui-style'), false, 'all');
			wp_enqueue_style('datatablesel-style-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'datatable/css/select.dataTables.min.css', array('jquery-ui-style'), false, 'all');
			wp_enqueue_style('datatablerow-style-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'datatable/css/rowReorder.dataTables.min.css', array('jquery-ui-style'), false, 'all');
			wp_enqueue_style('materialize-style-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'materialize/materialize.min.css', array('jquery-ui-style'), false, 'all');
			wp_enqueue_style('jconfirm-style-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'jqconfirm/jquery-confirm.css', array('jquery-ui-style'), false, 'all');

			wp_enqueue_style('woocfcl-style-admin', WOOCFCL_ASSETS_ADM_URL . 'css/woocfcl-admin.css', array(), false, 'all');

			// javascript
			wp_enqueue_script('datatable-script-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'datatable//js/jquery.dataTables.min.js', $deps, '', true);
			wp_enqueue_script('datatablesel-script-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'datatable//js/dataTables.select.min.js', $deps, '', true);
			wp_enqueue_script('datatablerow-script-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'datatable//js/dataTables.rowReorder.min.js', $deps, '', true);
			wp_enqueue_script('materialize-script-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'materialize/materialize.min.js', $deps, '', true);
			wp_enqueue_script('jconfirm-script-admin', WOOCFCL_ASSETS_ADM_VENDORS_URL . 'jqconfirm/jquery-confirm.js', $deps, '', true);
			wp_enqueue_script('woocfcl-script-admin', WOOCFCL_ASSETS_ADM_URL . 'js/woocfcl-admin.js', $deps, WOOCFCL_VERSION, true);
		}

		public function register_admin_hooks()
		{
			// Show in order details page
			add_action('woocommerce_admin_order_data_after_order_details', array($this, 'order_data_after_order_details'), 20, 1);
			add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'order_data_after_billing_address'), 20, 1);
			add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'order_data_after_shipping_address'), 20, 1);
		}

		/******* Display & Update Field Values *******/
		/*********************************************/
		public function order_data_after_order_details($order)
		{
			echo 'WOOCFCL_Admin.order_data_after_order_details <br/>';
			WOOCFCL()->additional->display_defaultfields_in_admin_order($order, '<p>&nbsp;</p>');
		}

		public function order_data_after_billing_address($order)
		{
			echo 'WOOCFCL_Admin.order_data_after_billing_address <br/>';
			WOOCFCL()->billing->display_defaultfields_in_admin_order($order, '');
		}

		public function order_data_after_shipping_address($order)
		{
			echo 'WOOCFCL_Admin.order_data_after_shipping_address <br/>';
			WOOCFCL()->shipping->display_defaultfields_in_admin_order($order, '');
		}

		public function set_admin_ui()
		{
			if (!WOOCFCL_Utils::plug_is_current_screen()) return;

			//$this->add_version_notice();
			$this->currentMessage = '';
			$this->currentTab = WOOCFCL_Utils::get_querystring('tab', 'fields');
			$this->currentSection = WOOCFCL_Utils::get_querystring('section', 'billing');
			$this->currentAction = WOOCFCL_Utils::get_postvar('f_action', false);

			if (isset($_POST['reset_fields'])) {
				$this->reset_checkout_field();
			}

			if (isset($_POST['save_fields'])) {
				$this->save_checkout_field($this->currentSection);
			}

			if ($this->currentAction === 'new' || $this->currentAction === 'edit') {
				$this->set_checkout_field($this->currentSection, $this->currentAction);
			}

			$os = array_keys($this->tabs);

			if (in_array($this->currentTab, $os)) {
				$se = array_keys($this->nav[$this->currentTab]);
				if (in_array($this->currentSection, $se)) {
					$this->get_current_checkout_fields();
					$this->set_field_property_to_form();
				}
				$this->renderView('advanced');
			}
		}

		private function set_current_nav()
		{
			$defaultTab='fields';
			$tab = WOOCFCL_Utils::get_querystring('tab',$defaultTab);
			$this->currentTab=isset($this->nav[$tab])?$tab:$defaultTab;

			$sections=$this->nav[$this->currentTab];
			$sectionkeys = array_keys($sections);
			$defaultSect=$sectionkeys[0];			

			$section = WOOCFCL_Utils::get_querystring('section', $defaultSect);
			$this->currentSection=isset($sections[$section])?$section:$defaultSect;

		}		

		public function get_admin_url($tab = false, $section = false)
		{
			$url = $this->admin_link;
			if ($tab && !empty($tab)) {
				$url .= '&tab=' . $tab;
			}
			if ($section && !empty($section)) {
				$url .= '&section=' . $section;
			}
			return admin_url($url);
		}

		/**
		 *    Create post type forms
		 */

		private function renderView($view, $data = array())
		{
			extract($data);
			include WOOCFCL_PATH_VIEW_BACKEND . $view . '.php';
		}

		private function renderPartView($view, $data = array())
		{
			$view = 'parts/' . $view;
			$this->renderView($view, $data);
		}

		public function render_form_field_element($props)
		{
			if (is_array($props)) {
				$partPath = 'formelem-' . (isset($props['type']) ? $props['type'] : 'text');
				$this->renderPartView($partPath, $props);
			}
		}

		private function render_table_field_element($field, $type)
		{
			$partPath = 'fields-' . $type;
			$this->renderPartView($partPath, $field);
		}

		private function reset_checkout_field()
		{
			$this->currentMessage = WOOCFCL()->reset_checkout_field() ? '<div class="updated"><p>' . WOOCFCL_Utils::translate('SUCCESS: Checkout fields successfully reset') . '</p></div>' : '<div class="error"><p>' . WOOCFCL_Utils::translate('ERROR: fatal erro on reset Checkout fields') . '</p></div>';
			return $this->currentMessage;
		}

		private function save_checkout_field($fieldname = '')
		{
			$result = false;
			try {

				$f_names = WOOCFCL_Utils::get_postvar('f_name', array());
				$f_order = WOOCFCL_Utils::get_postvar('f_order', array());
				$f_deleted = WOOCFCL_Utils::get_postvar('f_deleted', array());
				$f_enabled = WOOCFCL_Utils::get_postvar('f_enabled', array());

				if (empty($f_names)) {
					$this->currentMessage = '<div class="error"><p> ' . WOOCFCL_Utils::translate('Your changes were not saved due to no fields found.') . '</p></div>';
					return $result;
				}
				$result = WOOCFCL()->save_checkout_field($fieldname, $f_names, $f_order, $f_deleted, $f_enabled);

				$this->currentMessage = ($result === true) ? '<div class="updated"><p>' . WOOCFCL_Utils::translate('Your changes were saved.') . '</p></div>' : '<div class="error"><p>' . WOOCFCL_Utils::translate('Your changes were not saved due to an error (or you made none!).') . '</p></div>';
			} catch (Exception $e) {
				$this->currentMessage = '<div class="error"><p>' . WOOCFCL_Utils::translate('Your changes were not saved due to an error.') . '   ' . $e->getMessage() . '</p></div>';
			}
			return $result;
		}

		private function set_checkout_field($fieldname = '', $action)
		{
			if (empty($fieldname)) {
				$this->currentMessage = '<div class="error"><p>' . WOOCFCL_Utils::translate('Your changes were not saved due to section not defined.') . '</p></div>';
				return false;
			}

			try {
				$result = false;
				$new = false;
				$unsetfield = false;

				$field = $this->prepare_field_from_posted_data($_POST);
				if (!(isset($field) && isset($field['name']) && $field['name'])) {
					$this->currentMessage = '<div class="error"><p>' . WOOCFCL_Utils::translate('you made none!') . '</p></div>';
					return result;
				}

				$name = isset($field['name']) && $field['name'] ? $field['name'] : false;
				$oname = isset($_POST['i_oname']) ? trim(stripslashes($_POST['i_oname'])) : false;

				if ($action === 'new') {
					$new = true;
				} else if ($action === 'edit') {
				if ($name && $oname && $name !== $oname) {
					$unsetfield = $oname;
				}
			}
			$result = WOOCFCL()->set_checkout_field($fieldname, $field, $new, $unsetfield);

			$this->currentMessage = $result ? '<div class="updated"><p>' . WOOCFCL_Utils::translate('Your changes were saved.') . '</p></div>' : '<div class="error"><p>' . WOOCFCL_Utils::translate('Your changes were not saved due to an error (or you made none!).') . '</p></div>';
		} catch (Exception $e) {
			$this->currentMessage = '<div class="error"><p>' . WOOCFCL_Utils::translate('Your changes were not saved due to an error.') . '   ' . $e->getMessage() . '</p></div>';
		}
		}

		private function prepare_field_from_posted_data($posted)
		{
			$field_props = WOOCFCL()->form->field_form_props;
			$field = array();

			foreach ($field_props as $pname => $prop) {
				$iname = 'i_' . $pname;
				$posted[$iname] = isset($posted[$iname]) ? $posted[$iname] : '';

				$pvalue = ($prop['type'] === 'checkbox') ? ($posted[$iname] ? 1 : 0) : (is_array($posted[$iname]) ? $posted[$iname] : trim(stripslashes($posted[$iname])));

				if ($pname === 'class') {

					$pvalue = is_string($pvalue) ? preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $pvalue) : $pvalue;
					$pvalue = is_array($pvalue) ? $pvalue : array();
				}

				$field[$pname] = $pvalue;
			}
			$field['type'] = (isset($field['type']) && $field['type']) ? $field['type'] : (isset($posted['i_otype']) ? trim(stripslashes($posted['i_otype'])) : '');

			$field['name'] = isset($field['name']) && $field['name'] ? $field['name'] : (isset($posted['i_oname']) ? trim(stripslashes($posted['i_oname'])) : '');

			if ($field['type'] === 'select' || $field['type'] === 'radio') {

				if ($type === 'select') {
					$field['validate'] = '';
				} else if ($type === 'radio') {
					$field['validate'] = '';
					$field['placeholder'] = '';
				}
				$options_json = isset($posted['i_options_json']) ? trim(stripslashes($posted['i_options_json'])) : '';
				$options_arr = WOOCFCL_Utils::prepare_options_array($options_json);

				$field['options'] = $options_arr;
			} else {
				$field['options'] = '';
			}

			$field['autocomplete'] = isset($posted['i_autocomplete']) ? $posted['i_autocomplete'] : '';
			$field['priority'] = isset($posted['i_priority']) ? $posted['i_priority'] : '';

			$field['custom'] = isset($posted['i_custom']) && $posted['i_custom'] ? 1 : 0;

			return $field;
		}

		private function get_current_checkout_fields()
		{

			$this->currentFields = WOOCFCL()->get_checkout_fields($this->currentSection);
			return $this->currentFields;
		}

		private function set_field_property_to_form()
		{

			$this->currentFieldsForm = WOOCFCL()->set_field_property_to_form($this->currentSection);

			return $this->currentFieldsForm;
		}
		//form field

		/** *************************** RENDER TEST PAGE ********************************
		 *******************************************************************************
		* This function renders the admin page and the example list table. Although it's
		* possible to call prepare_items() and display() from the constructor, there
		* are often times where you may need to include logic here between those steps,
		* so we've instead called those methods explicitly. It keeps things flexible, and
		* it's the way the list tables are used in the WordPress core.
		*/
		public function tt_render_list_page()
		{

			//Create an instance of our package class...
			$testListTable = new TT_Example_List_Table();
			//Fetch, prepare, sort, and filter our data...
			$testListTable->prepare_items();

			?>
				<div class="wrap">

					<div id="icon-users" class="icon32"><br /></div>
					<h2>List Table Test</h2>

					<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
						<p>This page demonstrates the use of the <tt><a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WP_List_Table</a></tt> class in plugins.</p>
						<p>For a detailed explanation of using the <tt><a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WP_List_Table</a></tt>
							class in your own plugins, you can view this file <a href="<?php echo admin_url('plugin-editor.php?plugin=' . plugin_basename(__FILE__)); ?>" style="text-decoration:none;">in the Plugin Editor</a> or simply open <tt style="color:gray;"><?php echo __FILE__ ?></tt> in the PHP editor of your choice.</p>
						<p>Additional class details are available on the <a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WordPress Codex</a>.</p>
					</div>

					<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
					<form id="movies-filter" method="get">
						<!-- For plugins, we also need to ensure that the form posts back to our current page -->
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
						<!-- Now we can render the completed list table -->
						<?php $testListTable->display()?>
					</form>

				</div>
			<?php
	}

		/**
		 * Callback function for 'wp_ajax__ajax_fetch_custom_list' action hook.
		 *
		 * Loads the Custom List Table Class and calls ajax_response method
		 */
		public function _ajax_fetch_custom_list_callback()
		{

			if (!WOOCFCL_Utils::plug_is_current_screen()) {
				return;
			}

			$wp_list_table = new TT_Example_List_Table();
			$wp_list_table->ajax_response();
		}

		/**
		 * This function adds the jQuery script to the plugin's page footer
		 */
		public function ajax_script()
		{
			if (!WOOCFCL_Utils::plug_is_current_screen()) {
				return;
			}

			?>
				<script type="text/javascript">
					(function($) {

						list = {

							/**
							 * Register our triggers
							 *
							 * We want to capture clicks on specific links, but also value change in
							 * the pagination input field. The links contain all the information we
							 * need concerning the wanted page number or ordering, so we'll just
							 * parse the URL to extract these variables.
							 *
							 * The page number input is trickier: it has no URL so we have to find a
							 * way around. We'll use the hidden inputs added in TT_Example_List_Table::display()
							 * to recover the ordering variables, and the default paged input added
							 * automatically by WordPress.
							 */
							init: function() {

								// This will have its utility when dealing with the page number input
								var timer;
								var delay = 500;

								// Pagination links, sortable link
								$('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
									// We don't want to actually follow these links
									e.preventDefault();
									// Simple way: use the URL to extract our needed variables
									var query = this.search.substring(1);

									var data = {
										paged: list.__query(query, 'paged') || '1',
										order: list.__query(query, 'order') || 'asc',
										orderby: list.__query(query, 'orderby') || 'title'
									};
									list.update(data);
								});

								// Page number input
								$('input[name=paged]').on('keyup', function(e) {

									// If user hit enter, we don't want to submit the form
									// We don't preventDefault() for all keys because it would
									// also prevent to get the page number!
									if (13 == e.which)
										e.preventDefault();

									// This time we fetch the variables in inputs
									var data = {
										paged: parseInt($('input[name=paged]').val()) || '1',
										order: $('input[name=order]').val() || 'asc',
										orderby: $('input[name=orderby]').val() || 'title'
									};

									// Now the timer comes to use: we wait half a second after
									// the user stopped typing to actually send the call. If
									// we don't, the keyup event will trigger instantly and
									// thus may cause duplicate calls before sending the intended
									// value
									window.clearTimeout(timer);
									timer = window.setTimeout(function() {
										list.update(data);
									}, delay);
								});
							},

							/** AJAX call
							 *
							 * Send the call and replace table parts with updated version!
							 *
							 * @param    object    data The data to pass through AJAX
							 */
							update: function(data) {
								$.ajax({
									// /wp-admin/admin-ajax.php
									url: ajaxurl,
									// Add action and nonce to our collected data
									data: $.extend({
											_ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
											action: '_ajax_fetch_custom_list',
										},
										data
									),
									// Handle the successful result
									success: function(response) {

										// WP_List_Table::ajax_response() returns json
										var response = $.parseJSON(response);

										// Add the requested rows
										if (response.rows.length)
											$('#the-list').html(response.rows);
										// Update column headers for sorting
										if (response.column_headers.length)
											$('thead tr, tfoot tr').html(response.column_headers);
										// Update pagination for navigation
										if (response.pagination.bottom.length)
											$('.tablenav.top .tablenav-pages').html($(response.pagination.top).html());
										if (response.pagination.top.length)
											$('.tablenav.bottom .tablenav-pages').html($(response.pagination.bottom).html());

										// Init back our event handlers
										list.init();
									}
								});
							},

							/**
							 * Filter the URL Query to extract variables
							 *
							 * @see http://css-tricks.com/snippets/javascript/get-url-variables/
							 *
							 * @param    string    query The URL query part containing the variables
							 * @param    string    variable Name of the variable we want to get
							 *
							 * @return   string|boolean The variable value if available, false else.
							 */
							__query: function(query, variable) {

								var vars = query.split("&");
								for (var i = 0; i < vars.length; i++) {
									var pair = vars[i].split("=");
									if (pair[0] == variable)
										return pair[1];
								}
								return false;
							},
						}

						// Show time!
						list.init();

					})(jQuery);
				</script>
	<?php
	}
	}

endif;

class TT_Example_List_Table extends WP_List_Table
{

    /**
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query().
     *
     * @var array
     */

    public $example_data = array(
        array(
            'ID' => 1,
            'title' => '300',
            'rating' => 'R',
            'director' => 'Zach Snyder',
        ),
        array(
            'ID' => 2,
            'title' => 'Eyes Wide Shut',
            'rating' => 'R',
            'director' => 'Stanley Kubrick',
        ),
        array(
            'ID' => 3,
            'title' => 'Moulin Rouge!',
            'rating' => 'PG-13',
            'director' => 'Baz Luhrman',
        ),
        array(
            'ID' => 4,
            'title' => 'Snow White',
            'rating' => 'G',
            'director' => 'Walt Disney',
        ),
        array(
            'ID' => 5,
            'title' => 'Super 8',
            'rating' => 'PG-13',
            'director' => 'JJ Abrams',
        ),
        array(
            'ID' => 6,
            'title' => 'The Fountain',
            'rating' => 'PG-13',
            'director' => 'Darren Aronofsky',
        ),
        array(
            'ID' => 7,
            'title' => 'Watchmen',
            'rating' => 'R',
            'director' => 'Zach Snyder',
        ),
        array(
            'ID' => 8,
            'title' => 'The Descendants',
            'rating' => 'R',
            'director' => 'Alexander Payne',
        ),
        array(
            'ID' => 9,
            'title' => 'Moon',
            'rating' => 'R',
            'director' => 'Duncan Jones',
        ),
        array(
            'ID' => 10,
            'title' => 'Elysium',
            'rating' => 'R',
            'director' => 'Neill Blomkamp',
        ),
        array(
            'ID' => 11,
            'title' => 'Source Code',
            'rating' => 'PG-13',
            'director' => 'Duncan Jones',
        ),
        array(
            'ID' => 12,
            'title' => 'Django Unchained',
            'rating' => 'R',
            'director' => 'Quentin Tarantino',
        ),
    );

    /**
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     */
    public function __construct()
    {

        global $status, $page;

        //Set parent defaults
        parent::__construct(
            array(
                //singular name of the listed records
                'singular' => 'state',
                //plural name of the listed records
                'plural' => 'states',
                //does this table support ajax?
                'ajax' => true,
            )
        );
    }

    /**
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     *
     * @return string Text or HTML to be placed inside the column <td>
     */
    public function column_default($item, $column_name)
    {

        switch ($column_name) {

            case 'Name':
            case 'CodReg':
                return $item[$column_name];
            default:
                //Show the whole array for troubleshooting purposes
                return print_r($item, true);
        }
    }

    /**
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     * @see WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     *
     * @return string Text to be placed inside the column <td> (movie title only)
     */
    public function column_title($item)
    {

        //Build row actions
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['ID']),
            'delete' => sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['ID']),
        );

        //Return the title contents
        return sprintf(
            '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/
            $item['Name'],
            /*$2%s*/
            $item['ID'],
            /*$3%s*/
            $this->row_actions($actions)
        );
    }

    /**
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     *
     * @return string Text to be placed inside the column <td> (movie title only)
     */
    public function column_cb($item)
    {

        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'], //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/
            $item['ID']//The value of the checkbox should be the record's id
        );
    }

    /**
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::single_row_columns()
     *
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     */
    public function get_columns()
    {

        // return $columns = array(
        //     'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
        //     'title'        => 'Title',
        //     'rating'    => 'Rating',
        //     'director'    => 'Director'
        // );

        $columns = [
            'cb' => '<input type="checkbox" />',
            'IdID' => WOOCFCL_Utils::t('ID'),
            'Name' => WOOCFCL_Utils::t('Name'),
            'CodReg' => WOOCFCL_Utils::t('CodReg'),
            'CodINE' => WOOCFCL_Utils::t('CodINE'),
            // 'fifth_column_name' => _WOOCFCL_Utils::t_('Fifth Column Name') ,
            // 'sicth_column_name' => WOOCFCL_Utils::t('Sixth Column Name') ,
            // 'created' => WOOCFCL_Utils::t('Date', 'ux')
        ];
        return $columns;
    }

    /**
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */
    public function get_sortable_columns()
    {

        return $sortable_columns = array(
            'ID' => array('ID', false),
            'Name' => array('Name', false), //true means it's already sorted
            'CodReg' => array('CodReg', false),
            'CodINE' => array('CodINE', false),
        );
    }

    /**
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     */
    public function get_bulk_actions()
    {

        return $actions = array(
            'delete' => 'Delete',
        );
    }

    /**
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     */
    public function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    /**
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */
    public function prepare_items()
    {

        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 30;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        // $data = $this->example_data;

        $data = array_values(WOOCFCL()->states->states_Extend);
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a, $b)
        {

            //If no sort, default to title
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title';
            //If no order, default to asc
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            //Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
            //Send final sort direction to usort
            return ('asc' === $order) ? $result : -$result;
        }
        usort($data, 'usort_reorder');

        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/

        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);

        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(
            array(
                //WE have to calculate the total number of items
                'total_items' => $total_items,
                //WE have to determine how many items to show on a page
                'per_page' => $per_page,
                //WE have to calculate the total number of pages
                'total_pages' => ceil($total_items / $per_page),
                // Set ordering values if needed (useful for AJAX)
                'orderby' => !empty($_REQUEST['orderby']) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'title',
                'order' => !empty($_REQUEST['order']) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc',
            )
        );
    }

    /**
     * Display the table
     * Adds a Nonce field and calls parent's display method
     *
     * @since 3.1.0
     * @access public
     */
    public function display()
    {

        wp_nonce_field('ajax-custom-list-nonce', '_ajax_custom_list_nonce');

        echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
        echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';

        parent::display();
    }

    /**
     * Handle an incoming ajax request (called from admin-ajax.php)
     *
     * @since 3.1.0
     * @access public
     */
    public function ajax_response()
    {

        check_ajax_referer('ajax-custom-list-nonce', '_ajax_custom_list_nonce');

        $this->prepare_items();

        extract($this->_args);
        extract($this->_pagination_args, EXTR_SKIP);

        ob_start();
        if (!empty($_REQUEST['no_placeholder'])) {
            $this->display_rows();
        } else {
            $this->display_rows_or_placeholder();
        }

        $rows = ob_get_clean();

        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();

        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();

        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();

        $response = array('rows' => $rows);
        $response['pagination']['top'] = $pagination_top;
        $response['pagination']['bottom'] = $pagination_bottom;
        $response['column_headers'] = $headers;

        if (isset($total_items)) {
            $response['total_items_i18n'] = sprintf(_n('1 item', '%s items', $total_items), number_format_i18n($total_items));
        }

        if (isset($total_pages)) {
            $response['total_pages'] = $total_pages;
            $response['total_pages_i18n'] = number_format_i18n($total_pages);
        }

        die(json_encode($response));
    }
}
