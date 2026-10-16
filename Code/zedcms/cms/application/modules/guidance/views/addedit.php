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
						echo form_open_multipart('guidance/addedit',array('id'=>'addedit_sector','onsubmit'=> 'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="d_name">
							<label>Name</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'd_name' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['d_name'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'd_name',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="d_url">
							<label>URL</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'd_url' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['d_url'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'd_url',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group " id='#sector'>
							<label>Sector</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'sector' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['sector'];
							}
							else
							{
								$val = '';
							}
							$categories=$parent;
							$js = 'class="form-control" ';
							echo form_dropdown('sector',$categories,$val,$js);?>
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
							<input type="submit" class="btn btn-primary" name="zed-save" id="zed-save" value="Save">
							<a href="<?php echo base_url();?>guidance" class="btn btn-default">Cancel</a>
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
	var ep_name=$('input[name=d_name]').val();
	var ep_url=$('input[name=d_url]').val();
	var sector=$('select[name=sector]').val();
	if(ep_name!='')
	{
		$("#d_name").removeClass('has-error');
	}
	else
	{
		$("#d_name").addClass('has-error');
	}
	if(sector!='' && sector!=0)
	{
		$("#sector").removeClass('has-error');
	}
	else
	{
		$("#sector").addClass('has-error');
	}
	if(ep_url!='')
	{
		$("#d_url").removeClass('has-error');
	}
	else
	{
		$("#d_url").addClass('has-error');
	}
	if(d_url!='' && sector!='' && d_name!='')
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