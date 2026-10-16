 <section  class="bg-search-header">
 	<div class="container">
			<div class="row">
 				<div class="col-md-offset-1 col-md-10 col-sm-12 col-xs-12">
						 <h3>You Serached for "<?php echo html_escape(isset($search) ? $search : '');?>"</h3>
						 <p>Found <?php echo isset($details) ? count($details) : 0;?> results matching your search term.</p>
				 </div>
			</div>
			<?php ?>
			<div class="row">
 				<div class="col-md-offset-1 col-md-10 col-sm-12 col-xs-12">
					 <!-- no results found block  -->
					 <?php if(!isset($details)){?>
					 <ul class="no-result">
					 	<li><p>Check if your spelling is correct.</p></li>
						<li><p>Remove quotes around phrases to search for each word individually. bike shed will often show more results than "bike shed".</p></li>
						<li><p>Consider loosening your query with OR. bike OR shed will often show more results than bike shed.</p></li>
					</ul>
					<?php }?>
					<!-- no results found block  -->
					<?php if(isset($details)) {
						foreach ($details as $key => $value) { ?>
					<div class="card search-card ">
						<div class="card-header"><h4><?php echo $value->page_name;?></h4></div>
						<div class="card-body"><p><?php echo substr(preg_replace("/<img[^>]+\>/i", "", $value->page_description),0,1000); ?>...</p></div> 
					</div> 
					<?php }?>
					<?php } ?>
				 </div>
			</div>
	</div>
</section>
