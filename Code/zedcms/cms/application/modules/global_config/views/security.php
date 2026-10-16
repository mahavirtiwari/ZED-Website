<div class="container form-page">
	<div class="row">
		<?php if ($this->session->flashdata('message')) { ?>
		<div class="alert alert-success"><?php echo html_escape($this->session->flashdata('message'));?></div>
		<?php } ?>
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1" data-toggle="tab" data-target="#tab1">Security</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
						<?php echo form_open(base_url().'global_config/security', array('id' => 'security-settings')); ?>
						<div class="form-group">
							<label for="captcha_cms_login">Captcha on CMS login</label>
							<?php echo form_dropdown('captcha_cms_login', array('1' => 'Enabled', '0' => 'Disabled'), $details['captcha_cms_login'], 'id="captcha_cms_login" class="form-control"'); ?>
							<p class="help-block">A 6-character security code on the CMS login and forgot-password pages.</p>
						</div>
						<div class="form-group">
							<label for="contact_form_type">Contact Us form</label>
							<?php echo form_dropdown('contact_form_type', array('zoho' => 'Zoho Desk form (creates a Zoho Desk ticket)', 'site' => 'Website form (saved in CMS Contacts and e-mailed)'), $details['contact_form_type'], 'id="contact_form_type" class="form-control"'); ?>
							<p class="help-block">The Zoho Desk form uses Zoho's own captcha. The captcha switch below applies to the website form.</p>
						</div>
						<div class="form-group">
							<label for="captcha_contact_us">Captcha on Contact Us (website form)</label>
							<?php echo form_dropdown('captcha_contact_us', array('1' => 'Enabled', '0' => 'Disabled'), $details['captcha_contact_us'], 'id="captcha_contact_us" class="form-control"'); ?>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary" name="zed-save" value="1">Save</button>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
