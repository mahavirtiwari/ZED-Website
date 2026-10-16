<!-- Start Page Header Section -->
<section class="bg-page-header">
  <div class=" ">
    <div class="container">
      <div class="row">
        <div class="page-header">
          <div class="page-title">
            <h2>FAQ</h2>
          </div><!-- .page-title -->
          <div class="page-header-content">
            <ol class="breadcrumb">
              <li><a href="{{ url('') }}">Home</a></li>
              <li class="active">FAQ</li>
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
    <div class="row">
      <div class="single-services single-page faq-section">
        <div class="row">
              <div class="col-md-12">
                <h3>FAQ</h3>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                  <?php 
                  $i=0;
                  foreach($faqs as $value){
                  ?>
                    <div class="panel panel-default">
                      <div class="panel-heading" role="tab" id="headingOne<?php echo $i;?>">
                        <h4 class="panel-title">
                          <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne<?php echo $i;?>" aria-expanded="true" aria-controls="collapseOne<?php echo $i;?>">
                          <span class="Q"> Q. </span> <?php echo $value->faq_question;?> ? <i class="accordion_icon fa fa-plus"></i>
                          </a>
                        </h4>
                      </div>
                      <div id="collapseOne<?php echo $i;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne<?php echo $i;?>">
                        <div class="panel-body">
                          <span class="A">A. </span> <?php echo $value->faq_answer;?>
                           </div>
                      </div>
                    </div>
                  <?php
                    $i++;
                    }
                  ?>
                </div><!--accordin_id-->
              </div><!-- .row -->
            </div><!-- .single-services -->
          </div><!-- .row -->
        </div><!-- .container -->
      </section>
    <!-- End Recent Project Section -->

