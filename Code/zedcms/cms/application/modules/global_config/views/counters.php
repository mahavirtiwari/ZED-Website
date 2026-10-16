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
						echo form_open_multipart(base_url().'global_config/counters',array('id'=>'counters'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group">
							<label>MSMEs Participated in Awareness Programmes</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'masmes_participated_in_awareness_programmes' );
							}
							elseif( isset($details['masmes_participated_in_awareness_programmes']) &&  count($details) > 0)
							{
								$val = $details['masmes_participated_in_awareness_programmes']; 
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'masmes_participated_in_awareness_programmes',
								'id'	=>	'masmes_participated_in_awareness_programmes',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'number',
									//'required' => 'required',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>MSME Registered</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'msme_registered' );
							}
							elseif( isset($details['msme_registered']) &&  count($details) > 0)
							{
								$val = $details['msme_registered'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'msme_registered',
								'id'	=>	'msme_registered',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'number',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Pledge</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'pledge' );
							}
							elseif( isset($details['pledge']) &&  count($details) > 0)
							{
								$val = $details['pledge'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'pledge',
								'id'	=>	'pledge',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'number',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Bronze Certified</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'bronze_certified' );
							}
							elseif( isset($details['bronze_certified']) &&  count($details) > 0)
							{
								$val = $details['bronze_certified'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'bronze_certified',
								'id'	=>	'bronze_certified',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'number',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Silver Certified</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'silver_certified' );
							}
							elseif( isset($details['silver_certified']) &&  count($details) > 0)
							{
								$val = $details['silver_certified'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'silver_certified',
								'id'	=>	'silver_certified',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'number',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Gold Certified</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'gold_certified' );
							}
							elseif( isset($details['gold_certified']) &&  count($details) > 0)
							{
								$val = $details['gold_certified'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'gold_certified',
								'id'	=>	'gold_certified',
								'class' => 	'form-control',
								'value'	=>	$val,
								'type'=>'number',
								);			
							echo form_input($element);


							?>
						</div>
						<div class="form-group">
							<label>Status</label>
							<?php 		   
							if( isset($details) &&  isset($details['dashboard_status']))
							{
								$val = $details['dashboard_status'];
							}
							else
							{
								$val = '';
							}
							$categories=array('1'=>'Active','0'=>'In Active');
							$js = 'id="tabbedpage" class="form-control" ';
							echo form_dropdown('dashboard_status',$categories,$val,$js);?>
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
