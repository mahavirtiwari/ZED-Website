<div class="container form-page">
	<div class="row">
		<div class="col-sm-22">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
					<li><a href="javascript:void(0);" data-toggle="tab" data-target="#tab2">SEO</a></li>
				</ul>
				<?php 

				if($this->session->flashdata('message1')){ ?>
				<div class="alert alert-danger"><?php echo $this->session->flashdata('message1');?></div>
				<?php  } ?>
				<?php 	
				echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
				echo form_open_multipart('pages/addedit',array('id' => 'addedit_page','onsubmit'=>'return validateform()'));
				?>
				<?php echo (isset($details)&&isset($details[0]['id']) ? form_hidden('id', $details[0]['id']) : '');?>
				<div class="tab-content">

					<div class="tab-pane active fade in" id="tab1">
						<div class="form-group" id="name">
							<label>Page Name</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'pg_name' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['page_name'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'pg_name',
								'id'	=>	'page_name',
								'class' => 	'form-control',
								'value'	=>	$val,
									//'required' => 'required',
								);			
								echo form_input($element); ?>
								<?php if(isset($form_errors['pg_name'])) { ?>
								<p class="alert alert-danger fade in">
									<?php
									echo (isset($form_errors)&&isset($form_errors['pg_name']) ? $form_errors['pg_name'] : '');
									?></p>
									<?php } ?>
								</div>
								<div class="form-group <?php echo (isset($form_errors)&&isset($form_errors['pg_description']) ? 'has-error' : '' );?>">
									<label>Page Description</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 'pg_description' );
									}
									elseif( isset($details) &&  count($details) > 0)
									{
										$val = $details[0]['page_description'];
									}
									else
									{
										$val = '';
									}
									$element = array(
										'name'	=>	'pg_description',
										'id'	=>	'page_description',
										'class' => 	'form-control',
										'value'	=>	$val,
										);			
										echo form_textarea($element); ?>

									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label>Page Type</label>
												<?php 		   
												if(  isset($_POST['zed-save'])  )
												{
													$val = $this->input->post( 'pg_type' );
												}
												elseif( isset($details) &&  count($details) > 0)
												{
													$val = $details[0]['page_type'];
												}
												else
												{
													$val = '';
												}
												$categories=array('1'=>'Individual Page','2'=>'Tabbed Page','3'=>'Home Page');
												$js = 'id="tabbedpage" class="form-control" ';
												echo form_dropdown('pg_type',$categories,$val,$js);?>
											</div>
										</div>
										<div class="col-sm-12" id="submenu">
											<label>Menus</label>
											<div class="form-group">
												<?php 		   
												if(  isset($_POST['zed-save'])  )
												{
													$val = $this->input->post( 'pg_menu' );
												}
												elseif( isset($details) &&  isset($details[0]['page_menu']))
												{
													$val = $details[0]['page_menu'];
												}
												else
												{
													$val = '';
												}
												$categories=$page_menu;
												$js = ' id="menu" class="form-control" ';
												echo form_dropdown('pg_menu',$categories,$val,$js);?>
											</div>
										</div> 
									</div>
									<div class="form-group" id="home_description">
										<label>Home Short Description</label>
										<?php			   
										if(  isset($_POST['zed-save'])  )
										{
											$val = $this->input->post( 'pg_short_description' );
										}
										elseif( isset($details) &&  count($details) > 0)
										{
											$val = $details[0]['page_short_description'];
										}
										else
										{
											$val = '';
										}
										$element = array(
											'name'	=>	'pg_short_description',
											'id'	=>	'page_home_description',
											'class' => 	'form-control',
											'data-reflecton'=>'.zed-featured-body',
											'value'	=>	$val,
											'rows'  =>1,
												//'maxlength'=>50,
											);			
											echo form_textarea($element); ?>
										</div>
										<div class="form-group" id="hpgorder">
											<label>Home Page  Order</label>
											<?php			   
											if(  isset($_POST['zed-save'])  )
											{
												$val = $this->input->post( 'pg_order' );
											}
											elseif( isset($details) &&  count($details) > 0)
											{
												$val = $details[0]['page_home_order'];
											}
											else
											{
												$val = '';
											}
											$element = array(
												'name'	=>	'pg_order',
												'type'  =>   'number',
												'id'	=>	'page_home_order',
												'class' => 	'form-control',
												'value'	=>	$val,
												'placeholder'=>'Home Page Order',
												);			
											echo form_input($element);
											?>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group" id="preview">
													<label>Preview on Home Page</label>
													<label class="zed-checkbox">
														<input class="zed-checkbox" type="checkbox" name="preview_home" id="preview_home" ><span></span></label>
													</div>
												</div>

												<div class="col-sm-12" id="preview_home_div">
													<div class="zed-home-feature">
														<div class="zed-feature-heading" >
															<h3 id="zed-featured-heading" ></h3>
														</div>
														<div class="zed-feature-body" >
															<p id="zed-featured-body" class="zed-featured-body" text-align="justify"></p>
															<a href="javascript:void(0);" class="btn btn-primary">Read More</a>
														</div>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="zed-switch">
													<input type="checkbox"  name="pg_status"
													<?php if(isset($details) && isset($details[0]['status'])){
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
										<div class="tab-pane fade" id="tab2">
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
													$val = $this->input->post( 'pg_seo_title' );
												}
												elseif( isset($details) &&  count($details) > 0)
												{
													$val = $details[0]['page_seo_title'];
												}
												else
												{
													$val = '';
												}
												$element = array(
													'name'	=>	'pg_seo_title',
													'id'	=>	'page_seo_title',
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
														$val = $this->input->post( 'pg_seo_keywords' );
													}
													elseif( isset($details) &&  count($details) > 0)
													{
														$val = $details[0]['page_seo_keywords'];
													}
													else
													{
														$val = '';
													}
													$element = array(
														'name'	=>	'pg_seo_keywords',
														'id'	=>	'page_seo_keywords',
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
															$val = $this->input->post( 'pg_seo_description' );
														}
														elseif( isset($details) &&  count($details) > 0)
														{
															$val = $details[0]['page_seo_description'];
														}
														else
														{
															$val = '';
														}
														$element = array(
															'name'	=>	'pg_seo_description',
															'id'	=>	'page_seo_description',
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
														<input type="submit" class="btn btn-primary" name="zed-save" id="zed-save" value="<?php echo(!isset($details) ? 'Add ' : 'Update ');?>">
														<!-- <a href="javascript:void(0);" class="btn btn-success">Save &amp; Add Another</a> -->
														<a href="<?php echo base_url();?>pages" class="btn btn-default">Cancel</a>
													</div>
													<?php echo form_close();?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- trumbowyg editor initialization script -->
								<script type="text/javascript">

									/*$('#page_description').trumbowyg({
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
												serverPath: '<?php echo base_url();?>pages/tbwimage',
												fileFieldName: 'tbwimagefield',
												urlPropertyName: 'data.link'

											}
										}
									});*/
									$('#page_description').trumbowyg({
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
												serverPath: '<?php echo base_url();?>pages/tbwimage',
												fileFieldName: 'tbwimagefield',
												urlPropertyName: 'data.link'

											},
										}
									});

								</script>
								<script type="text/javascript">
									$(document).ready(function(){
										$('#preview_home_div').hide();
									// Preview Enable condition in home page
									if($('#preview_home').attr('checked', true)) 
									{
										var page_name=$('#page_name').val();
										var page_description=$('#page_home_description').val();
										$('#zed-featured-heading').html(page_name);
										$('#zed-featured-body').html(page_description);
										$('#preview_home_div').show();

									}
									// previews checkbox change function
									$("#preview_home").change(function() {
										if(!this.checked)
										{
											$('#preview_home_div').hide();	
										}
										else
										{
											var page_name=$('#page_name').val();
											var page_description=$('#page_home_description').val();
											$('#zed-featured-heading').html(page_name);
											$('#zed-featured-body').html(page_description);
											$('#preview_home_div').show();
										}
									});

								});
							</script> 
							<script type="text/javascript">
								$(document).ready(function(){
									$('#home_description').hide();
									$('#preview').hide();
									$('#hpgorder').hide();
									// home page enable condition
									if($('#tabbedpage :selected').val()==3){

										$('#home_description').show();
										$('#hpgorder').show();
										$('#preview').show();
										$('#preview_home').attr('checked', true);
										$('#submenu').show();
									}
									else if($('#tabbedpage :selected').val()==1)
									{
										$('#submenu').hide();   
										$('#preview_home_div').hide();

									}
									else
									{
										$('#preview_home_div').hide();
									}
									// select tag changing function
									$("#tabbedpage").change(function(){

										if(($('#tabbedpage').val()==3))
										{
											$('#home_description').show();
											$('#preview').show();
											$('#hpgorder').show();
											$('#preview_home').attr('checked', true); // if home page checkbox checked
											$('#preview_home_div').show();	// preview shows if select home page
											$('#submenu').show();
										}
										else if(($('#tabbedpage').val()==2))
										{
											$('#home_description').hide();
											$('#preview').hide();   
											$('#hpgorder').hide();   
											$('#preview_home_div').hide();
											$('#submenu').show();



										}
										else
										{
											//  if not home page hide 
											$('#home_description').hide();
											$('#preview').hide();    
											$('#hpgorder').hide();    
											$('#preview_home_div').hide();
											$('#submenu').hide();

										}
									});
								});
							</script> 
							<script type="text/javascript">
								function validateform() {
									var name=$('#page_name').val();
									var stitle=$('#page_seo_title').val();
									var skeywords=$('#page_seo_keywords').val();
									var sdescription=$('#page_seo_description').val();

									if(name!='')
									{
										$("#name").removeClass('has-error');
									}
									else
									{
										$("#name").addClass('has-error');
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

									if( name!='' && stitle!= '' && skeywords!='' && sdescription!='')
									{

										$('#addedit_page').submit();

									}
									else
									{
										$.toasty('Please review the information you have entered! ','danger');
										return false;

									}
								}
							</script> 