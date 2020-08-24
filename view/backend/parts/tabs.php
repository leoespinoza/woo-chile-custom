
<div class="row botton0">
	<div class="col s12">
<?php if (!WOOCFCL_Utils::array_empty($this->tabs)): ?>
<ul class="tabs">
	<?php foreach( $this->tabs as $id => $label ):
				$active = ( $this->currentTab == $id ) ? 'active' : ''; ?>
				<li class="tab col s3"><a target="_self" class="<?php echo $active; ?>" href="<?php echo $this->get_admin_url($id); ?>"><?php echo $label; ?></a></li>
	<?php endforeach; ?>
</ul>

<?php endif; ?>
</div>
</div>