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
					echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');?>
						<?php 	
						echo form_open_multipart('programs/addedit',array('id'=>'addedit_program','onsubmit'=> 'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="code">
							<label>Name</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'name' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['name'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'name',
								'id'	=>	'name',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="sprofile">
						<label>Image</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'image' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val =  $details[0]['image'];
							}
							else
							{
								$val = '';
							}
							?>
							<input type="file" class="dropify" data-default-file="<?php echo isset($details[0]['image']) && !empty( $details[0]['image']) ? URL_FOLDER. $details[0]['image'] : '';?>"  data-show-remove="false" name="image"/>
						</div>
						<div class="form-group" id="name">
							<label>Caption</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'caption' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['caption'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'caption',
								'id'	=>	'caption',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group ">
						<label>Button Name</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'button_name' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['button_name'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'button_name',
								'id'	=>	'button_name',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group ">
						<label>Link</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'link' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['link'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'link',
								'id'	=>	'link',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="slidersorting">
								<label>Sorting</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 'sorting' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['sorting'];
								}
								else
								{
									$val = '';
								}
								$element = array(
									'name'	=>	'sorting',
									'type'  =>  'number',
									'min'   =>  1,
									'max'   =>  1000,
									'id'	=>	'sorting',
									'class' => 	'form-control',
									'value'	=>	$val,
									//'required' => 'required',
									);			
								echo form_input($element);
								?>
							</div>
							<div class="form-group">
								<label class="zed-switch">
									<input type="checkbox"  name="status"
									<?php if(isset($details) && isset($details[0]['status']) ){
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
							<input type="submit" class="btn btn-primary" name="zed-save" id="zed-save" value="Save">
							<a href="<?php echo base_url();?>sector" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
 function validateform() {
	var name=$('#name').val();
	var caption=$('#caption').val();
	var button_name=$('#button_name').val();
	/*var link=$('#link').val();*/
	var sorting=$('#sorting').val();
	if(name!='')
	{
		$("#name").removeClass('has-error');
	}
	else
	{
		$("#name").addClass('has-error');
	}
	if(caption!='')
	{
		$("#caption").removeClass('has-error');
	}
	else
	{
		$("#caption").addClass('has-error');
	}
	if(button_name!='')
	{
		$("#button_name").removeClass('has-error');
	}
	else
	{
		$("#button_name").addClass('has-error');
	}
	/*if(link!='')
	{
		$("#link").removeClass('has-error');
	}
	else
	{
		$("#link").addClass('has-error');
	}*/
	if(sorting!='')
	{
		$("#sorting").removeClass('has-error');
	}
	else
	{
		$("#sorting").addClass('has-error');
	}
	if(name!='' && caption!='' && button_name!='' && link!='' && sorting!='')
	{
		$('#addedit_program').submit();
	}
	else
	{
		$.toasty('Please review the information you have entered! ','danger');
		return false;
	}
}
</script> 