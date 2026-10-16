<!-- Start Page Header Section -->
<section class="bg-page-header">
  <div class="page-header-overlay">
    <div class="container">
      <div class="row">
        <div class="page-header">
          <div class="page-title">
            <h2>News</h2>
          </div><!-- .page-title -->
          <div class="page-header-content">
            <ol class="breadcrumb">
              <li><a href="<?php echo base_url();?>">Home</a></li>
              <li>News</li>
            </ol>
          </div><!-- .page-header-content -->
        </div><!-- .page-header -->
      </div><!-- .row -->
    </div><!-- .container -->
  </div><!-- .page-header-overlay -->
</section>
<!-- End Page Header Section -->
<!-- Start Single Events Section -->
<section class="bg-single-events">
  <div class="container">
    <div class="row">
      <div class="single-events">
        <div class="row">
          <div class="col-md-8">
            <div class="single-event-item">
              <div class="single-event-img">
                <div class="date-box">
                  <h3><?php echo date("d",strtotime((string) $details->post_display_date))?></h3>
                  <h5><?php echo date("F",strtotime((string) $details->post_display_date))?></h5>
                </div><!-- .date-box -->
              </div><!-- .single-event-img -->
              <div class="single-event-content">
                <h3 style="margin-left:62px;"><?php echo $details->post_name;?></h3>
                <p><?php echo $details->post_description;?></p>
                <div class="single-date-map">
                  <div class="single-date-option">
                    <ul class="single-date">
                      <li>
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <div class="h-adress-content">
                          <h6>date</h6>
                          <p><?php echo date(" D d F Y", strtotime((string) $details->post_display_date));?></p>
                        </div><!-- .h-adress-content -->
                      </li>
                    </ul>
                  </div>
                </div><!-- .single-date-map -->
              </div><!-- .single-event-content -->
            </div><!-- .single-event-item -->
          </div><!-- .col-md-8 -->
          <div class="col-md-4">
            <div class="sidebar">
              <div class="widget">
                <h4 class="sidebar-widget-title">Recent News</h4>
                <div class="widget-content">
                  <ul class="popular-news-option">
                    <?php foreach($popular_news as $value ){?>
                    <li>
                      <div class="popular-news-contant">
                        <?php if($value->post_category==2){ ?>
                        <h5><a href="<?php echo $value->post_external_link; ?>" target="_blank"><?php echo $value->post_name;?></a></h5>
                        <p><?php echo date('d F Y',strtotime((string) $value->post_display_date))?></p>
                        <?php } ?>
                        <?php if ($value->post_category==3) { ?>
                        <h5><a href="<?php echo $value->post_image; ?>" target="_blank"><?php echo $value->post_name;?></a></h5>
                        <p><?php echo date('d F Y',strtotime((string) $value->post_display_date))?></p>
                        <?php  } if ($value->post_category==1) { ?>
                        <h5><a href="<?php echo $value->post_slug; ?>"><?php echo $value->post_name;?></a></h5>
                        <p><?php echo date('d F Y',strtotime((string) $value->post_display_date))?></p>
                        <?php } ?>
                      </div><!-- .popular-news-img -->
                    </li>
                    <?php } ?>
                  </ul>
                </div><!-- .widget-content -->
              </div><!-- .widget -->
              <div class="widget">
                <h4 class="sidebar-widget-title">Photo Gallery</h4>
                <div class="widget-content">
                  <div class="footer-instagram">
                    <?php foreach($footer_gallery as $value){ ?>
                    <a href="<?php echo $value->media_slug;?>" data-rel="lightcase:myCollection" data-lc-categories="myCategory1"><img src="<?php echo $value->media_slug;?>" /></a>
                    <?php } ?>
                  </div><!-- .gallery-instagram -->
                </div><!-- .widget-content -->
              </div><!-- .widget -->
            </div><!-- .sidebar -->
          </div><!-- .col-md-4 -->
        </div><!-- .row -->
      </div><!-- .single-events -->
    </div><!-- .row -->
  </div><!-- .container -->
</section>
<!-- End Single Events Section -->
