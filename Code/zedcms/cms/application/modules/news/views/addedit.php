<div class="container form-page">
	<div class="row">
		<div class="col-sm-22">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label>News Type</label>
						<?php 		   

						if( isset($details) &&  count($details) > 0)
						{
							$val = $details[0]['post_category'];
						}
						else
						{
							$val = '';
						}
						$options=array('1'=>'Internal Page','2'=>'External Link','3'=>'File Link');
						$js=array('class'=>'form-control','id'=>'news_type');
						echo form_dropdown('p_type',$options,$val,$js);
						?>
					</div>
				</div>
			</div>
			<div class="zed-tabs">
				<?php
				$error_message = isset($message1) ? $message1 : $this->session->flashdata('message1');
				if($error_message){ ?>
				<div class="alert alert-danger"><?php echo html_escape($error_message);?></div>
				<?php  } ?>
				<!-- External Link Tab -->
				<div class="tab-content" id="external_link">
					<div class="tab-pane active fade in" id="tab3">
						<?php echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
						echo form_open_multipart('news/addedit',array('id' => 'addedit_external'));
						?>
				<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
					<div class="form-group ">
						<label>News Name</label>
						<?php			   
						if(  isset($_POST['zed-save'])  )
						{
							$val = $this->input->post( 'p_name' );
						}
						elseif( isset($details) &&  count($details) > 0)
						{
							$val = $details[0]['post_name'];
						}
						else
						{
							$val = '';
						}
						$element = array(
							'name'	=>	'p_name',
							'class' => 	'form-control',
							'value'	=>	$val,
							);			
							echo form_input($element); ?>
					</div>
					<div class="form-group" >
						<label>News External Link</label>
						<?php			   
						if(  isset($_POST['zed-save'])  )
						{
							$val = $this->input->post( 'p_external_link' );
						}
						elseif( isset($details) &&  count($details) > 0)
						{
							$val = $details[0]['post_external_link'];
						}
						else
						{
							$val = '';
						}
						$element = array(
							'name'	=>	'p_external_link',
							'id'	=>	'post_external_link',
							'class' => 	'form-control',
							'value'	=>	$val,
							'required'=>'required',
							);			
							echo form_input($element); ?>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group" id="description">
								<label>Display Date</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 'p_display_date' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['post_display_date'];
								}
								else
								{
									$val = '';
								}
								$element = array(
									'name'	=>	'p_display_date',
									'type'	=>	'date',
									'id'	=>	'post_display_date',
									'class' => 	'form-control',
									'value'	=>	$val,
									);			
									echo form_input($element); ?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="zed-checkbox"><input type="checkbox" value="1" name="p_breaking_news"<?php if(isset($details)&& isset($details[0]['post_breaking_news']))
							{
								if($details[0]['post_breaking_news'] ==1)
								{
									echo 'checked="checked"';
								}
							}
							?>><span></span>&nbsp;&nbsp;Breaking News</label></br></br>
					</div>
					<div class="form-group">
						<label class="zed-switch">
							<input type="checkbox"  name="p_status"
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
						<input type="submit" class="btn btn-primary" name="zed-external" id="zed-save" value="Save">
						<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->
						<a href="<?php echo base_url();?>news" class="btn btn-default">Cancel</a>
					</div>
					<?php echo form_close();?>
				</div>
			</div>
<!-- end external Links tab  -->
<!-- File Upload Tab -->
<div class="tab-content" id="news_file_upload">
	<div class="tab-pane active fade in" id="tab4">
		<div class="row">
			<div class="col-sm-12">
				<?php echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
				echo form_open_multipart('news/addedit',array('id' => 'addedit_fileuploading','onsubmit'=>'return validationImage()'));
				?>
				<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
				<div class="form-group " >
					<label>News Name</label>
					<?php			   
					if(  isset($_POST['zed-save'])  )
					{
						$val = $this->input->post( 'p_name' );
					}
					elseif( isset($details) &&  count($details) > 0)
					{
						$val = $details[0]['post_name'];
					}
					else
					{
						$val = '';
					}
					$element = array(
						'name'	=>	'p_name',
						'class' => 	'form-control',
						'value'	=>	$val,
						);			
						echo form_input($element); ?>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group" id="simage">
								<label>News File Upload</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 'p_file_upload' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['post_image'];
								}
								else
								{
									$val ='';

								}
								$element = array(
									'name'	=>	'p_file_upload',
									'id'	=>	'post_image',
									'value'	=>	$val,
									);			
								echo form_upload($element);
								?>
								<input type="hidden" name="existed_file"  id="external_existed_file" value="<?php if( isset($details) &&  count($details) > 0) { echo $details[0]['post_image']; }?>">
							</div>
						</div>
						<div class="col-sm-12">
							<?php echo(!empty($details[0]['post_image']) ? '<a href="'.$details[0]['post_image'].'" class="btn btn-success" target="_blank">News File Submitted</a>': '');?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group" id="description">
								<label>Display Date</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 'p_display_date' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['post_display_date'];
								}
								else
								{
									$val = '';
								}
								$element = array(
									'name'	=>	'p_display_date',
									'type'	=>	'date',
									'id'	=>	'post_display_date',
									'class' => 	'form-control',
									'value'	=>	$val,
									);			
									echo form_input($element); ?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="zed-checkbox"><input type="checkbox" value="1" name="p_breaking_news"<?php if(isset($details)&& isset($details[0]['post_breaking_news']))
							{
								if($details[0]['post_breaking_news'] ==1)
								{
									echo 'checked="checked"';
								}
							}
							?>><span></span>&nbsp;&nbsp;Breaking News</label></br></br>
						</div>
						<div class="form-group">
							<label class="zed-switch">
								<input type="checkbox"  name="p_status"
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
							<input type="submit" class="btn btn-primary" name="zed-fileupload" id="zed-save" value="Save">
							<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->
							<a href="<?php echo base_url();?>news" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- end File Upload Tab -->
