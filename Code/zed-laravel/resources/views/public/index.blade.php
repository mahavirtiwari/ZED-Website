<div>
 <!-- component-first Slider Section --> 
<section class="first-component">
        <div class="container">
            <div class="row">
                    <div class="col-md-7 col-md-push-5 col-xs-12">
                          <div class="first-component-carousel">
                            <div class="owl-slider1 owl-carousel owl-theme">  
                              <?php $i=0; foreach( $slider as $value ) { ?>
                                <div class="item"> 
                                      <img src="<?php echo $value->slider_slug;?>" alt=""> 
                                  </div> 
                              <?php $i++;} ?>
                            </div> 
                          </div> 
                    </div>
                    <div class="col-md-5 col-md-pull-7 col-xs-12"> 
                            <div class="first-component-items"> 
                                    <h2 class="section-header-new">{{ site_text('home_hero_title') }}</h2> 
                                    <div>
                                          <p><?php echo site_text('home_hero_text'); ?></p> 
                                          <div class="buttons-block"> 
                                            @if (site_text('home_hero_button1_text') !== '')<a href="{{ site_url('home_hero_button1_url') }}" class="btn btn-zed btn-block">{{ site_text('home_hero_button1_text') }}</a>@endif
                                            @if (site_text('home_hero_button2_text') !== '')<a href="{{ site_url('home_hero_button2_url') }}" class="btn btn-default btn-block">{{ site_text('home_hero_button2_text') }}</a>@endif
                                          </div>
                                        </div>        
                              </div>
                    </div>  
              </div>
              <div class="row">
              <?php if(!empty($breaking_news)){ ?> 
                   <div class="col-md-12 zed-announcement">  
                       <p>{{ site_text('home_announcement_label') }}</p>
                       <div class="zed-announcement-item ">
                         <marquee onmouseover="this.stop();" onmouseout="this.start();" direction="left">
                           <?php $i=0;foreach ($breaking_news as $key => $value) { ?>
                           <?php if($value->post_image){
                             ?>
                             <a href="<?php echo $value->post_image ?>" target="_blank" class="<?php echo count($breaking_news)>1 ? 'importance-sperator' :''?>"><?php echo $value->post_name ?></a>
                             <?php
                           }else if($value->post_external_link){ ?>
                           <a href="<?php echo $value->post_external_link ?>" target="_blank" class="<?php echo count($breaking_news)>1 ? 'importance-sperator' :''?>"><?php echo $value->post_name ?></a>
                           <?php }else { ?>
                           <a href="<?php echo $value->post_slug ?>" target="_blank" class="<?php echo count($breaking_news)>1 ? 'importance-sperator' :''?>"><?php echo $value->post_name ?></a>
                           <?php } ?>
                           <?php $i++;} ?>
                           </marquee> 
                     </div> 
                   </div>
                    <?php } ?> 
            </div>
        </div>
</section>
@if (site_flag('home_show_initiatives') && !empty($allprograms))
<section class="bg-services-section">
  <div class="container">
    <div class="services-section">
      <div class="row">
        <div class="col-md-12">
          <h2 class="section-header">{{ site_text('home_title_initiatives') }}</h2> 
      </div>
        <?php 
          foreach( $allprograms as $value ) {
        ?>
          <div class="col-sm-3 item">
          <div class="panel panel-default services-content">
              <div class="panel-body">   
                    <img src="<?php echo $value->image;?>" alt=""  />
                    <h4><?php echo $value->name;?></h4>
                    <small>(<?php echo $value->caption;?>)</small>
                    <br>
                    <a href="<?php echo $value->link;?>" target="_blank" class="btn btn-zed btn-block"><?php echo e(trim((string) $value->button_name) !== '' ? $value->button_name : 'REGISTER NOW'); ?></a>  
                </div>
              </div><!-- .services-content --> 
          </div><!-- .col-md-3 -->
        <?php
          }
        ?>
      </div>
      <div class="financial-subsidy text-center" style="display:none;"><img src="/assets/images/financial_subsidy.png" alt="Financial Support (Subsidy)"></div>
    </div><!-- .services-section -->
  </div><!-- .container -->
