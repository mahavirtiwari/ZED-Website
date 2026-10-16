<?php $pageWidget = (string) ($details->page_widget ?? ''); ?>
<style>
  .jumbotron p{
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 400;
  }
  </style>
<div class="bg-page-main">
<!-- Start Page Header Section -->
<section class="bg-page-header ">
  <div class=" ">
    <div class="container">
      <div class="row"> 
        <div class="page-header">
          <div class="page-title">
            <h2><?php echo $details->page_name;?></h2>
          </div><!-- .page-title -->
          <div class="page-header-content">
            <ol class="breadcrumb"> 
              <li><a href="{{ url('') }}">Home</a></li>
              <li class="active"><?php echo (isset($page_sidemenu[0]->menu_name) ? $page_sidemenu[0]->menu_name : $details->page_name);?></li>
              <li class="active"><?php echo $details->page_name;?></li>
            </ol>
          </div><!-- .page-header-content -->
        </div><!-- .page-header --> 
      </div><!-- .row -->
    </div><!-- .container -->
  </div><!-- .page-header-overlay -->
</section>
<!-- Start Recent Project Section -->
<section class="bg-single-services">
  <div class="container">
    <div class=" ">
      <div class="single-services single-page">
        <div class="row">
          <?php 
          if ($page_type==2 || $page_type==3) 
            { ?>
          <div class="col-md-3">
            <div id="scrollNav" class="single-left-services-list">
              <ul class="nav nav-tabs" role="tablist">
                <?php  foreach ($page_sidemenu as $key => $value) {
                  $submenu=explode(',',$value->page_name);
                  // sort($submenu);
                  $class_name='';
                  if(!empty($submenu)){
                    $class_name="dropdown";
                    $sub_slug=explode(',',$value->page_slug);
                    $active=(in_array(request()->segment(1),$sub_slug) ? 'active': ''); 
                  }
                  ?>
                  <?php for($i=0;$i<count($submenu);$i++) { ?>
                  <?php $sub_link = isset($sub_slug[$i]) ? strtolower($sub_slug[$i]) : url_title(strtolower($submenu[$i])); ?>
                  <li <?php echo request()->segment(1) == $sub_link ? 'class="active"' : ''; ?>><a href="<?php echo base_url().$sub_link;?>"><?php echo $submenu[$i]?><span class="pull-right"><i class="fa fa-angle-double-right" aria-hidden="true"></i> </span></a></li>
                  <?php } ?>
                  <?php } ?>
                </ul> 
              </div><!-- .single-left-services-list -->
            </div><!-- .col-md-3 -->
            <div class="col-md-9">
              <div class="single-services-content single-page-content">
                @unless ($pageWidget === 'embed_only')
                <div class="single-page-full-content"> <?php echo content_html($details->page_description);?></div>
                @endunless
                @include('public.partials.page-widget')
                </div><!-- .tab-content -->
              </div><!-- .col-md-9 -->
              <?php  }
              else { ?>
              <div class="col-md-12 text-center">
                <h3><?php echo ucwords($details->page_name);?></h3>
                @unless ($pageWidget === 'embed_only')
                <div class="single-page-full-content"><?php echo content_html($details->page_description);?></div>
                @endunless
                @include('public.partials.page-widget')
              </div>
              <?php } ?>
</div><!-- .row -->
            </div><!-- .single-services -->
          </div><!-- .row -->
        </div><!-- .container -->
      </section> 
    <!-- End Recent Project Section -->
    </div>
    <script type="text/javascript">
    //  var width = $(window).width();
    //   if (width > 768) {
    //     $(window).scroll(function(){
    //       if ($(this).scrollTop() > 150) {
    //         $('#scrollNav').addClass('scroll-nav');
    //       } else {
    //         $('#scrollNav').removeClass('scroll-nav');
    //       }
    //     }); 
    //   }
</script>