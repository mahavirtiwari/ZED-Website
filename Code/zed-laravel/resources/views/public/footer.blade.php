<!-- Start Footer Section --> 
<footer id="footerPos">
	<div class="bg-footer-top">
		<div class="container">
			<div class="row">
				<div class="footer-top">
					<div class="row">
						<div class="col-sm-3">
							<div class="footer-widgets">
								<div class="widgets-title">
									<h3>Contact Us</h3>
								</div>
								<div class="address-box">
									<ul class="address">
										<?php if(isset($counters['address']) && !empty($counters['address'])){ ?>
											<li><span><?php echo $counters['address']; ?></span></li>
										<?php } ?>
										<?php if(isset($counters['label_name_1']) && !empty($counters['label_name_1'])){ ?>
											<li><i class="fa fa-phone" aria-hidden="true"></i><span><b><?php echo $counters['label_name_1']; ?>:<br></b><?php echo $counters['zed_phone_1']; ?><br><?php echo $counters['zed_phone_2']; ?></span></li>
										<?php } ?>
										<?php if(isset($counters['zed_email_id']) && !empty($counters['zed_email_id'])){ ?>
											<li><i class="fa fa-envelope-o" aria-hidden="true"></i><span><?php echo $counters['zed_email_id']; ?></span> </li>
										<?php } ?>
										<?php if(isset($counters['label_name_1']) && !empty($counters['label_name_1'])){ ?>
											<li><i class="fa fa-phone" aria-hidden="true"></i><span><b><?php echo $counters['label_name_2']; ?>:<br></b><?php echo $counters['tp_phone_1']; ?><br><?php echo $counters['tp_phone_2']; ?><br><?php echo $counters['tp_phone_3']; ?></span></li>
										<?php } ?>
										<?php if(isset($counters['tp_email_id']) && !empty($counters['tp_email_id'])){ ?>
											<li><i class="fa fa-envelope-o" aria-hidden="true"></i><span><?php echo $counters['tp_email_id']; ?></span> </li>
										<?php } ?>
										<?php if(isset($counters['button_url']) && !empty($counters['button_url']) && isset($counters['button_name']) && !empty($counters['button_name'])){ ?>
											<li><a href="<?php echo $counters['button_url']; ?>" target="_blank" class="link-url"><?php echo $counters['button_name']; ?></a> </li>
										<?php } ?>
									</ul>
								</div><!-- .address -->
							</div><!-- .footer-widgets -->
						</div>
						
						
							<?php foreach($menu as $value) { 
								if($value->menu_type==2){ ?>
								<div class="col-sm-3">
								<div class="footer-widgets">
									<div class="widgets-title">
										<h3><?php echo $value->menu_name;?></h3>
									</div>
									<div class="list-group">
									<?php $submenu=explode(',',$value->page_name); $sub_slug=explode(',',(string) $value->page_slug); ?>
									<?php for($i=0;$i<count($submenu);$i++) { ?>
									 <a href="<?php echo base_url().(isset($sub_slug[$i]) ? strtolower($sub_slug[$i]) : url_title(strtolower($submenu[$i])));?>" class="list-group-item"><?php echo $submenu[$i]?></a>
									<?php } ?>
									</div>
								</div>
</div>
								<?php }
							} ?>
						
					</div> 
				</div><!-- .footer-top -->
			</div><!-- .row -->
		</div><!-- .container -->
	</div><!-- .bg-footer-top -->
	<div class="bg-footer-bottom">
		<div class="container">
			<div class="row">
				<div class="footer-bottom"> 
					<div class="row">
						<div class="col-sm-4">
							<div class="copyright-txt">
								<p>ZED © 2022 All Rights Reserved | Version- 2.1.1</p>
							</div><!-- .copyright-txt -->
						</div>
						<div class="col-sm-4 text-center">
							<a href="http://www.qcin.org/" target="_blank" class="qci-logo-footer"><img src="<?php echo base_url() ?>assets/images/zed-icon.png" alt="QCI"></a>
						</div>
						<div class="col-sm-4">
							<div class="social-box">
								<ul class="social-icon-rounded">
									<li><a target="_blank" href="https://www.facebook.com/ZED.QCI"><img src="/assets/images/svgicons/fb.svg" alt="facebook"/></a></li>
									<li><a target="_blank" href="https://twitter.com/ZED_QCI"><img src="/assets/images/svgicons/twitter.svg" alt="twitter"/></a></li>
									<li><a target="_blank" href="https://www.youtube.com/channel/UCSoN3CodGbQGe7a0t7wZiHQ"><img src="/assets/images/svgicons/youtube.svg" alt="youtube"/></a></a></li>
									<li><a target="_blank" href="https://www.linkedin.com/in/zed-qci-13b758140/"><img src="/assets/images/svgicons/linkedin.svg" alt="linkedin"/></a></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div><!-- .footer-bottom -->
			</div><!-- .row -->
		</div><!-- .container -->
	</div><!-- .bg-footer-bottom -->
</footer>
<!-- End Footer Section -->
<!-- Start Scrolling -->
<div class="scroll-img"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
<!-- End Scrolling -->
<!-- Start Pre-Loader-->


