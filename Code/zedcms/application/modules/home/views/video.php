<section class="container" id="zed-main-container">
 <div class="row">
   <div class="col-md-24"><h1></h1></div>
   <nav class="col-md-7 col-sm-7 col-sm-24">
    <div class="list-group">
      <a href="<?php echo base_url();?>home" class="<?php echo ($this->uri->segment(1)=='home' ? 'active list-group-item': 'list-group-item');?>">Home</a>
      <?php foreach($menu as $value) { 
        if($value->menu_type==2){ 
          $submenu=explode(',',$value->page_name);  ?>
          <?php for($i=0;$i<count($submenu);$i++) { ?>
          <a class="<?php echo ($this->uri->segment(1)==url_title(strtolower($submenu[$i])) ? 'active list-group-item': 'list-group-item');?>" href="<?php echo base_url().url_title(strtolower($submenu[$i]));?>"><?php echo $submenu[$i]?></a>
          <?php } ?>
          <?php }  ?>
          <?php }
          ?>
          <a href="<?php echo base_url();?>gallery" class="<?php echo ($this->uri->segment(1)=='gallery' ? 'active list-group-item': 'list-group-item');?>">Gallery</a>
          <a href="<?php echo base_url();?>video" class="<?php echo ($this->uri->segment(1)=='video' ? 'active list-group-item': 'list-group-item');?>">video</a>
          <a href="<?php echo base_url();?>contact-us" class="<?php echo ($this->uri->segment(1)=='contact-us' ? 'active list-group-item': 'list-group-item');?>">Contact Us</a>
        </div>
      </nav>
      <article class="col-md-17 col-sm-17" role="main">
        <h2>Video</h2>
      </article>
    </div>
  </section>