</div>
<?php echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
echo form_open_multipart('news/addedit',array('id' => 'addedit_post','onsubmit'=>'return validateform()'));
?>
<div class="zed-tabs" id="internal_news">
	<ul class="nav nav-tabs">
		<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
		<li><a href="javascript:void(0);" data-toggle="tab" data-target="#tab2">SEO</a></li>
	</ul>
	<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
	<div class="tab-content">
		<div class="tab-pane active fade in" id="tab1">
			<div class="form-group " id="name">
				<label>News Name</label>
				<?php			   
				if(  isset($_POST['zed-save'])  )
				{
					$val = $this->input->post( 'p_name' );
				}
				elseif( isset($details) &&  count($details) > 0)
				{
					$val = $details[0]['post_name'];
				}
				else
				{
					$val = '';
				}
				$element = array(
					'name'	=>	'p_name',
					'id'	=>	'post_name',
					'class' => 	'form-control',
					'value'	=>	$val,

					);			
					echo form_input($element); ?>
					<?php if(isset($form_errors['p_name'])) { ?>
					<p class="alert alert-danger fade in">
						<?php
						echo (isset($form_errors)&&isset($form_errors['p_name']) ? $form_errors['p_name'] : '');
						?>
					</p>
					<?php } ?>
				</div>
				<div class="form-group" id="simage">
					<label>Featured Image</label>
					<input type="file" id="fileInput" name="news_featured_image" accept="image/*" /><br>
					<canvas id="canvas">
						Your browser does not support the HTML5 canvas element.
					</canvas>
					<div id="result"><?php echo (!empty($details[0]['featured_slug']) ? '<img src="'.$details[0]['featured_slug'].' " >' : '')?></div><br>
					<input type="button" class="btn btn-primary" id="btnCrop" value="Crop" />
					<input type="button" id="btnRestore" class="btn btn-default" value="Restore" />
					<input type="hidden" name="imageData" id="croppeImageData" value='<?php echo (isset($details[0]['featured_slug']) ? $details[0]['featured_slug'] : "");?>'>
					<input type="hidden" name="existed_file" id="existed_file" value="<?php echo (isset($details[0]['featured_slug']) ? $details[0]['featured_slug'] : "");?>">
					<input type="hidden" name="existed_file"  id="existed_file" value="<?php if( isset($details) &&  count($details) > 0) { echo $details[0]['featured_slug']; }?>">
				</div>
				<div class="form-group" id="description">
					<label>News Description</label>
					<?php			   
					if(  isset($_POST['zed-save'])  )
					{
						$val = $this->input->post( 'p_description' );
					}
					elseif( isset($details) &&  count($details) > 0)
					{
						$val = $details[0]['post_description'];
					}
					else
					{
						$val = '';
					}
					$element = array(
						'name'	=>	'p_description',
						'id'	=>	'post_description',
						'class' => 	'form-control',
						'value'	=>	$val,
						);			
						echo form_textarea($element); ?>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group" id="description">
							<label>Display Date</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'p_display_date' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['post_display_date'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'p_display_date',
								'type'	=>	'date',
								'id'	=>	'post_display_date',
								'class' => 	'form-control',
								'value'	=>	$val,
								);			
								echo form_input($element); ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="zed-checkbox"><input type="checkbox" value="1" name="p_breaking_news"<?php if(isset($details)&& isset($details[0]['post_breaking_news']))
						{
							if($details[0]['post_breaking_news'] ==1)
							{
								echo 'checked="checked"';
							}
						}
						?>><span></span>&nbsp;&nbsp;Breaking News</label></br></br>
				</div>
					<div class="form-group">
						<label class="zed-checkbox"><input type="checkbox" value="1" name="p_latest_news" <?php if(isset($details)&& isset($details[0]['post_latest_news']))
							{
								if($details[0]['post_latest_news'] ==1)
								{
									echo 'checked="checked"';
								}
							}
							?>><span></span>&nbsp;&nbsp;Latest News</label></br></br>
					</div>
				<div class="form-group">
					<label class="zed-switch">
						<input type="checkbox"  name="p_status"
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
			
			</div>
			<!-- SEO TAB -->
			<div class="tab-pane  fade " id="tab2">
				<div class="google-search-preview">
					<div class="gsearch-content">
						<small class="text-muted">GOOGLE SEARCH RESULT PREVIEW</small>
						<div class="preview-title"><a href="javascript:void(0);" class="preview-title-a"></a></div>
						<div class="preview-url">http://www.zed.com/ <span class="caret"></span></div>
						<div class="preview-content"><a href="javascript:void(0);" class=".preview-content"></a></div>
					</div>
				</div>
				<div class="form-group" id="stitle">
					<label>SEO Tiltle</label>
					<?php			   
					if(  isset($_POST['zed-save'])  )
					{
						$val = $this->input->post( 'p_seo_title' );
					}
					elseif( isset($details) &&  count($details) > 0)
					{
						$val = $details[0]['post_seo_title'];
					}
					else
					{
						$val = '';
					}
					$element = array(
						'name'	=>	'p_seo_title',
						'id'	=>	'post_seo_title',
						'class' => 	'form-control',
						'data-reflecton'=>'.preview-title-a',
						'value'	=>	$val,
						);			
						echo form_input($element); ?>
					</div>
					<div class="form-group" id="skeywords">
						<label>SEO Keywords</label>
						<?php			   
						if(  isset($_POST['zed-save'])  )
						{
							$val = $this->input->post( 'p_seo_keywords' );
						}
						elseif( isset($details) &&  count($details) > 0)
						{
							$val = $details[0]['post_seo_keywords'];
						}
						else
						{
							$val = '';
						}
						$element = array(
							'name'	=>	'p_seo_keywords',
							'id'	=>	'post_seo_keywords',
							'class' => 	'form-control',
							'value'	=>	$val,
							'rows'  =>5,
							);			
							echo form_textarea($element); ?>
						</div>
						<div class="form-group" id="sdescription">
							<label>SEO Description</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'p_seo_description' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['post_seo_description'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'p_seo_description',
								'id'	=>	'post_seo_description',
								'class' => 	'form-control',
								'data-reflecton'=>'.preview-content',
								'value'	=>	$val,
								'rows'  =>5,
								);			
								echo form_textarea($element); ?>
							</div>
						</div> 
						<!-- end of seo tabs -->
						<div class="form-group">
							<input type="submit" class="btn btn-primary" name="zed-save" id="zed-save" value="Save">
							<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->
							<a href="<?php echo base_url();?>news" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	/*$('#post_description').trumbowyg({
		svgPath: '<?php echo base_url();?>assets/plugins/trumbowyg/dist/ui/icons.svg',
		autogrow: true,
		fullscreenable: false,
		semantic: false,
		btnsDef: {
			image: {
				dropdown: ['insertImage', 'upload'],
				ico: 'insertImage'
			}
		},
		btns: [
		'viewHTML',
		['undo', 'redo'],
		['formatting'],
		'|', 'btnGrp-design',
		'|', 'btnGrp-justify',
		'link','image',
		'|', 'foreColor', 'backColor', 'btnGrp-lists'],
		plugins: {
			upload: {
				serverPath: '<?php echo base_url();?>/news/tbwimage',
				fileFieldName: 'tbwimagefield',
				urlPropertyName: 'data.link'

			}
		}
	});*/
	$('#post_description').trumbowyg({
			autogrow: true,
			removeformatPasted: true,
			btns: [
		        ['viewHTML'],
		        ['undo', 'redo'], // Only supported in Blink browsers
		        ['formatting'],
		        ['fontsize'],
		        ['strong', 'em', 'del'],
		        ['superscript', 'subscript'],
		        ['link'],
		        ['insertImage','upload'],
		        ['foreColor', 'backColor'],
		        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
		        ['unorderedList', 'orderedList'],
		        ['table'],
		        ['horizontalRule'],
		        ['removeformat'],
		    ],
		    plugins: {
				upload: {
					serverPath: '<?php echo base_url();?>news/tbwimage',
					fileFieldName: 'tbwimagefield',
					urlPropertyName: 'data.link'

				},
			}
		});
