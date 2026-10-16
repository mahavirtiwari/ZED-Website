<div class="container form-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url();?>global_config" class="btn btn-primary">About Us</a>
				<a  href="<?php echo base_url();?>global_config/gallery" class="btn btn-primary" >Gallery SEO</a>
				<a  href="<?php echo base_url();?>global_config/home" class="btn btn-primary">Home Page SEO </a>
				<a  href="<?php echo base_url();?>global_config/counters" class="btn btn-primary">Counters</a>
			</div>
		</div>
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
						<?php 	
						echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
						echo form_open_multipart(base_url().'global_config/home',array('id'=>'home'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group">
							<label>Home Seo Title</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'home_seo_title' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details['home_seo_title'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'home_seo_title',
								'id'	=>	'home_seo_title',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Home Seo Keywords</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'Home_seo_keywords' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details['home_seo_keywords'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'home_seo_keywords',
								'id'	=>	'home_seo_keywords',
								'class' => 	'form-control',
								'value'	=>	$val,
								'rows'  => '8'
								);			
							echo form_textarea($element);


							?>
						</div>
						<div class="form-group">
							<label>Home Seo Description</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'home_seo_description' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details['home_seo_description'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'home_seo_description',
								'id'	=>	'home_seo_description',
								'class' => 	'form-control',
								'value'	=>	$val,
								'rows'  => '8'
								);			
							echo form_textarea($element);


							?>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary" name="zed-save">Save</button>
							<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->

						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
