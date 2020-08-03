<?php if (!WOOCFCL_Utils::array_empty($this->tabs)): ?>
<h2 class="woocfcl-tabs nav-tab-wrapper woo-nav-tab-wrapper">
<?php foreach( $this->tabs as $id => $label ):
			$active = ( $this->currentTab == $id ) ? 'nav-tab-active' : '';
			$label  = WOOCFCL_Utils::translate($label); ?>
			<a class="nav-tab <?php echo $active; ?>" href="<?php echo $this->get_admin_url($id); ?>"><?php echo $label; ?></a>
<?php endforeach; ?>
</h2>	
<?php endif; ?>