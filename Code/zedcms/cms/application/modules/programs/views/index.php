<div class="container grid-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url(); ?>programs/addedit" class="btn btn-primary">+ Add Program</a>
				<a  href="javascript:void(0)" class="btn btn-danger" id="delete">Delete Program</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
			<?php 	
				echo form_open(base_url().'programs/bulk_actions',array('id'=>'bulk_actions_form')); ?>
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Caption</th>
							<th>Button Name</th>
							<th>Link</th>
							<th>Order</th>
							<th>Image</th>
							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($details as $value) { ?>
						<tr title="Created by: <?php echo $this->ion_auth->user($value['created_by'])->row()->first_name;?>  |  Modified by: <?php if($value['modified_by']!=0){ echo $this->ion_auth->user($value['modified_by'])->row()->first_name; } ?>">
							<td><label class="zed-checkbox"><input type="checkbox" name="ids[]" value="<?php echo $value['id'];?>" ><span></span></td>
							<td><?php echo $value['name'];?></td>
							<td><?php echo $value['caption'];?></td>
							<td><?php echo $value['button_name'];?></td>
							<td><?php echo $value['link'];?></td>
							<td><?php echo $value['sorting'];?></td>
							<?php
								if(isset($value['image']) && !empty($value['image']))
								{
							?>
							<td><a href="<?php  echo URL_FOLDER.$value['image'];?>" target="_blank"><?php  echo URL_FOLDER.$value['image'];?></a></td>
							<?php
								}
								else
								{
							?>
								<td></td>
							<?php
								}
							?>
							<td><?php echo ($value['status']==1 ? 'Active': 'Inactive');?></td>
							<td class="zed-table-settings-td">
								<div class="zed-table-settings">
									<ul class="list-inline">
										<li><a href="<?php echo base_url();?>programs/addedit/<?php echo $value['id'];?>" class="btn btn-default">Edit</a></li>
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