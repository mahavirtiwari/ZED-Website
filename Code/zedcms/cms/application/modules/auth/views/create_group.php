p<div class="container form-page">
  <div class="row">
    <div class="col-sm-18">
    <?php if(isset($message)) { ?>
    <div class="alert alert-danger fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div id="infoMessage"><?php echo $message;?></div>
    </div>
    <?php } ?>
      <div class="zed-tabs">
        <ul class="nav nav-tabs">
          <li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active fade in" id="tab1">
           <?php echo form_open("auth/create_group");?>

      <div class="form-group">
            <?php echo lang('create_group_name_label', 'group_name');?> <br />
            <?php echo form_input($group_name);?>
      </div>

      <div class="form-group">
            <?php echo lang('create_group_desc_label', 'description');?> <br />
            <?php echo form_input($description);?>
      </div>

      <div class="form-group">
      <?php echo form_submit('submit', lang('create_group_submit_btn'),'class="btn btn-primary"');?>
      </div>
	<?php echo form_close();?>
         
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



