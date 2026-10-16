<div class="container form-page">
	<div class="row">
		<?php if($this->session->flashdata('message')){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
					<!-- <li><a href="javascript:void(0);" data-toggle="tab" data-target="#tab2">Comments</a></li> -->
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
						<?php 	
						echo validation_errors('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>','</div>');
						echo form_open_multipart('parameter/addedit',array('id'=>'addedit_parameter','onsubmit'=>'return validateform()'));
						?>
						<?php echo (isset($details)&&isset($details[0]['parameterid']) ? form_hidden('id', $details[0]['parameterid']) : '');?>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group" id="code">
									<label>Parameter Code</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 'parameter_code' );
									}
									elseif( isset($details) &&  count($details) > 0)
									{
										$val = $details[0]['code'];
									}
									else
									{
										$val = '';
									}
									$element = array(
										'name'	=>	'parameter_code',
										'id'	=>	'parameter_code',
										'class' => 	'form-control',
										'value'	=>	$val,
									//'required' => 'required',
										);			
									echo form_input($element);
									?>
								</div>
							</div>
							<div class="col-sm-18">
								<div class="form-group" id="name">
									<label>Parameter Name</label>
									<?php			   
									if(  isset($_POST['zed-save'])  )
									{
										$val = $this->input->post( 'parameter_name' );
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
										'name'	=>	'parameter_name',
										'id'	=>	'parameter_name',
										'class' => 	'form-control',
										'value'	=>	$val,
									//'required' => 'required',
										);			
									echo form_input($element);
									?>
								</div>
							</div>
						</div>
						<div class="form-group" id="sector">
							<label>Sector</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'parameter_sector' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val =explode(",",$details[0]['sectorid']);
							}
							else
							{
								$val = '';
							}
							$options=$sector_parameter;
							$js = ' id="parameter_sector" class="form-control zed-select2" multiple';
							echo form_dropdown('parameter_sector[]',$options,$val,$js);
							?>
							<!-- <?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'parameter_sector' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['sectorid'];
							}
							else
							{
								$val = '';
							}
							$categories=$sector_parameter;
							$js = 'id="parameter_sector" class="form-control" ';
							echo form_dropdown('parameter_sector',$categories,$val,$js);?> -->
						</div>
						<div class="form-group" id="discipline">
							<label>Discipline</label>
							<?php 		   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'parameter_discipline' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['disciplineid'];
							}
							else
							{
								$val = '';
							}
							$categories=$sector_discipline;
							$js = 'id="parameter_discipline" class="form-control" ';
							echo form_dropdown('parameter_discipline',$categories,$val,$js);?>
						</div>
						<div class="form-group" id="description">
							<label>Parameter Description</label>
							<?php			   
							if(  isset($_POST['zed-save'])  )
							{
								$val = $this->input->post( 'parameter_description' );
							}
							elseif( isset($details) &&  count($details) > 0)
							{
								$val = $details[0]['description'];
							}
							else
							{
								$val = '';
							}
							$element = array(
								'name'	=>	'parameter_description',
								'id'	=>	'parameter_description',
								'class' => 	'form-control',
								'value'	=>	$val,
								'rows'  =>5,
								);			
								echo form_textarea($element); ?>
							</div>
							<div class="form-group">
								<label class="zed-checkbox"><input type="checkbox" value="redorange" name="parameter_ro"<?php if(isset($details)&& isset($details[0]['ro']))
									{
										if($details[0]['ro'] =='redorange')
										{
											echo 'checked="checked"';
										}
									}
									?>><span></span>&nbsp;&nbsp;Red-Orange</label></br></br>
									<label class="zed-checkbox"><input type="checkbox" value="greenwhite" name="parameter_gw" <?php if(isset($details)&& isset($details[0]['gw']))
										{
											if($details[0]['gw'] =='greenwhite')
											{
												echo 'checked="checked"';
											}
										}
										?>><span></span>&nbsp;&nbsp;Green-white</label>
									</div>
									<div class="form-group">
										<label class="zed-switch">
											<input type="checkbox"  name="parameter_status"
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
									<div class="table-responsive">
										<table class="table table-bordered table-striped">
											<thead>
												<tr>
													<th>Option</th>
													<th>Option Text</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>1</td>
													<td>
														<?php			   
														if(  isset($_POST['zed-save'])  )
														{
															$val = $this->input->post( 'parameter_level1_comment' );
														}
														elseif( isset($details) &&  count($details) > 0)
														{
															$val = $details[0]['level1_comment'];
														}
														else
														{
															$val = '';
														}
														$element = array(
															'name'	=>	'parameter_level1_comment',
															'id'	=>	'parameter_level1_comment',
															'class' => 	'form-control',
															'value'	=>	$val,
															'rows'  =>5,
															);			
															echo form_textarea($element); ?>
														</td>
													</tr>
													<tr>
														<td>2</td>
														<td>
															<?php			   
															if(  isset($_POST['zed-save'])  )
															{
																$val = $this->input->post( 'parameter_level2_comment' );
															}
															elseif( isset($details) &&  count($details) > 0)
															{
																$val = $details[0]['level2_comment'];
															}
															else
															{
																$val = '';
															}
															$element = array(
																'name'	=>	'parameter_level2_comment',
																'id'	=>	'parameter_level2_comment',
																'class' => 	'form-control',
																'value'	=>	$val,
																'rows'  =>5,
																);			
																echo form_textarea($element); ?>
															</td>
														</tr>
														<tr>
															<td>3</td>
															<td>
																<?php			   
																if(  isset($_POST['zed-save'])  )
																{
																	$val = $this->input->post( 'parameter_level3_comment' );
																}
																elseif( isset($details) &&  count($details) > 0)
																{
																	$val = $details[0]['level3_comment'];
																}
																else
																{
																	$val = '';
																}
																$element = array(
																	'name'	=>	'parameter_level3_comment',
																	'id'	=>	'parameter_level3_comment',
																	'class' => 	'form-control',
																	'value'	=>	$val,
																	'rows'  =>5,
																	);			
																	echo form_textarea($element); ?>
																</td>
															</tr>
															<tr>
																<td>4</td>
																<td>
																	<?php			   
																	if(  isset($_POST['zed-save'])  )
																	{
																		$val = $this->input->post( 'parameter_level4_comment' );
																	}
																	elseif( isset($details) &&  count($details) > 0)
																	{
																		$val = $details[0]['level4_comment'];
																	}
																	else
																	{
																		$val = '';
																	}
																	$element = array(
																		'name'	=>	'parameter_level4_comment',
																		'id'	=>	'parameter_level4_comment',
																		'class' => 	'form-control',
																		'value'	=>	$val,
																		'rows'  =>5,
																		);			
																		echo form_textarea($element); ?>
																	</td>
																</tr>
																<tr>
																	<td>5</td>
																	<td>
																		<?php			   
																		if(  isset($_POST['zed-save'])  )
																		{
																			$val = $this->input->post( 'parameter_level5_comment' );
																		}
																		elseif( isset($details) &&  count($details) > 0)
																		{
																			$val = $details[0]['level5_comment'];
																		}
																		else
																		{
																			$val = '';
																		}
																		$element = array(
																			'name'	=>	'parameter_level5_comment',
																			'id'	=>	'parameter_level5_comment',
																			'class' => 	'form-control',
																			'value'	=>	$val,
																			'rows'  =>5,
																			);			
																			echo form_textarea($element); ?>
																		</td>
																	</tr>
																</tbody>
															</table>
														</div>
													</div>
													<div class="form-group">
														<input type="submit" class="btn btn-primary" id="zed-save" name="zed-save" value="Save & Add Another">
														<input type="submit" class="btn btn-primary" id="zed-save" name="zed-save" value="Save">
														<a href="<?php echo base_url();?>parameter" class="btn btn-default">Cancel</a>
													</div>
													<?php echo form_close();?>

												</div>
											</div>
										</div>
									</div>
								</div>
								<script>
									$(document).ready(function(){
										$('.zed-select2').select2();
									});
								</script>
								<script type="text/javascript">
									function validateform() {
										var code=$('#parameter_code').val();
										var name=$('#parameter_name').val();
										var sector=$('#parameter_sector').val();
										var discipline=$('#parameter_discipline').val();
										var description=$('#parameter_description').val();
										if(code!='')
										{
											$("#code").removeClass('has-error');
										}
										else
										{
											$("#code").addClass('has-error');
										}
										if(name!='')
										{
											$("#name").removeClass('has-error');
										}
										else
										{
											$("#name").addClass('has-error');
										}
										if(sector!=null)
										{
											$("#sector").removeClass('has-error');
										}
										else
										{
											$("#sector").addClass('has-error');
										}
										if(discipline!='')
										{
											$("#discipline").removeClass('has-error');
										}
										else
										{
											$("#discipline").addClass('has-error');
										}
										if(description!='')
										{
											$("#description").removeClass('has-error');
										}
										else
										{
											$("#description").addClass('has-error');
										}
										if(code!='' && name!='' && sector!='' && discipline!='' && description!='')
										{

											$('#addedit_parameter').submit();

										}
										else
										{
											$.toasty('Please review the information you have entered! ','danger');
											return false;

										}
									}
								</script> 