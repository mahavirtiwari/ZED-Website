<div class="bg-page-main">
<!-- Start Recent Project Section -->
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
<section class="incentive-page">
  <div class=" ">
        <div class=" ">
            <div class=" ">
            <iframe frameborder="0" src="{{ $details->page_embed_url }}" title="{{ $details->page_name }}"></iframe>
            </div>
        </div>
    </div><!-- .container -->
</section> 
    <!-- End Recent Project Section -->
</div> 