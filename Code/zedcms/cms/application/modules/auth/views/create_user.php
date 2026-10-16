<div class="container form-page">
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
           
            <?php echo form_open("auth/create_user");?>
            <div class="form-group">
              <?php echo lang('create_user_fname_label', 'first_name');?> <br />
              <?php echo form_input($first_name);?>
            </div>
            <div class="form-group">
              <?php echo lang('create_user_lname_label', 'last_name');?> <br />
              <?php echo form_input($last_name);?>
            </div>
            <?php
            if($identity_column!=='email') {
              echo '<p>';
              echo lang('create_user_identity_label', 'identity');
              echo '<br />';
              echo form_error('identity');
              echo form_input($identity);
              echo '</p>';
            }
            ?>
            <div class="form-group">
              <?php echo lang('create_user_company_label', 'company');?> <br />
              <?php echo form_input($company);?>
            </div>
            <div class="form-group">
              <?php echo lang('create_user_email_label', 'email');?> <br />
              <?php echo form_input($email);?>
            </div>
            <div class="form-group">
              <?php echo lang('create_user_phone_label', 'phone');?> <br />
              <?php echo form_input($phone);?>
            </div>
            <div class="form-group">
              <?php echo lang('create_user_password_label', 'password');?> <br />
              <?php echo form_input($password);?>
            </div>
            <div class="form-group">
              <?php echo lang('create_user_password_confirm_label', 'password_confirm');?> <br />
              <?php echo form_input($password_confirm);?>
            </div>
            <div class="form-group">
              <?php echo form_submit('submit', lang('create_user_submit_btn'),'class="btn btn-primary"');?>
              <a href="<?php echo base_url();?>auth" class="btn btn-default">Cancel</a>
            </div>
            <?php echo form_close();?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

