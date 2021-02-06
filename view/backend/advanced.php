<?php 
	include_once('parts/loader.php' ); 
?>
<div class="container">
<?php 
	include_once('parts/header.php' ); 
	include_once( 'parts/review.php' ); 
?>
<div class="wrap woocommerce">
	<?php  
			// echo ' <br/><br/><br/>'.$this->currentSection.'<br/>  '.print_r($this->currentFields); 
			// // //$v = var_export(WOOCFCL()->app->getTableOption());
			// print_r(WOOCFCL()->billing->fieldsPlugin);
			// WOOCFCL()->app->print_to_array();
			// //WOOCFCL()->wpapp->printToArray();
			// WOOCFCL()->states->print_to_array();
			// WOOCFCL()->cities->print_to_array();
			// WOOCFCL()->wpadditional->print_to_array();
			
        //    WOOCFCL()->form->printToArray();
				// WOOCFCL()->shipping->printToArray();
	?>
</div>           
<!-- <div class="wrapwoocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>
<br class="clear" /> -->
<?php 
	echo $this->currentMessage; 
	include_once( 'parts/tabs.php' );
	include_once( 'parts/sections.php' ); 
    if ($this->currentSection=='states') {
		// $this->tt_render_list_page();
		// $places = json_encode( $this->get_places() );

		include_once( 'parts/fields-states-datatable.php' ); 
	}  
	else {
		include_once( 'parts/fields.php' ); 
		$this->form_display=$this->form_new;
		include( 'parts/fields-popup.php' );
		$this->form_display=$this->form_edit;	
		include( 'parts/fields-popup.php' );
	}	

?>
</div>