<div class="container form-page">
	<div class="row">
		<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-danger"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
						<?php 	
						echo form_open_multipart('homeslider/addedit',array('id'=>'addedit_homeslider','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<input type="file" id="fileInput" name="s_image" accept="image/*" /><br>
						<div class="custom-container">
							<canvas id="canvas">
								Your browser does not support the HTML5 canvas element.
							</canvas>
							<div id="result"><?php echo (!empty($details[0]['slider_slug']) ? '<img src="'.$details[0]['slider_slug'].' " >' : '')?></div>
						</div>
						<br>
						<input type="button"  class="btn btn-primary" id="btnCrop" value="Crop" />
						<input type="button" id="btnRestore" class="btn btn-default" value="Restore" />
						<input type="hidden" name="imageData" id="croppeImageData" value='<?php echo (isset($details[0]['slider_name']) ? $details[0]['slider_slug'] : "");?>'>
						<input type="hidden" name="existed_file" id="existed_file" value="<?php echo (isset($details[0]['slider_name']) ? $details[0]['slider_slug'] : "");?>">
						<!-- <div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="simage">
									<label>Slider Image</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 's_image' );
									}
									elseif( isset($details) &&  count($details) > 0)
									{
										$val = $details[0]['slider_slug'];
									}
									else
									{
										$val =base_url().'slider/default.png';

									}
									$element = array(
										'name'	=>	's_image',
										'id'	=>	'slider-image',
										'value'	=>	$val,
										);			
									echo form_upload($element);
									?>
									<?php //if( isset($details) &&  count($details) > 0) { ?>
									<input type="hidden" name="existed_file"  id="existed_file" value="<?php if( isset($details) &&  count($details) > 0) { echo $details[0]['slider_slug']; }?>">
									<?php // } ?>
								</div>


							</div>
							<div class="col-sm-12">
								<img id="img-preview" src="<?php echo $val;?>" class="img-responsive pull-right">
							</div>
						</div> -->
						<div class="form-group" id="hsdescription">
							<label>Slider Description</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 's_description' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['slider_description'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	's_description',
								'id'	=>	'slider_description',
								'class' => 	'form-control',
								'rows'  =>5,
								'value'	=>	$val,
								);			
								echo form_textarea($element); ?>

							</div>
							<div class="form-group" id="slidersorting">
								<label>Slider Sorting</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 's_sorting' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['slider_sorting'];
								}
								else
								{
									$val = '';
								}
								$element = array(
									'name'	=>	's_sorting',
									'type'  =>  'number',
									'min'   =>  1,
									'max'   =>  1000,
									'id'	=>	'slider_sorting',
									'class' => 	'form-control',
									'value'	=>	$val,
									//'required' => 'required',
									);			
								echo form_input($element);
								?>
							</div>
							<div class="form-group">
								<label class="zed-switch">
									<input type="checkbox"  name="s_status"
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
								<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->
								<a href="<?php echo base_url();?>homeslider" class="btn btn-default">Cancel</a>
							</div>
							<?php echo form_close();?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- <script>
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function(e) {
					$('#img-preview').attr('src', e.target.result);
				}

				reader.readAsDataURL(input.files[0]);
			}
		}

		$("#slider-image").change(function() {
			readURL(this);
		});
	</script>  -->
	<!-- cropper jquery -->
	<script>
		var canvas  = $("#canvas"),
		context = canvas.get(0).getContext("2d"),
		$result = $('#result');
		canvas.hide();
		if($('#existed_file').val()!='')
		{
			$('#btnRestore').hide();
			$('#btnCrop').hide();
		}
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
								/*aspectRatio:2.4,*/
								highlight:false,
								background:false,
								zoomable:true,
								zoomOnTouch:true,
								zoomOnWheel:true,
								 modal:false,
								 minCropBoxHeight:455,

								//cropBoxResizable: false,
								viewMode:0
								
							});
							$('#btnCrop').click(function() {
                // Get a string base 64 data url
                var croppedImageDataURL = canvas.cropper('getCroppedCanvas',{width: 750, height: 455,maxHeight: 455,  minHeight: 455}).toDataURL("image/png"); 
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
		$('#slider_description').trumbowyg({
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
		        ['foreColor', 'backColor'],
		        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
		        ['unorderedList', 'orderedList'],
		        ['removeformat'],
		    ],
		    plugins: {
				upload: {
					serverPath: '<?php echo base_url();?>pages/tbwimage',
					fileFieldName: 'tbwimagefield',
					urlPropertyName: 'data.link'

				},
			}
		});
	</script>
	<!-- cropit jquery script -->
	<script type="text/javascript">
		function validateform(){
			var simage = $("#fileInput").val();
			var eimage=$('#existed_file').val();
			var cropped=$('#croppeImageData').val();
			var hsdescription=$('#slider_description').val();
			var sorting=$('#slider_sorting').val();
			if(simage==''  && eimage=='')
			{
				$.toasty('Image is required! ','danger');
			}
			else if(cropped=='')
			{
				$.toasty('cropping is required! ','danger');
			}
			else
			{

			}
			if(sorting!='')
			{
				$("#slidersorting").removeClass('has-error');	
			}
			else
			{
				$("#slidersorting").addClass('has-error');
			}
			if(sorting!='' && (simage!='' || eimage!='') && cropped!='')
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