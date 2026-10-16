<div class="container grid-page">
	<div class="row">
		<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url();?>gallery/addedit" class="btn btn-primary">+ Add Image</a>
				<a  href="javascript:void(0)" class="btn btn-danger" id="delete">Delete Image</a>
			</div>

		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
				<?php 	
				echo form_open(base_url().'gallery/bulk_actions',array('id'=>'bulk_actions_form'));
				echo form_hidden('action', '');
				?>
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Image</th>
							<th>Image Title</th>
							<th>Image Caption</th>
							<th></th>
						</tr>
					</thead>
					
					<tbody>
						
						<?php foreach($details as $value) { ?>
						<tr>
							<td><label class="zed-checkbox">
								<input type="checkbox" value="<?php echo $value->id;?>" name="ids[]"><span></span></td>
								<td><a href="<?php echo $value->media_slug;?>"><img src="<?php echo $value->media_slug?>" class="zed-img"></a></td>
								<td><?php echo $value->media_title;?></td>
								<td><?php echo $value->media_caption;?></td>
								<td class="zed-table-settings-td">
									<div class="zed-table-settings">
										<ul class="list-inline">
											<li><a href="<?php echo base_url();?>gallery/addedit/<?php echo $value->id;?>" class="btn btn-default">Edit</a>
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
