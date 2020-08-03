<?php 
if (!empty($this->currentFieldsForm)): 

	foreach( $this->currentFieldsForm as $namefield => $field ) :
		$this->render_table_field_element($field,'rowtable');
	endforeach; 
endif; ?>