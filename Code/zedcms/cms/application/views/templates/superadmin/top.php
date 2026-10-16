<div id="top" class="zed-top">
	<div class="row">
		<div class="col-sm-12">
			<div class="zed-breadcrumb">
				<ul class="breadcrumb">
					<?php echo $bread_crumb;?>
					
				</ul>
			</div>
		</div>
		<div class="col-sm-12 pr0">
			<div class="dropdown pull-right">
				<a href="javascript:void(0);" data-toggle="dropdown"><?php echo  ucfirst($this->ion_auth->user()->row()->first_name);?></a>
				<ul class="dropdown-menu">
					<li><a href="<?php echo base_url();?>dashboard/profile">Profile</a></li>
					<li><a href="<?php echo base_url();?>auth/logout">Logout</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>