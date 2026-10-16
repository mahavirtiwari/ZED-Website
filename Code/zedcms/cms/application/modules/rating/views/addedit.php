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
						echo form_open_multipart('rating/addedit',array('id'=>'addedit_sector','onsubmit'=> 'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['rating_id']) ? form_hidden('id', $details[0]['rating_id']) : '');?>
						<div class="form-group" id="ep_name">
							<label>Enterprise Name</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'ep_name' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['ep_name'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'ep_name',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="ep_name">
							<label>Enterprise URL</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'ep_url' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['ep_url'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'ep_url',
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
						<div class="form-group" id="unit_address">
							<label>Unit Address</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'unit_address' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['unit_address'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'unit_address',
								'id'	=>	'unit_address',
								'class' => 	'form-control',
								'value'	=>	$val,
							//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="state">
							<label>State</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'state' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['state'];
							}
							else
							{
								$val = '';
							}
							$states=array('AP' => 'Andhra Pradesh', 'AR' => 'Arunachal Pradesh', 'AS' => 'Assam', 'BR' => 'Bihar', 'CT' => 'Chhattisgarh', 'GA' => 'Goa', 'GJ' => 'Gujarat', 'HR' => 'Haryana', 'HP' => 'Himachal Pradesh', 'JK' => 'Jammu and Kashmir', 'JH' => 'Jharkhand', 'KA' => 'Karnataka', 'KL' => 'Kerala', 'MP' => 'Madhya Pradesh', 'MH' => 'Maharashtra', 'MN' => 'Manipur', 'ML' => 'Meghalaya', 'MZ' => 'Mizoram', 'NL' => 'Nagaland', 'OR' => 'Odisha', 'PB' => 'Punjab', 'RJ' => 'Rajasthan', 'SK' => 'Sikkim', 'TN' => 'Tamil Nadu', 'TG' => 'Telangana', 'TR' => 'Tripura', 'UT' => 'Uttarakhand', 'UP' => 'Uttar Pradesh', 'WB' => 'West Bengal', 'AN' => 'Andaman and Nicobar Islands', 'CH' => 'Chandigarh', 'DN' => 'Dadra and Nagar Haveli', 'DD' => 'Daman and Diu', 'DL' => 'Delhi', 'LD' => 'Lakshadweep', 'PY' => 'Puducherry');
							$states_array=[];
							$states_array['']=['Select State'];
							foreach ($states as $key => $value) {
								$states_array[$value]=$value;
							}
							$js = 'id="tabbedpage" class="form-control" ';
							echo form_dropdown('state',$states_array,$val,$js);?>
						</div>
						<div class="form-group" id="size">
							<label>Size</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'size' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['size'];
							}
							else
							{
								$val = '';
							}
							$sizes=array(''=>'Select Size','micro'=>'Micro','small'=>'Small','medium'=>'Medium');
							$js = 'id="tabbedpage" class="form-control" ';
							echo form_dropdown('size',$sizes,$val,$js);?>
						</div>
						<div class="form-group" id="rating">
							<label>Rating</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'rating' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['rating'];
							}
							else
							{
								$val = '';
							}
							$sizes=array(''=>'Select Rating','Silver'=>'Silver','Bronze'=>'Bronze','Gold'=>'Gold');
							$js = 'id="tabbedpage" class="form-control" ';
							echo form_dropdown('rating',$sizes,$val,$js);?>
						</div>
						<div class="form-group">
							<label class="zed-switch">
								<input type="checkbox"  name="r_status"
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
	var ep_name=$('input[name=ep_name]').val();
	var sector=$('select[name=sector]').val();
	var unit_address=$('input[name=unit_address]').val();
	var state=$('select[name=state]').val();
	var rating=$('select[name=rating]').val();
	var size=$('select[name=size]').val();
	if(ep_name!='')
	{
		$("#ep_name").removeClass('has-error');
	}
	else
	{
		$("#ep_name").addClass('has-error');
	}
	if(sector!='' && sector!=0)
	{
		$("#sector").removeClass('has-error');
	}
	else
	{
		$("#sector").addClass('has-error');
	}
	if(unit_address!='')
	{
		$("#unit_address").removeClass('has-error');
	}
	else
	{
		$("#unit_address").addClass('has-error');
	}
	if(state!='' & state!=0)
	{
		$("#state").removeClass('has-error');
	}
	else
	{
		$("#state").addClass('has-error');
	}
	if(rating!='')
	{
		$("#rating").removeClass('has-error');
	}
	else
	{
		$("#rating").addClass('has-error');
	}
	if(size!='' && size!=0)
	{
		$("#size").removeClass('has-error');
	}
	else
	{
		$("#size").addClass('has-error');
	}
	if(ep_name!='' && sector!='' && rating!='' && size!='' && state!='' && unit_address!='' && size!=0 && rating!=0 && state!=0 && sector!=0)
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