<style>
  .jumbotron p{
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 400;
  }
  </style>
<div class="bg-page-main">
<!-- Start Page Header Section -->
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
<!-- Start Recent Project Section -->
<section class="bg-single-services">
  <div class="container">
    <div class=" ">
      <div class="single-services single-page">
        <div class="row">
          <?php 
          if ($page_type==2 || $page_type==3) 
            { ?>
          <div class="col-md-3">
            <div id="scrollNav" class="single-left-services-list">
              <ul class="nav nav-tabs" role="tablist">
                <?php  foreach ($page_sidemenu as $key => $value) {
                  $submenu=explode(',',$value->page_name);
                  // sort($submenu);
                  $class_name='';
                  if(!empty($submenu)){
                    $class_name="dropdown";
                    $sub_slug=explode(',',$value->page_slug);
                    $active=(in_array(request()->segment(1),$sub_slug) ? 'active': ''); 
                  }
                  ?>
                  <?php for($i=0;$i<count($submenu);$i++) { ?>
                  <?php $sub_link = isset($sub_slug[$i]) ? strtolower($sub_slug[$i]) : url_title(strtolower($submenu[$i])); ?>
                  <li <?php echo request()->segment(1) == $sub_link ? 'class="active"' : ''; ?>><a href="<?php echo base_url().$sub_link;?>"><?php echo $submenu[$i]?><span class="pull-right"><i class="fa fa-angle-double-right" aria-hidden="true"></i> </span></a></li>
                  <?php } ?>
                  <?php } ?>
                </ul> 
              </div><!-- .single-left-services-list -->
            </div><!-- .col-md-3 -->
            <div class="col-md-9">
              <div class="single-services-content single-page-content">
                <!-- <h3><?php echo ucwords($details->page_name);?></h3> -->
                <div class="single-page-full-content"> <?php echo $details->page_description;?></div>
                <?php if(isset($details->page_name) && (strtolower($details->page_name)=='parameters')){ 
                 echo $__env->make('public.parameter-sector', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); } ?>  
                <div class="single-page-full-content">  <?php if(isset($details->page_slug) && (strtolower($details->page_slug)=='guidance-document')){ 
                 echo $__env->make('public.guidance-document', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); } ?></div>
                 <?php if(isset($details->page_slug) && (strtolower($details->page_slug)=='zed-maturity-matrix' ||  strtolower($details->page_slug)=='zed-maturity-assessment-model')){
                  echo $__env->make('public.zed-maturity', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); } ?>
                </div><!-- .tab-content -->
              </div><!-- .col-md-9 -->
              <?php  } 
              else { ?>
              <div class="col-md-12 text-center">
                <h3><?php echo ucwords($details->page_name);?></h3>
                <?php if(isset($details->page_slug) && (strtolower($details->page_slug)=='zed-certified-msmes')){ ?>
                  <iframe src="https://loginzed.msme.gov.in/Dashboard/certifiedmsme" title="ZED Certified MSMEs" style="width:100%;min-height:1600px;height:100%;border:0px">
                  </iframe>
                  <!-- <div class="col-md-3">
                    <div class="form-group">
                      <label>NIC 2 Sector</label>
                      <select class="form-control secotr_filter" name="secotr_filter">
                        <option value="">Select Sector</option>
                        <option value="10 - Manufacture of food products">10 - Manufacture of food products</option>
                        <option value="11 - Manufacture of beverages">11 - Manufacture of beverages</option
                        <option value="12 - Manufacture of tobacco products">12 - Manufacture of tobacco product</option>
                        <option value="13 - Manufacture of textiles">13 - Manufacture of textiles</option>
                        <option value="14 - Manufacture of wearing apparel">14 - Manufacture of wearing apparel</option>
                        <option value="15 - Manufacture of leather and related products">15 - Manufacture of leather and related products</option>
                        <option value="16 - Manufacture of wood and products of wood and cork, except furniture; manufacture of articles of straw and plaiting materials">16 - Manufacture of wood and products of wood and cork, except furniture; manufacture of articles of straw and plaiting materials</option>
                        <option value="17 - Manufacture of paper and paper products">17 - Manufacture of paper and paper products</option>
                        <option value="18 - Printing and reproduction of recorded media">18 - Printing and reproduction of recorded media</option>
                        <option value="19 - Manufacture of coke and refined petroleum products">19 - Manufacture of coke and refined petroleum products</option>
                        <option value="20 - Manufacture of chemicals and chemical products">20 - Manufacture of chemicals and chemical products</option>
                        <option value="21 - Manufacture of pharmaceuticals, medicinal chemical and botanical products">21 - Manufacture of pharmaceuticals, medicinal chemical and botanical products</option>
                        <option value="22 - Manufacture of rubber and plastics products">22 - Manufacture of rubber and plastics products</option>
                        <option value="23 - Manufacture of other non-metallic mineral products">23 - Manufacture of other non-metallic mineral products</option>
                        <option value="24 - Manufacture of basic metals">24 - Manufacture of basic metals</option>
                        <option value="25 - Manufacture of fabricated metal products, except machinery and equipment">25 - Manufacture of fabricated metal products, except machinery and equipment</option>
                        <option value="26 - Manufacture of computer, electronic and optical products">26 - Manufacture of computer, electronic and optical products</option>
                        <option value="27 - Manufacture of electrical equipment">27 - Manufacture of electrical equipment</option>
                        <option value="28 - Manufacture of machinery and equipment n.e.c.">28 - Manufacture of machinery and equipment n.e.c.</option>
                        <option value="29 - Manufacture of motor vehicles, trailers and semi-trailers">29 - Manufacture of motor vehicles, trailers and semi-trailers</option>
                        <option value="30 - Manufacture of other transport equipment">30 - Manufacture of other transport equipment</option>
                        <option value="31 - Manufacture of furniture">31 - Manufacture of furniture</option>
                        <option value="32 - Other manufacturing">32 - Other manufacturing</option>
                        <option value="33 - Repair and installation of machinery and equipment">33 - Repair and installation of machinery and equipment</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label>State</label>
                    <select class="form-control state_filter">
                      <option value="">Select State</option>
                    <?php $states=array('AP' => 'Andhra Pradesh', 'AR' => 'Arunachal Pradesh', 'AS' => 'Assam', 'BR' => 'Bihar', 'CT' => 'Chhattisgarh', 'GA' => 'Goa', 'GJ' => 'Gujarat', 'HR' => 'Haryana', 'HP' => 'Himachal Pradesh', 'JK' => 'Jammu and Kashmir', 'JH' => 'Jharkhand', 'KA' => 'Karnataka', 'KL' => 'Kerala', 'MP' => 'Madhya Pradesh', 'MH' => 'Maharashtra', 'MN' => 'Manipur', 'ML' => 'Meghalaya', 'MZ' => 'Mizoram', 'NL' => 'Nagaland', 'OR' => 'Odisha', 'PB' => 'Punjab', 'RJ' => 'Rajasthan', 'SK' => 'Sikkim', 'TN' => 'Tamil Nadu', 'TG' => 'Telangana', 'TR' => 'Tripura', 'UT' => 'Uttarakhand', 'UP' => 'Uttar Pradesh', 'WB' => 'West Bengal', 'AN' => 'Andaman and Nicobar Islands', 'CH' => 'Chandigarh', 'DN' => 'Dadra and Nagar Haveli', 'DD' => 'Daman and Diu', 'DL' => 'Delhi', 'LD' => 'Lakshadweep', 'PY' => 'Puducherry');
                      foreach ($states as $key => $value) {?>
                        <option value="<?php echo $value;?>"><?php echo $value;?></option>
                    <?php  }
                    ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Size</label>
                      <select class="form-control size_filter" name='search_filter'>
                        <option value="">Select Size</option>
                        <option value="micro">Micro</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Certification</label>
                      <select class="form-control rate_filter" name='search_filter'>
                        <option value="">Select Certification</option>
                        <option value="Bronze">Bronze</option>
                        <option value="Silver">Silver</option>
                        <option value="Gold">Gold</option>
                     </select>
                    </div>
                  </div>
                  <div class="single-page-full-content">
                    <table class="table table-bordered datatables">
                      <colgroup>
                          <col width="10">
                          <col width="50">
                          <col width="300">
                          <col width="300">
                          <col width="50">
                          <col width="30">
                      </colgroup>
                      <thead>
                        <tr>
                          <th style="text-align: Center;">S. No.</th>
                          <th style="text-align: Center;">Enterprise Name</th>
                          <th style="text-align: left;">NIC 2 Sector</th>
                          <th style="text-align: Justify;">Unit Address<a href="http://www.integratedfire.in/"></a></th>
                          <th style="text-align: Center;">State</th>
                          <th style="text-align: Center;">Size</th>
                          <th style="text-align: Center;">Certification</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($zed_rating as $value){
                          ?>
                          <tr>
                            <th style="text-align: Center;">63</th>
                            <td><p style="text-align: Center;"><b><a href="<?php echo $value['ep_url'];?>" target="blank"><?php echo $value['ep_name'];?></a></b></p></td>
                            <td style="text-align: left;"><?php echo $value['sector'];?></td>
                            <td style="text-align: left;"><?php echo $value['unit_address'];?></td>
                            <td style="text-align: Center;"><?php echo $value['state'];?></td>
                            <td style="text-align: Center;"><?php echo ucfirst($value['size']);?></td>
                            <td style="text-align: Center;"><?php echo ucfirst($value['rating']);?></td>
                          </tr>
                       <?php }?>
                      </tbody>
                    </table>
                  </div> -->
                  <?php } else{?>
                <div class="single-page-full-content"><?php echo $details->page_description;?></div>
                <?php if(isset($details->page_slug) && (strtolower($details->page_slug)=='zed-maturity-matrix')){ 
                  echo $__env->make('public.zed-maturity', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); } ?>
                </div>
                <?php } ?>
                <?php if(isset($details->page_slug) && (strtolower($details->page_slug)=='kawach-certified-msmes')){ ?>
                  <iframe src="https://msmekawach.org.in/Dashboard/CertifiedMsme" title="ZED Certified MSMEs" style="width:100%;min-height:1600px;height:100%;border:0px">
                  </iframe>
                <?php }?>
                <!-- for verified certificate start -->
                  <?php if(isset($details->page_slug) && (strtolower($details->page_slug)=='verify-zed-certificate')){ ?>
                    <!-- <form class="form-inline" style="margin-top:20px" action="/zee/verify-zed-certificate" method="post"> -->
                    <?php echo form_open("/verify-zed-certificate",array('class'=>'form-inline text-center','style'=>'margin-top:50px;margin-bottom:100px')); ?>

                      <div class="col-md-12" style="margin-bottom:20px">
                        <div class="form-group mb-5">
                          <label for="udyamNumber">Udyam Number: </label>
                          <input type="text" style="width:290px" class="form-control" id="udyamNumber" name="udyamNumber" value="<?php echo e($udyamNumber ?? ''); ?>" placeholder="Udyam Number">
                        </div>
                        <div class="form-group mx-sm-5 mb-5" style="margin-left: 15px;">
                          <label for="certificationNumber">Certification Number: </label>
                          <input type="text" style="width:290px" class="form-control" id="certificationNumber" name="certificationNumber" value="<?php echo e($certificationNumber ?? ''); ?>" placeholder="Certification Number">
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                      </div>
                    <!-- </form> -->
                    <?php echo form_close();?>
                    <?php if(!empty($verifyCertification)){ ?>
                    <div class=" ">
                      <div class="col-md-12 jumbotron" >
                        <?php $ctf = $verifyCertification; 
                              //$ctf = json_decode($vctf["path"],true);
                              if(isset($ctf['StatusCode']) && $ctf['StatusCode'] == 200){
                        ?>
                        <h4><u>Plant Details</u></h4>
                        <p> Unit Name: 
                          <?php echo e($ctf["PlantDetails"]["UnitName"] ?? ''); ?>
                        </p>
                        <p> Address: 
                          <?php echo e($ctf["PlantDetails"]["PAddress"] ?? ''); ?>
                        </p>
                        <p> Pin: 
                          <?php echo e($ctf["PlantDetails"]["PPin"] ?? ''); ?>
                        </p>
                        <p> District Name: 
                          <?php echo e($ctf["PlantDetails"]["DISTRICT_NAME"] ?? ''); ?>
                        </p>
                        <p> State Name: 
                          <?php echo e($ctf["PlantDetails"]["state_name"] ?? ''); ?>
                        </p>
                        <br/>
                        <h4><u>Certification Details</u></h4>
                        <p> Certification Date: 
                          <?php echo e($ctf["CertificationDetails"]["CertificationDate"] ?? ''); ?>
                        </p>
                        <p> Date of Expiry: 
                          <?php echo e($ctf["CertificationDetails"]["ExpiryDate"] ?? ''); ?>
                        </p>
                        <p> Certification: 
                        <?php 
                          if(isset($ctf["CertificationDetails"]["BronzeCertified"]) && $ctf["CertificationDetails"]["BronzeCertified"] =="Yes"){
                              echo "Bronze";
                          } if(isset($ctf["CertificationDetails"]["SilverCertified"]) && $ctf["CertificationDetails"]["SilverCertified"] =="Yes"){
                              echo "Silver";
                          } if(isset($ctf["CertificationDetails"]["GoldCertified"]) && $ctf["CertificationDetails"]["GoldCertified"] =="Yes"){
                              echo "Gold";
                          }
                        ?>
                        </p>
                        <?php if(isset($ctf["CertificationDetails"]["Certificate"]) && !is_null($ctf["CertificationDetails"]["Certificate"]) ){?>
                        <p> Certificate: 
                          <?php echo e($ctf["CertificationDetails"]["Certificate"]); ?>
                        </p>
                        <?php } ?>
                        <?php }else{
                          echo "<h4 style='margin-top:10px;left:20%'><center>No Certificate found</center></h4>";
                        } ?>
                      </div>
                    </div>
                    <?php } ?>
                  <?php } ?>
                <!-- verified end -->
                <?php } ?>
              </div><!-- .row -->
            </div><!-- .single-services -->
          </div><!-- .row -->
        </div><!-- .container -->
      </section> 
    <!-- End Recent Project Section -->
    </div>
    <script type="text/javascript">
    //  var width = $(window).width();
    //   if (width > 768) {
    //     $(window).scroll(function(){
    //       if ($(this).scrollTop() > 150) {
    //         $('#scrollNav').addClass('scroll-nav');
    //       } else {
    //         $('#scrollNav').removeClass('scroll-nav');
    //       }
    //     }); 
    //   }
</script>