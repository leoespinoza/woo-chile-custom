
<div id="<?php echo $this->form_display['id']; ?>" title="<?php echo $this->form_display['title']; ?>" class="woocfcl-popup-wrapper">
<?php 
	$field_props = WOOCFCL()->form->field_form_props; $padding = 5; $colspan = 2;
?>

<form method="post" id="woocfcl_<?php echo $this->form_display['type']; ?>_field_form" action="">
	<input type="hidden" name="f_action" value="<?php echo $this->form_display['type']; ?>" />
	<input type="hidden" name="i_autocomplete" value="" />
	<input type="hidden" name="i_priority" value="" />
	<input type="hidden" name="i_custom" value="" />
	<input type="hidden" name="i_oname" value="" />
	<input type="hidden" name="i_otype" value="" />
	<input type="hidden" name="i_options_json" value="" />

	<table width="100%">
		<tr>                
			<td colspan="2" class="err_msgs"></td>
		</tr>
		<?php 
		$this->render_form_field_element($field_props['type']);
		$this->render_form_field_element($field_props['name']);
		$this->render_form_field_element($field_props['label']);
		$this->render_form_field_element($field_props['placeholder']);
		$this->render_form_field_element($field_props['default']);
		$this->render_form_field_element($field_props['class']);

		$this->render_form_field_element($field_props['validate']);
		?>
		<tr><td colspan="<?php echo $colspan; ?>" style="padding-top:<?php echo $padding ?>px;"></td></tr>
		<tr class="row-options">
			<td width="30%" valign="top"><?php WOOCFCL_Utils::etranslate('Options'); ?></td>
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="woocfcl-option-list woocfcl-dynamic-row-table"><tbody>
					<tr>
						<td style="width:150px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:140px;"/></td>
						<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>
						<td class="action-cell sort ui-sortable-handle"></td>
					</tr>
				</tbody></table>            	
			</td>
		</tr>
		<tr><td colspan="<?php echo $colspan; ?>" style="padding-top:<?php echo $padding ?>px;"></td></tr>
		<tr class="row-required">
			<td>&nbsp;</td>                     
			<td>
				<?php 
				$this->render_form_field_element($field_props['required']);

				$this->render_form_field_element($field_props['enabled']);
				$this->render_form_field_element($field_props['show_in_email']);
				$this->render_form_field_element($field_props['show_in_order']);
				?>
			</td>
		</tr>                       
	</table>
</form>
</div>