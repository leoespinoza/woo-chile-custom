<?php
		$name  = isset($data['name']) ? $data['name'] : '';//ok
		$fname = isset($data['multiple']) && $data['multiple'] ? 'i_'.$name.'[]' : 'i_'.$name;
		$label = isset($data['label']) ? WOOCFCL_Utils::translate($data['label']) : '';
		$options = isset($data['options']) ? $data['options'] : array();
		$options = is_array($options) ? $options : $array();

		$field_attr = 'name="'.$fname.'"';
		if(isset($data['onchange']) && $data['onchange']){
			$field_attr .= ' onchange="'.$data['onchange'].'"';
		}

		if(isset($data['placeholder']) && $data['placeholder']){
			$field_attr .= ' data-placeholder="'.WOOCFCL_Utils::translate($data['placeholder']).'"';
		}

		if(isset($data['multiple']) && $data['multiple']){
			$field_attr .= ' multiple="multiple"';
			$field_attr .= ' class="woocfcl-enhanced-multi-select"';
			$field_attr .= ' style="width:250px; height:30px;"';
		}else{
			$field_attr .= ' style="width:250px;"';
		}

		?>
	<tr class="<?php echo 'row-'.$name; ?>">                
		<td width="30%"><?php echo $label; ?></td>
		<td>
			<select <?php echo $field_attr; ?> >
			<?php foreach($options as $key => $value){ ?>
				<option value="<?php echo trim($key); ?>"><?php echo $value; ?></option>
			<?php } ?>
			</select>
		</td>
	</tr>
