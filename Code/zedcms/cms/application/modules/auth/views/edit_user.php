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
           
           <?php echo form_open(uri_string());?>

           <div class="form-group">
            <?php echo lang('edit_user_fname_label', 'first_name');?> <br />
            <?php echo form_input($first_name);?>
          </div>

          <div class="form-group">
            <?php echo lang('edit_user_lname_label', 'last_name');?> <br />
            <?php echo form_input($last_name);?>
          </div>

          <div class="form-group">
            <?php echo lang('edit_user_company_label', 'company');?> <br />
            <?php echo form_input($company);?>
          </div>

          <div class="form-group">
            <?php echo lang('edit_user_phone_label', 'phone');?> <br />
            <?php echo form_input($phone);?>
          </div>

          <div class="form-group">
            <?php echo lang('edit_user_password_label', 'password');?> <br />
            <?php echo form_input($password);?>
          </div>

          <div class="form-group">
            <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?><br />
            <?php echo form_input($password_confirm);?>
          </div>

          <?php if ($this->ion_auth->is_admin()): ?>
            <div class="form-group">
              <lable>Role</lable>
              <?php 
              $role=array();
              $currentrole=array();
              foreach($groups as $grp){
               $role[$grp['id']]=$grp['name'];
             }
             foreach($currentGroups as $grp) {
               $currentrole=$grp->id;
             }
             $options=$role;
             echo form_dropdown('u_role', $options, $currentrole,'class="form-control" ' );
             ?>

           </div>
         <?php endif ?>
         <?php echo form_hidden('id', $user->id);?>
         <?php echo form_hidden($csrf); ?>

         <div class="form-group">
          <?php echo form_submit('submit', lang('edit_user_submit_btn'),'class="btn btn-primary"');?>
          
        </div>
      </div>
      

      <?php echo form_close();?>

    </div>
  </div>
</div>
</div>
</div>
</div>

