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
									<h3>{{ site_text('footer_contact_heading') }}</h3>
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
										<?php if(isset($counters['label_name_2']) && !empty($counters['label_name_2'])){ ?>
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
								<p>{{ site_text('footer_copyright') }}</p>
							</div><!-- .copyright-txt -->
						</div>
						<div class="col-sm-4 text-center">
							<a href="{{ site_url('footer_logo_url') ?: url('/') }}" target="_blank" rel="noopener" class="qci-logo-footer"><img src="{{ site_image('footer_logo') }}" alt="QCI"></a>
						</div>
						<div class="col-sm-4">
							<div class="social-box">
								<ul class="social-icon-rounded">
									@foreach (['social_facebook' => ['fb', 'Facebook'], 'social_twitter' => ['twitter', 'X (Twitter)'], 'social_youtube' => ['youtube', 'YouTube'], 'social_linkedin' => ['linkedin', 'LinkedIn']] as $setting => [$icon, $name])
										@if ($link = site_url($setting))
											<li><a target="_blank" rel="noopener" href="{{ $link }}"><img src="/assets/images/svgicons/{{ $icon }}.svg" alt="{{ $name }}"/></a></li>
										@endif
										@endforeach
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


