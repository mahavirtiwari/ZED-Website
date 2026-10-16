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
						echo form_open_multipart('gallery/addedit',array('id'=>'addedit_gallery','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="gtitle">
							<label>Title</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'g_title' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['media_title'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'g_title',
								'id'	=>	'g_title',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="simage">
									<label>Image</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 'g_image' );
									}
									elseif( isset($details) &&  count($details) > 0)
									{
										$val = $details[0]['media_slug'];
									}
									else
									{
										$val =base_url().'slider/default.png';

									}
									$element = array(
										'name'	=>	'g_image',
										'id'	=>	'gallery_image',
										'value'	=>	$val,
										);			
									echo form_upload($element);
									?>
									<?php //if( isset($details) &&  count($details) > 0) { ?>
									<input type="hidden" name="existed_file"  id="existed_file" value="<?php if( isset($details) &&  count($details) > 0) { echo $details[0]['media_slug']; }?>">
									<?php // } ?>
								</div>


							</div>
							<div class="col-sm-12">
								<img id="img-preview" src="<?php echo $val?>" class="img-responsive pull-right">
							</div>
						</div>
						<div class="form-group" id="gcaption">
							<label>Image Caption</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'g_caption' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['media_caption'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'g_caption',
								'id'	=>	'g_caption',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group" id="galt">
							<label>SEO Alt Text</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'g_seo_alt_text' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['media_seo_alt_text'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'g_seo_alt_text',
								'id'	=>	'g_seo_alt_text',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group">
							<input type="submit" class="btn btn-primary" name="zed-save" value="Save">
							<a href="<?php echo base_url();?>gallery" class="btn btn-default">Cancel</a>
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

	$("#gallery_image").change(function() {
		readURL(this);
	});
</script>
<script type="text/javascript">
	function validateform(){
		var gimage = $("#gallery_image").val();
		var eimage = $("#existed_file").val();
		var title=$('#g_title').val();
		var caption=$('#g_caption').val();
		var alt=$('#g_seo_alt_text').val();
		if(gimage=='' && eimage=='')
		{
			$.toasty('Image is required! ','danger');
		}
		if(title!='')
		{
			$("#gtitle").removeClass('has-error');	
		}
		else
		{
			$("#gtitle").addClass('has-error');
		}
		if(caption!='')
		{
			$("#gcaption").removeClass('has-error');	
		}
		else
		{
			$("#gcaption").addClass('has-error');
		}
		if(alt!='')
		{
			$("#galt").removeClass('has-error');	
		}
		else
		{
			$("#galt").addClass('has-error');
		}
		if(title!='' && caption!='' && alt!='' && (gimage!=''|| eimage!=''))
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