</section>
@endif
<?php if(site_flag('home_show_incentives') && isset($incentives ) && !empty($incentives )){?>
<section class="bg-services-section">
  <div class="container">
    <div class="services-section">
      <div class="row">
        <div class="col-md-12">
          <h2 class="section-header">{{ site_text('home_title_incentives') }}</h2> 
      </div>
        <?php 
          foreach( $incentives as $value ) {
        ?>
          <div class="col-sm-3 item">
          <div class="panel panel-default benefits services-content">
              <div class="panel-body">   
                    <img src="<?php echo $value->image;?>" alt=""  />
                    <h4><?php echo $value->name;?></h4>
                    <small>(<?php echo $value->caption;?>)</small> 
                    <a href="<?php echo $value->link;?>" target="_blank" class="btn btn-zed btn-block"><?php echo $value->button_name;?>
                    </a>  
                </div>
              </div><!-- .services-content --> 
          </div><!-- .col-md-3 -->
        <?php
          }
        ?>
      </div>
      <div class="financial-subsidy text-center" style="display:none;"><img src="/assets/images/financial_subsidy.png" alt="Financial Support (Subsidy)"></div>
    </div><!-- .services-section -->
  </div><!-- .container -->
</section>
<?php } ?>
@if (site_flag('home_show_about'))
<section class="bg-about-greenforest" id="about">
  <div class="container">
    <div class="row">
      <div class="about-greenforest block-highlight">
        <div class="row">
          <div class="col-md-12">
            <div class="about-greenforest-content">
            
              <?php if(!empty($social_urls['about_short_description']))
              {?>
              <h2 class="section-header"><?php echo $social_urls['footer_heading'];?></h2>
              <?php } ?>
              <div class="about-section">
              <div class="about-section-content">
              <?php if(!empty($social_urls['about_short_description']))
              { ?>
              <p><?php echo $social_urls['about_short_description'];?></p>
              <?php } ?>
              @if (site_text('home_about_button_text') !== '')<a href="{{ site_url('home_about_button_url') }}" class="btn btn-default">{{ site_text('home_about_button_text') }}</a>@endif
              </div>
              <div>
                <div class="about-greenforest-img hide">
                  <img src="assets/images/zed-home-o.png" alt="ZED Assessment QCI" class="img-responsive" />
                </div><!-- .about-greenforest-img -->

                <img src="<?php echo isset($ratingImageProcess->image_path) ?  base_url().$ratingImageProcess->image_path : '';?>" alt="ZED Rating">
            </div>
          
              </div><!--about-section-->
            </div><!-- .about-greenforest-content -->
          </div><!-- .col-md-12 -->
          
        </div>
      </div><!-- .about-greenforest -->
    </div><!-- .row -->
  </div><!-- .container -->
</section>
@endif 
@if (site_flag('home_show_steps'))
<section class="zed-4-step-process">
  <div class="container">
    <div class="row ">
      <div class="col-md-12">
        {{-- CMS > Settings > Home page > Certification steps image (built-in Steps-img.png until replaced) --}}
        <img src="{{ site_image('home_steps_image') }}" alt="ZED certification steps" />
      </div>
    </div>
  </div>
</section>
@endif
@if (site_flag('home_show_notifications') && !empty($allnotifications))
<section class="bg-highlight-news">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="text-center news-sec">
          @foreach ($allnotifications as $value)
            <a class="btn btn-news btn-lg" href="{{ $value->type === 'link' ? $value->link : media_url($value->file_path) }}" target="_blank" rel="noopener">{{ $value->name }}</a>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
