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
						echo form_open_multipart('stakeholders/addedit',array('id'=>'addedit_gallery','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="stitle">
							<label>Stakeholder Title</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'stake_title' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['stakeholder_title'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'stake_title',
								'id'	=>	'stake_title',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Stakeholder Logo</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 'stake_logo' );
									}
									elseif( isset($details) &&  count($details) > 0)
									{
										$val = $details[0]['stakeholder_slug'];
									}
									else
									{
										$val =base_url().'slider/default.png';

									}
									$element = array(
										'name'	=>	'stake_logo',
										'id'	=>	'stakeholder_logo',
										'value'	=>	$val,
										);			
									echo form_upload($element);
									?>
									<?php //if( isset($details) &&  count($details) > 0) { ?>
									<input type="hidden" name="existed_file"  id="existed_file" value="<?php if( isset($details) &&  count($details) > 0) { echo $details[0]['stakeholder_slug']; }?>">
									<?php // } ?>
								</div>


							</div>
							<div class="col-sm-12">
								<img id="img-preview" src="<?php echo $val?>" class="img-responsive pull-right">
							</div>
						</div>
						<div class="form-group" id="scaption">
							<label>Stakeholder Caption</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'stake_caption' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['stakeholder_caption'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'stake_caption',
								'id'	=>	'stake_caption',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="slink">
							<label>Stakeholder Link</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'stake_link' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['stakeholder_link'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'stake_link',
								'id'	=>	'stake_link',
								'class' => 	'form-control',
								'value'	=>	$val,
								);			
							echo form_input($element);
							?>
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
<script>
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#img-preview').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#stakeholder_logo").change(function() {
		readURL(this);
	});
</script>
<script type="text/javascript">
function validateform(){
	var logo = $("#stakeholder_logo").val();
	var eimage = $("#existed_file").val();
	var title=$('#stake_title').val();
	if(logo=='' && eimage=='')
	{
		$.toasty('Logo is required! ','danger');
	}
	if(title!='')
	{
		$("#stitle").removeClass('has-error');	
	}
	else
	{
		$("#stitle").addClass('has-error');
	}
	if(title!='' && (logo!=''|| eimage!=''))
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