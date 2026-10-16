<!-- Start Page Header Section -->
<section class="bg-page-header">
  <div class=" ">
    <div class="container">
      <div class="row">
        <div class="page-header">
          <div class="page-title">
            <h2>Photo Gallery</h2>
          </div><!-- .page-title -->
          <div class="page-header-content">
            <ol class="breadcrumb">
              <li>By Month</li>
            </ol>
          </div><!-- .page-header-content -->
        </div><!-- .page-header -->
      </div><!-- .row -->
    </div><!-- .container -->
  </div><!-- .page-header-overlay -->
</section>
<!-- End Page Header Section -->
<!-- Start Recent Project Section -->
<section class="bg-single-services">
  <div class="container">
    <div class="row">
      <div class="recent-project gallery2-items">
        <div class="portfolio-items">
          <?php foreach($details as $value ) { ?>
          <div class="item cat-1" data-category="transition">
            <div class="item-inner">
              <div class="portfolio-img">
                <div class="overlay-project"></div><!-- .overlay-project -->
                <img src="<?php echo $value->media_slug;?>" alt="gallery-img-1">
                <div class="project-plus">
                  <a href="<?php echo base_url();?>gallery/<?php echo strtolower($value->year);?>/<?php echo strtolower($value->month);?>"><i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
                <div class="recent-project-content">
                  <p><a href="<?php echo base_url();?>gallery/<?php echo strtolower($value->year);?>/<?php echo strtolower($value->month);?>"><?php echo $value->month?> - <?php echo $value->year;?></a></p>
                </div><!-- .latest-port-content -->
              </div><!-- /.portfolio-img -->
            </div><!-- .item-inner -->
          </div><!-- .items -->
          <?php } ?>
        </div><!-- .isotope-items -->
      </div><!-- .recent-project -->
    </div><!-- .row -->
  </div><!-- .container -->
</section>
<!-- End Recent Project Section -->

