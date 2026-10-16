<?php $this->load->view('auth/_auth_head'); ?>
          <h4><?php echo lang('reset_password_heading');?></h4>
          <?php if ( ! empty($message)) { ?>
          <div id="infoMessage" class="alert alert-danger"><?php echo $message;?></div>
          <?php } ?>
          <?php echo form_open('auth/reset_password/' . $code, 'class="login-form" autocomplete="off"');?>
          <div class="form-group">
            <label for="new"><?php echo sprintf(lang('reset_password_new_password_label'), $min_password_length);?></label>
            <?php echo form_input($new_password);?>
          </div>
          <div class="form-group">
            <?php echo lang('reset_password_new_password_confirm_label', 'new_confirm');?>
            <?php echo form_input($new_password_confirm);?>
          </div>
          <?php echo form_input($user_id);?>
          <?php echo form_hidden($csrf); ?>
          <?php echo form_submit('submit', lang('reset_password_submit_btn'), 'class="btn btn-primary btn-block"');?>
          <?php echo form_close();?>
<?php $this->load->view('auth/_auth_foot'); ?>
