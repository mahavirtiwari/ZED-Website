<div class="container form-page">
	<div class="row">
	<?php if($this->session->flashdata()){ ?>
		<div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
		<?php }?>
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
						<div class="table-responsive">   
    <?php echo form_open(base_url().'news/news_block',array('id'=>'news_actions_form'));?> 
    <table class="table">
      <thead>
        <tr>
          <th>Block</th>
          <th>Show</th>
          <th>Block Heading</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>News Block 1</th>
          <td><input type="radio" name="block_status_1" value="active" <?php echo ($results[0]== 'active') ?  "checked" : "" ;  ?>>&nbsp;&nbsp;Yes 
            <input type="radio" name="block_status_1" value="inactive" <?php echo ($results[0]== 'inactive') ?  "checked" : "" ;  ?>>&nbsp;&nbsp;No</td>
            <td><input type="text" name="block_heading_1" id="news_block_name" value="<?php echo $results[1]?>"></td>
          </tr>
          <tr>
            <th>News Block 2</th>
            <td><input type="radio" name="block_status_2" value="active" <?php echo ($results[2]== 'active') ?  "checked" : "" ;  ?>>&nbsp;&nbsp;Yes 
              <input type="radio" name="block_status_2" value="inactive" <?php echo ($results[2]== 'inactive') ?  "checked" : "" ;  ?>>&nbsp;&nbsp;No</td>
              <td><input type="text" name="block_heading_2" id="news_block_name" value="<?php echo $results[3];?>"></td>
            </tr>
            <tr>
              <th>News Block 3</th>
              <td><input type="radio" name="block_status_3" value="active" <?php echo ($results[4]== 'active') ?  "checked" : "" ;  ?>>&nbsp;&nbsp;Yes 
                <input type="radio" name="block_status_3" value="inactive" <?php echo ($results[4]== 'inactive') ?  "checked" : "" ;  ?>>&nbsp;&nbsp;No</td>
                <td><input type="text" name="block_heading_3" id="news_block_name" value="<?php echo $results[5];?>"></td>
              </tr>

            </tbody>
          </table>
					</div>
					<button type="submit" class="btn btn-primary" name="zed-save">Save</button>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>
</div>
