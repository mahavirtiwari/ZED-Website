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
						echo form_open_multipart('sector/addedit',array('id'=>'addedit_sector','onsubmit'=> 'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['sectorid']) ? form_hidden('id', $details[0]['sectorid']) : '');?>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group" id="code">
									<label>Sector Code</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 's_code' );
									}
									elseif( isset($details) &&  count($details) > 0)
									{
										$val = $details[0]['code'];
									}
									else
									{
										$val = '';
									}
									$element = array(
										'name'	=>	's_code',
										'id'	=>	'sector_code',
										'class' => 	'form-control',
										'value'	=>	$val,
									//'required' => 'required',
										);			
									echo form_input($element);
									?>
								</div>
							</div>
							<div class="col-sm-18">
								<div class="form-group" id="name">
									<label>Sector Name</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 's_name' );
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
										'name'	=>	's_name',
										'id'	=>	'sector_name',
										'class' => 	'form-control',
										'value'	=>	$val,
									//'required' => 'required',
										);			
									echo form_input($element);
									?>
								</div>
							</div>
						</div>
						<div class="form-group ">
						<label>Sector Parent</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 's_parent' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['parentcode'];
							}
							else
							{
								$val = '';
							}
							$categories=$parent;
							$js = 'id="tabbedpage" class="form-control" ';
							echo form_dropdown('s_parent',$categories,$val,$js);?>
						</div>
						<div class="form-group">
							<label class="zed-switch">
								<input type="checkbox"  name="s_status"
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
	var code=$('#sector_code').val();
	var name=$('#sector_name').val();
	if(code!='')
	{
		$("#code").removeClass('has-error');
	}
	else
	{
		$("#code").addClass('has-error');
	}
	if(name!='')
	{
		$("#name").removeClass('has-error');
	}
	else
	{
		$("#name").addClass('has-error');
	}
	if(code!='' && name!='')
	{
		$('#addedit_sector').submit();
	}
	else
	{
		$.toasty('Please review the information you have entered! ','danger');
		return false;
	}
}
</script> 