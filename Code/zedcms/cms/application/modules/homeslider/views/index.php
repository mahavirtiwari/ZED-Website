<div class="container grid-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url();?>homeslider/addedit" class="btn btn-primary">+ Add slider</a>
				<a  href="javascript:void(0)" class="btn btn-danger" id="delete">Delete Slider</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
				<?php 	
				echo form_open(base_url().'homeslider/bulk_actions',array('id'=>'bulk_actions_form'));
				echo form_hidden('action', '');
				?>
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Slider Image</th>
							<th>Slider Sorting</th>
							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($details as $value ) { ?>
						<tr title="Created by: <?php echo $this->ion_auth->user($value->created_by)->row()->first_name;?>  |  Modified by: <?php if($value->modified_by!=0){ echo $this->ion_auth->user($value->modified_by)->row()->first_name; } ?>">
							<td><label class="zed-checkbox">
								<input type="checkbox" value="<?php echo $value->id;?>" name="ids[]"><span></span></td>
								<td><?php if(isset($value) && isset($value->slider_name)){ ?>
									<img class="zed-img" src="<?php echo $value->slider_slug;?>" >
									<?php } ?>
								</td>
								<td><?php echo $value->slider_sorting;?></td>
								<td><?php echo ($value->status ? 'Active' : 'Inactive');?></td>
								<td class="zed-table-settings-td">
									<div class="zed-table-settings">
										<ul class="list-inline">
											<li><a href="<?php echo base_url();?>homeslider/addedit/<?php echo base64_encode($value->id)?>" class="btn btn-default">Edit</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>