@endif
<?php if($counters['dashboard_status']==1){?>
<section class="bg-count2-section">
  <img class="dashboard-bricks" src="/assets/images/svgicons/dashboard-bricks.svg"/>
  <img class="dashboard-circle" src="/assets/images/svgicons/dashboard-circle.svg"/>
  <div class="count-overlay">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <h2 class="section-header">{{ site_text('home_title_dashboard') }}</h2> 
          </div>
        <?php foreach (['MsmeRegistered' => 'msme_registered', 'Pledge' => 'pledge', 'BronzeCertified' => 'bronze_certified', 'SilverCertified' => 'silver_certified', 'GoldCertified' => 'gold_certified'] as $liveKey => $cmsKey) { if (!isset($counters['counts'][$liveKey]) && !empty($counters[$cmsKey])) { $counters['counts'][$liveKey] = $counters[$cmsKey]; } } ?>
        <div class="count-option">
          <div class="row">
            <?php if( $counters['masmes_participated_in_awareness_programmes'] > 0){?>
            <div class="col-md-4 col-sm-6 col-xs-6">
              <div class="count-items">
                <!-- <i><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><path fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" d="M20,46H2v-1.242c0-1.775,1.164-3.334,2.866-3.838C6.46,40.448,8.604,40,11,40c2.356,0,4.514,0.456,6.125,0.932C18.83,41.436,20,42.994,20,44.773V46z" stroke-linejoin="miter"/><circle fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="11" cy="31" r="5" stroke-linejoin="miter"/><path fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" d="M46,46H28v-1.242c0-1.775,1.164-3.334,2.866-3.838C32.46,40.448,34.604,40,37,40c2.356,0,4.514,0.456,6.125,0.932C44.83,41.436,46,42.994,46,44.773V46z" stroke-linejoin="miter"/><circle fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="37" cy="31" r="5" stroke-linejoin="miter"/><path data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" d="M33,22H15v-1.242c0-1.775,1.164-3.334,2.866-3.838C19.46,16.448,21.604,16,24,16c2.356,0,4.514,0.456,6.125,0.932C31.83,17.436,33,18.994,33,20.773V22z" stroke-linejoin="miter"/><circle data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="24" cy="7" r="5" stroke-linejoin="miter"/></g></svg></i>
                <br> -->
                <span class="counter aware-count-clr" data-num="<?php echo $counters['masmes_participated_in_awareness_programmes']; ?>"><?php echo $counters['masmes_participated_in_awareness_programmes']; ?></span>
                <h4>{{ site_text('counter_label_awareness') }}</h4>
              </div><!-- .count-items -->
            </div><!-- .col-md-3 -->
          <?php } ?>
          <?php //if( $counters['msme_registered'] > 0){ // From DB
              if( isset($counters['counts']['MsmeRegistered'] ) ) { ?>
            <div class="col-md-4 col-sm-6 col-xs-6">
              <div class="count-items count-css">
                <!-- <i><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><rect x="6" y="2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" width="36" height="44" stroke-linejoin="miter"/><circle data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="24" cy="24" r="7" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="24" y1="17" x2="24" y2="14" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="28.95" y1="19.05" x2="31.071" y2="16.929" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="31" y1="24" x2="34" y2="24" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="28.95" y1="28.95" x2="31.071" y2="31.071" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="24" y1="31" x2="24" y2="34" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="19.05" y1="28.95" x2="16.929" y2="31.071" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="17" y1="24" x2="14" y2="24" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="19.05" y1="19.05" x2="16.929" y2="16.929" stroke-linejoin="miter"/><circle data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="24" cy="24" r="1" stroke-linejoin="miter"/><circle data-color="color-2" data-stroke="none" fill="#ffffff" cx="24" cy="24" r="1" stroke-linejoin="miter" stroke-linecap="square"/></g></svg></i>
                <br> -->
                <!-- <span class="counter msmer-count-clr" data-num="<?php //echo $counters['msme_registered']; ?>"><?php //echo $counters['msme_registered']; ?></span> -->
                <span class="counter msmer-count-clr" data-num="<?php echo $counters['counts']['MsmeRegistered']; ?>"><?php echo $counters['counts']['MsmeRegistered']; ?></span>
                <h4>{{ site_text('counter_label_registered') }}</h4>
              </div><!-- .count-items -->
            </div><!-- .col-md-3 -->
          <?php } ?>
          <?php //if( $counters['pledge'] > 0){
            if( isset( $counters['counts']['Pledge'] ) ) {
            ?>
            <div class="col-md-4 col-sm-6 col-xs-6">
              <div class="count-items count-css">
                <!-- <i><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><polyline fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="37,9 42,9 42,46 6,46 6,9 11,9 " stroke-linejoin="miter"/><polyline data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="17,28 22,33 31,24 " stroke-linejoin="miter"/><path fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" d="M29,7c0-2.761-2.239-5-5-5s-5,2.239-5,5h-4v6h18V7H29z" stroke-linejoin="miter"/></g></svg></i>
                <br> -->
                <!-- <span class="counter pledge-count-clr" data-num="<?php //echo $counters['pledge']; ?>"><?php //echo $counters['pledge']; ?></span> -->
                <span class="counter msmer-count-clr" data-num="<?php echo $counters['counts']['Pledge']; ?>">
				<?php echo $counters['counts']['Pledge']; ?></span>

                <h4>{{ site_text('counter_label_pledge') }}</h4>
              </div><!-- .count-items -->
            </div><!-- .col-md-3 -->
          <?php } ?>
          <?php //if( $counters['pledge'] > 0){
            if( isset( $counters['counts']['FinancialSupport'] ) ) {
            ?>
            <div class="col-md-4 col-sm-6 col-xs-6">
              <div class="count-items count-css">
                <span class="counter msmer-count-clr" data-num="<?php echo $counters['counts']['FinancialSupport']; ?>">
				<?php echo "&#8377;" . $counters['counts']['FinancialSupport'];  ?>
				</span>
                <h4>{{ site_text('counter_label_financial') }}</h4>
              </div><!-- .count-items -->
            </div><!-- .col-md-3 -->
          <?php } ?>
          </div><!-- .row --> 
          <div class="row">
            <?php //if( $counters['bronze_certified'] > 0){ 
                if( isset( $counters['counts']['BronzeCertified'] ) ) {
              ?>
            <div class="col-md-4 col-sm-6 col-xs-6">
              <div class="count-items count-css">
                <!-- <i><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><circle fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="24" cy="24" r="2" stroke-linejoin="miter"/><path data-cap="butt" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" d="M35.519,42.718C41.8,38.844,46,31.921,46,24c0-12.15-9.85-22-22-22S2,11.85,2,24c0,7.921,4.2,14.844,10.481,18.718" stroke-linejoin="miter" stroke-linecap="butt"/><path data-cap="butt" data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" d="M30.283,34.21C33.709,32.097,36,28.321,36,24c0-6.627-5.373-12-12-12s-12,5.373-12,12c0,4.321,2.291,8.097,5.717,10.21" stroke-linejoin="miter" stroke-linecap="butt"/></g></svg></i>
                <br> -->
                <!-- <span class="counter bronze-count-clr" data-num="<?php //echo $counters['bronze_certified']; ?>"><?php //echo $counters['bronze_certified']; ?></span> -->
                <span class="counter msmer-count-clr" data-num="<?php echo $counters['counts']['BronzeCertified']; ?>"><?php echo $counters['counts']['BronzeCertified']; ?></span>
                <h4>{{ site_text('counter_label_bronze') }}</h4>
              </div><!-- .count-items -->
            </div><!-- .col-md-3 -->
            <?php } ?>
            <?php //if( $counters['silver_certified'] > 0){ 
                if( isset( $counters['counts']['SilverCertified'] ) ) {
              ?>
            <div class="col-md-4 col-sm-6 col-xs-6">
              <div class="count-items count-css">
                <!-- <i><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><line fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="24" y1="36" x2="24" y2="44" stroke-linejoin="miter"/><line data-cap="butt" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" x1="20" y1="36" x2="12" y2="46" stroke-linejoin="miter" stroke-linecap="butt"/><line data-cap="butt" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" x1="28" y1="36" x2="36" y2="46" stroke-linejoin="miter" stroke-linecap="butt"/><polyline fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="42,36 42,8 6,8 6,36 " stroke-linejoin="miter"/><polyline fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="22,8 22,4 26,4 26,8 " stroke-linejoin="miter"/><line fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="2" y1="36" x2="46" y2="36" stroke-linejoin="miter"/><line fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="46" y1="2" x2="30" y2="18" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="14" y1="18" x2="22" y2="18" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="14" y1="26" x2="34" y2="26" stroke-linejoin="miter"/></g></svg></i>
                <br> -->
                <!-- <span class="counter silver-count-clr" data-num="<?php //echo $counters['silver_certified']; ?>"><?php //echo $counters['silver_certified']; ?></span> -->
                <span class="counter msmer-count-clr" data-num="<?php echo $counters['counts']['SilverCertified']; ?>"><?php echo $counters['counts']['SilverCertified']; ?></span>
                <h4>{{ site_text('counter_label_silver') }}</h4>
              </div><!-- .count-items -->
            </div><!-- .col-md-3 -->
            <?php } ?>
            <?php //if( $counters['gold_certified'] > 0){
              if( isset( $counters['counts']['GoldCertified'] ) ) {
              ?>
            <div class="col-md-4 col-sm-6 col-xs-6">
              <div class="count-items count-css">
                <!-- <i><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><polyline data-cap="butt" data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" points="29.7,30.5 26,38 22,38 18.3,30.5 " stroke-linejoin="miter" stroke-linecap="butt"/><line data-cap="butt" data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" x1="18" y1="46" x2="22" y2="38" stroke-linejoin="miter" stroke-linecap="butt"/><line data-cap="butt" data-color="color-2" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" x1="26" y1="38" x2="30" y2="46" stroke-linejoin="miter" stroke-linecap="butt"/><path data-cap="butt" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" d="M17.4,30H16C8.3,30,2,36.3,2,44v2h44v-2c0-7.7-6.3-14-14-14h-1.4" stroke-linejoin="miter" stroke-linecap="butt"/><path data-cap="butt" fill="none" stroke="#333" stroke-width="2" stroke-miterlimit="10" d="M12,15.5c1.9,1.6,4.3,2.5,7,2.5c4.4,0,8.3-2.6,10-6.4c1.3,2.9,3.9,5.1,7,6" stroke-linejoin="miter" stroke-linecap="butt"/><path fill="none" stroke="#333" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" d="M24,32L24,32c-6.6,0-12-5.4-12-12v-6c0-6.6,5.4-12,12-12h0c6.6,0,12,5.4,12,12v6C36,26.6,30.6,32,24,32z" stroke-linejoin="miter"/></g></svg></i>
                <br> -->
                <!-- <span class="counter gold-count-clr" data-num="<?php //echo $counters['gold_certified']; ?>"><?php //echo $counters['gold_certified']; ?></span> -->
                <span class="counter msmer-count-clr" data-num="<?php echo $counters['counts']['GoldCertified']; ?>"><?php echo $counters['counts']['GoldCertified']; ?></span>
                <h4>{{ site_text('counter_label_gold') }}</h4>
              </div><!-- .count-items -->
            </div><!-- .col-md-3 -->
          <?php } ?>
          </div><!-- .row -->
        </div><!-- .count-section -->
      </div><!-- .row -->
    </div><!-- .container -->
  </div>
</section>
<?php } ?>
<!-- <section class="bg-about-greenforest hide">
  <div class="container">
    <div class="about-greenforest-content text-center" style="padding: 40px 0;">

    </div>
  </div>
