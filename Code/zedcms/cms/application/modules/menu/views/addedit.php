<!-- <link rel="stylesheet" href="/assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.css"> -->
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
						echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
						echo form_open_multipart('menu/addedit',array('id'=>'addedit_menu','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
						<div class="form-group" id="name">
							<label>MENU Name</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'm_name' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['menu_name'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'm_name',
								'id'	=>	'menu_name',
								'class' => 	'form-control',
								'value'	=>	$val,
								'placeholder'=>'Menu Name',
								);			
							echo form_input($element);

							echo (isset($form_errors)&&isset($form_errors['m_name']) ? $form_errors['m_name'] : '');
							?>
						</div>
						<div class="form-group" id="menu">
							<label>MENU Related Pages</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'm_related_pages' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = explode(",",$details[0]['menu_related_pages']);
								$related_pages = array_replace(array_flip($val),$related_pages);
							}
							else
							{
								$val = '';
							}

							$options=$related_pages;
							echo form_dropdown('m_related_pages[]',$options,$val,'class="form-control zed-select2" id="menu_related_pages" multiple');
							?>
							<?php
							// $mRelatedPages = explode(",",$details[0]['menu_related_pages']);

							?>
						</div>
						<div class="form-group" id="link">
							<label>MENU External Link</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'm_link' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['menu_external_link'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'm_link',
								'id'	=>	'menu_link',
								'class' => 	'form-control',
								'value'	=>	$val,
								'placeholder'=>'Menu External Link',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group">
						<label>MENU Type</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'm_type' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['menu_type'];
							}
							else
							{
								$val = '';
							}
							$categories=array('0'=>'Individual Menu','1'=>'Sub Menu','2'=>'Footer Menu','3'=>'External Menu');
							$js = 'id="menu_type" class="form-control" ';
							echo form_dropdown('m_type',$categories,$val,$js);?>
						</div>
						<div class="form-group" id="morder">
							<label>MENU Order</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'm_order' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['menu_order'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'm_order',
								'type'  =>   'number',
								'id'	=>	'menu_order',
								'class' => 	'form-control',
								'value'	=>	$val,
								'placeholder'=>'Menu Order',
								);			
							echo form_input($element);
							?>
						</div>
						<div class="form-group">
							<label class="zed-switch">
								<input type="checkbox"  name="m_status"
								<?php if(isset($details)&& isset($details[0]['status'])){
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
							<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->
							<a href="<?php echo base_url();?>menu" class="btn btn-default">Cancel</a>
						</div>
						<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.zed-select2').select2_sortable();
	});
</script>
<script type="text/javascript">
 function validateform() {
	var name=$('#menu_name').val();
	var morder=$('#menu_order').val();
	var relatedpages=$('#menu_related_pages').val();
	if(name!='')
	{
		$("#name").removeClass('has-error');
	}
	else
	{
		$("#name").addClass('has-error');
	}
	/*if(relatedpages!=null)
	{
		$("#menu").removeClass('has-error');
	}
	else
	{
		//console.log(relatedpages);
		$("#menu").addClass('has-error');
	}*/
	if(morder!='')
	{
		$("#morder").removeClass('has-error');
	}
	else
	{
		$("#morder").addClass('has-error');
	}
	if(name!='' && morder!='' )
	{
		$('#addedit_menu').submit();
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
	if($('#menu_type :selected').val()==3){
		$('#menu').hide();
		$('#link').show();
	}
	else
	{
		$('#menu').show();
		$('#link').hide();
	}
});
$('#link').hide();
	$(document).on('change','#menu_type',function(e){
		$('#link').hide();
		var mtype=$(this).val();
		if(mtype==3)
		{
			$('#menu').hide();
			$('#link').show();
		}
		else
		{
			$('#menu').show();
		 	$('#link').hide();
		}
	});
</script>