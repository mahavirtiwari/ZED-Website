<!-- Start Page Header Section -->
<section class="bg-page-header">
	<div class=" ">
		<div class="container">
			<div class="row">
				<div class="page-header">
					<div class="page-title">
						<h2><?php echo html_escape($month.' - '.$year);?></h2>
					</div><!-- .page-title -->
					<div class="page-header-content">
						<ol class="breadcrumb">
							<li><a href="/gallery">View All</a></li>
						</ol>
					</div><!-- .page-header-content -->
				</div><!-- .page-header -->
			</div><!-- .row -->
		</div><!-- .container -->
	</div><!-- .page-header-overlay -->
</section>
<!-- End Page Header Section -->
<!-- Start Recent Project Section -->
<section class="bg-single-services bg-recent-project-home3">
	<div class="container">
		<div class="row">
			<div class="recent-project photo-gallery">
				<div class="portfolio-items portfolio-items-home3">
					<?php $i=1; foreach($details as $value ) { ?>
					<div class="item cat" data-category="transition">
						<div class="item-inner">
							<div class="portfolio-img">
								<div class="overlay-project"></div><!-- .overlay-project -->
								<img src="<?php echo $value->media_slug;?>" alt="<?php echo $value->media_caption;?>" title="<?php echo $value->media_title;?>" ">
								<div class="project-plus">
									<a href="<?php echo $value->media_slug;?>" data-rel="lightcase:myCollection" data-lc-categories="myCategorymonth"><i class="fa fa-plus" aria-hidden="true"></i></a>
								</div>
								<div class="recent-project-content">
									<p><a href="<?php echo $value->media_slug;?>" data-rel="lightcase:myCollection" data-lc-categories="myCategorycontent" data-lc-caption="<?php echo $value->media_caption;?>" ><?php echo substr($value->media_title,0,20).'...';?></a></p>
								</div><!-- .latest-port-content -->
							</div><!-- /.portfolio-img -->
						</div><!-- .item-inner -->
					</div><!-- .items -->
					<?php $i++;} ?>
				</div><!-- .isotope-items -->
			</div><!-- .recent-project -->
		</div><!-- .row -->
	</div><!-- .container -->
</section>
<!-- End Recent Project Section -->