</section> --> 
@if (site_flag('home_show_blocks'))
<section class="bg-services-section bg-services-section-2" id="services">
  <div class="container">
    <div class="row">
      <div class="our-services-option">
        <div class="row">
          <?php $icons=array('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="26" y1="8" x2="46" y2="8" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="2" y1="8" x2="8" y2="8" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="42" y1="24" x2="46" y2="24" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="2" y1="24" x2="24" y2="24" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="26" y1="40" x2="46" y2="40" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="2" y1="40" x2="8" y2="40" stroke-linejoin="miter"/><circle fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="14" cy="8" r="6" stroke-linejoin="miter"/><circle data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="30" cy="24" r="6" stroke-linejoin="miter"/><circle fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="14" cy="40" r="6" stroke-linejoin="miter"/></g></svg>','<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48"><g  transform="translate(0, 0)"><polygon fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="20,30 8,30 4,42 24,42 " stroke-linejoin="miter"/><polygon fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="40,30 28,30 24,42 44,42 " stroke-linejoin="miter"/><polygon fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="30,18 18,18 14,30 34,30 " stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="2" y1="24" x2="8" y2="24" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="8.4" y1="8.4" x2="12.7" y2="12.7" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="24" y1="2" x2="24" y2="8" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="39.6" y1="8.4" x2="35.3" y2="12.7" stroke-linejoin="miter"/><line data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="46" y1="24" x2="40" y2="24" stroke-linejoin="miter"/></g></svg>','<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="64px" height="64px" viewBox="0 0 64 64"><g  transform="translate(0, 0)"><line data-cap="butt" data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-miterlimit="10" x1="8" y1="36" x2="26" y2="36" stroke-linejoin="miter" stroke-linecap="butt"/><line data-cap="butt" data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-miterlimit="10" x1="8" y1="16" x2="26" y2="16" stroke-linejoin="miter" stroke-linecap="butt"/><polyline data-cap="butt" data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-miterlimit="10" points="8,2 8,56 26,56 " stroke-linejoin="miter" stroke-linecap="butt"/><rect x="26" y="11" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" width="30" height="10" stroke-linejoin="miter"/><rect x="26" y="51" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" width="30" height="10" stroke-linejoin="miter"/><rect x="26" y="31" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" width="30" height="10" stroke-linejoin="miter"/></g></svg>','<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="64px" height="64px" viewBox="0 0 64 64"><g  transform="translate(0, 0)"><circle fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="32" cy="32" r="26" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="32" y1="2" x2="32" y2="12" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="62" y1="32" x2="52" y2="32" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="32" y1="62" x2="32" y2="52" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="2" y1="32" x2="12" y2="32" stroke-linejoin="miter"/><circle data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="32" cy="32" r="4" stroke-linejoin="miter"/></g></svg>', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="64px" height="64px" viewBox="0 0 64 64"><g  transform="translate(0, 0)"><circle data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="32" cy="32" r="8" stroke-linejoin="miter"/><path fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" d="M62,38V26h-6.12c-1.484,0-2.92-0.857-3.577-2.446c-0.596-1.442-0.278-3.094,0.825-4.197l4.328-4.328l-8.485-8.485l-4.328,4.328c-1.044,1.044-2.655,1.458-4.244,0.806C38.951,11.083,38,9.685,38,8.12V2H26v6.12c0,1.483-0.856,2.92-2.446,3.577c-1.442,0.596-3.093,0.278-4.197-0.825l-4.328-4.328l-8.485,8.485l4.328,4.328c1.103,1.103,1.421,2.755,0.825,4.197C11.042,25.138,9.61,26,8.12,26H2v12h6.12c1.484,0,2.92,0.857,3.577,2.446c0.596,1.442,0.278,3.094-0.825,4.197l-4.328,4.328l8.485,8.485l4.328-4.328c1.044-1.044,2.655-1.458,4.244-0.806C25.049,52.917,26,54.315,26,55.88V62h12v-6.12c0-1.565,0.951-2.963,2.399-3.557c1.585-0.651,3.197-0.241,4.244,0.806l4.328,4.328l8.485-8.485l-4.328-4.328c-1.103-1.103-1.421-2.755-0.825-4.197C52.958,38.862,54.39,38,55.88,38H62z" stroke-linejoin="miter"/></g></svg>','<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="64px" height="64px" viewBox="0 0 64 64"><g  transform="translate(0, 0)"><polyline data-cap="butt" fill="none" stroke="#50ab1f" stroke-width="2" stroke-miterlimit="10" points="14,2 14,14 2,14 " stroke-linejoin="miter" stroke-linecap="butt"/><polyline fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" points="42,54 2,54 2,14 14,2 48,2 48,26 " stroke-linejoin="miter"/><polyline data-cap="butt" data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-miterlimit="10" points="54,48.3 54,60 48,56 42,60 42,48.3 " stroke-linejoin="miter" stroke-linecap="butt"/><circle data-color="color-2" fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" cx="48" cy="38" r="12" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="10" y1="24" x2="34" y2="24" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="10" y1="32" x2="28" y2="32" stroke-linejoin="miter"/><line fill="none" stroke="#50ab1f" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10" x1="10" y1="40" x2="28" y2="40" stroke-linejoin="miter"/></g></svg>');
          $i=0;
          foreach($page_sidemenu as $value){ ?>
          <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="our-services-box">
              <div class="our-services-items">
                <i><?php echo $icons[$i % count($icons)];?></i>
                <div class="our-services-content">
                  <h4><a href="<?php echo base_url();?><?php echo $value->page_slug;?>"><?php echo $value->page_name;?></a></h4>
                  <p align="justify"><?php echo trim($value->page_short_description);?></p>
                  <a href="<?php echo base_url();?><?php echo $value->page_slug;?>">read more<i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                </div><!-- .our-services-content -->
              </div><!-- .our-services-items -->
            </div><!-- .our-services-box -->
          </div><!-- .col-md-4 -->
          <?php $i = $i+1 ;} ?>
        </div><!-- .row -->
      </div><!-- .our-services-option -->
    </div><!-- .row -->
  </div><!-- .container -->
</section>
@endif
<?php if(site_flag('home_show_news') && !empty($news)){ ?>
<section class="bg-upcoming-events" id="events">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
      <div class="upcoming-events">
        <div class="section-header">
          <h2>{{ site_text('home_title_news') }}</h2>
        </div><!-- .section-header -->
        <div class="owl-latestnews owl-carousel owl-theme"> 
          <?php foreach($news as $value) { ?>
          <div class="item">
            <div class="event-items">
              <div class="event-img"> 
                  <?php //if(isset($value->featured_slug)){ ?>

                  <img src="<?php echo (isset($value->featured_slug) && strlen($value->featured_slug) ? $value->featured_slug : '/assets/images/defaultimage.png'); ?>" class="img-responsive" />
               
                  <div class="date-box">
                    <h3><?php echo date("d",strtotime((string) $value->post_display_date))?></h3>
                    <h5><?php echo date("F", strtotime((string) $value->post_display_date));?></h5>
                  </div><!-- .date-box -->
                </div><!-- .event-img -->
                <div class="events-content">
                  <h3><a href="<?php echo $value->post_slug;?>"><?php echo $value->post_name;?></a></h3>
                  <ul class="meta-post">
                  </ul>
                  <p><?php echo substr($value->post_description,0,300).'...';?></p>
                  <a href="<?php echo $value->post_slug;?>" class="btn-readmore">Read More</a>
                </div><!-- .events-content -->
              </div><!-- .events-items -->
            </div><!-- .col-md-6 -->
            <?php } ?>
          </div><!-- .row -->
        </div><!-- .upcoming-events -->
      
      
      
      </div><!-- .col-md-12 -->
      <div class="col-md-12 text-center">
            @if (site_text('home_news_button_text') !== '')<a href="{{ url('news') }}" class="btn btn-default">{{ site_text('home_news_button_text') }}</a>@endif    
      </div><!-- .col-md-12 -->
      </div><!-- .row -->
    </div><!-- .container -->
  </section>
  <?php } ?>
    <!-- Start campaian video Section -->
    <!-- <section class="bg-compaian-video">
    <div class="compaian-video-overlay">
      <div class="container">
        <div class="row">
          <div class="compaian-video">
            <?php $i=0; foreach ($videos as $key => $value) { 
              $link=str_replace("https://www.youtube.com/watch?v=","https://www.youtube.com/embed/",$value->video_link); ?>
              <a href="<?php echo $link;?>" data-rel="lightcase:myCollection" data-lc-categories="myCategory1">
                <?php if($i==0){ ?>
                <img src="assets/images/home02/video-icon.png" alt="video-icon" />
                <?php } ?>
              </a>
              <?php $i++; } ?>
              <h3>WATCH OUR LATEST VIDEOS</h3>
            </div> 
          </div> 
        </div> 
      </div> 
    </section> -->
    <!-- End campaian video Section -->
  
    <!--Start testimonials section-->
  <?php if(site_flag('home_show_testimonials') && !empty($testimonials))
    {
  ?>
  <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2>{{ site_text('home_title_testimonials') }}</h2>
            </div><!-- .section-header -->
            <div class="row">
                <div class="col-md-12">
                <div class="owl-testimonal owl-carousel owl-theme"> 
                    <?php foreach($testimonials as $value) {?>
                      <div class="item">
                        <div class="testimonial"> 
                          <img src="/assets/images/svgicons/quotes.svg"/>
                            <q>
                                <p class="text-center"><?php echo $value->description;?></p></q>
                            <hr style="width: 12%;margin: 20px 0;"/>
                            <div class="testimonial-details">
                              <div>
                                  <?php if(isset($value->profile) && !empty($value->profile)){ ?>
                                  <img src="<?php echo base_url().$value->profile; ?> " alt="">
                                <?php }?>
                              </div>
                              <div> 
                                <h5>
                                    <?php echo $value->name;?> 
                                </h5>
                                <!-- <p><?php echo $value->designation;?></p>  -->
                                <p><?php echo $value->org_name;?></p> 
                                <p><?php echo $value->location;?></p>
                              </div>
                            </div>
                        </div>
                      </div>
                    <?php }?>
                </div><!--owl carousel-->
              </div>
            </div><!-- .row -->
        </div><!-- .container -->
    </section>
  <?php
    }
  ?>
  @if (site_flag('home_show_gallery'))
  <section class="gallery-section"> 
  <div class="container">
            <div class="section-header">
                <h2>{{ site_text('home_title_gallery') }}</h2>
            </div><!-- .section-header -->
            <!-- images row -->
            <div class="row">
                <div class="col-md-offset-1 col-md-10 col-xs-offset-1 col-xs-10">
                    <div class="owl-carousel owl-gallery owl-theme">
                      <?php if(isset($gallery) && !empty($gallery)){
                        foreach($gallery as $value)
                        {
                      ?>
                        <div class="item">
                          <a target="_blank" href="<?php echo $value->media_slug;?>">
                            <img src="<?php echo $value->media_slug;?>"/>
                          </a>
                        </div>
                      <?php }
                      ?>

                      <?php }?>
                    </div>
                </div>
            </div><!--row-->
            <!-- videos row -->
            <div class="row">
                <div class="col-md-offset-1 col-md-10 col-xs-offset-1 col-xs-10">
                    <div class="owl-carousel owl-videos owl-theme"> 
                     <?php if(isset($videos) && !empty($videos)){
                        foreach($videos as $value)
                        {
                      ?>
                    <div class="item">
                      <div class="event-items">
                        <div class="event-img">   
                              <div class="item-video">
                                  <iframe src="<?php echo e(youtube_embed_url($value->video_link));?>" title="<?php echo $value->video_title;?>" frameborder="0" allow="accelerometer;clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                              </div> 
                          </div> 
                          <div class="events-content">
                            <h3><?php echo $value->video_title;?></h3>
                           
                            <p><?php echo isset($value->video_description) ? $value->video_description : ''?></p> 
                          </div> 
                        </div> 
                      </div>
                      <?php }
                      ?>

                      <?php }?> 
                    </div>
                </div>
            </div><!--row-->
  </div><!--container-->
  </section>
  @endif
    <!--End testimonials section-->
    <!-- Start Sponsors Section -->
    <?php if(site_flag('home_show_partners') && !empty($stakeholders)) { ?>
    <section class="bg-sponsors-section">
      <div class="container">
        <div class="section-header">
          <h2>{{ site_text('home_title_partners') }}</h2>
        </div><!-- .section-header -->
        <div class="row">
          <div class="col-md-12">
           <div class="owl-carousel owl-partners owl-theme">
            <?php foreach($stakeholders as $value) { ?>
                  <div class="item">
                    <div class="swiper-slide">
                  <div class="sopnsors-items item">
                    <a href="<?php echo (isset($value->stakeholder_link) ? $value->stakeholder_link : '#');?>" target="_blank"><img src="<?php echo $value->stakeholder_slug;?>" alt="<?php echo (isset($value->stakeholder_logo) ? $value->stakeholder_logo : '');?>" title="<?php echo (isset($value->stakeholder_title) ? $value->stakeholder_title : '');?>" class="img-responsive sponsors-img" /></a>
                  </div><!-- .sponsors-items -->
                </div><!-- .swiper-slide -->
                  </div>
              <?php } ?>
                
              </div><!-- .owl-carousel -->
          </div><!-- .col-md-12 -->
        </div><!-- .row -->
      </div><!-- .container -->
    </section>
    <?php } ?>
    <!-- End Sponsors Section -->
    <script type="text/javascript">
    //slider carousel
          $(document).ready(function () {
            var owl = $('.owl-slider1.owl-carousel');
            owl.owlCarousel({
                nav: true,
                margin: 15,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                loop: true,
                autoplay: true,
                navText: [" ", " "],
                dots: true,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 1,  
                    },
                    1000: {
                        items: 1
                    }
                }
            })
        })
    // Testimonials carousel
        $(document).ready(function () {
            var owl = $('.owl-testimonal.owl-carousel');
            owl.owlCarousel({
                nav: true,
                margin: 15,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                 loop: ( $('.owl-testimonal.owl-carousel .items').length > 2 ),
                autoplay: true,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 2,  
                    },
                    1000: {
                        items: 2
                    }
                }
            })
        })
        // owl-latestnews carousel
        $(document).ready(function () {
            var owl = $('.owl-latestnews.owl-carousel');
            owl.owlCarousel({
                nav: true,
                margin: 15,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                 loop: false,//( $('.owl-testimonal.owl-carousel .items').length > 2 ),
                autoplay: true,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 2,  
                    },
                    1000: {
                        items: 3
                    }
                }
            })
        })
        // owl-gallery carousel
        $(document).ready(function () {
            var owl = $('.owl-gallery.owl-carousel');
            owl.owlCarousel({
                nav: true,
                margin: 15,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                 loop: false,//( $('.owl-gallery.owl-carousel .items').length > 2 ),
                autoplay: true,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                dots: false,
                responsive: {
                    0: {
                        items: 2,
                    },
                    600: {
                        items: 4,  
                    },
                    1000: {
                        items: 6
                    }
                }
            })
        })
          // owl-videos carousel
          $(document).ready(function () {
            var owl = $('.owl-videos.owl-carousel');
            owl.owlCarousel({ 
                nav: true,
                margin: 15,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                 loop: false,//( $('.owl-gallery.owl-carousel .items').length > 2 ),
                autoplay: true,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 2,  
                    },
                    1000: {
                        items: 3
                    }
                }
            })
        })
        //owl-partners
        $(document).ready(function () {
            var owl = $('.owl-partners.owl-carousel');
            owl.owlCarousel({ 
                nav: true,
                margin: 15,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                 loop: false,//( $('.owl-gallery.owl-carousel .items').length > 2 ),
                autoplay: true,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 2,  
                    },
                    1000: {
                        items: 4
                    }
                }
            })
        })
    </script>
    <style>
      .count-css .msmer-count-clr{
        font-size:40px;
      }
    </style>
    </div>