</script>
<script>
	var canvas  = $("#canvas"),
	context = canvas.get(0).getContext("2d"),
	$result = $('#result');
	canvas.hide();
	$('#fileInput').on( 'change', function(){
		canvas.show();
		$('#croppeImageData').val('');
		$('#result').hide();
		$('#btnRestore').show();
		$('#btnCrop').show();
		if (this.files && this.files[0]) {
			if ( this.files[0].type.match(/^image\//) ) {
				var reader = new FileReader();
				reader.onload = function(evt) {
					var img = new Image();
					img.onload = function() {
						context.canvas.height = img.height;
						context.canvas.width  = img.width;
						context.drawImage(img, 0, 0);
						var cropper = canvas.cropper({
							aspectRatio:1.5,
							background:false,
							zoomable:true,
							zoomOnTouch:true,
							zoomOnWheel:true,
							modal:false,
						});
						$('#btnCrop').click(function() {
            // Get a string base 64 data url
            var croppedImageDataURL = canvas.cropper('getCroppedCanvas',{width:960, height:640}).toDataURL("image/png"); 
            $result.html( $('<img>').attr('src', croppedImageDataURL) );
            $('#croppeImageData').val(croppedImageDataURL);
            $('#result').show();
            $('.cropper-container').hide();
        });
						$('#btnRestore').click(function() {
							canvas.cropper('reset');
							$result.empty();
							$('#croppeImageData').val('');
							$('.cropper-container').show();
						});
					};
					img.src = evt.target.result;
				};
				reader.readAsDataURL(this.files[0]);
			}
			else {
				alert("Invalid file type! Please select an image file.");
			}
		}
		else {
			alert('No file(s) selected.');
		}
	});
</script>
<script type="text/javascript">
	function validateform() {
		var name=$('#post_name').val();
		var description=$('#post_description').val();
		var news_block=$('#n_block').val();
		var stitle=$('#post_seo_title').val();
		var skeywords=$('#post_seo_keywords').val();
		var sdescription=$('#post_seo_description').val();

		if(name!='')
		{
			$("#name").removeClass('has-error');
		}
		else
		{
			$("#name").addClass('has-error');
		}
		if(description!='')
		{
			$("#description").removeClass('has-error');
		}
		else
		{
			$("#description").addClass('has-error');
		}
		if(stitle!='')
		{
			$("#stitle").removeClass('has-error');
		}
		else
		{
			$("#stitle").addClass('has-error');
		}
		if(skeywords!='')
		{
			$("#skeywords").removeClass('has-error');
		}
		else
		{
			$("#skeywords").addClass('has-error');
		}
		if(sdescription!='')
		{
			$("#sdescription").removeClass('has-error');
		}
		else
		{
			$("#sdescription").addClass('has-error');
		}
		if( name!='' && description!='' && stitle!= '' && skeywords!='' && sdescription!='')
		{

			if ($('#fileInput').get(0).files.length != 0) {
				if($('#croppeImageData').val()!='')
				{
					$('#addedit_post').submit();
				}
				else
				{
					$.toasty('Cropping Is required! ','danger');
					return false;
				}
			}
			else
			{
				$('#addedit_post').submit();
			}
		}
		else
		{
			$.toasty('Please review the information you have entered! ','danger');
			return false;

		}
	}
</script> 
<script type="text/javascript">
	$(document).ready(function(){
		if($('#news_type :selected').val()==3){
			$('#internal_news').hide();
			$('#external_link').hide();
			$('#news_file_upload').show();
		}
		if($('#news_type :selected').val()==2){
			$('#internal_news').hide();
			$('#external_link').show();
			$('#news_file_upload').hide();
		}

	});
	// selcted page
	$('#external_link').hide();
	$('#news_file_upload').hide();
	$(document).on('change','#news_type',function(e){
	//console.log($(this).val());
	var ntype=$(this).val();
	if(ntype==2)
	{
		$('#internal_news').hide();
		$('#external_link').show();
		$('#news_file_upload').hide();
	}
	else if(ntype==3)
	{
		$('#internal_news').hide();
		$('#external_link').hide();
		$('#news_file_upload').show();
	}
	else
	{
		$('#internal_news').show();
		$('#external_link').hide();
		$('#news_file_upload').hide();
	}
});
</script>
<script type="text/javascript">
function validationImage()
{
	if ($('#post_image').val()=='') {
		$.toasty('News file upload required! ','danger');
		return false;
	}
	else
	{
		return true;
	}
}
</script>