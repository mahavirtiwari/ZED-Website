<div class="container zed-profile">
	<div class="row">
		<div class="col-sm-12">
		<?php if($this->session->flashdata('message')){
			echo "<div class='alert alert-success fade in'><a href='#' class='close' data-dismiss='alert'>&times;</a>".$this->session->flashdata('message')."</div>";
			}?>
			<?php echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>', '</div>'); ?>
			<div class="panel panel-default">
				<div class="panel-body">
					<h2><?php echo ucfirst($user->first_name); ?>&nbsp;&nbsp;<?php echo ucfirst($user->last_name); ?></h2>
					<?php echo form_open_multipart( base_url().'dashboard/profile');?>
					<div class="form-group">
						<label>Role</label>
						<span class="form-control-static text-left"><?php echo ucfirst($this->ion_auth->get_users_groups($user->user_id)->row()->name); ?></span>
					</div>
					<div class="row">
						<div class="col-sm-12">
						<div class="form-group">
							<label>Full Name</label>
						<input type="text" class="form-control" placeholder="Your Full Name" name="first_name" value="<?php echo $user->first_name; ?>">
						</div>
						</div>
						<div class="col-sm-12">
						<div class="form-group">
							<label>Last Name</label>
							<input type="text" class="form-control" name="last_name" value="<?php echo $user->last_name;?>">
						</div>
						</div>
					</div>
					<div class="form-group">
						<label>New Password (If you want to Change)</label>
						<input type="password" class="form-control" name="password">
					</div>
					<!-- <div class="form-group">
						<label>Repeat New Password</label>
						<input type="password" class="form-control" name="password_confirm">
					</div> -->
					<button type="submit" class="btn btn-primary">Update</button> 
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>
</div>