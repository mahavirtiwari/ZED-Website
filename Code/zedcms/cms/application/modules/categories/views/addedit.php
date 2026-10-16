<div class="container form-page">
	<div class="row">
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
					<?php 	
					echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
					echo form_open_multipart('categories/addedit',array('id'=>'addedit_category'));
				?>
				<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
							<div class="form-group">
								<label>Category Name</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 'c_name' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['category_name'];
								}
								else
								{
									$val = '';
								}
								$element = array(
									'name'	=>	'c_name',
									'id'	=>	'category_name',
									'class' => 	'form-control',
									'value'	=>	$val,
									//'required' => 'required',
									);			
								echo form_input($element);

								echo (isset($form_errors)&&isset($form_errors['name']) ? $form_errors['name'] : '');
								?>
							</div>
							<div class="form-group <?php echo (isset($form_errors)&&isset($form_errors['description']) ? 'has-error' : '' );?>">
							</div>
							<div class="form-group">
								<label class="zed-switch">
									<input type="checkbox"  name="c_status"
										<?php if(isset($details)){
			                         	if($details[0]['status'] ==1)
			                         	{
			                         	echo 'checked="checked"';
			                         	}
			                        	}
			                        	else
			                        	{
			                        	echo 'checked=""';
			                        	}?>>
									<span></span>
									<b></b>
								</label>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary" name="zed-save">Save</button>
								<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->
								<a href="<?php echo base_url();?>categories" class="btn btn-default">Cancel</a>
							</div>
					<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
