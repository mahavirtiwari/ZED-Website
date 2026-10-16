<div class="container form-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
						<?php 	
						echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
						echo form_open_multipart(base_url().'global_config/address',array('id'=>'adress'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group">
							<label>Adress</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'address' );
							}
							elseif( isset($details['address']) &&  count($details) > 0)
							{
								$val = $details['address']; 
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'address',
								'id'	=>	'address',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								'rows' => 4,   
								'cols' => 5
									//'required' => 'required',
								);			
							echo form_textarea($element);


							?>
						</div>
						<div class="form-group">
							<label>Label Name 1</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'label_name_1' );
							}
							elseif( isset($details['label_name_1']) &&  count($details) > 0)
							{
								$val = $details['label_name_1'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'label_name_1',
								'id'	=>	'label_name_1',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Phone Number 1</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'zed_phone_1' );
							}
							elseif( isset($details['zed_phone_1']) &&  count($details) > 0)
							{
								$val = $details['zed_phone_1'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'zed_phone_1',
								'id'	=>	'zed_phone_1',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Phone Number 2</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'zed_phone_2' );
							}
							elseif( isset($details['zed_phone_2']) &&  count($details) > 0)
							{
								$val = $details['zed_phone_2'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'zed_phone_2',
								'id'	=>	'zed_phone_2',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Email Id</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'zed_email_id' );
							}
							elseif( isset($details['zed_email_id']) &&  count($details) > 0)
							{
								$val = $details['zed_email_id'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'zed_email_id',
								'id'	=>	'zed_email_id',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Label Name 2</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'label_name_2' );
							}
							elseif( isset($details['label_name_2']) &&  count($details) > 0)
							{
								$val = $details['label_name_2'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'label_name_2',
								'id'	=>	'label_name_2',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Phone Number 1</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'tp_phone_1' );
							}
							elseif( isset($details['tp_phone_1']) &&  count($details) > 0)
							{
								$val = $details['tp_phone_1'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'tp_phone_1',
								'id'	=>	'tp_phone_1',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Phone Number 2</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'tp_phone_2' );
							}
							elseif( isset($details['tp_phone_2']) &&  count($details) > 0)
							{
								$val = $details['tp_phone_2'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'tp_phone_2',
								'id'	=>	'tp_phone_2',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Phone Number 3</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'tp_phone_3' );
							}
							elseif( isset($details['tp_phone_3']) &&  count($details) > 0)
							{
								$val = $details['tp_phone_3'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'tp_phone_3',
								'id'	=>	'tp_phone_3',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Email Id</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'tp_email_id' );
							}
							elseif( isset($details['tp_email_id']) &&  count($details) > 0)
							{
								$val = $details['tp_email_id'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'tp_email_id',
								'id'	=>	'tp_email_id',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Button Name</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'button_name' );
							}
							elseif( isset($details['button_name']) &&  count($details) > 0)
							{
								$val = $details['button_name'];
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
								'type'=>'text',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Button URL</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'button_url' );
							}
							elseif( isset($details['button_url']) &&  count($details) > 0)
							{
								$val = $details['button_url'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'button_url',
								'id'	=>	'button_url',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'text',
								);			
							echo form_input($element);


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
