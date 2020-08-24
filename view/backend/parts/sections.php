<?php  $nav=$this->nav[$this->currentTab]; 
	if (!WOOCFCL_Utils::array_empty($nav)): 
;?>
<div class="row">
	<div class="col s12">
		<!-- Dropdown Structure -->

		<nav>
			<div class="nav-wrapper">
				<!-- <a href="#!" class="brand-logo  hide-on-med-and-down">Opciones</a> -->
				<ul class="left">
				<?php foreach( $nav as $id => $label ):
					$active = ( $this->currentSection == $id ) ? 'active' : '';
					$url = $this->get_admin_url($this->currentTab, sanitize_title($id));	?>
					<li><a target="_self" class="<?php echo $active; ?>" href="<?php echo $url; ?>"><?php echo $label; ?></a></li>
				<?php endforeach; ?>
				</ul>
				<ul class="right">
					<li><a href="sass.html">Sass</a></li>
					<li><a href="badges.html">Components</a></li>
					<li><a href="sass.html"><i class="material-icons">search</i></a></li>
					<li><a href="badges.html"><i class="material-icons">view_module</i></a></li>
					<li><a href="collapsible.html"><i class="material-icons">refresh</i></a></li>
					<li><a href="mobile.html"><i class="material-icons">more_vert</i></a></li>
				<!-- Dropdown Trigger -->
				</ul>
			</div>
		</nav>
	</div>
</div>


<?php endif; ?>