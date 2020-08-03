<tr class="row_<?php echo $index; echo $enabled ? '' : ' woocfcl-disabled' ?>">
	<td width="1%" class="sort ui-sortable-handle">
		<input type="hidden" name="f_name[<?php echo $index; ?>]" class="f_name" value="<?php echo esc_attr($name); ?>" />
		<input type="hidden" name="f_name_new[<?php echo $index; ?>]" class="f_name_new" value="" />
		<input type="hidden" name="f_order[<?php echo $index; ?>]" class="f_order" value="<?php echo $index; ?>" />
		<input type="hidden" name="f_deleted[<?php echo $index; ?>]" class="f_deleted" value="0" />
		<input type="hidden" name="f_enabled[<?php echo $index; ?>]" class="f_enabled" value="<?php echo $enabled; ?>" />
		<input type="hidden" name="f_props[<?php echo $index; ?>]" class="f_props" value='<?php echo $json; ?>' />
		<input type="hidden" name="f_options[<?php echo $index; ?>]" class="f_options" value='<?php echo $options; ?>' />
	</td>
	<td class="td_select"><input type="checkbox" name="select_field"/></td>
	<td class="td_name"><?php echo esc_attr( $name ) ?></td>
	<th class="td_priority"><?php echo $priority; ?></th>
	<td class="td_type"><?php echo $type; ?></td>
	<td class="td_label"><?php echo $label; ?></td>
	<td class="td_placeholder"><?php echo $placeholder; ?></td>
	<td class="td_validate"><?php echo $validate; ?></td>
	<td class="td_required status"><?php echo $required ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-'; ?></td>
	<td class="td_enabled status"><?php echo $enabled ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-'; ?></td>
	<td class="td_edit action">
		<button type="button" class="button action-btn f_edit_btn" <?php echo($enabled ? '' : 'disabled') ?> 
		onclick="woocfclOpenEditFieldForm(this, <?php echo $index; ?>)"><?php WOOCFCL_Utils::etranslate('Edit'); ?></button>
	</td>
</tr>