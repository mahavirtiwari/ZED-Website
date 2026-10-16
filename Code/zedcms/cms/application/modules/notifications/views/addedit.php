<div class="container form-page">
	<div class="row">
		<?php if($this->session->flashdata('file_errors')){ ?>
		<div class="alert alert-danger"><?php echo $this->session->flashdata('file_errors');?></div>
		<?php }?>
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
						echo form_open_multipart('notifications/addedit',array('id'=>'addedit_program','onsubmit'=> 'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="code">
							<label>Button Name</label>
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
						<div class="form-group ">
						<label>Notifcation Type</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'type' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['type'];
							}
							else
							{
								$val = '';
							}
							$categories=['file'=>'File','link'=>'Link'];
							$js = 'id="notification_type" class="form-control" ';
							echo form_dropdown('type',$categories,$val,$js);?>
						</div>
						<div class="form-group" id="simage">
							<label>File Upload</label>
							<?php			   
							$element = array(
								'name'	=>	'r_file_upload',
								'id'	=>	'resources_file_upload',
								);			
							echo form_upload($element);
							?>
						</div>
						<div class="form-group hide" id="link-group">
							<label>URL</label>
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
							<a href="<?php echo base_url();?>notifications" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$(window).on('load', function() {
		$('#notification_type').trigger('change');
	});
	$(document).on('change','#notification_type',function(){
		if($(this).val()=='file'){
			$('#simage').removeClass('hide');
			$('#link-group').addClass('hide');
		}
		else{
			$('#simage').addClass('hide');
			$('#link-group').removeClass('hide');

		}
	});
});
 function validateform() {
	var name=$('#name').val();
	var sorting=$('#sorting').val();
	if(name!='')
	{
		$("#name").removeClass('has-error');
	}
	else
	{
		$("#name").addClass('has-error');
	}
	if(sorting!='')
	{
		$("#sorting").removeClass('has-error');
	}
	else
	{
		$("#sorting").addClass('has-error');
	}
	if(name!='' && sorting!='')
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