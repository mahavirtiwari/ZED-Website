<div class="container form-page">
  <div class="row">
    <div class="col-sm-18">
      <div class="zed-tabs">
        <ul class="nav nav-tabs">
          <li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active fade in" id="tab1">
           <?php echo form_open("auth/deactivate/".$user->id);?>
           <div class="form-group">
             <?php echo lang('deactivate_confirm_y_label', 'confirm');?>
             <input type="radio" name="confirm" value="yes" checked="checked" />
             <?php echo lang('deactivate_confirm_n_label', 'confirm');?>
             <input type="radio" name="confirm" value="no" />
           </div>

           <?php echo form_hidden($csrf); ?>
           <?php echo form_hidden(array('id'=>$user->id)); ?>

           <p><?php echo form_submit('submit', lang('deactivate_submit_btn'),'class="btn btn-primary"');?></p>

           <?php echo form_close();?>
           
         </div>
       </div>
     </div>
   </div>
 </div>
</div>


