<?php if (!WOOCFCL_Utils::array_empty($this->sections)): 
	$array_keys = array_keys( $this->sections );?>
	<ul class="woocfcl-sections">
		<?php foreach( $this->sections as $id => $label ):
			$active = ( $this->currentSection == $id ) ? 'current' : '';
			$label  = WOOCFCL_Utils::translate($label); 
			$url = $this->get_admin_url($this->currentTab, sanitize_title($id));	?>
			<li><a class="<?php echo $active; ?>" href="<?php echo $url; ?>"><?php echo $label; ?></a><?php echo (end( $array_keys ) == $id ? '' : '|') ; ?></li>
		<?php endforeach; ?>
	</ul>	
<?php endif; ?>

