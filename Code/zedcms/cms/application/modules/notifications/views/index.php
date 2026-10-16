<div class="container grid-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url(); ?>notifications/addedit" class="btn btn-primary">+ Add Notification</a>
				<a  href="javascript:void(0)" class="btn btn-danger" id="delete">Delete Notification</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
			<?php 	
				echo form_open(base_url().'notifications/bulk_actions',array('id'=>'bulk_actions_form')); ?>
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Button Name</th>
							<th>Type</th>
							<th>URL</th>
							<th>Order</th>
							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($details as $value) { ?>
						<tr title="Created by: <?php echo $this->ion_auth->user($value['created_by'])->row()->first_name;?>  |  Modified by: <?php if($value['modified_by']!=0){ echo $this->ion_auth->user($value['modified_by'])->row()->first_name; } ?>">
							<td><label class="zed-checkbox"><input type="checkbox" name="ids[]" value="<?php echo $value['id'];?>" ><span></span></td>
							<td><?php echo $value['name'];?></td>
							<td><?php echo $value['type'];?></td>
							<?php 
								if($value['type']=='link'){
							?>
							<td><a href="<?php echo $value['link'];?>" target="_blank"><?php echo $value['link'];?>"></a></td>
							<?php
								}
								else{
							?>
							<td><a href="<?php echo URL_FOLDER.$value['file_path'];?>" target="_blank"><?php echo URL_FOLDER.$value['file_path'];?>"></a></td>
							<?php
								}
							?>
							<td><?php echo $value['sorting'];?></td>
							<td><?php echo ($value['status']==1 ? 'Active': 'Inactive');?></td>
							<td class="zed-table-settings-td">
								<div class="zed-table-settings">
									<ul class="list-inline">
										<li><a href="<?php echo base_url();?>notifications/addedit/<?php echo $value['id'];?>" class="btn btn-default">Edit</a></li>
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