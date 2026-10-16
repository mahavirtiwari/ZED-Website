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
						echo form_open_multipart('homevideos/addedit',array('id'=>'addedit_gomevideos','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="vtitle">
							<label>Video Title</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'v_title' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['video_title'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'v_title',
								'id'	=>	'video_title',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="vtitle">
							<label>Video Description</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'v_description' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['video_description'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'v_description',
								'id'	=>	'video_description',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_textarea($element);
							?>
						</div>
						<div class="form-group" id="vlink">
							<label>Youtube Link</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'v_link' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['video_link'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'v_link',
								'id'	=>	'video_link',
								'class' => 	'form-control',
								'value'	=>	$val,
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="vcaption">
							<label>Video Caption</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'v_caption' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['video_caption'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'v_caption',
								'id'	=>	'video_caption',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="slidersorting">
							<label>Video Sorting</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'v_sorting' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['video_sorting'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'v_sorting',
								'type'  =>  'number',
								'min'   =>  1,
								'max'   =>  1000,
								'id'	=>	'video_sorting',
								'class' => 	'form-control',
								'value'	=>	$val,
								//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group">
							<label class="zed-switch">
								<input type="checkbox"  name="v_status"
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
							<input type="submit" class="btn btn-primary" name="zed-save" value="Save">
							<a href="<?php echo base_url();?>homevideos" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function validateform(){
		var title=$('#video_title').val();
		var link=$('#video_link').val();
		if(title!='')
		{
			$("#vtitle").removeClass('has-error');	
		}
		else
		{
			$("#vtitle").addClass('has-error');
		}
		if(link!='')
		{
			$("#vlink").removeClass('has-error');	
		}
		else
		{
			$("#vlink").addClass('has-error');
		}
		if(title!='' && link!='')
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