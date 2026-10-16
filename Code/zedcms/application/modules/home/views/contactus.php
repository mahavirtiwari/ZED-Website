<!-- Start Page Header Section -->
<section class="bg-page-header">
    <div class=" ">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <div class="page-title">
                        <h2>Contact</h2>
                    </div><!-- .page-title -->
                    <div class="page-header-content">
                        <ol class="breadcrumb">
                            <li><a href="<?php echo base_url();?>">Home</a></li>
                            <li>Contact</li>
                        </ol>
                    </div><!-- .page-header-content -->
                </div><!-- .page-header -->
            </div><!-- .row -->
        </div><!-- .container -->
    </div><!-- .page-header-overlay -->
</section>
<section class="bg-contact-us">
    <div class="container">
        <div class="row">
            <div class="contact-us">
                <div class="row">
                    <?php if($this->session->flashdata()){ ?>
                    <div class="alert alert-success"><?php echo $this->session->flashdata('message');?></div>
                    <?php }?>



                    <div class="col-md-8">
                        <?php $this->load->view('zoho_form'); ?>
                    </div>



                    <div class="col-md-4">
                        <h3 class="contact-title">Contact Info</h3>
                        <ul class="contact-address">
                            <li>
                                <i class="flaticon-time-left"></i>
                                <div class="contact-content">
                                    <p>Working days<br>Mon-Fri, 9:00 AM - 5:30 PM</p>
                                </div>
                            </li>
                            <li>
                                <i class="flaticon-placeholder"></i>
                                <div class="contact-content">
                                    <?php if(isset($counters['address']) && !empty($counters['address'])){ ?>
                                    <p><span><?php echo $counters['address']; ?></span></p>
                                    <?php } ?>
                                </div>
                            </li>
                            <?php if(isset($counters['label_name_1']) && !empty($counters['label_name_1'])){ ?>
                            <li class="contact-address-no-pad-li">
                                <i class="flaticon-vibrating-phone"></i>
                                <div class="contact-content">
                                    <p><b><?php echo $counters['label_name_1']; ?>:<br></b><?php echo $counters['zed_phone_1']; ?><br><?php echo $counters['zed_phone_2']; ?>
                                    </p>
                                </div>
                            </li>
                            <?php } ?>
                            <?php if(isset($counters['zed_email_id']) && !empty($counters['zed_email_id'])){ ?>
                            <li>
                                <i class="flaticon-message"></i>
                                <div class="contact-content">
                                    <p>Email:<br><?php echo $counters['zed_email_id']; ?></p>
                                </div>
                            </li>
                            <?php } ?>
                            <?php if(isset($counters['label_name_1']) && !empty($counters['label_name_1'])){ ?>
                            <li class="contact-address-no-pad-li">
                                <i class="flaticon-vibrating-phone"></i>
                                <div class="contact-content">
                                    <?php // <p>011-23708371</p> ?>
                                    <p><b><?php echo $counters['label_name_2']; ?></b><br><?php echo $counters['tp_phone_1']; ?><br><?php echo $counters['tp_phone_2']; ?><br><?php echo $counters['tp_phone_3']; ?>
                                    </p>
                                </div>
                            </li>
                            <?php } ?>
                            <?php if(isset($counters['tp_email_id']) && !empty($counters['tp_email_id'])){ ?>
                            <li>
                                <i class="flaticon-message"></i>
                                <div class="contact-content">
                                    <p>Email:<br><?php echo $counters['tp_email_id']; ?> </p>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div><!-- .col-md-4 -->
                </div><!-- .row -->
            </div><!-- .contact-us -->
        </div><!-- .row -->
    </div><!-- .container -->
</section>
<!-- End Contact us Section -->
<!-- STart Maps Section -->
<!-- <div id="map"></div> -->
