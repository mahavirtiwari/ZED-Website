<div class="container grid-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
			<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-24">
			<div class="zed-page-actions">
				<a  href="javascript:void(0)" class="btn btn-danger" id="delete">Delete Contacts</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">
			<div class="zed-page-table">
				<?php 	
				echo form_open(base_url().'contactlist/bulk_actions',array('id'=>'bulk_actions_form'));
				?>
				<table class="table table-hover zed-data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Email</th>
							<th>Phone Number</th>
							<th>Enterprise Name</th>
							<th>Query Category</th>
							<th>Message</th>
							<th>Registration Date</th>
							<th></th>
							
						</tr>
					</thead>
					<tbody>
						<?php foreach($details as $value) { ?>
						<tr id="row-<?php echo $value->id;?>">
							<td><label class="zed-checkbox"><input type="checkbox" name="ids[]" value="<?php echo $value->id;?>"><span></span></td>
							<td class="cnt-name"><?php echo html_escape($value->contact_name);?></td>
							<td class="cnt-email"><?php echo html_escape($value->contact_email);?></td>
							<td class="cnt-phone"><?php echo html_escape($value->contact_phone);?></td>
							<td class="cnt-enterprise"><?php echo html_escape($value->enterprise_name);?></td>
							<td class="cnt-category"><?php echo html_escape($value->query_category);?></td>
							<td class="cnt-query" title="<?php echo html_escape($value->contact_query);?>"><?php echo html_escape($value->contact_query);?></td>
							<td class="cnt-date"><?php echo html_escape($value->created_on);?></td>
							<td class="btn-xs btn-sm btn-success view-message" data-target-id="row-<?php echo $value->id;?>" >View More</td>
							
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php echo form_close();?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('body').on('click','.view-message',function(){
		var name=$($('#'+$(this).data('targetId')).find('.cnt-name')[0]).text();
		var email=$($('#'+$(this).data('targetId')).find('.cnt-email')[0]).text();
		var phone=$($('#'+$(this).data('targetId')).find('.cnt-phone')[0]).text();
		var enterprise=$($('#'+$(this).data('targetId')).find('.cnt-enterprise')[0]).text();
		var category=$($('#'+$(this).data('targetId')).find('.cnt-category')[0]).text();
		var msg=$($('#'+$(this).data('targetId')).find('.cnt-query')[0]).text();
		var registration=$($('#'+$(this).data('targetId')).find('.cnt-date')[0]).text();
		
		// .text() decodes the escaped values, so insert them as text, never as HTML
		var rows=[['Name',name],['Email',email],['Mobile No',phone],['Enterprise',enterprise],['Category',category],['Message',msg],['Registration',registration]];
		var modal=$('#view-message-modal').empty();
		$.each(rows,function(i,row){
			modal.append($('<p>').append($('<span>').append($('<b>').text(row[0]+': '))).append(document.createTextNode(row[1])));
		});
		$('#myModal').modal('show');
	});
</script>