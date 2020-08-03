

<?php if (!empty($this->currentSection)): ?>
<tr>	
	<th colspan="6">
		<button type="button" class="button button-primary" onclick="woocfclOpenNewFieldForm('<?php echo $this->currentSection; ?>')">+ <?php WOOCFCL_Utils::etranslate( 'Add field' ); ?></button>
		<button type="button" class="button" onclick="woocfclRemoveSelectedFields()"><?php WOOCFCL_Utils::etranslate('Remove'); ?></button>
		<button type="button" class="button" onclick="woocfclEnableSelectedFields()"><?php WOOCFCL_Utils::etranslate('Enable'); ?></button>
		<button type="button" class="button" onclick="woocfclDisableSelectedFields()"><?php WOOCFCL_Utils::etranslate('Disable'); ?></button>
	</th>
	<th colspan="4">
		<input type="submit" name="save_fields" class="button-primary" value="<?php WOOCFCL_Utils::etranslate( 'Save changes' ) ?>" style="float:right" />
		<input type="submit" name="reset_fields" class="button" value="<?php WOOCFCL_Utils::etranslate( 'Reset to default fields') ?>" style="float:right; margin-right: 5px;" 
		onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.');"/>
	</th>
</tr>
<?php endif; ?>