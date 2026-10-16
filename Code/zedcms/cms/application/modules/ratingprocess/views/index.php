<div class="container grid-page">
	<div class="row">
		<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url();?>ratingprocess/addedit" class="btn btn-primary">+Upload Rating Image</a>
				<a  href="javascript:void(0)" class="btn btn-danger" id="delete">Delete Image</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
				<?php 	
				echo form_open(base_url().'ratingprocess/bulk_actions',array('id'=>'bulk_actions_form'));
				echo form_hidden('action', '');
				?>
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th></th>
							<th>Image Name</th>
							<th>URL</th>
							<th>Status</th>
							<th>Created On</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($details as $value) { ?>
						<tr> 
							<td><label class="zed-checkbox">
								<input type="checkbox" value="<?php echo $value->id;?>" name="ids[]"><span></span></td>
							<td><?php  echo $value->name;?></td>
							<td><a href="<?php  echo URL_FOLDER.$value->image_path;?>" target="_blank"><?php  echo URL_FOLDER.$value->image_path;?></a></td>
								<td><?php echo $value->status==1 ? 'Active' : 'Inactive';?></td>
								<td><?php echo $value->created_on;?></td>
							</tr>
							<?php } ?>

						</tbody>
					</table>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>
