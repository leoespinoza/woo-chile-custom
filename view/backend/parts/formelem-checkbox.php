<?php
		$name  = isset($data['name']) ? $data['name'] : '';//ok
		$fname = 'i_'.$name;
		$label = isset($data['label']) ? WOOCFCL_Utils::translate($data['label']) : '';

		$field_attr = 'id="'.$fname.'" name="'.$fname.'" value="1"';
		if(isset($data['checked']) && $data['checked']){
			$field_attr .= ' checked="checked"';
		}

		?>
		<input type="checkbox" <?php echo $field_attr; ?> />
        <label for="<?php echo $fname; ?>"><?php echo $label; ?></label><br/>