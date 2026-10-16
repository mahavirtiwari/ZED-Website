<?php
/**
 * The site's own Contact Us form (CMS Settings > Security > Contact Us form = Website form).
 * Saved to CMS Contacts and e-mailed by Home::contactus().
 */
$field_error = function ($name) {
	$error = form_error($name, '<span class="help-block">', '</span>');
	return $error;
};
?>
<div class="zed-contact-form">
    <h3>Send us your query</h3>
    <?php if (validation_errors() || ! empty($captcha_error)) { ?>
    <div class="alert alert-danger">Please correct the highlighted fields and try again.</div>
    <?php } ?>
    <?php echo form_open(base_url().'contact-us', array('id' => 'zed-contact-form', 'novalidate' => 'novalidate')); ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group<?php echo form_error('name') ? ' has-error' : ''; ?>">
                    <label for="cf-name">Name <span class="text-danger">*</span></label>
                    <input type="text" id="cf-name" name="name" class="form-control" maxlength="100" required value="<?php echo set_value('name'); ?>">
                    <?php echo $field_error('name'); ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group<?php echo form_error('email') ? ' has-error' : ''; ?>">
                    <label for="cf-email">Email <span class="text-danger">*</span></label>
                    <input type="email" id="cf-email" name="email" class="form-control" maxlength="150" required value="<?php echo set_value('email'); ?>">
                    <?php echo $field_error('email'); ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group<?php echo form_error('phone') ? ' has-error' : ''; ?>">
                    <label for="cf-phone">Phone</label>
                    <input type="tel" id="cf-phone" name="phone" class="form-control" maxlength="100" value="<?php echo set_value('phone'); ?>">
                    <?php echo $field_error('phone'); ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group<?php echo form_error('enterprise_name') ? ' has-error' : ''; ?>">
                    <label for="cf-enterprise">Enterprise name</label>
                    <input type="text" id="cf-enterprise" name="enterprise_name" class="form-control" maxlength="200" value="<?php echo set_value('enterprise_name'); ?>">
                    <?php echo $field_error('enterprise_name'); ?>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group<?php echo form_error('query_category') ? ' has-error' : ''; ?>">
                    <label for="cf-category">Query category <span class="text-danger">*</span></label>
                    <select id="cf-category" name="query_category" class="form-control" required>
                        <option value="">Select a category</option>
                        <?php foreach ($query_categories as $category) { ?>
                        <option value="<?php echo html_escape($category); ?>"<?php echo set_select('query_category', $category); ?>><?php echo html_escape($category); ?></option>
                        <?php } ?>
                    </select>
                    <?php echo $field_error('query_category'); ?>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group<?php echo form_error('message') ? ' has-error' : ''; ?>">
                    <label for="cf-message">Message <span class="text-danger">*</span></label>
                    <textarea id="cf-message" name="message" class="form-control" rows="6" maxlength="3000" required><?php echo set_value('message'); ?></textarea>
                    <?php echo $field_error('message'); ?>
                </div>
            </div>
            <?php if ( ! empty($captcha)) { ?>
            <div class="col-sm-12">
                <div class="form-group zed-captcha<?php echo (form_error('captcha') || ! empty($captcha_error)) ? ' has-error' : ''; ?>">
                    <label for="cf-captcha">Security code <span class="text-danger">*</span></label>
                    <div class="zed-captcha-row">
                        <img id="cf-captcha-img" src="<?php echo base_url('captcha/image/contact'); ?>?r=<?php echo time(); ?>" width="190" height="60" alt="Security code image">
                        <button type="button" class="btn btn-default" data-captcha-refresh="cf-captcha-img" data-captcha-input="cf-captcha" title="Show a new code" aria-label="Show a new security code">&#x21bb;</button>
                    </div>
                    <input type="text" id="cf-captcha" name="captcha" class="form-control zed-captcha-input" maxlength="6" required autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="Enter the code">
                    <?php echo $field_error('captcha'); ?>
                    <?php if ( ! empty($captcha_error)) { ?><span class="help-block"><?php echo html_escape($captcha_error); ?></span><?php } ?>
                </div>
            </div>
            <?php } ?>
            <div class="col-sm-12">
                <button type="submit" name="submit" value="1" class="btn btn-primary">Submit</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<script src="<?php echo base_url(); ?>assets/js/zed-captcha.js"></script>
