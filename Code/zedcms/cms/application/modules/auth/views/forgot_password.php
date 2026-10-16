<?php $this->load->view('auth/_auth_head'); ?>
          <h4><?php echo lang('forgot_password_heading');?></h4>
          <p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
          <?php if ( ! empty($message)) { ?>
          <div id="infoMessage" class="alert alert-danger"><?php echo $message;?></div>
          <?php } ?>
          <?php echo form_open("auth/forgot_password", 'class="login-form" autocomplete="off"');?>
          <div class="form-group">
            <label for="identity"><?php echo (($type=='email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label));?></label>
            <?php echo form_input($identity);?>
          </div>
          <?php if ( ! empty($captcha)) { $this->load->view('auth/_captcha', array('captcha_form' => 'forgot_password')); } ?>
          <?php echo form_submit('submit', lang('forgot_password_submit_btn'), 'class="btn btn-primary btn-block"');?>
          <?php echo form_close();?>
<?php $this->load->view('auth/_auth_foot'); ?>
