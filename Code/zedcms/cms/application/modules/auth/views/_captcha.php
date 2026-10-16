<?php
/**
 * Captcha field for the stand-alone auth forms.
 * @var string $captcha_form  'login' or 'forgot_password'
 */
$captcha_img = 'captcha-img-'.$captcha_form;
?>
          <div class="form-group zed-captcha">
            <label for="captcha">Security code</label>
            <div class="zed-captcha-row">
              <img id="<?php echo $captcha_img; ?>" src="<?php echo base_url('captcha/image/'.$captcha_form); ?>?r=<?php echo time(); ?>" width="190" height="60" alt="Security code image">
              <button type="button" class="btn btn-default zed-captcha-refresh" data-captcha-refresh="<?php echo $captcha_img; ?>" data-captcha-input="captcha" title="Show a new code">&#x21bb;</button>
            </div>
            <input type="text" name="captcha" id="captcha" class="form-control" maxlength="6" placeholder="Enter the code" autocomplete="off" autocapitalize="characters" spellcheck="false" required>
          </div>
