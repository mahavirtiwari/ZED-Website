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
						echo form_open_multipart('ratingimages/addedit',array('id'=>'addedit_resources'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="simage">
							<label>Image Upload</label>
							<?php			   
							$element = array(
								'name'	=>	'ratingprocess_image_upload',
								'id'	=>	'ratingprocess_image_upload',
								);			
							echo form_upload($element);
							?>
							<input type="hidden" name="external_existed_file"  id="external_existed_file" value="<?php //if( isset($details) &&  count($details) > 0) { echo $details[0]['post_image']; }?>">
						</div>
						<div class="form-group">
							<input type="submit" class="btn btn-primary" name="zed-save" value="Save">
							<a href="<?php echo base_url();?>ratingimages" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
