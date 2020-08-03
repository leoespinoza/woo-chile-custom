<?php
		$name  = isset($data['name']) ? $data['name'] : '';//ok
		$label = isset($data['label']) ? WOOCFCL_Utils::translate($data['label']) : '';
		$options = isset($data['options']) ? $data['options'] : array();
		$options = is_array($options) ? $options : $array();

		?>
	<tr class="<?php echo 'row-'.$name; ?>">                
		<td width="30%"><?php echo $label; ?></td>
		<td>

			<?php foreach($options as $key => $value){ ?>
				<input type="radio" name="<?php echo $name; ?>" value="<?php echo trim($key); ?>"> <?php echo $value; ?>
			<?php } ?>
		</td>
	</tr>
