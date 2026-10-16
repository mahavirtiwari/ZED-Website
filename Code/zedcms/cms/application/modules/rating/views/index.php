<div class="container grid-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url(); ?>rating/addedit" class="btn btn-primary">+ Add Rating</a>
				<a  href="javascript:void(0)" class="btn btn-danger" id="delete">Delete Rating</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
			<?php 	
				echo form_open(base_url().'rating/bulk_actions',array('id'=>'bulk_actions_form')); ?>
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Enterprise Name</th>
							<th>Enterprise URL</th>
							<th>Sector Name</th>
							<th>State</th>
							<th>Size</th>
							<th>Rating</th>
							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($details as $value) { ?>
						<tr title="Created by: <?php echo $this->ion_auth->user($value['created_by'])->row()->first_name;?>  |  Modified by: <?php if($value['modified_by']!=0){ echo $this->ion_auth->user($value['modified_by'])->row()->first_name; } ?>">
							<td><label class="zed-checkbox"><input type="checkbox" name="ids[]" value="<?php echo $value['rating_id'];?>" ><span></span></td>
							<td><?php echo $value['ep_name'];?></td>
							<td><?php echo $value['ep_url'];?></td>
							<td><?php echo $value['sector'];?></td>
							<td><?php echo $value['state'];?></td>
							<td><?php echo $value['size'];?></td>
							<td><?php echo $value['rating'];?></td>
							<td><?php echo ($value['status']==1 ? 'Active': 'Inactive');?></td>
							<td class="zed-table-settings-td">
								<div class="zed-table-settings">
									<ul class="list-inline">
										<li><a href="<?php echo base_url();?>rating/addedit/<?php echo $value['rating_id'];?>" class="btn btn-default">Edit</a></li>
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