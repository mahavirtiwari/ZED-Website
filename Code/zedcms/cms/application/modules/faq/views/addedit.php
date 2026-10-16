<div class="container form-page">
	<div class="row">
		<?php if($this->session->flashdata('message')){ ?>
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
						echo form_open_multipart('faq/addedit',array('id'=>'addedit_faq','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details) && isset($details[0]['id']) ? form_hidden('id',$details[0]['id']) : '');?>
						<div class="form-group" id="question">
							<label>FAQ Question</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'f_question' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['faq_question'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'f_question',
								'id'	=>	'faq_question',
								'class' => 	'form-control',
								'value'	=>	$val,

								);			
								echo form_input($element); ?>
							</div>
							<div class="form-group" id="answer">
								<label>FAQ Answer</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 'f_answer' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['faq_answer'];
								}
								else
								{
									$val = '';
								}
								$element = array(
									'name'	=>	'f_answer',
									'id'	=>	'faq_answer',
									'class' => 	'form-control',
									'value'	=>	$val,
									//'required' => 'required',
									);			
								echo form_textarea($element);
								?>
							</div>
							<div class="form-group" id="order">
								<label>FAQ Order</label>
								<?php			   
								if(  isset($_POST['zed-save'])  )
								{
									$val = $this->input->post( 'f_order' );
								}
								elseif( isset($details) &&  count($details) > 0)
								{
									$val = $details[0]['faq_order'];
								}
								else
								{
									$val = '';
								}
								$element = array(
									'name'	=>	'f_order',
									'id'	=>	'faq_order',
									'class' => 	'form-control',
									'value'	=>	$val,
									);			
									echo form_input($element); ?>
							</div>
							<div class="form-group">
								<label class="zed-switch">
									<input type="checkbox"  name="f_status"
									<?php if(isset($details)){
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
								<input type="submit" class="btn btn-primary" name="zed-save" value="Save &amp; Add Another">
								<a href="<?php echo base_url();?>faq" class="btn btn-default">Cancel</a>
							</div>
							<?php echo form_close();?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- trumbowyg editor initialization script -->
	<script type="text/javascript">

		/*$('#faq_answer').trumbowyg({
			svgPath: '<?php echo base_url();?>assets/plugins/trumbowyg/dist/ui/icons.svg',
			autogrow: true,
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
					serverPath: '<?php echo base_url();?>faq/tbwimage',
					fileFieldName: 'tbwimagefield',
					urlPropertyName: 'data.link'

				}
			}
		});*/
		$('#faq_answer').trumbowyg({
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
					serverPath: '<?php echo base_url();?>faq/tbwimage',
					fileFieldName: 'tbwimagefield',
					urlPropertyName: 'data.link'

				},
			}
		});

	</script>
	<script type="text/javascript">
	function validateform() {
		var question=$('#faq_question').val();
		var answer=$('#faq_answer').val();
		var order=$('#faq_order').val();

		if(question!='')
		{
			$("#question").removeClass('has-error');
		}
		else
		{
			$("#question").addClass('has-error');
		}
		if(answer!='')
		{
			$("#answer").removeClass('has-error');
		}
		else
		{
			$("#answer").addClass('has-error');
		}
		if(order!='')
		{
			$("#order").removeClass('has-error');
		}
		else
		{
			$("#order").addClass('has-error');
		}
		if( question!='' && answer!='' && order!='')
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