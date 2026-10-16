<header id="headerPos" class="header-style-1">
  <div class="bg-header-top">
    <div class="container"> 
        <div class="header-top"> 
              <a href="http://msme.gov.in/" target="_blank" class="img-responsive">
              <img src="<?php echo base_url();?>assets/images/ministry.png" alt="MSME Logo Updated">
                <!-- <span>Ministry of <strong>Micro, small &amp; Medium Enterprises </strong></span> -->
              </a> 
              <a href="/"><img src="<?php echo base_url();?>assets/images/zed-logo.png" alt="ZED Logo" class="img-responsive "></a>
              
              <a href="http://www.qcin.org" target="_blank" class="img-responsive" style="display:none;">
                <!-- <span><strong>Quality Council<br>of India</strong></span> -->
                <img src="<?php echo base_url() ?>assets/images/qci.png"  alt="QCI Logo">
              </a>  
        </div><!-- .header-top --> 
    </div><!-- .container -->
  </div><!-- .bg-header-top -->
  <!-- Start Menu -->
  <div class="bg-main-menu menu-scroll">
    <div class="container">
      <div class="row">
        <div class="main-menu">
          <nav class="navbar">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <div class="menu-right-option pull-right visible-xs" id="search-container">
                   <div class="input-icons">
                      <i class="fa fa-search icon"></i>
                       <?php echo form_open_multipart(base_url().'search',array('onsubmit'=>'return validateform()')); ?>
                      <input class="form-control input-field" type="text" placeholder="Search" name="search_query"> 
                        <?php echo form_close();?>
                   </div> 
                </div><!-- .header-search-box -->
              <a class="navbar-brand" style="display:none" href="/"><img src="<?php echo base_url();?>assets/images/zed-logo-hq.png" alt="ZED Logo" class="img-responsive"></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li><a href="<?php echo base_url();?>">
              <img src="/assets/images/svgicons/home.svg"/></a></li>
                <?php foreach($menu as $value) {  
                  if($value->menu_type==1){ 
                    $submenu=explode(',',$value->page_name);
                    $class_name='';
                    if(!empty($submenu)){ 
                      $class_name="dropdown";
                      $sub_slug=explode(',',$value->page_slug);
                      $active=(in_array($this->uri->segment(1),$sub_slug) ? 'active': ''); 
                    }
                    ?>
                    <li class="<?php echo $class_name;?>"><a href="javascript:void(0);"  <?php echo !empty($class_name) ? 'class="dropdown-toggle '.$active.'" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"' : '';?>><?php echo $value->menu_name;?><?php if(!empty($submenu)){ ?> <span class="caret"></span><?php  } ?></a>
                      <?php if(!empty($submenu)){?>
                      <ul class="dropdown-menu sub-menu">
                        <?php for($i=0;$i<count($submenu);$i++) { ?>
                        <?php $sub_link = isset($sub_slug[$i]) ? strtolower($sub_slug[$i]) : url_title(strtolower($submenu[$i])); ?>
                        <li <?php echo $this->uri->segment(1) == $sub_link ? 'class="sub-active"' : ''; ?>>
                          <a href="<?php echo base_url().$sub_link;?>"> <?php echo $submenu[$i]?></a>
                        </li>
                        <?php } ?>
                      </ul>
                      <?php } ?>
                    </li>
                    <?php 
                  }
                  elseif($value->menu_type==0){ ?>
                  <?php $page_link = strtolower((string) $value->page_slug); ?>
                  <li <?php echo $this->uri->segment(1)==$page_link ? 'class="active"' : ''; ?>><a class="<?php echo $page_link=='benefits-to-msmes' ? 'benefits-to-msmes' : ''?>" href="<?php echo base_url().$page_link;?>"><?php echo $value->menu_name;?></a></li>
                  <?php } 
                  elseif($value->menu_type==3){ ?>
                  <li><a href="<?php echo $value->menu_external_link;?>" target="_blank"><?php echo $value->menu_name;?></a></li>
                  <?php } ?>
                  <?php 	} ?>
                </ul>
                <div class="menu-right-option pull-right" id="search-container">
                   <div class="input-icons">
                      <i class="fa fa-search icon"></i>
                       <?php echo form_open_multipart(base_url().'search',array('onsubmit'=>'return validateform()')); ?>
                      <input class="form-control input-field" type="text" placeholder="Search" name="search_query"> 
                        <?php echo form_close();?>
                   </div>
                  <!-- <div class="search-box search-toggle"> 
                    <i class="fa fa-search first_click" aria-hidden="true" style="display: block;"></i>
                    <i class="fa fa-times second_click" aria-hidden="true" style="display: none;"></i>
                  </div>
                  <div class="search-box-text">
                    <?php echo form_open_multipart(base_url().'search',array('onsubmit'=>'return validateform()')); ?>
                    <input type="search" autocomplete="off" name="search_query" id="all-search" placeholder="Start typing...">
                    <?php echo form_close();?>
                  </div> -->
                </div><!-- .header-search-box -->
              </div><!-- .navbar-collapse -->
            </nav>
          </div><!-- .main-menu -->
        </div><!-- .row -->
      </div><!-- .container -->
    </div><!-- .bg-main-menu -->
  </header>

