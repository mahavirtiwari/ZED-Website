<?php $this->load->view('auth/_auth_head'); ?>
          <?php if ( ! empty($message)) { ?>
          <div id="infoMessage" class="alert alert-danger"><?php echo $message;?></div>
          <?php } ?>
          <?php echo form_open("auth/login", 'class="login-form" autocomplete="off"');?>
          <div class="form-group">
            <?php echo form_input($identity);?>
          </div>
          <div class="form-group">
            <?php echo form_input($password);?>
          </div>
          <?php if ( ! empty($captcha)) { $this->load->view('auth/_captcha', array('captcha_form' => 'login')); } ?>
          <?php echo form_submit('submit', lang('login_submit_btn'),'class="btn btn-primary btn-block"');?>
          <?php echo form_close();?>
<?php $this->load->view('auth/_auth_foot'); ?>
