<?php
    $name  = isset($data['name']) ? $data['name'] : ''; //ok
	$fname = 'i_'.$name;
	$label = isset($data['label']) ? WOOCFCL_Utils::translate($data['label']) : '';

	$field_attr = 'name="'.$fname.'" value=""';
	if(isset($data['placeholder']) && $data['placeholder']){
		$field_attr .= ' placeholder="'.WOOCFCL_Utils::translate($data['placeholder']).'"';
	}
	$field_attr .= ' style="width:250px;"';
	
	?>
	<tr class="<?php echo 'row-'.$name; ?>">                
		<td width="30%"><?php echo $label; ?></td>
		<td><input type="text" <?php echo $field_attr; ?> /></td>
	</tr>
