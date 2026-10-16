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
						echo form_open_multipart('testimonials/addedit',array('id'=>'addedit_gallery','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="sname">
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
						<label>Profile</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'profile' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val =  $details[0]['profile'];
							}
							else
							{
								$val = '';
							}
							?>
							<input type="file" class="dropify" data-default-file="<?php echo isset($details[0]['profile']) && !empty( $details[0]['profile']) ? URL_FOLDER. $details[0]['profile'] : '';?>"  data-show-remove="false" name="profile"/>
						</div>
						<div class="form-group" id="sdesignation">
						<label>Designation</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'designation' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['designation'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'designation',
								'id'	=>	'designation',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="sorg_name">
						<label>Organization</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'org_name' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['org_name'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'org_name',
								'id'	=>	'org_name',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="slocation">
						<label>Location</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'location' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['location'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'location',
								'id'	=>	'location',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="sdescription">
						<label>Description</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'description' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['description'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'description',
								'id'	=>	'description',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_textarea($element);
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
								<?php if(isset($details) && isset($details[0]['status'])){
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
							<input type="submit" class="btn btn-primary" name="zed-save" value="Save">
							<a href="<?php echo base_url();?>stakeholders" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#description').trumbowyg({
			autogrow: true,
			removeformatPasted: true,
			btns: [
		        ['viewHTML'],
		        ['undo', 'redo'], // Only supported in Blink browsers
		        ['formatting'],
		        ['strong', 'em', 'del'],
		        ['superscript', 'subscript'],
		        ['link'],
		        ['foreColor', 'backColor'],
		        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
		        ['unorderedList', 'orderedList'],
		        ['table'],
		        ['horizontalRule'],
		        ['removeformat'],
		    ],
		});
	});
function validateform(){
	var description=$('#description').val();
	var name=$('#name').val();
	var designation=$('#designation').val();
	var org_name=$('#org_name').val();
	var location=$('#location').val();
	if(name!='')
	{
		$("#sname").removeClass('has-error');	
	}
	else
	{
		$("#sname").addClass('has-error');
	}
	if(designation!='')
	{
		$("#sdesignation").removeClass('has-error');	
	}
	else
	{
		$("#sdesignation").addClass('has-error');
	}
	if(org_name!='')
	{
		$("#sorg_name").removeClass('has-error');	
	}
	else
	{
		$("#sorg_name").addClass('has-error');
	}
	if(location!='')
	{
		$("#slocation").removeClass('has-error');	
	}
	else
	{
		$("#slocation").addClass('has-error');
	}
	if(description!='')
	{
		$("#sdescription").removeClass('has-error');	
	}
	else
	{
		$("#sdescription").addClass('has-error');
	}
	if(name!='' && designation!='' && org_name!='' && location!='' && description!='')
	{
		
		this.submit();
	}
	else
	{
		$.toasty('Please review the information you have entered! ','danger');
		return false;
	}
	
}
</script> 