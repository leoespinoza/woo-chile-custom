<?php 
	include_once('parts/header.php' ); 
	include_once( 'parts/review.php' ); 
?>
<br class="clear" />
<div class="wrap woocommerce">
	<?php  


				// echo ' <br/><br/><br/>'.$this->currentSection.'<br/>  '.print_r($this->currentFields); 
				// // //$v = var_export(WOOCFCL()->app->getTableOption());
				// print_r(WOOCFCL()->billing->fieldsPlugin);
			// WOOCFCL()->app->print_to_array();
			// //WOOCFCL()->wpapp->printToArray();
			// //WOOCFCL()->wpstates->print_to_array();
			// WOOCFCL()->states->print_to_array();
			WOOCFCL()->wpbilling->print_to_array();
			
        //    WOOCFCL()->form->printToArray();
				// WOOCFCL()->shipping->printToArray();
				?></div>           
<div class="wrap woocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>
<br class="clear" />
<?php 
	echo $this->currentMessage; 
	include_once( 'parts/tabs.php' );
	include_once( 'parts/sections.php' ); 
    if ($this->currentTab=='states') {
		$this->tt_render_list_page();
	}  
	else {
		include_once( 'parts/fields.php' ); 
		$this->form_display=$this->form_new;
		include( 'parts/fields-popup.php' );
		$this->form_display=$this->form_edit;	
		include( 'parts/fields-popup.php' );
	}	

?>