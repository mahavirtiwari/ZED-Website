<div class="container grid-page">
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a href="<?php echo base_url();?>auth/create_user" class="btn btn-primary">+ Add User</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th>#</th>
							<th><?php echo lang('index_fname_th');?></th>
							<th><?php echo lang('index_lname_th');?></th>
							<th><?php echo lang('index_email_th');?></th>
							<th><?php echo lang('index_groups_th');?></th>
							<th><?php echo lang('index_status_th');?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $user) { ?>
						<tr>
							<td><label class="zed-checkbox"><input type="checkbox"><span></span></td>
							<td><?php echo htmlspecialchars($user->first_name,ENT_QUOTES,'UTF-8');?></td>
							<td><?php echo htmlspecialchars($user->last_name,ENT_QUOTES,'UTF-8');?></td>
							<td><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
							<td>
								<?php foreach ($user->groups as $group):?>
									<?php echo anchor("auth/edit_group/".$group->id, htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')) ;?><br />
								<?php endforeach?>
							</td>
							<td><?php echo ($user->active) ? anchor("auth/deactivate/".$user->id, lang('index_active_link')) : anchor("auth/activate/". $user->id, lang('index_inactive_link'));?></td>
							<td class="zed-table-settings-td">
								<div class="zed-table-settings">
									<ul class="list-inline">
										<li><?php echo anchor("auth/edit_user/".$user->id, 'Edit', 'class="btn btn-default"') ;?></li>
									</ul>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>